/**
 * Registry Form Pro - Unified Dashboard & Grid Builder
 * Developed by: James P. Friday
 */

jQuery(document).ready(function($) {
    const canvasList = $("#rfp-sortable-list");

    // 1. Tab Navigation Logic
    $('.rfp-tab-btn').on('click', function() {
        $('.rfp-tab-btn, .rfp-tab-content').removeClass('active');
        $(this).addClass('active');
        const target = $(this).data('tab');
        $('#tab-' + target).addClass('active');
    });

    // 2. Surgical Slugify for technical names
    function rfpSlugify(text) {
        return text.toString().toLowerCase().trim()
            .replace(/\s+/g, '_')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '_');
    }

    // 3. Check for Empty Canvas
    function checkEmpty() {
        if (canvasList.children('li.rfp-field-item').length === 0) {
            if ($(".rfp-empty-canvas").length === 0) {
                canvasList.append(`<div class="rfp-empty-canvas" style="text-align:center; padding:50px; color:#cbd5e1; border:2px dashed #e2e8f0; border-radius:20px;">
                    <span class="dashicons dashicons-plus-alt" style="font-size:40px; width:40px; height:40px;"></span>
                    <p>Click a field to start building.</p></div>`);
            }
        } else {
            $(".rfp-empty-canvas").remove();
        }
    }

    // 4. Initialize Sortable
    canvasList.sortable({
        placeholder: "rfp-sortable-placeholder",
        forcePlaceholderSize: true,
        handle: ".rfp-drag-handle",
        cursor: "move",
        stop: function() { checkEmpty(); }
    });

    // 5. FIELD GENERATOR (With Grid & Step Support)
    function rfpAppendField(type, label = '', name = '', required = false, step = '1', width = '100') {
        $(".rfp-empty-canvas").remove();

        const stepTitles = $('#rfp-steps-input').val() || 'Step 1';
        const stepsArray = stepTitles.split(',').map(s => s.trim());
        let stepOptions = '';
        stepsArray.forEach((title, index) => {
            const val = index + 1;
            stepOptions += `<option value="${val}" ${step == val ? 'selected' : ''}>Move to: ${title}</option>`;
        });

        const fieldHtml = `
            <li class="rfp-field-item rfp-new-field" data-type="${type}" style="border-left: 5px solid #3b82f6; background: #fff; margin-bottom: 15px; border-radius: 12px; display: flex; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div class="rfp-drag-handle" style="padding: 20px; cursor: grab; border-right: 1px solid #f1f5f9; display: flex; align-items: center; color: #cbd5e1;">
                    <span class="dashicons dashicons-menu"></span>
                </div>
                
                <div class="rfp-field-main" style="flex: 1; padding: 15px 20px;">
                    <div class="rfp-field-header" style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                        <strong style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">${type} Field</strong>
                        <select class="field-width" style="font-size: 10px; height: 22px; border-radius: 4px; border: 1px solid #e2e8f0;">
                            <option value="100" ${width == '100' ? 'selected' : ''}>Full Width</option>
                            <option value="50" ${width == '50' ? 'selected' : ''}>1/2 Width</option>
                        </select>
                    </div>
                    
                    <input type="text" class="field-label" placeholder="Enter Question / Label" value="${label}" style="width: 100%; border: none; border-bottom: 2px solid #f1f5f9; padding: 8px 0; font-size: 16px; font-weight: 600; outline: none;">
                    <input type="hidden" class="field-name" value="${name}">
                    
                    <div style="display: flex; align-items: center; gap: 15px; margin-top: 12px;">
                        <select class="field-step" style="background: #f0f9ff; border: 1px solid #3b82f6; color: #2563eb; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer;">
                            ${stepOptions}
                        </select>
                        <label style="font-size: 12px; color: #64748b; font-weight: 600; cursor: pointer;">
                            <input type="checkbox" class="field-required" ${required ? 'checked' : ''}> Mandatory?
                        </label>
                    </div>
                </div>

                <div class="rfp-field-actions" style="padding: 15px; display: flex; align-items: center;">
                    <button type="button" class="rfp-remove-field" style="background:#fff1f2; color:#e11d48; border:none; width:32px; height:32px; border-radius:8px; cursor:pointer;">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </div>
            </li>`;

        const $newField = $(fieldHtml);
        canvasList.append($newField);

        $newField.find('.field-label').on('input', function() {
            $(this).siblings('.field-name').val(rfpSlugify($(this).val()) || 'field_' + Date.now());
        });
    }

    // 6. INITIAL LOAD (Unified Config)
    function rfpLoadData() {
        $.post(ajaxurl, { action: 'rfp_load_builder_data', nonce: rfp_builder_data.nonce }, function(res) {
            if (res.success && res.data) {
                const config = res.data; // This is the full object
                if (config.fields) {
                    config.fields.forEach(f => rfpAppendField(f.type, f.label, f.name, f.required, f.step, f.width));
                }
            }
            checkEmpty();
        });
    }
    rfpLoadData();

    // 7. TOOLBAR CLICK
    $(".rfp-draggable").on("click", function() {
        rfpAppendField($(this).data("type"));
    });

    // 8. REMOVE
    $(document).on("click", ".rfp-remove-field", function() {
        $(this).closest('.rfp-field-item').remove();
        checkEmpty();
    });

    // 9. UNIFIED SAVE (Fields + Steps + Emails)
    $("#rfp-save-form").on("click", function() {
        const $btn = $(this);
        let fields = [];
        
        $btn.text('Saving Config...');

        $("#rfp-sortable-list li.rfp-field-item").each(function() {
            fields.push({
                type: $(this).data("type"),
                label: $(this).find(".field-label").val(),
                name: $(this).find(".field-name").val() || 'field_' + Date.now(),
                step: $(this).find(".field-step").val(),
                width: $(this).find(".field-width").val(),
                required: $(this).find(".field-required").is(':checked')
            });
        });

        const fullConfig = {
            fields: fields,
            steps: $('#rfp-steps-input').val(),
            emails: $('#rfp-admin-emails').val()
        };

        $.post(ajaxurl, {
            action: 'rfp_save_builder_data',
            nonce: rfp_builder_data.nonce,
            payload: JSON.stringify(fullConfig)
        }, function() {
            $btn.text('All Changes Saved!');
            // Sync the hidden field titles bridge
            $('#rfp_step_titles_data').val(fullConfig.steps);
            setTimeout(() => $btn.text('Save All Changes'), 2000);
        });
    });
});