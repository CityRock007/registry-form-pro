<?php
/**
 * Registry Form Pro - Master Template (Final Professional Update)
 * Developed by: James P. Friday
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// 1. DATA PULL & CLEANING
$raw_config = get_option('rfp_full_config', '{}');
while (strpos($raw_config, '\"') !== false) { $raw_config = stripslashes($raw_config); }
$full_config = json_decode($raw_config, true);

$saved_fields    = isset($full_config['fields']) ? $full_config['fields'] : array();
$step_titles_raw = isset($full_config['steps']) ? $full_config['steps'] : 'Step 1, Step 2, Step 3';

// 2. BRANDING
$brand_color = get_theme_mod('rfp_brand_color', '#3b82f6');
$submit_text = get_theme_mod('rfp_btn_text', 'Submit Application');
$gateway     = get_option('rfp_gateway', 'none');
$fee         = get_option('rfp_reg_fee', '5000');

// 3. STEP LOGIC
$steps = array_filter(array_map('trim', explode(',', $step_titles_raw)));
$steps[] = "Review & Summary"; 
$total_steps = count($steps);
?>

<style>
    :root { 
        --rfp-primary: <?php echo esc_attr($brand_color); ?>; 
        --rfp-primary-soft: <?php echo esc_attr($brand_color); ?>15;
        --rfp-border: #e2e8f0;
        --rfp-bg: #f8fafc;
        --rfp-text: #1e293b;
        --rfp-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }

    .rfp-form-container {
        max-width: 850px;
        margin: 40px auto;
        background: #ffffff;
        padding: 60px;
        border-radius: 32px;
        box-shadow: var(--rfp-shadow);
        font-family: 'Plus Jakarta Sans', sans-serif;
        position: relative;
    }

    /* Progress Stepper */
    .rfp-progress-wrapper { display: flex; align-items: center; justify-content: space-between; margin-bottom: 60px; position: relative; padding: 0 10px; }
    .rfp-progress-line { position: absolute; top: 21px; left: 0; height: 4px; background: #f1f5f9; width: 100%; z-index: 1; border-radius: 10px;}
    .rfp-progress-fill { position: absolute; top: 21px; left: 0; height: 4px; background: var(--rfp-primary); width: 0%; z-index: 2; transition: width 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); border-radius: 10px;}
    
    .rfp-step-node { 
        width: 42px; height: 42px; border-radius: 50%; background: #fff; border: 3px solid #f1f5f9;
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;
        z-index: 3; color: #cbd5e1; transition: 0.4s ease;
    }
    .rfp-step-node.active { border-color: var(--rfp-primary); color: var(--rfp-primary); transform: scale(1.15); box-shadow: 0 0 20px var(--rfp-primary-soft); }
    .rfp-step-node.completed { background: var(--rfp-primary); border-color: var(--rfp-primary); color: #fff; }

    /* Step Content Animation */
    .rfp-step-content { display: none; }
    .rfp-step-content.active { display: block; animation: rfpSlideUp 0.5s ease forwards; }
    @keyframes rfpSlideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    .rfp-step-header { margin-bottom: 45px; text-align: center; }
    .rfp-step-header h2 { font-size: 2rem; font-weight: 800; color: var(--rfp-text); margin: 0; letter-spacing: -1px; }

    /* Grid Layout - Fixed Gutter for 1/2 Width */
    .rfp-fields-grid { display: flex; flex-wrap: wrap; margin: 0 -15px; }
    .rfp-input-group { margin-bottom: 25px; padding: 0 15px; box-sizing: border-box; }
    
    .rfp-input-group label { display: block; font-size: 12px; font-weight: 800; color: #64748b; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.8px; }
    .rfp-input-group input, .rfp-input-group textarea, .rfp-input-group select {
        width: 100%; padding: 16px 20px; border: 2px solid var(--rfp-border); border-radius: 14px;
        font-size: 15px; font-weight: 500; background: var(--rfp-bg); color: var(--rfp-text); transition: all 0.3s ease; box-sizing: border-box;
    }
    .rfp-input-group input:hover { border-color: #cbd5e1; }
    .rfp-input-group input:focus { border-color: var(--rfp-primary); outline: none; background: #fff; box-shadow: 0 0 0 5px var(--rfp-primary-soft); transform: translateY(-2px); }

    /* Summary Card Styling - Professional "Receipt" Style */
    .rfp-summary-card { 
        background: #fcfdfe; 
        border: 2px dashed #e2e8f0; 
        border-radius: 24px; 
        padding: 40px; 
        margin-bottom: 20px; 
        position: relative;
    }
    .rfp-summary-card::before, .rfp-summary-card::after {
        content: ''; position: absolute; width: 20px; height: 20px; background: #fff; border-radius: 50%; top: 50%; transform: translateY(-50%);
    }
    .rfp-summary-card::before { left: -12px; }
    .rfp-summary-card::after { right: -12px; }

    .rfp-summary-item { display: flex; justify-content: space-between; padding: 18px 0; border-bottom: 1px solid #f1f5f9; align-items: center; }
    .rfp-summary-item:last-child { border-bottom: none; }
    .rfp-summary-label { font-weight: 800; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
    .rfp-summary-value { font-weight: 700; color: var(--rfp-text); text-align: right; font-size: 15px; }

    /* Navigation */
    .rfp-navigation { display: flex; justify-content: space-between; margin-top: 50px; padding-top: 30px; border-top: 2px solid #f1f5f9; }
    .rfp-btn { padding: 16px 36px; border-radius: 18px; font-weight: 800; font-size: 16px; cursor: pointer; border: none; display: flex; align-items: center; gap: 12px; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .rfp-btn-prev { background: #f1f5f9; color: #475569; }
    .rfp-btn-next, .rfp-btn-submit { background: var(--rfp-primary); color: #fff; }
    .rfp-btn:hover { transform: translateY(-4px); box-shadow: 0 12px 20px var(--rfp-primary-soft); filter: brightness(1.1); }
    .rfp-btn:active { transform: translateY(0); }

    @media (max-width: 600px) {
        .rfp-form-container { padding: 30px 20px; }
        .rfp-input-group { flex: 0 0 100% !important; max-width: 100% !important; }
        .rfp-summary-item { flex-direction: column; align-items: flex-start; gap: 5px; }
        .rfp-summary-value { text-align: left; }
    }
</style>

<div class="rfp-form-container">
    <div class="rfp-progress-wrapper">
        <div class="rfp-progress-line"></div>
        <div id="rfp-progress-fill" class="rfp-progress-fill"></div>
        <?php for ($i = 1; $i <= $total_steps; $i++) : ?>
            <div class="rfp-step-node <?php echo ($i === 1) ? 'active' : ''; ?>" id="rfp-node-<?php echo $i; ?>">
                <?php echo $i; ?>
            </div>
        <?php endfor; ?>
    </div>

    <form id="rfp-main-form" enctype="multipart/form-data" onsubmit="return false;">
        <?php 
        $idx = 1;
        foreach ($steps as $step_title) : 
            $is_summary = ($idx === $total_steps);
        ?>
            <div class="rfp-step-content <?php echo ($idx === 1) ? 'active' : ''; ?>" id="rfp-step-<?php echo $idx; ?>">
                <div class="rfp-step-header">
                    <h2><?php echo esc_html($step_title); ?></h2>
                </div>

                <div class="rfp-fields-grid">
                    <?php if ($is_summary) : ?>
                        <div id="rfp-summary-display" class="rfp-summary-card" style="width:100%;">
                            <p style="text-align:center; color:#94a3b8;">Generating your summary...</p>
                        </div>
                    <?php else : ?>
                        <?php 
                        foreach ($saved_fields as $field) {
                            if ( (int)$field['step'] === $idx ) {
                                rfp_render_dynamic_field(
                                    $field['type'], $field['name'], $field['label'], 
                                    !empty($field['required']), isset($field['width']) ? $field['width'] : '100'
                                );
                            }
                        }
                        ?>
                    <?php endif; ?>
                </div>

                <div class="rfp-navigation">
                    <?php if ($idx > 1) : ?>
                        <button type="button" class="rfp-btn rfp-btn-prev" onclick="rfpTriggerStep(<?php echo $idx - 1; ?>)">
                           <span class="dashicons dashicons-arrow-left-alt2"></span> Back
                        </button>
                    <?php else : echo '<div></div>'; endif; ?>

                    <?php if ($idx < $total_steps) : ?>
                        <button type="button" class="rfp-btn rfp-btn-next" onclick="rfpTriggerStep(<?php echo $idx + 1; ?>)">
                            Continue <span class="dashicons dashicons-arrow-right-alt2"></span>
                        </button>
                    <?php else : ?>
                        <button type="submit" class="rfp-btn rfp-btn-submit" onclick="rfpHandleSubmit(event)">
                            <span class="rfp-btn-label-text"><?php echo ($gateway !== 'none') ? "Pay ₦" . number_format($fee) . " & Submit" : esc_html($submit_text); ?></span>
                            <span class="dashicons dashicons-paper-plane"></span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php $idx++; endforeach; ?>
    </form>
    <div id="rfp-feedback"></div>
</div>

<script>
    /**
     * Professional Summary Builder
     */
    function rfpBuildSummary() {
        let html = '';
        jQuery('#rfp-main-form .rfp-input-group').each(function() {
            const label = jQuery(this).find('label').text().replace('*', '').trim();
            const input = jQuery(this).find('input, select, textarea');
            let val = input.val();

            if (input.attr('type') === 'file') {
                val = input[0].files[0] ? input[0].files[0].name : 'No document uploaded';
            }

            if (label && val && input.attr('type') !== 'hidden') {
                html += `
                <div class="rfp-summary-item">
                    <span class="rfp-summary-label">${label}</span>
                    <span class="rfp-summary-value">${val}</span>
                </div>`;
            }
        });
        jQuery('#rfp-summary-display').html(html || '<p style="text-align:center;">Information is missing.</p>');
    }

    /**
     * Surgical Navigation Trigger
     */
    function rfpTriggerStep(target) {
        const total = <?php echo $total_steps; ?>;
        const current = jQuery('.rfp-step-content.active');
        const currentNum = parseInt(current.attr('id').split('-')[2]);
        
        // 1. Validation Logic
        if (target > currentNum) {
            let valid = true;
            current.find('[required]').each(function() {
                if(!jQuery(this).val()) {
                    jQuery(this).css({ 'border-color': '#ef4444', 'background-color': '#fef2f2' });
                    valid = false;
                } else {
                    jQuery(this).css({ 'border-color': '', 'background-color': '' });
                }
            });
            if (!valid) return;
        }

        // 2. Build Summary logic
        if (target == total) rfpBuildSummary();

        // 3. Navigation Switch (Handled via rfp-core.js alias logic)
        if(typeof window.rfpMoveStep === 'function'){
             window.rfpMoveStep(null, target);
        } else {
            // Native fallback if core isn't ready
            jQuery('.rfp-step-content').hide().removeClass('active');
            jQuery('#rfp-step-' + target).fadeIn(400).addClass('active');
            
            const percent = ((target - 1) / (total - 1)) * 100;
            jQuery('#rfp-progress-fill').css('width', percent + '%');

            jQuery('.rfp-step-node').removeClass('active completed');
            for(let i=1; i<=total; i++) {
                if(i < target) jQuery('#rfp-node-' + i).addClass('completed');
                if(i == target) jQuery('#rfp-node-' + i).addClass('active');
            }
        }
    }

    function rfpHandleSubmit(e) {
        if(e) e.preventDefault();
        
        // Final sanity check before passing to rfp-core.js
        if(typeof window.rfp_execute_ajax_submit === 'function') {
            window.rfp_execute_ajax_submit();
        }
    }
</script>