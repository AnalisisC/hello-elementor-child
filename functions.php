<?php

/**
 * Theme functions and definitions
 * @package HelloElementor-child
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_VERSION', '2.8.1');

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

        $hook_result = apply_filters_deprecated('elementor_hello_theme_load_textdomain', [true], '2.0', 'hello_elementor_load_textdomain');
        if (apply_filters('hello_elementor_load_textdomain', $hook_result)) {
            load_theme_textdomain('hello-elementor', get_template_directory() . '/languages');
        }

        $hook_result = apply_filters_deprecated('elementor_hello_theme_register_menus', [true], '2.0', 'hello_elementor_register_menus');
        if (apply_filters('hello_elementor_register_menus', $hook_result)) {
            register_nav_menus(['menu-1' => __('Header', 'hello-elementor')]);
            register_nav_menus(['menu-2' => __('Footer', 'hello-elementor')]);
        }

        $hook_result = apply_filters_deprecated('elementor_hello_theme_add_theme_support', [true], '2.0', 'hello_elementor_add_theme_support');
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
                    'height' => 100,
                    'width' => 350,
                    'flex-height' => true,
                    'flex-width' => true,
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
            $hook_result = apply_filters_deprecated(
                'elementor_hello_theme_add_woocommerce_support',
                [true],
                '2.0',
                'hello_elementor_add_woocommerce_support'
            );
            if (apply_filters('hello_elementor_add_woocommerce_support', $hook_result)) {
                // WooCommerce in general.
                add_theme_support('woocommerce');
                // Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
                add_theme_support('wc-product-gallery-zoom'); // zoom.
                add_theme_support('wc-product-gallery-lightbox'); // lightbox.
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
    if (!$hello_theme_db_version || version_compare($hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<')) {
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
            [true],
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

if (!function_exists('hello_elementor_register_elementor_locations')) {
    /**
     * Register Elementor Locations.
     * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
     * @return void
     */
    function hello_elementor_register_elementor_locations($elementor_theme_manager)
    {
        $hook_result = apply_filters_deprecated(
            'elementor_hello_theme_register_elementor_locations',
            [true],
            '2.0',
            'hello_elementor_register_elementor_locations'
        );
        if (apply_filters('hello_elementor_register_elementor_locations', $hook_result)) {
            $elementor_theme_manager->register_all_core_location();
        }
    }
}
add_action('elementor/theme/register_locations', 'hello_elementor_register_elementor_locations');

if (!function_exists('hello_elementor_content_width')) {
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


/**
 * Wrapper function to deal with backwards compatibility.
 */
if (!function_exists('hello_elementor_body_open')) {
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
 * Sortcode [only_logged_in_users] to redirect non-logged in users to login
 * @return void
 */
function only_logged_in_users_shortcode()
{
    if (!is_user_logged_in()) {
        $slug = apply_filters('wpml_current_language', null) == "es" ? '/login' : '/en/sign-in';
        echo '<script>window.location.href = "' . get_site_url() . $slug . '";</script>';
        die();
    }
}
add_shortcode('only_logged_in_users', 'only_logged_in_users_shortcode');

/**
 * SHORTCODE PARA MOSTRAR BOTÓN REGISTRO/ACCEDER EN PRECIOS
 */
add_shortcode('basico', 'mostrar_basico');
function mostrar_basico($atts): string
{
    global $current_user;
    //    add_filter('widget_text', 'apply_shortcodes');
    if ($current_user->ID) {
        return '<div><p><b>' . esc_html__("Active!", 'nodechartsfam') . '</b></p></div>';
    } else {
        if (apply_filters('wpml_current_language', null) == "es") {
            return '<a class="link-pricing" href="https://nodecharts.com/registrarse"><div class="boton"><p>'
                . esc_html__("Start free", 'nodechartsfam') . '</p></div></a>';
        } else {
            return '<a class="link-pricing" href="https://nodecharts.com/en/sign-up"><div class="boton"><p>'
                . esc_html__("Start free", 'nodechartsfam') . '</p></div></a>';
        }
    }
}

add_shortcode('principiantemensual', 'mostrar_principiante_mensual');
function mostrar_principiante_mensual(): string
{
    global $current_user;
    if ($current_user->ID) {
        $subscription = getActiveSubscription2($current_user->ID);
        if (in_array(
            $subscription,
            ['Principiante Mensual', 'Principiante Anual', 'Experto Mensual', 'Experto Anual', 'Profesional Anual']
        )) {
            $res = '<div><p><b>' . esc_html__("Active!", 'nodechartsfam') . '</b></p></div>';
        } else {
            $res = '<a rel="nofollow" href="?add-to-cart=356" data-quantity="1" 
                data-product_id="356" data-product_sku="principiante-mensual" 
                class="button product_type_simple add_to_cart_button ajax_add_to_cart added">
                <div class="boton"><p>' . esc_html__("Buy!", 'nodechartsfam') . '</p></div></a>';
        }
    } else {
        $res = '<a rel="nofollow" href="?add-to-cart=356" data-quantity="1" 
                data-product_id="356" data-product_sku="principiante-mensual" 
                class="button product_type_simple add_to_cart_button ajax_add_to_cart added">
                <div class="boton"><p>' . esc_html__("Buy!", 'nodechartsfam') . '</p></div></a>';
    }
    return $res;
}

add_shortcode('principianteanual', 'mostrar_principiante_anual');
function mostrar_principiante_anual(): string
{
    global $current_user;
    if ($current_user->ID) {
        if (
            in_array(
                getActiveSubscription2($current_user->ID),
                ["Principiante Anual", "Experto Anual", "Profesional Anual"]
            )
        ) {
            $res = '<div><p><b>' . esc_html__("Active!", 'nodechartsfam') . '</b></p></div>';
        } else {
            $res = '<a rel="nofollow" href="?add-to-cart=358" data-quantity="1" 
            data-product_id="358" data-product_sku="principiante-anual" 
            class="button product_type_simple add_to_cart_button ajax_add_to_cart added">
            <div class="boton"><p>' . esc_html__("Buy!", 'nodechartsfam') . '</p></div></a>';
        }
    } else {
        $res = '<a rel="nofollow" href="?add-to-cart=358" data-quantity="1" 
        data-product_id="358" data-product_sku="principiante-anual" 
        class="button product_type_simple add_to_cart_button ajax_add_to_cart added">
        <div class="boton"><p>' . esc_html__("Buy!", 'nodechartsfam') . '</p></div></a>';
    }
    return $res;
}

add_shortcode('expertomensual', 'mostrar_experto_mensual');
function mostrar_experto_mensual(): string
{
    global $current_user;
    if ($current_user->ID) {
        $subscr = getActiveSubscription2($current_user->ID);
        if (in_array($subscr, ["Experto Mensual", "Experto Anual", "Profesional Anual"])) {
            $res = '<div><p><b>' . esc_html__("Active!", 'nodechartsfam') . '</b></p></div>';
        } else {
            $res = '<a rel="nofollow" href="?add-to-cart=360" data-quantity="1" 
            data-product_id="360" data-product_sku="experto-mensual" 
            class="button product_type_simple add_to_cart_button ajax_add_to_cart added">
            <div class="boton"><p>' . esc_html__("Buy!", 'nodechartsfam') . '</p></div></a>';
        }
    } else {
        $res = '<a rel="nofollow" href="?add-to-cart=360" data-quantity="1" 
        data-product_id="360" data-product_sku="experto-mensual" 
        class="button product_type_simple add_to_cart_button ajax_add_to_cart added">
        <div class="boton"><p>' . esc_html__("Buy!", 'nodechartsfam') . '</p></div></a>';
    }
    return $res;
}

add_shortcode('expertoanual', 'mostrar_experto_anual');
function mostrar_experto_anual(): string
{
    global $current_user;
    if ($current_user->ID) {
        $subscription = getActiveSubscription2($current_user->ID);
        if ($subscription == "Experto Anual" || $subscription == "Profesional Anual") {
            $res = '<div><p><b>' . esc_html__("Active!", 'nodechartsfam') . '</b></p></div>';
        } else {
            $res = '<a rel="nofollow" href="?add-to-cart=361" data-quantity="1"
            data-product_id="361" data-product_sku="experto-anual" 
            class="button product_type_simple add_to_cart_button ajax_add_to_cart added">
            <div class="boton"><p>' . esc_html__("Buy!", 'nodechartsfam') . '</p></div></a>';
        }
    } else {
        $res = '<a rel="nofollow" href="?add-to-cart=361" data-quantity="1"
        data-product_id="361" data-product_sku="experto-anual" 
        class="button product_type_simple add_to_cart_button ajax_add_to_cart added">
        <div class="boton"><p>' . esc_html__("Buy!", 'nodechartsfam') . '</p></div></a>';
    }
    return $res;
}

add_shortcode('profesionalmensual', 'mostrar_profesional_mensual');
function mostrar_profesional_mensual(): string
{
    global $current_user;
    // wp_get_current_user();
    // add_filter('widget_text', 'apply_shortcodes');
    if ($current_user->ID) {
        $subscription = getActiveSubscription2($current_user->ID);
        if (in_array($subscription, ["Profesional Mensual", "Profesional Anual"])) {
            $res = '<div><p><b>' . esc_html__("Active!", 'nodechartsfam') . '</b></p></div>';
        } else {
            $res = '<div class="boton disable"><p style="color: gray!important;">'
                . esc_html__("Solo anual", 'nodechartsfam') . '</p></div>';
        }
    } else {
        $res = '<div class="boton disable"><p style="color: gray!important;">'
            . esc_html__("Available yearly", 'nodechartsfam') . '</p></div>';
    }
    return $res;
}


add_shortcode('profesionalanual', 'mostrar_profesional_anual');
function mostrar_profesional_anual()
{
    global $current_user;
    // wp_get_current_user();
    // add_filter('widget_text', 'apply_shortcodes');
    if (
        $current_user->ID &&
        getActiveSubscription2($current_user->ID) == "Profesional Anual"
    ) {
        return '<div><p><b>' . esc_html__("Active!", 'nodechartsfam') .
            '</b></p></div>';
    } else {
        return '<a rel="nofollow" href="?add-to-cart=362" data-quantity="1" 
        data-product_id="362" data-product_sku="profesional-anual" 
        class="button product_type_simple add_to_cart_button ajax_add_to_cart added">
        <div class="boton"><p>' . esc_html__("Buy!", 'nodechartsfam') .
            '</p></div></a>';
    }
    // } else {
    //     $my_current_lang = apply_filters('wpml_current_language', null);
    //     if ($my_current_lang == "es") {
    //         return '<a class="link-pricing" href="https://nodecharts.com/registrarse"><div class="boton"><p>'
    //             . esc_html__("Sign up", 'nodechartsfam') . '</p></div></a>';
    //     } else {
    //         return '<a class="link-pricing" href="https://nodecharts.com/en/sign-up"><div class="boton"><p>'
    //             . esc_html__("Sign up", 'nodechartsfam') . '</p></div></a>';
    //     }
    // }
}

function nodecharts_jwt()
{
    $current_user = wp_get_current_user(); // Obtiene el usuario actualmente autenticado
    if ($current_user instanceof WP_User) {
        $user_hash = get_user_meta($current_user->ID, 'hash', true);
        if ($user_hash)
            echo '<script>window.jwt="' . $user_hash . '"</script>';
    }
}
add_shortcode('nodecharts_jwt', 'nodecharts_jwt');

function getActiveSubscription2(int $userId): string
{
    $nombremembresia = get_transient('getActiveSubs' . $userId);
    if (!$nombremembresia) {
        $subscriptions = wcs_get_users_subscriptions($userId);
        $nombremembresia = 'no';
        foreach ($subscriptions as $subscr) {
            if ($subscr->has_status(array('active', 'pending-cancel'))) {
                $items = wcs_get_subscription($subscr->get_id())->get_items();
                foreach ($items as $item) {
                    $product = $item->get_product();
                    $nombremembresia = $product->get_name();
                    break 2;
                }
            }
        }
        set_transient('getActiveSubs' . $userId, $nombremembresia, 60);
    }
    return $nombremembresia;
}

/**
 * URL redirect after user logout -- aleixtuset
 */
add_action('wp_logout', 'ps_redirect_after_logout');
function ps_redirect_after_logout()
{
    wp_redirect(home_url((apply_filters('wpml_current_language', null) == 'en'
        ? '/en/sign-in' : '/iniciar-sesion')));
    exit();
}

/**
 * URL redirect lost-password to custom page -- aleixtuset
 */
add_action('template_redirect', 'custom_url_forward');
function custom_url_forward()
{
    if ($_SERVER['REQUEST_URI'] == '/mi-cuenta/lost-password/') {
        wp_redirect("/restablecer-contrasena/");
        exit();
    }
    if ($_SERVER['REQUEST_URI'] == '/en/my-account/lost-password/') {
        wp_redirect("/en/reset-password/");
        exit();
    }
}


add_action('wp_footer', 'woocommerce_show_coupon', 99);
function woocommerce_show_coupon()
{
    echo '<script type="text/javascript">jQuery(document).ready(function($) {
$(\'.checkout_coupon\').show();});</script>';
}


/**
 * Avoid disabling webhooks when they fail
 *
 * @param [type] $number
 * @return void
 */
function overrule_webhook_disable_limit($number)
{
    return 999999999999; //very high number hopefully you'll never reach.
}
add_filter('woocommerce_max_webhook_delivery_failures', 'overrule_webhook_disable_limit');

/**
 * If webhook fails, it enqueues again for a new retry
 *
 * @param [array] $http_args
 * @param [array] $response
 * @param [unknown] $duration
 * @param [int] $arg
 * @param [int] $id
 * @return void
 */
function woocommerce_webhook_listener_custom($http_args, $response, $duration, $arg, $id)
{

    $responseCode = wp_remote_retrieve_response_code($response);
    if ($responseCode != 200) {
        // re-queue web-hook for another attempt, retry every 5 minutes until success
        $timestamp = new DateTime('+3 minutes');
        $argsArray = array('webhook_id' => $id, 'arg' => $arg);
        WC()->queue()->schedule_single($timestamp, 'woocommerce_deliver_webhook_async', $args = $argsArray, $group = 'woocommerce-webhooks');
        error_log("Retrying webhook delivery for ID " . $id . " with arg $arg -  " .
            json_encode($http_args) . PHP_EOL . '  Response:' . json_encode($response));
    }
}

add_action('woocommerce_webhook_delivery', 'woocommerce_webhook_listener_custom', 10, 5);


// function enqueue_react_script()
// {
//     wp_enqueue_script('react-bundle', '/wp-content/dist/bundle.js', array(), '1.0.0', true);
// }
// add_action('wp_enqueue_scripts', 'enqueue_react_script');

function custom_api_get_translation_links($req): array
{
    $trans = '';
    $lang = $req->get_param('lang'); // What lang to translate to
    $slug = $req->get_param('slug');
    if (!empty($slug)) {
        $page = get_page_by_path($slug);
        $trid = apply_filters('wpml_element_trid', NULL, $page->ID, 'post_page');
        if ($trid) {
            $trans = apply_filters('wpml_get_element_translations', null, $trid, 'post_page');
            $trans = ($lang == 'en' ? '/en/' : '/') . get_post($trans[$lang]->element_id)->post_name;
        }
    } else {
        $trans = ($lang == 'en' ? '/en/' : '/');
    }
    return ['page_slug' => $trans];
}

/**
 * @return string Page slug in other language set by lang param
 */
function register_translation_links_endpoint()
{
    register_rest_route(
        'nodecharts-api',
        '/translation-links',
        array(
            'methods' => 'POST',
            'callback' => 'custom_api_get_translation_links',
            'args' => array(
                'slug' => array(
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    },
                ),
                'lang' => array(
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return in_array($param, ['en', 'es']);
                    },
                ),
            ),
            'permission_callback' => true
        )
    );
}
add_action('rest_api_init', 'register_translation_links_endpoint');


function getUserMenuData($req)
{
    $vals = get_transient('usermenu_' . hash('sha256', $req['jwt'] . '_' . $req['slug'] . '_' . $req['lang']));
    if (!$vals) {
        $user = get_users([
            'meta_key' => 'hash',
            'meta_value' => $req['jwt'],
            'meta_compare' => '=',
            'number' => 1
        ]);
        if ($user) {
            $vals['page_slug'] = custom_api_get_translation_links($req)['page_slug'];
            $vals['lang'] = $req['lang'];
            $vals['user']['ID'] = $user[0]->ID;
            $vals['user']['display_name'] = $user[0]->display_name;
            $vals['user']['user_email'] = $user[0]->user_email;
            foreach ($user[0]->roles as $rol) {
                if ($rol == 'affiliate') {
                    $vals['user']['affiliate'] = true;
                    break;
                }
            }
            set_transient('usermenu_' . hash('sha256', $req['jwt'] . '_' . $req['slug'] . '_' . $req['lang']), $vals, 600);
        } else {
            throw new Exception('Invalid JWT ' . $req['jwt'], 400);
        }
    }
    return $vals;
}

/**
 * REST API for User Menu
 * @return void
 */
function usermenu()
{
    register_rest_route(
        'nodecharts-api',
        '/usermenu',
        array(
            'methods' => 'POST',
            'callback' => 'getUserMenuData',
            'args' => array(
                'slug' => array(
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    },
                ),
                'lang' => array(
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return in_array($param, ['en', 'es']);
                    },
                ),
                'jwt' => array(
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    }
                )
            ),
            'permission_callback' => true
        )
    );
}
add_action('rest_api_init', 'usermenu');

function delete_usercache($req)
{
    $params = $req->get_params();
    $username = $params['billing']['email'] ?? null;
    $x_wc_webhook_signature = $req->get_header('X-WC-Webhook-Signature');
    if ($username && checkWebhookSignature(
        $x_wc_webhook_signature,
        $req->get_body()
    )) {
        $path = WP_CONTENT_DIR . '/cache/wp-rocket/' .
            parse_url(site_url(), PHP_URL_HOST) . '-' .
            urlencode($username) . '-';
        if (file_exists($path)) {
            deleteDirRecursively($path);
            wp_send_json_success(array('message' => "OK [$path] removed."));
        } else {
            wp_send_json_error(array('message' => "KO [$path] not found."));
        }
    }
}

function register_api_clear_usercache()
{
    register_rest_route('nodecharts-api', '/deleteusercache/', array(
        'methods' => 'POST',
        'callback' => 'delete_usercache',
        'permission_callback' => function () {
            return true; //No need to check permissions: Webhook Signature is checked
            //return current_user_can('manage_options');
        },
    ));
}

add_action('rest_api_init', 'register_api_clear_usercache');

function deleteDirRecursively($dir)
{
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $path = $dir . '/' . $file;
                if (is_dir($path)) {
                    deleteDirRecursively($path); // Recursive call to delete subdirectories
                } else {
                    unlink($path); // Delete files
                }
            }
        }
        rmdir($dir); // Delete the current directory
    }
}


/**
 * Checks webhook signature. 
 *
 * @param string $x_wc_webhook_signature
 * @param string $body
 * @return boolean True is signature is ok
 */
function checkWebhookSignature(string $x_wc_webhook_signature, string $body): bool
{
    return $x_wc_webhook_signature ==
        base64_encode(hash_hmac(
            'sha256',
            $body,
            WOOCOMMERCE_WEBHOOK_SECRET,
            true
        ));
}
