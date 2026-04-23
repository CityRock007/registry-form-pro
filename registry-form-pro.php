<?php
/**
 * Plugin Name:       Registry Form Pro
 * Plugin URI:        https://github.com/CityRock007/registry-form-pro
 * Description:       A high-performance, enterprise-grade multi-step form engine for WordPress. Featuring a fluid Drag & Drop builder, native Servinux & Paystack integration, and automated multi-admin notifications. Designed for high-speed reliability on LiteSpeed and CyberPanel environments.
 * Version:           1.1.0
 * Author:            James P. Friday
 * Author URI:        https://github.com/CityRock007/CityRock007/
 * Text Domain:       rfp
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// --- CONFIGURATION & CONSTANTS ---
define( 'RFP_VERSION', '1.1.0' );
define( 'RFP_PATH', plugin_dir_path( __FILE__ ) );
define( 'RFP_URL', plugin_dir_url( __FILE__ ) );
define( 'RFP_BASENAME', plugin_basename( __FILE__ ) );

/**
 * CORE MODULE LOADER
 */
require_once RFP_PATH . 'inc/ajax-handler.php';
require_once RFP_PATH . 'inc/payment-gateways.php';

/**
 * PROTECTED SIGNATURE ENGINE
 */
function rfp_get_signature() {
    $sig = 'RGV2ZWxvcGVkIGJ5OiA8YSBocmVmPSJodHRwczovL2dpdGh1Yi5jb20vQ2l0eVJvY2swMDcvIiB0YXJnZXQ9Il9ibGFuayIgc3R5bGU9ImNvbG9yOiBpbmhlcml0OyB0ZXh0LWRlY29yYXRpb246IG5vbmU7IG9wYWNpdHk6IDAuODsgZm9udC1zaXplOiAxMXB4OyI+SmFtZXMgUC4gRnJpZGF5PC9hPg==';
    return base64_decode($sig);
}

/**
 * ADMIN MENU & BUILDER SETUP
 */
add_action('admin_menu', 'rfp_register_builder_menu');
function rfp_register_builder_menu() {
    add_menu_page(
        'RFP Form Builder', 
        'RFP Builder', 
        'manage_options', 
        'rfp-builder', 
        'rfp_render_builder_page', 
        'dashicons-feedback', 
        30
    );
}

function rfp_render_builder_page() {
    include RFP_PATH . 'inc/builder-ui.php'; 
}

/**
 * PLUGIN BRANDING: Custom Icon Injection
 */
add_action( 'admin_head', 'rfp_plugin_branding_css' );
function rfp_plugin_branding_css() {
    $icon_url = RFP_URL . 'assets/images/RegistryFormPro.jpg';
    ?>
    <style>
        /* Sidebar Menu Icon Fix */
        #toplevel_page_rfp-builder .wp-menu-image {
            background-image: url('<?php echo esc_url($icon_url); ?>');
            background-size: 20px;
            background-repeat: no-repeat;
            background-position: center;
        }
        #toplevel_page_rfp-builder .wp-menu-image img { opacity: 0; }
        
        /* Plugins List Icon Fix */
        .plugins tr[data-slug="registry-form-pro"] .plugin-title:before {
            content: "";
            display: inline-block;
            width: 40px; height: 40px;
            margin-right: 15px;
            vertical-align: middle;
            background-image: url('<?php echo esc_url($icon_url); ?>');
            background-size: cover;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
    <?php
}

/**
 * ASSET MANAGEMENT (Admin & Frontend)
 */
add_action( 'wp_enqueue_scripts', 'rfp_enqueue_frontend_assets' );
function rfp_enqueue_frontend_assets() {
    wp_enqueue_script( 'rfp-core-js', RFP_URL . 'assets/js/rfp-core.js', array('jquery'), RFP_VERSION, true );

    $gateway = get_option('rfp_gateway', 'none');
    
    // Gateway-Specific SDK Loaders
    if ( $gateway === 'paystack' ) {
        wp_enqueue_script( 'paystack-inline', 'https://js.paystack.co/v1/inline.js', array(), null, true );
    } elseif ( $gateway === 'servinux' ) {
        wp_enqueue_script( 'servinux-checkout', 'https://merchant.servinux.com/sdk/checkout-v2.js', array(), null, true );
    }

    wp_localize_script( 'rfp-core-js', 'rfp_data', array(
        'ajax_url'    => admin_url( 'admin-ajax.php' ),
        'nonce'       => wp_create_nonce( 'rfp_secure_nonce' ),
        'gateway'     => $gateway,
        'servinux_sk' => get_option('rfp_servinux_sk', ''),
        'paystack_pk' => get_option('rfp_paystack_pk', ''),
        'fee'         => get_option('rfp_reg_fee', '5000'),
        'currency'    => 'NGN',
    ));
}

add_action( 'admin_enqueue_scripts', 'rfp_enqueue_admin_assets' );
function rfp_enqueue_admin_assets($hook) {
    if ( 'toplevel_page_rfp-builder' !== $hook ) return;

    wp_enqueue_style( 'rfp-admin-css', RFP_URL . 'assets/css/rfp-admin-style.css', array(), RFP_VERSION );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'rfp-admin-builder-js', RFP_URL . 'assets/js/rfp-admin-builder.js', array('jquery', 'jquery-ui-sortable'), RFP_VERSION, true );

    wp_localize_script( 'rfp-admin-builder-js', 'rfp_builder_data', array(
        'nonce' => wp_create_nonce('rfp_builder_nonce')
    ));
}

/**
 * ADD BUILDER LINK TO PLUGIN PAGE
 */
add_filter( 'plugin_action_links_' . RFP_BASENAME, 'rfp_add_action_links' );
function rfp_add_action_links( $links ) {
    $builder_link = '<a href="' . admin_url( 'admin.php?page=rfp-builder' ) . '"><strong>' . __( 'Form Builder', 'rfp' ) . '</strong></a>';
    array_unshift( $links, $builder_link );
    return $links;
}

/**
 * AJAX: SAVE FULL BUILDER CONFIG
 */
add_action('wp_ajax_rfp_save_builder_data', 'rfp_save_builder_data_handler');
function rfp_save_builder_data_handler() {
    check_ajax_referer('rfp_builder_nonce', 'nonce');
    if ( ! current_user_can('manage_options') ) wp_send_json_error('Unauthorized');

    $payload = isset($_POST['payload']) ? wp_unslash($_POST['payload']) : '';
    
    // Save the builder JSON
    update_option('rfp_full_config', $payload);

    // Surgically capture and save Dashboard settings (Gateway, Fee, Keys)
    if ( isset($_POST['gateway']) ) update_option('rfp_gateway', sanitize_text_field($_POST['gateway']));
    if ( isset($_POST['fee']) )     update_option('rfp_reg_fee', sanitize_text_field($_POST['fee']));
    if ( isset($_POST['ps_pk']) )   update_option('rfp_paystack_pk', sanitize_text_field($_POST['ps_pk']));
    if ( isset($_POST['sn_sk']) )   update_option('rfp_servinux_sk', sanitize_text_field($_POST['sn_sk']));
    if ( isset($_POST['emails']) ) {
        $config = json_decode($payload, true);
        $config['emails'] = sanitize_text_field($_POST['emails']);
        update_option('rfp_full_config', json_encode($config));
    }

    wp_cache_delete('rfp_full_config', 'options'); 
    wp_send_json_success('Configuration Saved');
}

/**
 * AJAX: LOAD BUILDER DATA
 */
add_action('wp_ajax_rfp_load_builder_data', 'rfp_load_builder_data_handler');
function rfp_load_builder_data_handler() {
    check_ajax_referer('rfp_builder_nonce', 'nonce');
    if ( ! current_user_can('manage_options') ) wp_send_json_error('Unauthorized');

    $config = get_option('rfp_full_config', '{}');
    while (strpos($config, '\"') !== false) { $config = stripslashes($config); }
    wp_send_json_success(json_decode($config));
}

/**
 * THE RENDERER
 */
add_shortcode( 'registry_form', 'rfp_main_form_render' );
function rfp_main_form_render( $atts ) {
    ob_start();
    include RFP_PATH . 'templates/form-template.php';
    echo '<div class="rfp-sig-container" style="text-align: center; margin-top: 20px;">' . rfp_get_signature() . '</div>';
    return ob_get_clean();
}

/**
 * DYNAMIC FIELD GENERATOR
 */
function rfp_render_dynamic_field( $type, $name, $label, $required = false, $width = '100' ) {
    $req_attr = $required ? 'required' : '';
    $id = 'rfp_field_' . sanitize_title($name);
    
    echo "<div class='rfp-input-group' style='flex: 0 0 $width%; max-width: $width%; padding: 10px; box-sizing: border-box;'>";
    echo "<label for='$id'>$label " . ($required ? '<span style="color:red">*</span>' : '') . "</label>";

    switch ( $type ) {
        case 'tel': echo "<input type='tel' name='$name' id='$id' $req_attr>"; break;
        case 'file': echo "<div class='rfp-file-wrapper'><input type='file' name='$name' id='$id' $req_attr></div>"; break;
        case 'address': echo "<textarea name='$name' id='$id' rows='3' $req_attr></textarea>"; break;
        case 'email': echo "<input type='email' name='$name' id='$id' $req_attr>"; break;
        case 'date': echo "<input type='date' name='$name' id='$id' $req_attr>"; break;
        default: echo "<input type='$type' name='$name' id='$id' $req_attr>"; break;
    }
    echo "</div>";
}

/**
 * PLUGIN ACTIVATION
 */
register_activation_hook( __FILE__, 'rfp_plugin_activation' );
function rfp_plugin_activation() {
    $upload_dir = wp_upload_dir();
    $rfp_dir = $upload_dir['basedir'] . '/rfp_submissions';
    if ( ! file_exists( $rfp_dir ) ) wp_mkdir_p( $rfp_dir );
}