(() => {
    let selectedMethod = null;

    function selectMethod(method) {
        selectedMethod = method;
        updateMethodDisplay(method);
        window.enableNext();
    }

    function updateMethodDisplay(method) {
        const methodElement = document.getElementById('selected-method');
        if (methodElement) {
            methodElement.textContent = method;
        }
    }

    function validateStep() {
        if (!selectedMethod) {
            alert('Please select a shipping method');
            return false;
        }
        
        return true;
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadMethodOptions();
    });

    function loadMethodOptions() {
        console.log('Loading method options...');
    }
    window.selectMethod = selectMethod;
    window.validateStep = validateStep;
})();
