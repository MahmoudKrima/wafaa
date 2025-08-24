// Main Shipping Application
// This file provides the main application initialization

// Main application initialization
document.addEventListener("DOMContentLoaded", function () {
    // Initialize the shipping application
    if (typeof initShippingForm === 'function') {
        initShippingForm();
    } else {
        console.error('initShippingForm function not found');
    }
});
