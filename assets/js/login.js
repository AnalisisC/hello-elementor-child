
document.addEventListener("DOMContentLoaded", function () {
    jQuery('#wp-submit').addClass('elementor-button-link elementor-button elementor-size-lg');
    jQuery('#user_login').attr('type', 'email').attr('placeholder', 'Email').addClass('elementor-field elementor-size-md elementor-field-textual').attr('required', 'true');
    jQuery('#user_pass').attr('placeholder', 'Password').addClass('elementor-field elementor-size-md elementor-field-textual').attr('required', 'true');
    //jQuery("#loginform").attr('action', '/wp-login.php');
});

