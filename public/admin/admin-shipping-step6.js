(() => {
    let shippingDetails = {};

    function loadShippingDetails() {
        const form = document.querySelector('form');
        if (!form) return;
        const formData = new FormData(form);  
        shippingDetails = {
            package_type: formData.get('package_type'),
            package_number: formData.get('package_number'),
            length: formData.get('length'),
            width: formData.get('width'),
            height: formData.get('height'),
            weight: formData.get('weight'),
            package_description: formData.get('package_description')
        };
    }

    function validateShippingDetails() {
        const requiredFields = ['package_type', 'package_number', 'length', 'width', 'height', 'weight'];
        
        for (const field of requiredFields) {
            if (!shippingDetails[field]) {
                alert(`Please fill in ${field}`);
                return false;
            }
        }
        
        return true;
    }

    function updateShippingDetails() {
        const hiddenFields = {
            'shipping_company_id': window.selectedCompany?.id,
            'shipping_method': window.selectedMethod,
            'selected_receivers': JSON.stringify(window.selectedReceivers),
            'sender_name': window.userData?.name,
            'sender_phone': window.userData?.phone,
            'sender_email': window.userData?.email,
            'sender_address': window.userData?.address,
            'sender_city': window.userData?.city,
            'sender_postal_code': window.userData?.postal_code
        };

        Object.keys(hiddenFields).forEach(key => {
            const field = document.getElementById(key);
            if (field) {
                field.value = hiddenFields[key];
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadShippingDetails();
    });

    window.loadShippingDetails = loadShippingDetails;
    window.validateShippingDetails = validateShippingDetails;
    window.updateShippingDetails = updateShippingDetails;
})();
