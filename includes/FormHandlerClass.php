<?php

namespace CUSTOMOTP\Includes;

class FormHandlerClass
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this,'cotp_login_enqueue_scripts']);
        add_shortcode('copt_registeration_form',[$this,'formRender']);
        $this->registerEndPointsForAjax();
    }

    public function registerEndPointsForAjax()
    {
        add_action('wp_ajax_send_otp', [$this,'sendOtp']);
        add_action('wp_ajax_nopriv_send_otp', [$this,'sendOtp']);

        add_action('wp_ajax_verify_otp', [$this,'verifyOtp']);
        add_action('wp_ajax_nopriv_verify_otp', [$this,'verifyOtp']);

        add_action('wp_ajax_complete_registration', [$this,'completeRegistration']);
        add_action('wp_ajax_nopriv_complete_registration', [$this,'completeRegistration']);

    }

    public function sendOtp()
    {
        $email = sanitize_email($_POST['email']);

        if (!is_email($email)) {
            wp_send_json_error(['message' => 'Invalid email address.']);
        }

        $otp = wp_rand(100000, 999999);
        $expiration = time() + 300;

        update_option('otp_' . md5($email), ['otp' => $otp, 'expires' => $expiration]);

        wp_mail($email, 'Your OTP Code', "Your OTP code is: $otp");

        wp_send_json_success(['message' => 'OTP sent to your email.']);
    }


    public function verifyOtp()
    {
        $email = sanitize_email($_POST['email']);
        $otp = sanitize_text_field($_POST['otp']);

        $storedOTP = get_option('otp_' . md5($email));

        if(!$storedOTP || time() > $storedOTP['expires'])
        {
            delete_option('otp_' . md5($email));
            wp_send_json_error(['message' => 'expired OTP.']);
        }

        if (!$storedOTP ||  $storedOTP['otp'] != $otp)
        {
            wp_send_json_error(['message' => 'Invalid  OTP.']);
        }

        delete_option('otp_' . md5($email));

        $user = get_user_by('email', $email);

        if ($user)
        {
            wp_set_auth_cookie($user->ID);
            wp_send_json_success(['message' => 'Login successful.', 'redirect_url' => home_url()]);
        }
        else
        {
            wp_send_json_success(['message' => 'OTP verified. Proceed to registration.', 'newRegister' => true]);
        }
    }

    public function completeRegistration()
    {
        $email = sanitize_email($_POST['email']);
        $name = sanitize_text_field($_POST['name']);

        if (email_exists($email)) {
            wp_send_json_error(['message' => 'Email already registered.']);
        }

        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $role = 'customer';
        } else {
            $role = 'subscriber';
        }

        $user_id = wp_insert_user([
            'user_login' => $email,
            'user_email' => $email,
            'first_name' => $name,
            'role' => $role,
        ]);

        if (is_wp_error($user_id)) {
            wp_send_json_error(['message' => 'Registration failed.']);
        }

        wp_set_auth_cookie($user_id);
        wp_send_json_success(['message' => 'Registration successful.', 'redirect_url' => home_url()]);
    }


    public function cotp_login_enqueue_scripts()
    {
        wp_enqueue_style('cotp-login-style', COTP_ASSETS . 'css/cotp-login.css');
        wp_enqueue_script('jquery');
        wp_enqueue_script('cotp-login-script', COTP_ASSETS . 'js/cotp-login.js', array('jquery'), '1.0', true);
        wp_localize_script('cotp-login-script', 'otpAjax', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }

    public function formRender()
    {
        if ( is_user_logged_in() ) {
            echo "<h1>Your are logged in </h1>";
        } else {
            ob_start();
            include COTP_TEMPLATES . 'registerform.php';
            return ob_get_clean();        }
    }
}