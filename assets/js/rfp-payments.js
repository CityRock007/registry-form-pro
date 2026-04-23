/**
 * Registry Form Pro - Payment Integration
 */
function rfp_init_paystack() {
    const form = jQuery('#rfp-main-form');
    const userEmail = form.find('input[type="email"]').val();
    const amount = parseInt(rfp_data.reg_fee) * 100; // Convert to Kobo

    if (!userEmail) {
        alert("Please provide a valid email to proceed with payment.");
        return;
    }

    let handler = PaystackPop.setup({
        key: rfp_data.paystack_pk, // Pulls from Customizer
        email: userEmail,
        amount: amount,
        currency: "NGN",
        callback: function(response) {
            // Payment Successful!
            jQuery('#rfp-feedback').html('<span style="color:blue;">Payment verified: ' + response.reference + '. Finalizing submission...</span>');
            
            // Now trigger the actual form submission
            window.rfp_execute_ajax_submit();
        },
        onClose: function() {
            alert('Transaction was not completed.');
            location.reload();
        }
    });

    handler.openIframe();
}