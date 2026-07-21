import './bootstrap';

import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => {
    if (! Alpine.started) {
        Alpine.start();
        Alpine.started = true;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
                // Un slide oculto no debe recibir foco de teclado.
                slide.toggleAttribute('inert', ! isActive);
            });
            dots.forEach((dot, dotIndex) => dot.classList.toggle('active', dotIndex === active));
        };

        const next = () => go(active + 1);
        const prev = () => go(active - 1);
        const stop = () => window.clearInterval(timer);
        const start = () => {
            stop();
            if (slides.length > 1 && intervalMs > 0 && ! prefersReducedMotion) {
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
        const cardAmount = () => {
            const card = track.querySelector(':scope > *');
            return card ? card.getBoundingClientRect().width + 16 : track.clientWidth * 0.85;
        };
        const scrollByCard = (direction) => {
            track.scrollBy({ left: direction * cardAmount(), behavior: 'smooth' });
        };

        // --- Avance automatico con transicion ---
        const intervalMs = Number(track.dataset.scrollCarouselInterval || 0);
        const isScrollable = () => track.scrollWidth > track.clientWidth + 4;
        const atEnd = () => track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
        let timer = null;

        const tick = () => {
            if (atEnd()) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                scrollByCard(1);
            }
        };
        const stop = () => {
            window.clearInterval(timer);
            timer = null;
        };
        const start = () => {
            stop();
            if (intervalMs > 0 && isScrollable() && ! prefersReducedMotion) {
                timer = window.setInterval(tick, intervalMs);
            }
        };
        const restart = () => start();

        document.querySelector(`[data-scroll-carousel-prev="${track.id}"]`)?.addEventListener('click', () => {
            scrollByCard(-1);
            restart();
        });
        document.querySelector(`[data-scroll-carousel-next="${track.id}"]`)?.addEventListener('click', () => {
            scrollByCard(1);
            restart();
        });

        track.addEventListener('mouseenter', stop);
        track.addEventListener('mouseleave', start);
        // En tactil el desplazamiento nativo ya funciona; solo pausamos el autoplay.
        track.addEventListener('touchstart', stop, { passive: true });
        track.addEventListener('touchend', start, { passive: true });

        // --- Arrastre con mouse para mover el carrusel ---
        let isDown = false;
        let startX = 0;
        let startScroll = 0;
        let moved = false;

        track.addEventListener('pointerdown', (event) => {
            if (event.pointerType !== 'mouse' || event.button !== 0) {
                return;
            }
            isDown = true;
            moved = false;
            startX = event.clientX;
            startScroll = track.scrollLeft;
            stop();
            track.classList.add('is-dragging');
        });
        track.addEventListener('pointermove', (event) => {
            if (! isDown) {
                return;
            }
            const delta = event.clientX - startX;
            if (Math.abs(delta) > 4) {
                moved = true;
            }
            track.scrollLeft = startScroll - delta;
        });
        const endDrag = () => {
            if (! isDown) {
                return;
            }
            isDown = false;
            track.classList.remove('is-dragging');
            start();
        };
        track.addEventListener('pointerup', endDrag);
        track.addEventListener('pointercancel', endDrag);
        track.addEventListener('pointerleave', endDrag);
        // Evita que un arrastre dispare la navegacion del enlace.
        track.addEventListener('click', (event) => {
            if (moved) {
                event.preventDefault();
            }
        }, true);

        start();
    });

    // --- Toast flotante (no bloquea la vista, se oculta solo) ---
    const toastStack = document.querySelector('[data-toast-stack]');
    const showToast = (message, type = 'success') => {
        if (! toastStack) {
            return;
        }
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.setAttribute('role', 'status');
        toast.textContent = message;
        toastStack.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('is-visible'));
        window.setTimeout(() => {
            toast.classList.remove('is-visible');
            toast.addEventListener('transitionend', () => toast.remove(), { once: true });
        }, 2600);
    };

    // --- Agregar al carrito sin recargar ni saltar arriba ---
    document.querySelectorAll('[data-cart-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const button = form.querySelector('button[type="submit"], button:not([type])');
            if (button) {
                button.disabled = true;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                });
                const data = await response.json().catch(() => ({}));

                if (response.ok && data.ok) {
                    document.querySelectorAll('[data-cart-count]').forEach((el) => {
                        el.textContent = data.count;
                        el.classList.toggle('hidden', Number(data.count) < 1);
                    });
                    if (data.meta_event && window.goMetaTrack) {
                        window.goMetaTrack(data.meta_event.name, data.meta_event.custom_data || {}, data.meta_event.event_id);
                    }
                    showToast('Producto agregado al carrito.', 'success');
                } else {
                    const message = data.message
                        || (data.errors ? Object.values(data.errors)[0][0] : 'No se pudo agregar el producto.');
                    showToast(message, 'error');
                }
            } catch (error) {
                showToast('No se pudo conectar. Intenta de nuevo.', 'error');
            } finally {
                if (button) {
                    button.disabled = false;
                }
            }
        });
    });

    // --- Cupon sin recargar (mismo toast que agregar al carrito) ---
    const updateCartTotals = (totals) => {
        if (! totals) {
            return;
        }
        const setText = (selector, value) => {
            if (value === undefined) {
                return;
            }
            document.querySelectorAll(selector).forEach((el) => {
                el.textContent = value;
            });
        };
        setText('[data-cart-subtotal]', totals.subtotal !== undefined ? '$' + totals.subtotal : undefined);
        setText('[data-cart-shipping]', totals.shipping !== undefined ? '$' + totals.shipping : undefined);
        setText('[data-cart-discount]', totals.discount !== undefined ? '-$' + totals.discount : undefined);
        setText('[data-cart-total]', totals.total !== undefined ? '$' + totals.total : undefined);
        if (totals.count !== undefined) {
            document.querySelectorAll('[data-cart-count]').forEach((el) => {
                el.textContent = totals.count;
                el.classList.toggle('hidden', Number(totals.count) < 1);
            });
        }
    };

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-coupon-ajax]');
        if (! form) {
            return;
        }
        event.preventDefault();
        const button = form.querySelector('button[type="submit"], button:not([type])');
        if (button) {
            button.disabled = true;
        }

        try {
            const isRemovingCoupon = (form.querySelector('input[name="_method"]')?.value || '').toUpperCase() === 'DELETE';
            const formData = new FormData(form);
            if (isRemovingCoupon) {
                formData.delete('_method');
            }
            const response = await fetch(form.action, {
                method: isRemovingCoupon ? 'DELETE' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: isRemovingCoupon ? null : formData,
            });
            const data = await response.json().catch(() => ({}));

            if (response.ok && data.ok) {
                updateCartTotals(data.totals);
                const area = document.querySelector('[data-coupon-area]');
                if (area && data.coupon_html) {
                    area.innerHTML = data.coupon_html;
                    createIcons({ icons });
                }
                showToast(data.message || 'Listo.', 'success');
            } else {
                const message = data.message
                    || (data.errors ? Object.values(data.errors)[0][0] : (isRemovingCoupon ? 'No se pudo quitar el cupon.' : 'No se pudo aplicar el cupon.'));
                showToast(message, 'error');
            }
        } catch (error) {
            showToast('No se pudo conectar. Intenta de nuevo.', 'error');
        } finally {
            if (button) {
                button.disabled = false;
            }
        }
    });

    // --- Actualizar/eliminar lineas del carrito sin recargar ---
    const refreshCartEmptyState = () => {
        const lines = document.querySelector('[data-cart-lines]');
        if (! lines || lines.querySelector('[data-cart-line]')) {
            return;
        }

        lines.innerHTML = '<div class="panel p-8 text-zinc-400">Tu carrito esta vacio.</div>';
        document.querySelector('[data-cart-clear]')?.remove();

        const pay = document.querySelector('[data-cart-pay]');
        if (pay) {
            pay.classList.add('pointer-events-none', 'opacity-50');
            pay.setAttribute('aria-disabled', 'true');
            pay.setAttribute('tabindex', '-1');
        }
    };

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-cart-line-form]');
        if (! form) {
            return;
        }
        event.preventDefault();
        const button = form.querySelector('button[type="submit"], button:not([type])');
        if (button) {
            button.disabled = true;
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));

            if (response.ok && data.ok) {
                updateCartTotals(data.totals);
                const area = document.querySelector('[data-coupon-area]');
                if (area && data.coupon_html) {
                    area.innerHTML = data.coupon_html;
                    createIcons({ icons });
                }

                const quantityValue = Number(form.querySelector('input[name="quantity"]')?.value ?? 1);
                if (form.hasAttribute('data-cart-line-remove') || quantityValue < 1) {
                    form.closest('[data-cart-line]')?.remove();
                    refreshCartEmptyState();
                }

                showToast(data.message || 'Carrito actualizado.', 'success');
            } else {
                const message = data.message
                    || (data.errors ? Object.values(data.errors)[0][0] : 'No se pudo actualizar el carrito.');
                showToast(message, 'error');
            }
        } catch (error) {
            showToast('No se pudo conectar. Intenta de nuevo.', 'error');
        } finally {
            if (button) {
                button.disabled = false;
            }
        }
    });

    document.querySelectorAll('[data-checkout-wizard]').forEach((form) => {
        const steps = Array.from(form.querySelectorAll('[data-checkout-step]'));
        const stage = form.querySelector('.checkout-wizard-stage');
        const pills = Array.from(form.querySelectorAll('.checkout-step-pill[data-checkout-goto]'));
        const draftStatus = form.querySelector('[data-checkout-draft-status]');
        const draftClear = form.querySelector('[data-checkout-draft-clear]');
        let active = Math.max(0, steps.findIndex((step) => step.classList.contains('active')));

        if (! steps.length) {
            return;
        }

        const draftKey = 'gocenter.checkout.contact.v1';
        const draftTtl = 1000 * 60 * 60 * 24 * 30;
        const persistentDraftFields = [
            'customer_name',
            'customer_email',
            'customer_phone',
            'street',
            'external_number',
            'internal_number',
            'postal_code',
            'neighborhood',
            'city',
            'state',
            'references',
        ];
        const temporaryDraftFields = [
            'customer_notes',
            'payment_method',
        ];
        const draftFields = [...persistentDraftFields, ...temporaryDraftFields];

        const draftStore = {
            get() {
                try {
                    const raw = window.localStorage.getItem(draftKey);
                    if (! raw) {
                        return null;
                    }
                    const parsed = JSON.parse(raw);
                    if (! parsed?.saved_at || Date.now() - Number(parsed.saved_at) > draftTtl) {
                        window.localStorage.removeItem(draftKey);
                        return null;
                    }
                    return parsed;
                } catch (error) {
                    return null;
                }
            },
            set(data) {
                try {
                    window.localStorage.setItem(draftKey, JSON.stringify({
                        ...data,
                        saved_at: Date.now(),
                    }));
                } catch (error) {
                    // Si el navegador bloquea storage, el checkout sigue funcionando normal.
                }
            },
            clear() {
                try {
                    window.localStorage.removeItem(draftKey);
                } catch (error) {
                    // Sin accion: limpiar storage no debe bloquear el formulario.
                }
            },
        };

        const setDraftStatus = (message) => {
            if (! draftStatus) {
                return;
            }
            draftStatus.textContent = message;
        };

        const fieldValue = (name) => {
            const fields = Array.from(form.elements[name] ? (form.elements[name].length ? form.elements[name] : [form.elements[name]]) : []);
            const radio = fields.find((field) => field.type === 'radio' && field.checked);
            if (radio) {
                return radio.value;
            }
            const field = fields.find((item) => item.type !== 'radio');
            return field?.value || '';
        };

        const setFieldValue = (name, value) => {
            if (value === undefined || value === null || value === '') {
                return;
            }

            const fields = Array.from(form.elements[name] ? (form.elements[name].length ? form.elements[name] : [form.elements[name]]) : []);
            fields.forEach((field) => {
                if (field.type === 'radio') {
                    field.checked = field.value === value;
                    return;
                }
                if (! field.value) {
                    field.value = value;
                    field.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        };

        const collectDraft = () => {
            const values = {};
            draftFields.forEach((name) => {
                values[name] = fieldValue(name);
            });
            return values;
        };

        const saveDraft = () => {
            draftStore.set({ values: collectDraft() });
            setDraftStatus('Datos guardados en este dispositivo.');
        };

        const restoreDraft = () => {
            const draft = draftStore.get();
            if (! draft?.values) {
                return;
            }

            draftFields.forEach((name) => setFieldValue(name, draft.values[name]));
            setDraftStatus('Datos recuperados de este dispositivo.');
        };

        restoreDraft();

        const flash = () => {
            if (! stage) {
                return;
            }
            stage.classList.remove('is-flashing');
            void stage.offsetWidth;
            stage.classList.add('is-flashing');
        };

        const showStep = (index, shouldScroll = true) => {
            active = Math.max(0, Math.min(index, steps.length - 1));
            steps.forEach((step, stepIndex) => {
                const isActive = stepIndex === active;
                step.hidden = ! isActive;
                step.classList.toggle('active', isActive);
            });
            pills.forEach((pill) => {
                pill.classList.toggle('active', Number(pill.dataset.checkoutGoto) === active);
            });
            flash();
            if (shouldScroll) {
                window.setTimeout(() => {
                    const targetTop = (stage || steps[active]).getBoundingClientRect().top + window.scrollY - 92;
                    window.scrollTo({ top: Math.max(0, targetTop), behavior: 'smooth' });
                }, 40);
            }
        };

        const clearFieldError = (field) => {
            field.setCustomValidity('');
            const shell = field.closest('.input-shell') || field.closest('.field') || field;
            const fieldWrap = field.closest('.field');
            shell.classList.remove('is-invalid');
            field.classList.remove('is-invalid');
            fieldWrap?.querySelector('[data-field-error]')?.remove();
        };

        const setFieldError = (field, message) => {
            const shell = field.closest('.input-shell') || field.closest('.field') || field;
            const fieldWrap = field.closest('.field');

            shell.classList.remove('is-invalid');
            field.classList.remove('is-invalid');
            void shell.offsetWidth;

            shell.classList.add('is-invalid');
            field.classList.add('is-invalid');

            if (fieldWrap) {
                let error = fieldWrap.querySelector('[data-field-error]');
                if (! error) {
                    error = document.createElement('p');
                    error.dataset.fieldError = 'true';
                    error.className = 'field-error';
                    fieldWrap.appendChild(error);
                }
                error.textContent = message;
            }
        };

        const applyCustomRule = (field) => {
            field.setCustomValidity('');
            const value = (field.value || '').trim();
            const digits = value.replace(/\D+/g, '');

            if (field.name === 'customer_name' && value && value.split(/\s+/).length < 2) {
                field.setCustomValidity(field.dataset.errorMessage || 'Escribe nombre y apellido.');
            }

            if (field.name === 'customer_phone' && digits.length !== 10) {
                field.setCustomValidity(field.dataset.errorMessage || 'El telefono debe tener exactamente 10 digitos.');
            }

            if (field.name === 'postal_code' && digits.length !== 5) {
                field.setCustomValidity(field.dataset.errorMessage || 'El codigo postal debe tener exactamente 5 digitos.');
            }
        };

        const stepFields = (index) => Array.from(steps[index].querySelectorAll('input, select, textarea'))
                .filter((field) => ! field.disabled && field.type !== 'hidden');

        const validateStep = (index = active, shouldFocus = true) => {
            const fields = stepFields(index);
            let invalid = null;

            fields.forEach((field) => {
                clearFieldError(field);
                applyCustomRule(field);

                if (! invalid && ! field.checkValidity()) {
                    invalid = field;
                }
            });

            if (invalid) {
                const message = invalid.validationMessage || invalid.dataset.errorMessage || 'Revisa este dato.';
                setFieldError(invalid, message);

                if (shouldFocus) {
                    invalid.focus({ preventScroll: true });
                    invalid.reportValidity();
                    window.setTimeout(() => {
                        const targetTop = invalid.closest('.field')?.getBoundingClientRect().top ?? invalid.getBoundingClientRect().top;
                        window.scrollTo({ top: Math.max(0, targetTop + window.scrollY - 120), behavior: 'smooth' });
                    }, 40);
                }
                return false;
            }

            return true;
        };

        form.querySelectorAll('input, select, textarea').forEach((field) => {
            field.addEventListener('input', () => {
                clearFieldError(field);
                applyCustomRule(field);
                if (field.checkValidity()) {
                    clearFieldError(field);
                }
                saveDraft();
            });

            field.addEventListener('blur', () => {
                clearFieldError(field);
                applyCustomRule(field);
                if (field.value && ! field.checkValidity()) {
                    setFieldError(field, field.validationMessage || field.dataset.errorMessage || 'Revisa este dato.');
                }
                saveDraft();
            });
        });

        form.querySelectorAll('input[type="radio"]').forEach((field) => {
            field.addEventListener('change', saveDraft);
        });

        draftClear?.addEventListener('click', () => {
            draftStore.clear();
            draftFields.forEach((name) => {
                const fields = Array.from(form.elements[name] ? (form.elements[name].length ? form.elements[name] : [form.elements[name]]) : []);
                fields.forEach((field) => {
                    if (field.type === 'radio') {
                        field.checked = field.value === 'transferencia';
                        return;
                    }
                    field.value = '';
                    field.dispatchEvent(new Event('input', { bubbles: true }));
                });
            });
            draftStore.clear();
            setDraftStatus('Datos guardados eliminados.');
        });

        const postalInput = form.querySelector('[data-postal-code-field]');
        const stateInput = form.querySelector('[data-state-field]');
        const cityInput = form.querySelector('[data-city-field]');
        const neighborhoodInput = form.querySelector('[data-neighborhood-field]');
        const neighborhoodOptions = form.querySelector('[data-neighborhood-options]');
        const neighborhoodToggle = form.querySelector('[data-neighborhood-toggle]');
        const postalMessage = form.querySelector('[data-postal-message]');
        const postalLookupUrl = form.dataset.postalLookupUrl;
        let postalLookupTimer = null;
        let postalLookupToken = 0;
        let neighborhoodSuggestions = [];

        const setPostalMessage = (message = '', type = 'neutral') => {
            if (! postalMessage) {
                return;
            }

            postalMessage.textContent = message;
            postalMessage.dataset.state = type;
        };

        const resetNeighborhoodOptions = () => {
            neighborhoodSuggestions = [];
            if (neighborhoodOptions) {
                neighborhoodOptions.innerHTML = '';
                neighborhoodOptions.hidden = true;
            }
            neighborhoodInput?.setAttribute('aria-expanded', 'false');
        };

        const normalizeText = (value) => (value || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();

        const renderNeighborhoodOptions = (forceOpen = false) => {
            if (! neighborhoodInput || ! neighborhoodOptions) {
                return;
            }

            const search = normalizeText(neighborhoodInput.value);
            const matches = neighborhoodSuggestions
                .filter((settlement) => ! search || normalizeText(settlement.name).includes(search))
                .sort((a, b) => {
                    if (! search) {
                        return normalizeText(a.name).localeCompare(normalizeText(b.name));
                    }

                    const aName = normalizeText(a.name);
                    const bName = normalizeText(b.name);
                    const aStarts = aName.startsWith(search);
                    const bStarts = bName.startsWith(search);

                    if (aStarts !== bStarts) {
                        return aStarts ? -1 : 1;
                    }

                    return aName.localeCompare(bName);
                });

            neighborhoodOptions.innerHTML = '';

            if (! matches.length) {
                neighborhoodOptions.hidden = true;
                neighborhoodInput.setAttribute('aria-expanded', 'false');
                return;
            }

            matches.forEach((settlement) => {
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'neighborhood-option';
                option.setAttribute('role', 'option');
                option.dataset.value = settlement.name;
                const name = document.createElement('span');
                name.textContent = settlement.name;
                option.appendChild(name);
                if (settlement.type) {
                    const type = document.createElement('small');
                    type.textContent = settlement.type;
                    option.appendChild(type);
                }
                option.addEventListener('click', () => {
                    neighborhoodInput.value = settlement.name;
                    clearFieldError(neighborhoodInput);
                    neighborhoodOptions.hidden = true;
                    neighborhoodInput.setAttribute('aria-expanded', 'false');
                    neighborhoodInput.focus({ preventScroll: true });
                });
                neighborhoodOptions.appendChild(option);
            });

            neighborhoodOptions.hidden = ! forceOpen && document.activeElement !== neighborhoodInput;
            neighborhoodInput.setAttribute('aria-expanded', neighborhoodOptions.hidden ? 'false' : 'true');
        };

        const applyPostalData = (data) => {
            if (stateInput && data.state) {
                stateInput.value = data.state;
                clearFieldError(stateInput);
            }

            if (cityInput && (data.city || data.municipality)) {
                cityInput.value = data.city || data.municipality;
                clearFieldError(cityInput);
            }

            neighborhoodSuggestions = data.settlements || [];
            renderNeighborhoodOptions(false);

            if (neighborhoodInput && ! neighborhoodInput.value && data.settlements?.length === 1) {
                neighborhoodInput.value = data.settlements[0].name;
                clearFieldError(neighborhoodInput);
            }

            setPostalMessage('');
        };

        const lookupPostalCode = async () => {
            if (! postalInput || ! postalLookupUrl) {
                return;
            }

            const postalCode = postalInput.value.replace(/\D+/g, '');
            postalInput.value = postalCode.slice(0, 5);

            if (postalCode.length !== 5) {
                resetNeighborhoodOptions();
                setPostalMessage('');
                return;
            }

            const token = ++postalLookupToken;
            setPostalMessage('Buscando colonias…', 'loading');

            try {
                const response = await fetch(postalLookupUrl.replace('__CP__', postalCode), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await response.json().catch(() => ({}));

                if (token !== postalLookupToken) {
                    return;
                }

                if (response.ok && data.ok) {
                    applyPostalData(data);
                    return;
                }

                resetNeighborhoodOptions();
                setPostalMessage('No se encontro informacion referente a este CP. Puedes capturar la direccion manualmente.', 'error');
            } catch (error) {
                if (token === postalLookupToken) {
                    resetNeighborhoodOptions();
                    setPostalMessage('No se encontro informacion referente a este CP. Puedes capturar la direccion manualmente.', 'error');
                }
            }
        };

        postalInput?.addEventListener('input', () => {
            window.clearTimeout(postalLookupTimer);
            postalLookupTimer = window.setTimeout(lookupPostalCode, 280);
        });

        if (postalInput?.value.replace(/\D+/g, '').length === 5) {
            lookupPostalCode();
        }

        neighborhoodInput?.addEventListener('input', () => {
            clearFieldError(neighborhoodInput);
            renderNeighborhoodOptions(true);
        });

        neighborhoodInput?.addEventListener('focus', () => {
            renderNeighborhoodOptions(true);
        });

        neighborhoodToggle?.addEventListener('click', () => {
            neighborhoodInput?.focus({ preventScroll: true });
            renderNeighborhoodOptions(true);
        });

        // Navegacion con teclado en el combobox de colonias.
        const neighborhoodOptionButtons = () => Array.from(neighborhoodOptions?.querySelectorAll('.neighborhood-option') || []);

        const closeNeighborhoodOptions = (refocusInput = true) => {
            if (neighborhoodOptions) {
                neighborhoodOptions.hidden = true;
            }
            neighborhoodInput?.setAttribute('aria-expanded', 'false');
            if (refocusInput) {
                neighborhoodInput?.focus({ preventScroll: true });
            }
        };

        neighborhoodInput?.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                renderNeighborhoodOptions(true);
                neighborhoodOptionButtons()[0]?.focus();
            }
            if (event.key === 'Escape' && neighborhoodOptions && ! neighborhoodOptions.hidden) {
                event.stopPropagation();
                closeNeighborhoodOptions();
            }
        });

        neighborhoodOptions?.addEventListener('keydown', (event) => {
            const options = neighborhoodOptionButtons();
            const index = options.indexOf(document.activeElement);

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                options[Math.min(index + 1, options.length - 1)]?.focus();
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                if (index <= 0) {
                    closeNeighborhoodOptions();
                } else {
                    options[index - 1]?.focus();
                }
            } else if (event.key === 'Escape') {
                event.stopPropagation();
                closeNeighborhoodOptions();
            }
        });

        document.addEventListener('click', (event) => {
            if (! event.target.closest('[data-neighborhood-combobox]') && neighborhoodOptions) {
                neighborhoodOptions.hidden = true;
                neighborhoodInput?.setAttribute('aria-expanded', 'false');
            }
        });

        form.querySelectorAll('[data-checkout-next]').forEach((button) => {
            button.addEventListener('click', () => {
                if (validateStep(active)) {
                    showStep(active + 1);
                }
            });
        });

        document.querySelectorAll('[data-checkout-outside-next]').forEach((button) => {
            button.addEventListener('click', () => {
                if (validateStep(active)) {
                    showStep(active + 1);
                }
            });
        });

        form.querySelectorAll('[data-checkout-prev]').forEach((button) => {
            button.addEventListener('click', () => showStep(active - 1));
        });

        pills.forEach((pill) => {
            pill.addEventListener('click', () => {
                const requested = Number(pill.dataset.checkoutGoto || 0);
                const target = requested > active ? active + 1 : requested;

                if (target <= active || validateStep(active)) {
                    showStep(target);
                }
            });
        });

        form.addEventListener('submit', (event) => {
            const firstInvalidStep = steps.findIndex((step, index) => ! validateStep(index, false));

            if (firstInvalidStep !== -1) {
                event.preventDefault();
                showStep(firstInvalidStep);
                window.setTimeout(() => validateStep(firstInvalidStep), 120);
            }
        });

        showStep(active, false);
    });

    if (document.querySelector('[data-checkout-complete]')) {
        const draftKey = 'gocenter.checkout.contact.v1';
        const temporaryDraftFields = ['customer_notes', 'payment_method'];

        try {
            const raw = window.localStorage.getItem(draftKey);
            if (raw) {
                const draft = JSON.parse(raw);
                temporaryDraftFields.forEach((field) => {
                    delete draft?.values?.[field];
                });
                window.localStorage.setItem(draftKey, JSON.stringify({
                    ...draft,
                    saved_at: Date.now(),
                }));
            }
        } catch (error) {
            window.localStorage.removeItem(draftKey);
        }
    }

    document.querySelectorAll('[data-clip-test]').forEach((button) => {
        const result = document.querySelector(button.dataset.clipTestResult);
        const form = document.querySelector(button.dataset.clipTestForm);

        button.addEventListener('click', async () => {
            if (! form || ! result) {
                return;
            }

            button.disabled = true;
            result.textContent = 'Comprobando conexion con Clip…';
            result.className = 'mt-3 rounded-md border border-zinc-800 bg-zinc-950 p-3 text-sm text-zinc-300';

            try {
                const payload = new FormData(form);
                payload.delete('_method');

                const response = await fetch(button.dataset.clipTest, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json',
                    },
                    body: payload,
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

    document.querySelectorAll('[data-mail-test]').forEach((button) => {
        const result = document.querySelector(button.dataset.mailTestResult);
        const form = document.querySelector(button.dataset.mailTestForm);

        button.addEventListener('click', async () => {
            if (! form || ! result) {
                return;
            }

            button.disabled = true;
            result.textContent = 'Enviando correo de prueba...';
            result.className = 'mt-3 rounded-md border border-zinc-800 bg-zinc-950 p-3 text-sm text-zinc-300';

            try {
                const payload = new FormData(form);
                payload.delete('_method');

                const response = await fetch(button.dataset.mailTest, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json',
                    },
                    body: payload,
                });
                const data = await response.json();
                const ok = response.ok && data.ok;

                result.className = ok
                    ? 'mt-3 rounded-md border border-emerald-400/30 bg-emerald-400/10 p-3 text-sm text-emerald-100'
                    : 'mt-3 rounded-md border border-red-400/30 bg-red-400/10 p-3 text-sm text-red-100';
                result.textContent = data.message || (ok ? 'Correo probado correctamente.' : 'No se pudo probar el correo.');
            } catch (error) {
                result.className = 'mt-3 rounded-md border border-red-400/30 bg-red-400/10 p-3 text-sm text-red-100';
                result.textContent = 'No se pudo conectar con el servidor para probar el correo.';
            } finally {
                button.disabled = false;
            }
        });
    });

    document.querySelectorAll('[data-meta-test]').forEach((button) => {
        const result = document.querySelector(button.dataset.metaTestResult);
        const form = document.querySelector(button.dataset.metaTestForm);

        button.addEventListener('click', async () => {
            if (! form || ! result) {
                return;
            }

            button.disabled = true;
            result.textContent = 'Enviando evento de prueba a Meta...';
            result.className = 'mt-3 rounded-md border border-zinc-800 bg-zinc-950 p-3 text-sm text-zinc-300';

            try {
                const payload = new FormData(form);
                payload.delete('_method');

                const response = await fetch(button.dataset.metaTest, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json',
                    },
                    body: payload,
                });
                const data = await response.json();
                const ok = response.ok && data.ok;

                result.className = ok
                    ? 'mt-3 rounded-md border border-emerald-400/30 bg-emerald-400/10 p-3 text-sm text-emerald-100'
                    : 'mt-3 rounded-md border border-red-400/30 bg-red-400/10 p-3 text-sm text-red-100';
                result.textContent = data.message || (ok ? 'Meta probado correctamente.' : 'No se pudo probar Meta.');
            } catch (error) {
                result.className = 'mt-3 rounded-md border border-red-400/30 bg-red-400/10 p-3 text-sm text-red-100';
                result.textContent = 'No se pudo conectar con el servidor para probar Meta.';
            } finally {
                button.disabled = false;
            }
        });
    });
});
