<?php
/**
 * Registry Form Pro - AJAX & Email Engine
 * Unified with Dashboard Config & Official Document Generator
 * Developed by: James P. Friday
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_ajax_rfp_submit_action', 'rfp_process_form_submission');
add_action('wp_ajax_nopriv_rfp_submit_action', 'rfp_process_form_submission');

function rfp_process_form_submission() {
    // Check security nonce
    check_ajax_referer('rfp_secure_nonce', 'security');

    // 1. Fetch Unified Config from Dashboard
    $raw_config = get_option('rfp_full_config', '{}');
    while (strpos($raw_config, '\"') !== false) { $raw_config = stripslashes($raw_config); }
    $full_config = json_decode($raw_config, true);

    $admin_emails_raw = isset($full_config['emails']) ? $full_config['emails'] : get_option('admin_email');
    $success_msg = get_theme_mod('rfp_success_msg', 'Application Received Successfully.');

    $form_data = $_POST;
    $files = $_FILES;
    $uploaded_files = array();
    $attachments = array();

    // 2. Handle Surgical File Uploads
    if ( ! empty( $files ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        
        foreach ( $files as $key => $file ) {
            $movefile = wp_handle_upload( $file, array( 'test_form' => false ) );
            if ( $movefile && ! isset( $movefile['error'] ) ) {
                $uploaded_files[$key] = $movefile['url'];
                $attachments[] = $movefile['file']; // Path for real wp_mail attachment
            }
        }
    }

    // 3. Parse Multi-Admin Emails
    $admin_list = array_filter(array_map('trim', explode(',', $admin_emails_raw)), 'is_email');
    
    // 4. Construct Clean Data Summary
    $user_email = sanitize_email($form_data['email'] ?? $form_data['user_email'] ?? '');
    $user_name  = sanitize_text_field($form_data['full_name'] ?? $form_data['user_name'] ?? 'New Applicant');
    
    // 5. THE OFFICIAL DOCUMENT TEMPLATE (For Admin)
    $official_doc = "
    <div style='background:#f1f5f9; padding:40px; font-family:sans-serif;'>
        <div style='max-width:600px; margin:0 auto; background:#fff; padding:40px; border-radius:24px; box-shadow:0 10px 30px rgba(0,0,0,0.05); border:1px solid #e2e8f0;'>
            <div style='text-align:center; margin-bottom:30px;'>
                <h1 style='color:#1e293b; margin:0; font-size:24px; letter-spacing:-0.5px;'>Official Application Record</h1>
                <p style='color:#64748b; font-size:14px; margin-top:5px;'>" . get_bloginfo('name') . "</p>
            </div>
            
            <table style='width:100%; border-collapse:collapse;'>";

    // Loop through Text Data
    foreach($form_data as $key => $value) {
        if(in_array($key, ['action', 'security', 'step'])) continue;
        $label = ucwords(str_replace(['_', '-'], ' ', $key));
        $official_doc .= "
            <tr>
                <td style='padding:15px 0; border-bottom:1px solid #f8fafc; color:#64748b; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.5px;'>$label</td>
                <td style='padding:15px 0; border-bottom:1px solid #f8fafc; color:#1e293b; font-weight:600; text-align:right; font-size:14px;'>" . esc_html($value) . "</td>
            </tr>";
    }
    
    // Loop through File Links
    foreach($uploaded_files as $key => $url) {
        $label = ucwords(str_replace(['_', '-'], ' ', $key));
        $official_doc .= "
            <tr>
                <td style='padding:15px 0; border-bottom:1px solid #f8fafc; color:#64748b; font-weight:700; font-size:12px; text-transform:uppercase;'>$label</td>
                <td style='padding:15px 0; border-bottom:1px solid #f8fafc; text-align:right;'>
                    <a href='$url' style='color:#3b82f6; text-decoration:none; font-weight:700; font-size:13px;'>View Document</a>
                </td>
            </tr>";
    }
    
    $official_doc .= "
            </table>
            
            <div style='margin-top:40px; padding:20px; background:#f8fafc; border-radius:12px; text-align:center;'>
                <p style='margin:0; font-size:11px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px;'>
                    Submitted via Registry Form Pro on " . date('Y-m-d H:i:s') . "
                </p>
            </div>
        </div>
    </div>";

    $headers = array('Content-Type: text/html; charset=UTF-8');

    // 6. Dispatch Notifications
    // To Admins: The Official Document + Real Attachments
    wp_mail($admin_list, "New Official Application: $user_name", $official_doc, $headers, $attachments);

    // To User: Confirmation Message + Summary
    if($user_email) {
        $confirmation_body = "<html><body><div style='font-family:sans-serif; padding:20px; color:#1e293b;'>
            <h2 style='color:#3b82f6;'>Hello $user_name,</h2>
            <p>$success_msg</p>
            <hr style='border:0; border-top:1px solid #f1f5f9; margin:20px 0;'>
            $official_doc
        </div></body></html>";
        wp_mail($user_email, "Application Received - " . get_bloginfo('name'), $confirmation_body, $headers);
    }

    wp_send_json_success(array('message' => $success_msg));
}