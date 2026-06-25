<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'active' => $this->boolean('active'),
            'price' => str_replace(',', '', (string) $this->price),
            'compare_at_price' => $this->compare_at_price !== null ? str_replace(',', '', (string) $this->compare_at_price) : null,
        ]);
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $maxUpload = config('services.store.max_upload_kb', 2048);

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('products', 'slug')->ignore($product)],
            'sku' => ['nullable', 'string', 'max:80', Rule::unique('products', 'sku')->ignore($product)],
            'brand' => ['nullable', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:1', 'max:999999'],
            'compare_at_price' => ['nullable', 'numeric', 'gt:price', 'max:999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'featured' => ['boolean'],
            'active' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:220'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.$maxUpload],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.sku' => ['nullable', 'string', 'max:80'],
            'variants.*.size' => ['nullable', 'string', 'max:60'],
            'variants.*.color' => ['nullable', 'string', 'max:60'],
            'variants.*.flavor' => ['nullable', 'string', 'max:80'],
            'variants.*.presentation' => ['nullable', 'string', 'max:80'],
            'variants.*.price_modifier' => ['nullable', 'numeric', 'min:-999999', 'max:999999'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'variants.*.active' => ['nullable', 'boolean'],
        ];
    }
}
