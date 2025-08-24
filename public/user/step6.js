// Step 6: Payment Details
function setupPaymentDetails() {
    // Update COD display if applicable
    const cashOnDelivery = document.getElementById("cash_on_delivery");
    if (cashOnDelivery && cashOnDelivery.checked) {
        updateCodDisplay();
    }
}
