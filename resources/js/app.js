import './bootstrap';

import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => {
    if (! Alpine.started) {
        Alpine.start();
        Alpine.started = true;
    }

    createIcons({ icons });

    const searchDialog = document.getElementById('product-search-dialog');
    const searchInput = searchDialog?.querySelector('[data-search-input]');
    const openSearchDialog = () => {
        if (! searchDialog) {
            return;
        }

        if (! searchDialog.open) {
            searchDialog.showModal();
        }

        window.setTimeout(() => searchInput?.focus(), 80);
    };

    document.querySelectorAll('[data-search-open]').forEach((button) => {
        button.addEventListener('click', openSearchDialog);
    });

    document.querySelectorAll('[data-search-close]').forEach((button) => {
        button.addEventListener('click', () => searchDialog?.close());
    });

    searchDialog?.addEventListener('click', (event) => {
        if (event.target === searchDialog) {
            searchDialog.close();
        }
    });

    if (new URLSearchParams(window.location.search).get('buscar') === '1') {
        openSearchDialog();
    }

    document.querySelectorAll('[data-carousel]').forEach((carousel) => {
        const slides = Array.from(carousel.querySelectorAll('[data-carousel-slide]'));
        const dots = Array.from(carousel.querySelectorAll('[data-carousel-dot]'));
        const intervalMs = Number(carousel.dataset.carouselInterval || 6000);
        let active = Math.max(0, slides.findIndex((slide) => slide.classList.contains('active')));
        let timer = null;
        let startX = 0;
        let startY = 0;

        if (! slides.length) {
            return;
        }

        const go = (index) => {
            active = (index + slides.length) % slides.length;
            slides.forEach((slide, slideIndex) => {
                const isActive = slideIndex === active;
                slide.classList.toggle('active', isActive);
                slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            });
            dots.forEach((dot, dotIndex) => dot.classList.toggle('active', dotIndex === active));
        };

        const next = () => go(active + 1);
        const prev = () => go(active - 1);
        const stop = () => window.clearInterval(timer);
        const start = () => {
            stop();
            if (slides.length > 1 && intervalMs > 0) {
                timer = window.setInterval(next, intervalMs);
            }
        };

        carousel.classList.add('is-ready');
        go(active);
        start();

        carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
            next();
            start();
        });
        carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
            prev();
            start();
        });
        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                go(Number(dot.dataset.carouselDot || 0));
                start();
            });
        });

        carousel.addEventListener('mouseenter', stop);
        carousel.addEventListener('mouseleave', start);
        carousel.addEventListener('touchstart', (event) => {
            const touch = event.changedTouches[0];
            startX = touch.clientX;
            startY = touch.clientY;
        }, { passive: true });
        carousel.addEventListener('touchend', (event) => {
            const touch = event.changedTouches[0];
            const deltaX = touch.clientX - startX;
            const deltaY = touch.clientY - startY;

            if (Math.abs(deltaX) < 44 || Math.abs(deltaX) < Math.abs(deltaY) * 1.2) {
                return;
            }

            deltaX < 0 ? next() : prev();
            start();
        }, { passive: true });
    });

    document.querySelectorAll('[data-scroll-carousel]').forEach((track) => {
        const scrollByCard = (direction) => {
            const card = track.querySelector(':scope > *');
            const amount = card ? card.getBoundingClientRect().width + 16 : track.clientWidth * 0.85;
            track.scrollBy({ left: direction * amount, behavior: 'smooth' });
        };

        document.querySelector(`[data-scroll-carousel-prev="${track.id}"]`)?.addEventListener('click', () => scrollByCard(-1));
        document.querySelector(`[data-scroll-carousel-next="${track.id}"]`)?.addEventListener('click', () => scrollByCard(1));
    });

    document.querySelectorAll('[data-clip-test]').forEach((button) => {
        const result = document.querySelector(button.dataset.clipTestResult);
        const form = document.querySelector(button.dataset.clipTestForm);

        button.addEventListener('click', async () => {
            if (! form || ! result) {
                return;
            }

            button.disabled = true;
            result.textContent = 'Comprobando conexion con Clip...';
            result.className = 'mt-3 rounded-md border border-zinc-800 bg-zinc-950 p-3 text-sm text-zinc-300';

            try {
                const response = await fetch(button.dataset.clipTest, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                });
                const data = await response.json();
                const ok = response.ok && data.ok;

                result.className = ok
                    ? 'mt-3 rounded-md border border-emerald-400/30 bg-emerald-400/10 p-3 text-sm text-emerald-100'
                    : 'mt-3 rounded-md border border-red-400/30 bg-red-400/10 p-3 text-sm text-red-100';
                result.textContent = data.message || data.mensaje || (ok ? 'Conexion correcta con Clip.' : 'No se pudo comprobar Clip.');
            } catch (error) {
                result.className = 'mt-3 rounded-md border border-red-400/30 bg-red-400/10 p-3 text-sm text-red-100';
                result.textContent = 'No se pudo conectar con el servidor para probar Clip.';
            } finally {
                button.disabled = false;
            }
        });
    });
});
