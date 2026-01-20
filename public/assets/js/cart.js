// AJAX Add-to-Cart handler for customer pages
(function() {
    const baseUrl = window.BASE_URL || '';

    document.addEventListener('submit', async function(event) {
        const form = event.target.closest('.add-to-cart-form');
        if (!form) return;

        event.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalLabel = submitBtn ? submitBtn.textContent : '';
        const feedback = ensureFeedbackSlot(form);

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Adding...';
        }

        try {
            const response = await fetch(form.action || (baseUrl + 'customer/addToCart'), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: new FormData(form)
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const contentType = response.headers.get('content-type') || '';
            const text = await response.text();

            // If server didn't return JSON, treat as soft-success fallback
            if (!contentType.includes('application/json')) {
                feedback.textContent = 'Added to cart';
                feedback.style.color = '#0a7';
                if (submitBtn) {
                    submitBtn.textContent = 'Added';
                    setTimeout(() => {
                        submitBtn.textContent = originalLabel;
                        submitBtn.disabled = false;
                    }, 1200);
                }
                return;
            }

            let data;
            try {
                data = JSON.parse(text);
            } catch (parseErr) {
                throw new Error('Invalid JSON: ' + parseErr.message);
            }

            if (!data || data.success === false) {
                throw new Error((data && data.error) ? data.error : 'Could not add to cart');
            }

            feedback.textContent = data.message || 'Added to cart';
            feedback.style.color = '#0a7';

            if (submitBtn) {
                submitBtn.textContent = 'Added';
                setTimeout(() => {
                    submitBtn.textContent = originalLabel;
                    submitBtn.disabled = false;
                }, 1200);
            }
        } catch (err) {
            feedback.textContent = err.message || 'Unable to add to cart';
            feedback.style.color = '#c00';

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalLabel;
            }
        }
    });

    function ensureFeedbackSlot(form) {
        let slot = form.querySelector('.add-cart-feedback');
        if (!slot) {
            slot = document.createElement('div');
            slot.className = 'add-cart-feedback';
            slot.style.marginTop = '8px';
            slot.style.fontSize = '12px';
            form.appendChild(slot);
        }
        return slot;
    }
})();
