<?php
/**
 * Registry Form Pro - Customizer Setup
 * Developed by: James P. Friday
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function rfp_customize_register_plugin( $wp_customize ) {

    // --- PANEL: Registry Form Pro ---
    $wp_customize->add_panel( 'rfp_main_panel', array(
        'title'       => __( 'Registry Form Pro', 'rfp' ),
        'priority'    => 30,
        'description' => __( 'Configure your multi-step registry forms, payments, and notifications.', 'rfp' ),
    ) );

    // --- SECTION 1: Form Structure & Notifications ---
    $wp_customize->add_section( 'rfp_logic_section', array(
        'title' => __( 'Steps & Notifications', 'rfp' ),
        'panel' => 'rfp_main_panel',
    ) );

    // Multi-Step Titles
    $wp_customize->add_setting( 'rfp_step_titles', array(
        'default'           => 'Personal Info, Documentation',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'rfp_step_titles', array(
        'label'       => __( 'Step Titles', 'rfp' ),
        'description' => __( 'Separate with commas. Note: A "Review & Summary" step is automatically added at the end.', 'rfp' ),
        'section'     => 'rfp_logic_section',
        'type'        => 'text',
    ) );

    // Admin Emails (For Official Document Notifications)
    $wp_customize->add_setting( 'rfp_admin_emails', array(
        'default'           => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'rfp_admin_emails', array(
        'label'       => __( 'Admin Notification Emails', 'rfp' ),
        'description' => __( 'Separate multiple emails with commas. Each will receive the Official Document record.', 'rfp' ),
        'section'     => 'rfp_logic_section',
        'type'        => 'textarea',
    ) );

    // Success Message for User
    $wp_customize->add_setting( 'rfp_success_msg', array(
        'default'           => 'Thank you! Your application has been received successfully.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'rfp_success_msg', array(
        'label'   => __( 'User Success Message', 'rfp' ),
        'section' => 'rfp_logic_section',
        'type'    => 'textarea',
    ) );

    // --- SECTION 2: Payment Gateway & Submission Type ---
    $wp_customize->add_section( 'rfp_payment_section', array(
        'title' => __( 'Submission & Payment', 'rfp' ),
        'panel' => 'rfp_main_panel',
    ) );

    // Toggle: Submit Only vs Pay & Submit
    $wp_customize->add_setting( 'rfp_gateway', array( 
        'default'           => 'none',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'rfp_gateway', array(
        'label'   => __( 'Submission Type', 'rfp' ),
        'description' => __( 'Choose "Submit Only" for free forms or "Paystack" to require payment before submission.', 'rfp' ),
        'section' => 'rfp_payment_section',
        'type'    => 'select',
        'choices' => array(
            'none'     => 'Submit Only (Free)',
            'paystack' => 'Pay & Submit (Paystack)',
        ),
    ) );

    // Registration Fee
    $wp_customize->add_setting( 'rfp_reg_fee', array( 
        'default'           => '5000',
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'rfp_reg_fee', array(
        'label'   => __( 'Registration Fee (₦)', 'rfp' ),
        'section' => 'rfp_payment_section',
        'type'    => 'number',
    ) );

    // Paystack Public Key
    $wp_customize->add_setting( 'rfp_paystack_pk', array( 
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'rfp_paystack_pk', array(
        'label'   => __( 'Paystack Public Key', 'rfp' ),
        'section' => 'rfp_payment_section',
        'type'    => 'text',
    ) );

    // --- SECTION 3: Form Fields Builder ---
    $wp_customize->add_section( 'rfp_fields_section', array(
        'title' => __( 'Form Fields Builder', 'rfp' ),
        'panel' => 'rfp_main_panel',
    ) );

    $wp_customize->add_setting( 'rfp_fields_config', array(
        'default'           => 'text|full_name|Full Name|Enter your name|yes, tel|phone|Phone Number|080...|yes, file|id_card|Upload ID|PDF/JPG|yes',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'rfp_fields_config', array(
        'label'       => __( 'Fields Configuration', 'rfp' ),
        'description' => __( 'Format: type|name|label|placeholder|required. Types: text, email, tel, file, address.', 'rfp' ),
        'section'     => 'rfp_fields_section',
        'type'        => 'textarea',
    ) );

    // --- SECTION 4: Design & Branding ---
    $wp_customize->add_section( 'rfp_design_section', array(
        'title' => __( 'Design & Branding', 'rfp' ),
        'panel' => 'rfp_main_panel',
    ) );

    $wp_customize->add_setting( 'rfp_brand_color', array( 'default' => '#3b82f6' ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rfp_brand_color', array(
        'label'   => __( 'Primary Brand Color', 'rfp' ),
        'section' => 'rfp_design_section',
    ) ) );

    $wp_customize->add_setting( 'rfp_btn_text', array( 'default' => 'Submit Application' ) );
    $wp_customize->add_control( 'rfp_btn_text', array(
        'label'   => __( 'Default Button Text', 'rfp' ),
        'description' => __( 'Used when submission is free.', 'rfp' ),
        'section' => 'rfp_design_section',
    ) );
}
add_action( 'customize_register', 'rfp_customize_register_plugin' );