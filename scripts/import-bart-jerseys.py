import json
import re
import unicodedata
from concurrent.futures import ThreadPoolExecutor, as_completed
from io import BytesIO
from pathlib import Path
from urllib.parse import urlparse
from urllib.request import Request, urlopen

import numpy as np
from PIL import Image


BASE_URL = "https://bartjerseys.com"
CATALOG_PATH = Path("database/seeders/data/bart_jerseys_catalog.json")
IMAGE_DIR = Path("public/assets/jerseys/products")
BLUE = (82, 113, 244)


def fetch_json(url: str) -> dict:
    req = Request(url, headers={"User-Agent": "GoCenterCatalogImporter/1.0"})
    with urlopen(req, timeout=60) as response:
        return json.loads(response.read().decode("utf-8"))


def fetch_bytes(url: str) -> bytes:
    req = Request(url, headers={"User-Agent": "GoCenterCatalogImporter/1.0"})
    with urlopen(req, timeout=60) as response:
        return response.read()


def slug(value: str) -> str:
    value = unicodedata.normalize("NFKD", value).encode("ascii", "ignore").decode("ascii")
    value = re.sub(r"[^a-zA-Z0-9]+", "-", value).strip("-").lower()
    return value or "producto"


def clean_name(value: str) -> str:
    return unicodedata.normalize("NFKD", value).encode("ascii", "ignore").decode("ascii")


def money(value) -> float:
    return round(float(value or 0), 2)


def useful_image_urls(product: dict) -> list[str]:
    urls = []

    for image in product.get("images", []):
        src = image.get("src")
        if not src:
            continue

        name = Path(urlparse(src).path).name.upper()
        if "IMAGENESEXTRA" in name or "OFERTA" in name or "SIZE" in name or "CHART" in name:
            continue

        urls.append(src)

    if not urls:
        urls = [image["src"] for image in product.get("images", [])[:2] if image.get("src")]

    return urls[:2]


def remove_blue_background(image: Image.Image) -> Image.Image:
    image = image.convert("RGBA")
    data = np.array(image, dtype=np.float32)
    rgb = data[:, :, :3]
    dist = np.sqrt(np.sum((rgb - np.array(BLUE, dtype=np.float32)) ** 2, axis=2))
    r = data[:, :, 0]
    g = data[:, :, 1]
    b = data[:, :, 2]

    hard_mask = (dist < 58) & (b > 150) & (r < 145) & (g < 170)
    soft_mask = (dist >= 58) & (dist < 82) & (b > 135) & (r < 165) & (g < 190)

    data[:, :, 3][hard_mask] = 0
    data[:, :, 3][soft_mask] = np.clip(((dist[soft_mask] - 58) / 24) * 255, 0, 255)
    image = Image.fromarray(data.astype(np.uint8), "RGBA")

    bbox = image.getbbox()
    if bbox:
        image = image.crop(bbox)

    max_width = 1100
    if image.width > max_width:
        ratio = max_width / image.width
        image = image.resize((max_width, int(image.height * ratio)), Image.Resampling.LANCZOS)

    return image


def save_product_image(url: str, product_slug: str, index: int) -> str:
    target = IMAGE_DIR / f"{product_slug}-{index}.webp"
    if not target.exists():
        source = Image.open(BytesIO(fetch_bytes(url)))
        processed = remove_blue_background(source)
        processed.save(target, "WEBP", quality=80, method=4)

    return str(target).replace("\\", "/").removeprefix("public/")


def description_for(product: dict) -> str:
    title = clean_name(product["title"])
    options = {option["name"]: option.get("values", []) for option in product.get("options", [])}
    sizes = ", ".join(options.get("Talla", []))
    dorsals = ", ".join(options.get("Dorsal", []))
    versions = ", ".join(options.get("Versión", []))

    parts = [f"Jersey {title}."]

    if sizes:
        parts.append(f"Disponible en tallas {sizes}.")

    if dorsals:
        parts.append(f"Opciones de dorsal: {dorsals}.")

    if versions:
        parts.append(f"Versiones disponibles: {versions}.")

    parts.append("Tela ligera y comoda para uso diario, entrenamiento o coleccion.")

    return " ".join(parts)


def build_product(product: dict, image_paths: dict[str, list[str]]) -> dict | None:
    variants = [variant for variant in product.get("variants", []) if variant.get("available", True)]
    if not variants:
        variants = product.get("variants", [])

    if not variants:
        return None

    product_slug = slug(product["handle"] or product["title"])
    prices = [money(variant.get("price")) for variant in variants]
    compare_prices = [money(variant.get("compare_at_price")) for variant in variants if variant.get("compare_at_price")]
    base_price = min(prices)
    base_compare = min(compare_prices) if compare_prices else None
    images = image_paths.get(product_slug, [])

    return {
        "source_id": product["id"],
        "name": clean_name(product["title"]),
        "slug": product_slug,
        "brand": clean_name(product.get("vendor") or "Bart Jerseys"),
        "description": description_for(product),
        "price": base_price,
        "compare_at_price": base_compare,
        "stock": max(10, len(variants) * 10),
        "images": images,
        "variants": [
            {
                "sku": f"BJ-{product['id']}-{variant['id']}",
                "size": clean_name(variant.get("option1") or ""),
                "color": clean_name(variant.get("option2") or ""),
                "presentation": clean_name(variant.get("option3") or ""),
                "price_modifier": round(money(variant.get("price")) - base_price, 2),
                "stock": 10 if variant.get("available", True) else 0,
                "active": bool(variant.get("available", True)),
            }
            for variant in variants
        ],
    }


def main() -> None:
    CATALOG_PATH.parent.mkdir(parents=True, exist_ok=True)
    IMAGE_DIR.mkdir(parents=True, exist_ok=True)

    raw_products = []
    page = 1

    while True:
        data = fetch_json(f"{BASE_URL}/products.json?limit=250&page={page}")
        batch = data.get("products", [])
        print(f"page {page}: {len(batch)} products")
        raw_products.extend(batch)

        if len(batch) < 250:
            break

        page += 1

    image_jobs = []
    image_paths: dict[str, list[str]] = {}

    for product in raw_products:
        product_slug = slug(product["handle"] or product["title"])
        urls = useful_image_urls(product)
        image_paths[product_slug] = [
            str(IMAGE_DIR / f"{product_slug}-{index + 1}.webp").replace("\\", "/").removeprefix("public/")
            for index in range(len(urls))
        ]

        for index, url in enumerate(urls):
            image_jobs.append((url, product_slug, index + 1))

    with ThreadPoolExecutor(max_workers=10) as executor:
        futures = [executor.submit(save_product_image, *job) for job in image_jobs]

        for done, future in enumerate(as_completed(futures), start=1):
            future.result()
            if done % 100 == 0 or done == len(futures):
                print(f"images {done}/{len(futures)}")

    products = []

    for product in raw_products:
        built = build_product(product, image_paths)
        if built:
            products.append(built)

    CATALOG_PATH.write_text(
        json.dumps(products, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )
    print(f"wrote {len(products)} products to {CATALOG_PATH}")


if __name__ == "__main__":
    main()
