<?php

/* Template Name: Iniciar Sesión */
/* @author Miguel Gil  <miguelgilmartinez@gmail.com> */


function display_error_message($err_code)
{
    // Invalid username.
    if (in_array('invalid_username', $err_code)) {
        $err = '<strong>ERROR</strong>: Invalid username.';
    }
    // Incorrect password.
    if (in_array('incorrect_password', $err_code)) {
        $err = '<strong>ERROR</strong>: The password you entered is incorrect.';
    }
    // Empty username.
    if (in_array('empty_username', $err_code)) {
        $err = '<strong>ERROR</strong>: The username field is empty.';
    }
    // Empty password.
    if (in_array('empty_password', $err_code)) {
        $err = '<strong>ERROR</strong>: The password field is empty.';
    }
    // Empty username and empty password.
    if(in_array('empty_username', $err_code)  &&  in_array('empty_password', $err_code)) {
        $err = '<strong>ERROR</strong>: The username and password are empty.';
    }
    return $error;
}

/**
 * Starting the mess
 */

get_header();

if (!is_user_logged_in()) {
    $lang = get_locale();
    $redirect = ($lang === 'es_ES' ? '/estudio' : '/en/studio');
    $args = [
        'redirect' => $redirect,
        'form_id' => 'loginform-custom',
        'label_username' => __('Username:'),
        'label_password' => __('Password:'),
        'label_remember' => __('Remember Me'),
        //'label_log_in' => __('Iniciar sesión'),
        'remember' => true
    ];
    wp_login_form($args);
}
get_footer();

$err_codes = isset($_SESSION["err_codes"]) ? $_SESSION["err_codes"] : 0;
if($err_codes !== 0) {
    echo display_error_message($err_codes);
}
