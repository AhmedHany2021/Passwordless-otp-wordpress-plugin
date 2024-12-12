<div class="otp-login-container">
    <h2>Login with email</h2>

    <!-- Step 1: Enter Email -->
    <div id="otp-step-1">
        <form id="otp-login-form">
            <label for="otp-email">Email Address:</label>
            <input type="email" id="otp-email" placeholder="Enter your email" required>
            <button type="submit">Send OTP</button>
        </form>
    </div>

    <!-- Step 2: Enter OTP -->
    <div id="otp-step-2" style="display: none;">
        <form id="otp-input-form">
            <label for="otp-code">Enter OTP:</label>
            <input type="text" id="otp-code" placeholder="Enter the OTP" required>
            <button type="submit">Validate OTP</button>
        </form>
        <button id="resend-otp" style="margin-top: 10px;">Resend OTP</button>
    </div>

    <!-- Step 3: New User Registration -->
    <div id="otp-step-3" style="display: none;">
        <form id="otp-register-form">
            <label for="otp-name">Name:</label>
            <input type="text" id="otp-name" placeholder="Enter your name" required>
            <button type="submit">Complete Registration</button>
        </form>
    </div>

    <div id="otp-message" class="message"></div>
</div>
