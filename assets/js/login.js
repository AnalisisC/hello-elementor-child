
window.onload = function () {
    jQuery('#user_login').attr('type', 'email').attr('placeholder', 'Email').addClass('elementor-field elementor-size-md  elementor-field-textual').attr('required', 'true');
    jQuery('#user_pass').attr('placeholder', 'Password').addClass('elementor-field elementor-size-md  elementor-field-textual').attr('required', 'true');
    jQuery('#wp-submit').addClass('elementor-button-link elementor-button elementor-size-lg');
    jQuery("#loginform").attr('action', '/wp-login.php');
}