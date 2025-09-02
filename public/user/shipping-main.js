document.addEventListener("DOMContentLoaded", function () {
    if (typeof initShippingForm === 'function') {
        initShippingForm();
    } else {
        console.error('initShippingForm function not found');
    }
});
