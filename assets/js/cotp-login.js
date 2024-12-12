document.addEventListener('DOMContentLoaded', function () {
    const otpForm = document.getElementById('otp-login-form');
    const otpInputForm = document.getElementById('otp-input-form');
    const registerForm = document.getElementById('otp-register-form');
    const resendOtpBtn = document.getElementById('resend-otp');

    // Step 1: Send OTP
    otpForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const emailInput = document.getElementById('otp-email').value.trim();

        if (!validateEmail(emailInput)) {
            displayMessage('Please enter a valid email address.', 'error');
            return;
        }

        sendOtpRequest(emailInput);
    });

    // Step 2: Validate OTP
    otpInputForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const emailInput = document.getElementById('otp-email').value.trim();
        const otpInput = document.getElementById('otp-code').value.trim();

        if (otpInput === '') {
            displayMessage('Please enter the OTP.', 'error');
            return;
        }

        validateOtpRequest(otpInput,emailInput);
    });

    // Step 3: Complete Registration
    registerForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const nameInput = document.getElementById('otp-name').value.trim();
        const emailInput = document.getElementById('otp-email').value.trim();


        if (nameInput === '') {
            displayMessage('Please enter your name.', 'error');
            return;
        }

        completeRegistration(nameInput,emailInput);
    });

    // Resend OTP
    resendOtpBtn.addEventListener('click', function () {
        const emailInput = document.getElementById('otp-email').value.trim();
        if (emailInput !== '') sendOtpRequest(emailInput);
    });

    function sendOtpRequest(email) {
        displayMessage('Sending OTP...', 'success');
        fetch(otpAjax.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'send_otp', email })
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    displayMessage('OTP sent to your email.', 'success');
                    showStep(2);
                } else {
                    displayMessage(data.message, 'error');
                }
            })
            .catch(() => displayMessage('An error occurred while sending the OTP.', 'error'));
    }

    function validateOtpRequest(otp,email) {
        displayMessage('Validating OTP...', 'success');

        fetch(otpAjax.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'verify_otp', otp , email })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.data.newRegister == true) {
                        displayMessage('OTP validated. Please complete registration.', 'success');
                        showStep(3);
                    } else {
                        displayMessage('Login successful!', 'success');
                        window.location.reload();
                    }
                } else {
                    displayMessage(data.message, 'error');
                }
            })
            .catch(() => displayMessage('An error occurred while validating the OTP.', 'error'));
    }

    function completeRegistration(name,email) {
        displayMessage('Completing registration...', 'success');

        fetch(otpAjax.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'complete_registration', name , email })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMessage('Registration successful! Logging you in...', 'success');
                    window.location.reload();
                } else {
                    displayMessage(data.message, 'error');
                }
            })
            .catch(() => displayMessage('An error occurred while completing registration.', 'error'));
    }

    function showStep(step) {
        document.querySelectorAll('.otp-login-container > div').forEach(div => div.style.display = 'none');
        document.getElementById(`otp-step-${step}`).style.display = 'block';
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function displayMessage(message, type) {
        const messageContainer = document.getElementById('otp-message');
        messageContainer.textContent = message;
        messageContainer.className = `message ${type}`;
    }
});
