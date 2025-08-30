(() => {
    let paymentDetails = {};
    let summaryData = {};

    function loadPaymentDetails() {
        const form = document.querySelector('form');
        if (!form) return;
        const formData = new FormData(form);
        paymentDetails = {
            payment_method: formData.get('payment_method'),
            shipping_price_per_receiver: formData.get('shipping_price_per_receiver'),
            extra_weight_per_receiver: formData.get('extra_weight_per_receiver'),
            cod_price_per_receiver: formData.get('cod_price_per_receiver'),
            total_per_receiver: formData.get('total_per_receiver'),
            total_amount: formData.get('total_amount')
        };
    }

    function displaySummary() {
        const summaryContainer = document.getElementById('shipping-summary');
        if (!summaryContainer) return;

        const summaryHtml = `
            <div class="card">
                <div class="card-header">
                    <h5>Shipping Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Selected Company</h6>
                            <p>${window.selectedCompany?.name || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Shipping Method</h6>
                            <p>${window.selectedMethod || 'N/A'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>User</h6>
                            <p>${window.userData?.name || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Receivers</h6>
                            <p>${window.selectedReceivers?.length || 0}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Package Type</h6>
                            <p>${window.shippingDetails?.package_type || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Weight</h6>
                            <p>${window.shippingDetails?.weight || 'N/A'} kg</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        summaryContainer.innerHTML = summaryHtml;
    }

    function validateStep() {
        if (!paymentDetails.payment_method) {
            alert('Please select a payment method');
            return false;
        }
        
        return true;
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadPaymentDetails();
        
        displaySummary();
    });

    window.loadPaymentDetails = loadPaymentDetails;
    window.displaySummary = displaySummary;
    window.validateStep = validateStep;
})();
