<?php
/**
 * Registry Form Pro - Unified Builder Dashboard
 * Developed by: James P. Friday
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Fetch the unified configuration
$full_config = json_decode(get_option('rfp_full_config', '{}'), true);
$saved_steps = isset($full_config['steps']) ? $full_config['steps'] : 'Step 1, Step 2, Step 3';
$saved_emails = isset($full_config['emails']) ? $full_config['emails'] : get_option('admin_email');
?>

<input type="hidden" id="rfp_step_titles_data" value="<?php echo esc_attr($saved_steps); ?>">

<div class="wrap rfp-builder-wrap">
    <div class="rfp-builder-header">
        <div class="rfp-header-left">
            <h1><?php _e( 'RFP Form Dashboard', 'rfp' ); ?></h1>
            <p class="description"><?php _e( 'Manage your form fields, steps, and notifications in one place.', 'rfp' ); ?></p>
        </div>
        <div class="rfp-header-actions">
            <button id="rfp-save-form" class="button button-primary button-hero" style="cursor: pointer;">
                <span class="dashicons dashicons-saved"></span> <?php _e( 'Save All Changes', 'rfp' ); ?>
            </button>
        </div>
    </div>

    <div class="rfp-builder-tabs">
        <button class="rfp-tab-btn active" data-tab="fields">
            <span class="dashicons dashicons-format-aside"></span> <?php _e( '1. Form Fields', 'rfp' ); ?>
        </button>
        <button class="rfp-tab-btn" data-tab="steps">
            <span class="dashicons dashicons-list-view"></span> <?php _e( '2. Manage Steps', 'rfp' ); ?>
        </button>
        <button class="rfp-tab-btn" data-tab="notifications">
            <span class="dashicons dashicons-email-alt"></span> <?php _e( '3. Notifications', 'rfp' ); ?>
        </button>
    </div>

    <div id="rfp-builder-container">
        
        <div class="rfp-tab-content active" id="tab-fields">
            <div class="rfp-grid-layout">
                <aside class="rfp-toolbar">
                    <div class="rfp-toolbar-inner">
                        <h3><?php _e( 'Click to Add Fields', 'rfp' ); ?></h3>
                        <div class="rfp-draggable" data-type="text"><i class="dashicons dashicons-edit"></i> Short Text</div>
                        <div class="rfp-draggable" data-type="email"><i class="dashicons dashicons-email"></i> Email</div>
                        <div class="rfp-draggable" data-type="tel"><i class="dashicons dashicons-phone"></i> Phone</div>
                        <div class="rfp-draggable" data-type="file"><i class="dashicons dashicons-upload"></i> File Upload</div>
                        <div class="rfp-draggable" data-type="address"><i class="dashicons dashicons-location"></i> Address</div>
                    </div>
                </aside>

                <main class="rfp-canvas-area">
                    <div class="rfp-canvas-header">
                        <span class="rfp-canvas-status"><?php _e( 'Drag Fields Below', 'rfp' ); ?></span>
                        <span class="rfp-shortcode-preview">Shortcode: <code>[registry_form]</code></span>
                    </div>
                    <div id="rfp-canvas">
                        <ul id="rfp-sortable-list"></ul>
                    </div>
                </main>
            </div>
        </div>

        <div class="rfp-tab-content" id="tab-steps">
            <div class="rfp-settings-card">
                <h3><?php _e( 'Multi-Step Configuration', 'rfp' ); ?></h3>
                <p class="description"><?php _e( 'Enter your step titles separated by commas. These will appear in the field builder dropdowns after saving.', 'rfp' ); ?></p>
                <textarea id="rfp-steps-input" rows="3" class="large-text" placeholder="e.g. Personal Info, Documentation, Review"><?php echo esc_textarea($saved_steps); ?></textarea>
            </div>
        </div>

        <div class="rfp-tab-content" id="tab-notifications">
            <div class="rfp-settings-card">
                <h3><?php _e( 'Admin Notification Emails', 'rfp' ); ?></h3>
                <p class="description"><?php _e( 'Who should receive the form data? (Separate with commas for multiple admins)', 'rfp' ); ?></p>
                <input type="text" id="rfp-admin-emails" class="large-text" value="<?php echo esc_attr($saved_emails); ?>" placeholder="boss@mail.com, staff@mail.com">
            </div>
        </div>

    </div>
</div>

<style>
    /* Surgical UI Layout Updates */
    .rfp-builder-wrap { margin-top: 20px; max-width: 1200px; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif; }
    
    .rfp-builder-header { 
        display: flex; justify-content: space-between; align-items: center; 
        background: #fff; padding: 20px 30px; border-radius: 15px; 
        border: 1px solid #e2e8f0; margin-bottom: 20px;
    }

    /* Tabs Navigation */
    .rfp-builder-tabs { display: flex; gap: 10px; margin-bottom: 20px; }
    .rfp-tab-btn { 
        background: #fff; border: 1px solid #e2e8f0; padding: 12px 20px; 
        border-radius: 10px; cursor: pointer; font-weight: 600; color: #64748b;
        transition: 0.3s; display: flex; align-items: center; gap: 8px;
    }
    .rfp-tab-btn:hover { background: #f8fafc; color: #3b82f6; }
    .rfp-tab-btn.active { background: #3b82f6; color: #fff; border-color: #3b82f6; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }

    /* Tab Content Visibility */
    .rfp-tab-content { display: none; }
    .rfp-tab-content.active { display: block; animation: rfpFadeIn 0.4s ease; }
    @keyframes rfpFadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* Layout Grid */
    .rfp-grid-layout { display: grid; grid-template-columns: 280px 1fr; gap: 25px; }

    /* Settings Cards */
    .rfp-settings-card { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #e2e8f0; }
    .rfp-settings-card h3 { margin-top: 0; color: #0f172a; }
    .rfp-settings-card .large-text { margin-top: 15px; border-radius: 10px; border-color: #e2e8f0; padding: 15px; }

    /* Existing Builder Styles... */
    .rfp-toolbar-inner { background: #fff; border: 1px solid #e2e8f0; border-radius: 15px; padding: 20px; }
    .rfp-canvas-area { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 20px; padding: 30px; min-height: 500px; }
    
    .rfp-draggable {
        padding: 12px; margin-bottom: 8px; background: #fff; border: 1px solid #e2e8f0;
        border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: 0.2s;
    }
    .rfp-draggable:hover { transform: translateX(5px); border-color: #3b82f6; color: #3b82f6; }
</style>