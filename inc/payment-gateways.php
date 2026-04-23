<?php
/**
 * Registry Form Pro - Payment & Setup Guide
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Helper to provide the Shortcode in the Customizer
function rfp_customize_guide( $wp_customize ) {

    // --- SECTION: Help & Shortcodes ---
    $wp_customize->add_section( 'rfp_help_section', array(
        'title'    => __( '📖 Setup Guide & Shortcode', 'rfp' ),
        'panel'    => 'rfp_main_panel',
        'priority' => 1, // Make it the first thing they see
    ) );

    $wp_customize->add_setting( 'rfp_guide_display', array( 'sanitize_callback' => 'esc_html' ) );
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rfp_guide_display', array(
        'label'       => __( 'How to Use', 'rfp' ),
        'description' => '
            <strong>1. Shortcode:</strong><br> 
            Paste <code>[registry_form]</code> on any page.<br><br>
            
            <strong>2. Form Fields Builder:</strong><br>
            Go to "Form Fields Builder" and use the format:<br>
            <code>type|name|label|placeholder|required</code><br>
            Example: <code>text|fname|First Name|John|yes</code><br><br>
            
            <strong>3. Multiple Admins:</strong><br>
            In "Form Steps", add emails separated by commas to notify multiple staff.<br><br>
            
            <strong>4. Signature:</strong><br>
            Developer credit is hard-coded and encrypted in the engine.',
        'section'     => 'rfp_help_section',
        'type'        => 'hidden', // Just using the description as a UI guide
    ) ) );
}
add_action( 'customize_register', 'rfp_customize_guide' );