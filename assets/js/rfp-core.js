/**
 * Registry Form Pro - Frontend Core Logic
 * Developed by: James P. Friday
 */

jQuery(document).ready(function($) {
    const form = $('#rfp-main-form');
    const feedback = $('#rfp-feedback');
    let isSubmitting = false;

    /**
     * 1. GLOBAL NAVIGATION ENGINE
     * Handles step transitions, validation, and summary generation
     */
    window.rfpTriggerStep = function(targetStep) {
        const totalSteps = $('.rfp-step-node').length;
        const currentStepEl = $('.rfp-step-content.active');
        const currentStepNum = parseInt(currentStepEl.attr('id').split('-')[2]);
        const directionForward = targetStep > currentStepNum;

        // Validation: Only when moving FORWARD
        if(directionForward) {
            let stepValid = true;
            currentStepEl.find('[required]').each(function() {
                if(!$(this).val()) {
                    $(this).css({ 'border-color': '#ef4444', 'background-color': '#fef2f2' });
                    stepValid = false;
                } else {
                    $(this).css({ 'border-color': '', 'background-color': '' });
                }
            });

            if(!stepValid) {
                feedback.html('<p style="color:#ef4444; font-weight:700; text-align:center;">Please complete all required fields.</p>');
                return false;
            }
        }

        // Clear feedback
        feedback.empty();

        // Trigger Summary Build if entering last step
        if (targetStep === totalSteps && typeof window.rfpBuildSummary === 'function') {
            window.rfpBuildSummary();
        }

        // Switch Steps with Animation
        $('.rfp-step-content').css({ 'opacity': '0', 'transform': 'translateY(10px)' });
        
        setTimeout(() => {
            $('.rfp-step-content').removeClass('active').hide();
            const next = $('#rfp-step-' + targetStep);
            next.show().addClass('active');
            
            setTimeout(() => {
                next.css({ 'opacity': '1', 'transform': 'translateY(0)' });
            }, 50);
        }, 200);

        // Update Progress Bar
        const fillWidth = totalSteps > 1 ? ((targetStep - 1) / (totalSteps - 1)) * 100 : 100;
        $('#rfp-progress-fill').css('width', fillWidth + '%');

        // Update Nodes
        $('.rfp-step-node').each(function(index) {
            const stepNum = index + 1;
            $(this).removeClass('active completed');
            if (stepNum < targetStep) $(this).addClass('completed');
            if (stepNum === targetStep) $(this).addClass('active');
        });

        // Scroll to container
        $('html, body').animate({ scrollTop: $(".rfp-form-container").offset().top - 80 }, 500);
    };

    // Alias for legacy calls
    window.rfpMoveStep = function(e, target) {
        if(e && typeof e.preventDefault === 'function') e.preventDefault();
        window.rfpTriggerStep(target);
    };

    /**
     * 2. FORM SUBMISSION INTERCEPTOR
     * Intercepts based on Admin submission type (Free vs. Paid)
     */
    form.on('submit', function(e) {
        e.preventDefault();

        // Final validation check for required fields on the summary step (if any)
        let finalValid = true;
        form.find('.rfp-step-content.active [required]').each(function() {
            if(!$(this).val()) {
                $(this).css({ 'border-color': '#ef4444', 'background-color': '#fef2f2' });
                finalValid = false;
            }
        });

        if(!finalValid) return;

        // Check Admin Submission Type setting passed from PHP
        if(rfp_data.gateway === 'paystack') {
            if(typeof window.rfp_init_paystack === 'function') {
                window.rfp_init_paystack(); 
            } else {
                console.error("Paystack module not found. Falling back to direct submit.");
                window.rfp_execute_ajax_submit();
            }
        } else {
            window.rfp_execute_ajax_submit();
        }
    });

    /**
     * 3. THE AJAX ENGINE ROOM
     * FIXED: Only targets .rfp-btn-label-text to prevent double-texting
     */
    window.rfp_execute_ajax_submit = function() {
        if(isSubmitting) return;
        isSubmitting = true;

        const formData = new FormData(form[0]);
        formData.append('action', 'rfp_submit_action');
        formData.append('security', rfp_data.nonce); 

        const btn = $('.rfp-btn-submit');
        const btnLabel = btn.find('.rfp-btn-label-text'); 
        
        // UI State: Loading
        btn.prop('disabled', true).css('opacity', '0.7');
        
        // Surgical fix: Wipe and set single status to avoid double-text
        if(btnLabel.length > 0) {
            btnLabel.text('Processing Submission...');
        } else {
            btn.text('Processing Submission...');
        }
        
        feedback.html('<p style="color: #3b82f6; font-weight: 700; text-align:center;">Uploading documents, please wait...</p>');

        $.ajax({
            url: rfp_data.ajax_url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                isSubmitting = false;
                if(response.success) {
                    feedback.html('<div class="rfp-success-msg" style="color:#22c55e; text-align:center; padding:30px; border:2px solid #22c55e; border-radius:24px; background:#f0fdf4;"><strong>' + response.data.message + '</strong></div>');
                    form.fadeOut(600); 
                    $('html, body').animate({ scrollTop: feedback.offset().top - 100 }, 500);
                } else {
                    feedback.html('<p style="color:#ef4444; font-weight:700; text-align:center;">❌ ' + (response.data.message || 'Error processing request.') + '</p>');
                    btn.prop('disabled', false).css('opacity', '1');
                    if(btnLabel.length > 0) btnLabel.text('Try Again');
                }
            },
            error: function() {
                isSubmitting = false;
                feedback.html('<p style="color:#ef4444; font-weight:700; text-align:center;">❌ Connection lost. Please check your internet.</p>');
                btn.prop('disabled', false).css('opacity', '1');
                if(btnLabel.length > 0) btnLabel.text('Retry Submission');
            }
        });
    };
});