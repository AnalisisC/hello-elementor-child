<?php
/**
 * Theme functions and definitions
 * @package HelloElementor
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_VERSION', '2.7.1');

if (!isset($content_width)) {
    $content_width = 800; // Pixels.
}

if (!function_exists('hello_elementor_setup')) {
    /**
     * Set up theme support.
     * @return void
     */
    function hello_elementor_setup()
    {
        if (is_admin()) {
            hello_maybe_update_theme_version_in_db();
        }

        $hook_result = apply_filters_deprecated('elementor_hello_theme_load_textdomain', [ true ], '2.0', 'hello_elementor_load_textdomain');
        if (apply_filters('hello_elementor_load_textdomain', $hook_result)) {
            load_theme_textdomain('hello-elementor', get_template_directory() . '/languages');
        }

        $hook_result = apply_filters_deprecated('elementor_hello_theme_register_menus', [ true ], '2.0', 'hello_elementor_register_menus');
        if (apply_filters('hello_elementor_register_menus', $hook_result)) {
            register_nav_menus([ 'menu-1' => __('Header', 'hello-elementor') ]);
            register_nav_menus([ 'menu-2' => __('Footer', 'hello-elementor') ]);
        }

        $hook_result = apply_filters_deprecated('elementor_hello_theme_add_theme_support', [ true ], '2.0', 'hello_elementor_add_theme_support');
        if (apply_filters('hello_elementor_add_theme_support', $hook_result)) {
            add_theme_support('post-thumbnails');
            add_theme_support('automatic-feed-links');
            add_theme_support('title-tag');
            add_theme_support(
                'html5',
                [
                    'search-form',
                    'comment-form',
                    'comment-list',
                    'gallery',
                    'caption',
                    'script',
                    'style',
                ]
            );
            add_theme_support(
                'custom-logo',
                [
                    'height'      => 100,
                    'width'       => 350,
                    'flex-height' => true,
                    'flex-width'  => true,
                ]
            );

            /*
             * Editor Style.
             */
            add_editor_style('classic-editor.css');

            /*
             * Gutenberg wide images.
             */
            add_theme_support('align-wide');

            /*
             * WooCommerce.
             */
            $hook_result = apply_filters_deprecated('elementor_hello_theme_add_woocommerce_support', [ true ], '2.0', 'hello_elementor_add_woocommerce_support');
            if (apply_filters('hello_elementor_add_woocommerce_support', $hook_result)) {
                // WooCommerce in general.
                add_theme_support('woocommerce');
                // Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
                add_theme_support('wc-product-gallery-zoom');// zoom.
                add_theme_support('wc-product-gallery-lightbox');// lightbox.
                add_theme_support('wc-product-gallery-slider'); // swipe.
            }
        }
    }
}
add_action('after_setup_theme', 'hello_elementor_setup');

function hello_maybe_update_theme_version_in_db()
{
    $theme_version_option_name = 'hello_theme_version';
    // The theme version saved in the database.
    $hello_theme_db_version = get_option($theme_version_option_name);
    // If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
    if (! $hello_theme_db_version || version_compare($hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<')) {
        update_option($theme_version_option_name, HELLO_ELEMENTOR_VERSION);
    }
}

if (!function_exists('hello_elementor_scripts_styles')) {
    /**
     * Theme Scripts & Styles.
     * @return void
     */
    function hello_elementor_scripts_styles()
    {
        $enqueue_basic_style = apply_filters_deprecated(
            'elementor_hello_theme_enqueue_style',
            [ true ],
            '2.0',
            'hello_elementor_enqueue_style'
        );
        $min_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        if (apply_filters('hello_elementor_enqueue_style', $enqueue_basic_style)) {
            wp_enqueue_style(
                'hello-elementor',
                get_template_directory_uri() . '/style' . $min_suffix . '.css',
                [],
                HELLO_ELEMENTOR_VERSION
            );
        }

        if (apply_filters('hello_elementor_enqueue_theme_style', true)) {
            wp_enqueue_style(
                'hello-elementor-theme-style',
                get_template_directory_uri() . '/theme' . $min_suffix . '.css',
                [],
                HELLO_ELEMENTOR_VERSION
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'hello_elementor_scripts_styles');

if (! function_exists('hello_elementor_register_elementor_locations')) {
    /**
     * Register Elementor Locations.
     * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
     * @return void
     */
    function hello_elementor_register_elementor_locations($elementor_theme_manager)
    {
        $hook_result = apply_filters_deprecated(
            'elementor_hello_theme_register_elementor_locations',
            [ true ],
            '2.0',
            'hello_elementor_register_elementor_locations'
        );
        if (apply_filters('hello_elementor_register_elementor_locations', $hook_result)) {
            $elementor_theme_manager->register_all_core_location();
        }
    }
}
add_action('elementor/theme/register_locations', 'hello_elementor_register_elementor_locations');

if (! function_exists('hello_elementor_content_width')) {
    /**
     * Set default content width.
     * @return void
     */
    function hello_elementor_content_width()
    {
        $GLOBALS['content_width'] = apply_filters('hello_elementor_content_width', 800);
    }
}
add_action('after_setup_theme', 'hello_elementor_content_width', 0);

if (is_admin()) {
    require get_template_directory() . '/includes/admin-functions.php';
}

/**
 * If Elementor is installed and active, we can load the Elementor-specific Settings & Features
*/

// Allow active/inactive via the Experiments
require get_template_directory() . '/includes/elementor-functions.php';

/**
 * Include customizer registration functions
*/
function hello_register_customizer_functions()
{
    if (is_customize_preview()) {
        require get_template_directory() . '/includes/customizer-functions.php';
    }
}
add_action('init', 'hello_register_customizer_functions');

// if (! function_exists('hello_elementor_check_hide_title')) {
//     /**
//      * Check hide title.
//      * @param bool $val default value.
//      * @return bool
//      */
//     function hello_elementor_check_hide_title($val)
//     {
//         if (defined('ELEMENTOR_VERSION')) {
//             $current_doc = Elementor\Plugin::instance()->documents->get(get_the_ID());
//             if ($current_doc && 'yes' === $current_doc->get_settings('hide_title')) {
//                 $val = false;
//             }
//         }
//         return $val;
//     }
// }
// add_filter('hello_elementor_page_title', 'hello_elementor_check_hide_title');

/**
 * Wrapper function to deal with backwards compatibility.
 */
if (! function_exists('hello_elementor_body_open')) {
    function hello_elementor_body_open()
    {
        if (function_exists('wp_body_open')) {
            wp_body_open();
        } else {
            do_action('wp_body_open');
        }
    }
}

/**
 * SHORTCODE PARA MOSTRAR BOTÓN REGISTRO/ACCEDER EN PRECIOS
 */
add_shortcode('basico', 'mostrar_basico');
function mostrar_basico($atts)
{
    global $current_user, $user_login;
    wp_get_current_user();
    add_filter('widget_text', 'apply_shortcodes');
    if ($user_login) {
        return '<div><p><b>'.esc_html__("Active!", 'nodechartsfam').'</b></p></div>';
    } else {
        $my_current_lang = apply_filters('wpml_current_language', null);
        if ($my_current_lang == "es") {
            return '<a class="link-pricing" href="https://nodecharts.com/registrarse"><div class="boton"><p>'
            .esc_html__("Start free", 'nodechartsfam').'</p></div></a>';
        } else {
            return '<a class="link-pricing" href="https://nodecharts.com/en/sign-up"><div class="boton"><p>'
            .esc_html__("Start free", 'nodechartsfam').'</p></div></a>';
        }
    }
}


add_shortcode('principiantemensual', 'mostrar_principiante_mensual');
function mostrar_principiante_mensual($atts)
{
    global $current_user, $user_login;
    wp_get_current_user();
    add_filter('widget_text', 'apply_shortcodes');
    if ($user_login) {
        if (is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php')) {
            $subscription = getactivesubscription2(get_current_user_id());
            if ($subscription == "Principiante Mensual" || $subscription == "Principiante Anual" ||
            $subscription == "Experto Mensual" || $subscription == "Experto Anual" || $subscription == "Profesional Anual") {
                return '<div><p><b>'.esc_html__("Active!", 'nodechartsfam').'</b></p></div>';
            } else {
                return '<a rel="nofollow" href="?add-to-cart=356" data-quantity="1" 
                data-product_id="356" data-product_sku="principiante-mensual" 
                class="button product_type_simple add_to_cart_button ajax_add_to_cart added">
                <div class="boton"><p>'. esc_html__("Buy!", 'nodechartsfam').'</p></div></a>';
            }
        } else {
            return '<p style="color:red;">Woocommerce Subscription no encontrado</p>';
        }
    } else {
        $my_current_lang = apply_filters('wpml_current_language', null);
        if ($my_current_lang == "es") {
            return '<a class="link-pricing" href="https://nodecharts.com/registrarse"><div class="boton"><p>'.
            esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        } else {
            return '<a class="link-pricing" href="https://nodecharts.com/en/sign-up"><div class="boton"><p>'.
            esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        }
    }
}

add_shortcode('principianteanual', 'mostrar_principiante_anual');
function mostrar_principiante_anual($atts)
{
    global $current_user, $user_login;
    wp_get_current_user();
    add_filter('widget_text', 'apply_shortcodes');
    if ($user_login) {
        if (is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php')) {
            $user_id = get_current_user_id();
            $subscription = getactivesubscription2($user_id);
            if ($subscription == "Principiante Anual" || $subscription == "Experto Anual"
            || $subscription == "Profesional Anual") {
                return '<div><p><b>'.esc_html__("Active!", 'nodechartsfam').'</b></p></div>';
            } else {
                return '<a rel="nofollow" href="?add-to-cart=358" data-quantity="1" data-product_id="358" data-product_sku="principiante-anual" class="button product_type_simple add_to_cart_button ajax_add_to_cart added"><div class="boton"><p>'
                .esc_html__("Buy!", 'nodechartsfam').'</p></div></a>';
            }
        } else {
            return '<p style="color:red;">Woocommerce Subscription no encontrado</p>';
        }
    } else {
        $my_current_lang = apply_filters('wpml_current_language', null);
        if ($my_current_lang == "es") {
            return '<a class="link-pricing" href="https://nodecharts.com/registrarse"><div class="boton"><p>'
            .esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        } else {
            return '<a class="link-pricing" href="https://nodecharts.com/en/sign-up"><div class="boton"><p>'
            .esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        }
    }
}

add_shortcode('expertomensual', 'mostrar_experto_mensual');
function mostrar_experto_mensual($atts)
{
    global $current_user, $user_login;
    wp_get_current_user();
    add_filter('widget_text', 'apply_shortcodes');

    if ($user_login) {
        if (is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php')) {
            $user_id = get_current_user_id();
            $subscription = getactivesubscription2($user_id);
            if ($subscription == "Experto Mensual" || $subscription == "Experto Anual"
            || $subscription == "Profesional Anual") {
                return '<div><p><b>'.esc_html__("Active!", 'nodechartsfam').'</b></p></div>';
            } else {
                return '<a rel="nofollow" href="?add-to-cart=360" data-quantity="1" data-product_id="360" data-product_sku="experto-mensual" class="button product_type_simple add_to_cart_button ajax_add_to_cart added"><div class="boton"><p>'.esc_html__("Buy!", 'nodechartsfam').'</p></div></a>';
            }
        } else {
            return '<p style="color:red;">Woocommerce Subscription no encontrado</p>';
        }
    } else {
        $my_current_lang = apply_filters('wpml_current_language', null);
        if ($my_current_lang == "es") {
            return '<a class="link-pricing" href="https://nodecharts.com/registrarse"><div class="boton"><p>'
            .esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        } else {
            return '<a class="link-pricing" href="https://nodecharts.com/en/sign-up"><div class="boton"><p>'
            .esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        }
    }
}

add_shortcode('expertoanual', 'mostrar_experto_anual');
function mostrar_experto_anual($atts)
{
    global $current_user, $user_login;
    wp_get_current_user();
    add_filter('widget_text', 'apply_shortcodes');

    if ($user_login) {
        if (is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php')) {
            $user_id = get_current_user_id();
            $subscription = getactivesubscription2($user_id);
            if ($subscription == "Experto Anual" || $subscription == "Profesional Anual") {
                return '<div><p><b>'.esc_html__("Active!", 'nodechartsfam').'</b></p></div>';
            } else {
                return '<a rel="nofollow" href="?add-to-cart=361" data-quantity="1" data-product_id="361" data-product_sku="experto-anual" class="button product_type_simple add_to_cart_button ajax_add_to_cart added"><div class="boton"><p>'
                .esc_html__("Buy!", 'nodechartsfam').'</p></div></a>';
            }
        } else {
            return '<p style="color:red;">Woocommerce Subscription no encontrado</p>';
        }
    } else {
        $my_current_lang = apply_filters('wpml_current_language', null);
        if ($my_current_lang == "es") {
            return '<a class="link-pricing" href="https://nodecharts.com/registrarse"><div class="boton"><p>'
            .esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        } else {
            return '<a class="link-pricing" href="https://nodecharts.com/en/sign-up"><div class="boton"><p>'
            .esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        }
    }
}

add_shortcode('profesionalmensual', 'mostrar_profesional_mensual');
function mostrar_profesional_mensual($atts)
{
    global $current_user, $user_login;
    wp_get_current_user();
    add_filter('widget_text', 'apply_shortcodes');
    if ($user_login) {
        if (is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php')) {
            $user_id = get_current_user_id();
            $subscription = getactivesubscription2($user_id);
            if ($subscription == "Profesional Mensual") {
                return '<div><p><b>'.esc_html__("Active!", 'nodechartsfam').'</b></p></div>';
            } else {
                return '<div class="boton disable"><p style="color: gray!important;">'
                .esc_html__("Buy!", 'nodechartsfam').'</p></div>';
            }
        } else {
            return '<p style="color:red;">Woocommerce Subscription no encontrado</p>';
        }
    } else {
        $my_current_lang = apply_filters('wpml_current_language', null);
        if ($my_current_lang == "es") {
            return '<a class="link-pricing" href="https://nodecharts.com/registrarse"><div class="boton"><p>'.esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        } else {
            return '<a class="link-pricing" href="https://nodecharts.com/en/sign-up"><div class="boton"><p>'.esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        }
    }
}


add_shortcode('profesionalanual', 'mostrar_profesional_anual');
function mostrar_profesional_anual($atts)
{
    global $current_user, $user_login;
    wp_get_current_user();
    add_filter('widget_text', 'apply_shortcodes');
    if ($user_login) {
        if (is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php')) {
            $user_id = get_current_user_id();
            $subscription = getactivesubscription2($user_id);
            if ($subscription == "Profesional Anual") {
                return '<div><p><b>'.esc_html__("Active!", 'nodechartsfam').'</b></p></div>';
            } else {
                return '<a rel="nofollow" href="?add-to-cart=362" data-quantity="1" data-product_id="362" data-product_sku="profesional-anual" class="button product_type_simple add_to_cart_button ajax_add_to_cart added"><div class="boton"><p>'.esc_html__("Buy!", 'nodechartsfam').'</p></div></a>';
            }
        } else {
            return '<p style="color:red;">Woocommerce Subscription no encontrado</p>';
        }
    } else {
        $my_current_lang = apply_filters('wpml_current_language', null);
        if ($my_current_lang == "es") {
            return '<a class="link-pricing" href="https://nodecharts.com/registrarse"><div class="boton"><p>'
            .esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        } else {
            return '<a class="link-pricing" href="https://nodecharts.com/en/sign-up"><div class="boton"><p>'
            .esc_html__("Sign up", 'nodechartsfam').'</p></div></a>';
        }
    }
}

function getactivesubscription2($userId)
{
    $user_subscriptions = wcs_get_users_subscriptions($userId);
    $arrayidsubscription = array();
    $arrayproductosid = array();
    $arrayproductosnombre = array();
    $nombremembresia="no";
    foreach ($user_subscriptions as $subscription) {
        if ($subscription->has_status(array('active', 'pending-cancel'))) {
            $id_subscription = $subscription->get_id();
            array_push($arrayidsubscription, $id_subscription);
        }
    }

    foreach($arrayidsubscription as $idsus) {
        $wc_subscription = wcs_get_subscription($idsus);
        $items = $wc_subscription->get_items();
        foreach($items as $item) {
            $product = $item->get_product();
            $nombremembresia = $product->get_name();
        }
    }

    return $nombremembresia;
}

/**
 * URL redirect after user logout
 */
add_action('wp_logout', 'ps_redirect_after_logout');
function ps_redirect_after_logout()
{
    wp_redirect(home_url((apply_filters('wpml_current_language', null) == 'en'
    ? '/en/sign-in' : '/iniciar-sesion'))) ;
    exit();
}

//NOMBRE DEL USUARIO EN EL MENU
add_filter('wp_nav_menu_objects', 'set_navigation_user_name');
function set_navigation_user_name($menu_items)
{
    foreach ($menu_items as $menu_item) {
        if ('{user_name}' === $menu_item->title) {
            //Get user and his name
            $current_user   = wp_get_current_user();
            $user_firstname = $current_user->user_firstname;
            $user_lastname  = $current_user->user_lastname;
            $menu_item->title = $user_firstname . ' ' . $user_lastname;
        }
    }
    return $menu_items;
}

add_action('wp_footer', 'woocommerce_show_coupon', 99);
function woocommerce_show_coupon()
{
    echo '<script type="text/javascript">jQuery(document).ready(function($) {
$(\'.checkout_coupon\').show();});</script>';
}


/**
 * Replaces url when clicking on logo
 * @author Miguel Gil  <miguelgilmartinez@gmail.com>
 * @return void
 */
function nodecharts_custom_login_url()
{
    return home_url();
}
add_filter('login_headerurl', 'nodecharts_custom_login_url');

/**
 * @author Miguel Gil  <miguelgilmartinez@gmail.com>
 * @return void
 */
function nodecharts_login_logo_url_redirect()
{
    return home_url();
}
add_filter('login_headertitle', 'nodecharts_login_logo_url_redirect');

/**
 * Disable language dropdown in login screen. Do not work. Use WPML config
 * @author Miguel Gil  <miguelgilmartinez@gmail.com>
 */
add_filter('login_display_language_dropdown', '__return_false');



// ENABLE THIS IF YOU WANT TO ADD LINK TO FORGOT PASSWORD
//add_action('login_form_middle', 'add_lost_password_link');
// function add_lost_password_link()
// {
//     return '<a href="/wp-login.php?action=lostpassword">Forgot Your Password?</a>';
// }


/**
 * Login Error Handling
 * @author Miguel Gil  <miguelgilmartinez@gmail.com>
 * @return void
 */

add_filter('wp_login_failed', function () {
    wp_redirect(apply_filters('wpml_current_language', null) == 'en'
    ? '/en/sign-in?error=1' : '/iniciar-sesion?error=1');
    exit(); //Fuckin' spaguetti code
});

function nodecharts_login_page()
{
    if (!is_user_logged_in()) {
        if (isset($_REQUEST['error'])) {
            echo(apply_filters('wpml_current_language', null) == 'en' ?
            '<div class="error-login">Error user or password</div>' :
            '<div class="error-login">Error en usuario o contraseña</div>');
        }
        $args = array(
          'echo'           => true,
          'remember'       => true,
          'redirect'       => home_url((apply_filters('wpml_current_language', null) == 'en'
          ? '/en/studio' : '/estudio')),
          'form_id'        => 'loginform',
          'id_username'    => 'user_login',
          'id_password'    => 'user_pass',
          'id_remember'    => 'rememberme',
          'id_submit'      => 'wp-submit',
          'label_username' => __(''),
          'label_password' => __(''),
          'label_remember' => __('Remember Me'),
          'label_log_in'   => __('Log In'),
          'value_remember' => false
        );
        wp_login_form($args);
        wp_enqueue_script(
            'jslogin',
            str_contains($_SERVER["HTTP_HOST"], 'nodecharts.com') ?
            'https://nodecharts-frontend.s3.eu-west-1.amazonaws.com/wp-content/themes/hello-elementor/assets/js/login.js' :
            get_template_directory_uri() .'/assets/js/login.js',
            array('jquery'),
            '1.0',
            true
        );
    } elseif(!isset($_GET['action'])) { //Edit with elementor
        // Miguel: Redirections won't work here because header was already sent. ob_clean... neither works
        echo '<style>div[class*="elementor-"] h3 {margin-top: 200px;} .wp-image-2346{display:none !important}</style><div style="color:rgb(0, 102, 255); text-align: center; margin-top: 50px;" class="elementor-element elementor-widget elementor-widget-text-editor" data-element_type="widget" data-widget_type="text-editor.default">';
        if (apply_filters('wpml_current_language', null) == 'en') {
            echo '<div class="elementor-widget-container"><h3>User authenticated. Redirecting...</h3></div></div>';
            echo '<script>window.location.href="/estudio"</script>';
        } else {
            echo '<div class="elementor-widget-container"><h3>Usuario autenticado. Redirigiendo...</h3></div></div>';
            echo '<script>jQuery("h3:contains(Inicia sesión)").hide();window.location.href="/estudio"</script>';
        }
        exit();
    }
}
add_shortcode('nodecharts-login-page', 'nodecharts_login_page');

function redirect_login_page()
{
    $url = basename($_SERVER['REQUEST_URI']); // get requested URL
    isset($_REQUEST['redirect_to']) ? ($url = "wp-login.php") : null; // if users send request to wp-admin
    if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['customer-logout'])) {
        wp_logout();
        $url = apply_filters('wpml_current_language', null) == 'en'
        ? '/en/sign-in' : '/iniciar-sesion';
        wp_redirect(home_url($url));
        exit;
    }

    // Avoid infinite redirection when user is not logged in
    if ($url == "wp-login.php" && $_SERVER['REQUEST_METHOD'] && isset($_GET['redirect_to'])) {
        wp_redirect(home_url(apply_filters('wpml_current_language', null) == 'en'
        ? '/en/sign-in' : '/iniciar-sesion'));
        exit;
    }

    if ($url  == "wp-login.php" && isset($_POST['log'], $_POST['pwd'])) {
        $user = wp_authenticate($_POST['log'], $_POST['pwd']);
        if (!is_wp_error($user)) {
            wp_set_current_user($user->ID);
            $redirect = apply_filters('wpml_current_language', null) == 'en'
            ? '/en/studio' : '/estudio';
            wp_redirect(home_url($redirect));
        } else {
            wp_redirect(home_url(apply_filters('wpml_current_language', null) == 'en'
            ? '/en/sign-in' : '/iniciar-sesion'));
        }
    }

    if ($url == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['loginSocial'])) {
        $redirect = apply_filters('wpml_current_language', null) == 'en'
            ? '/en/studio' : '/estudio';
        wp_redirect(home_url($redirect));
    }

    // do not add this without checkin admin is on wp_redirect(home_url('/iniciar-sesion'));
}
add_action('init', 'redirect_login_page');
