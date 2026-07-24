document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-confirm').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.message || 'Are you sure?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    const seatForm = document.querySelector('.js-seat-form');
    if (seatForm) {
        const price = Number(seatForm.dataset.price || 0);
        const seatBoxes = Array.from(seatForm.querySelectorAll('input[name="seats[]"]'));
        const selectedText = seatForm.querySelector('.js-selected-seats');
        const totalText = seatForm.querySelector('.js-total');

        const refreshSummary = () => {
            const selected = seatBoxes.filter((box) => box.checked).map((box) => box.value);

            if (selected.length > 8) {
                selected.at(-1).checked = false;
                alert('You can book a maximum of 8 seats at once.');
                refreshSummary();
                return;
            }

            selectedText.textContent = selected.length ? selected.join(', ') : 'None';
            totalText.textContent = `LKR ${(selected.length * price).toFixed(2)}`;
        };

        seatBoxes.forEach((box) => box.addEventListener('change', refreshSummary));
        refreshSummary();
    }

    const registerForm = document.querySelector('.js-register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', (event) => {
            const password = registerForm.querySelector('#password').value;
            const confirmPassword = registerForm.querySelector('#confirm_password').value;

            if (password.length < 6) {
                event.preventDefault();
                alert('Password must be at least 6 characters.');
                return;
            }

            if (password !== confirmPassword) {
                event.preventDefault();
                alert('Passwords do not match.');
            }
        });
    }

    const paymentForm = document.querySelector('.js-payment-form');
    if (paymentForm) {
        const cardInput = paymentForm.querySelector('.js-card-number');

        cardInput.addEventListener('input', () => {
            const digits = cardInput.value.replace(/\D/g, '').slice(0, 19);
            cardInput.value = digits.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
        });

        paymentForm.addEventListener('submit', (event) => {
            const digits = cardInput.value.replace(/\D/g, '');
            const cvv = paymentForm.querySelector('#cvv').value.replace(/\D/g, '');
            const expiry = paymentForm.querySelector('#expiry').value.trim();

            if (digits.length < 12 || digits.length > 19) {
                event.preventDefault();
                alert('Card number must contain 12 to 19 digits.');
                return;
            }

            if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
                event.preventDefault();
                alert('Expiry must use MM/YY format.');
                return;
            }

            if (cvv.length < 3 || cvv.length > 4) {
                event.preventDefault();
                alert('CVV must contain 3 or 4 digits.');
            }
        });
    }
});
