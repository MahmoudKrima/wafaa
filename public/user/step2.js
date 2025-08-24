// Step 2: Method Selection
let selectedMethod = null;

function showMethodSelection() {
    if (!window.selectedCompany) return;

    const companyName = document.getElementById("selected-company-name");
    if (companyName) companyName.textContent = window.selectedCompany.name;

    const methodOptions = document.getElementById("method-options");

    let methodsHTML = "";

    if (window.selectedCompany.shippingMethods && window.selectedCompany.shippingMethods.includes("local")) {
        methodsHTML += '<div class="col-lg-6 col-md-6 mb-3">';
        methodsHTML += '<div class="card method-option h-100" onclick="selectMethod(this, \'local\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
        methodsHTML += '<div class="card-body text-center">';
        methodsHTML += '<div class="mb-2" style="font-size: 2rem;">🏠</div>';
        methodsHTML += '<h6 class="card-title">' + (window.translations?.local || "Local") + "</h6>";
        methodsHTML += '<p class="card-text text-muted">' + (window.translations?.local_delivery || "Local Delivery") + "</p>";
        methodsHTML += "</div></div></div>";
    }

    if (window.selectedCompany.shippingMethods && window.selectedCompany.shippingMethods.includes("international")) {
        methodsHTML += '<div class="col-lg-6 col-md-6 mb-3">';
        methodsHTML += '<div class="card method-option h-100" onclick="selectMethod(this, \'international\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
        methodsHTML += '<div class="card-body text-center">';
        methodsHTML += '<div class="mb-2" style="font-size: 2rem;">🌍</div>';
        methodsHTML += '<h6 class="card-title">' + (window.translations?.international || "International") + "</h6>";
        methodsHTML += '<p class="card-text text-muted">' + (window.translations?.worldwide_shipping || "Worldwide Shipping") + "</p>";
        methodsHTML += "</div></div></div>";
    }

    if (methodOptions) methodOptions.innerHTML = methodsHTML;
}

function selectMethod(card, method) {
    document.querySelectorAll(".method-option").forEach((c) => {
        c.style.borderColor = "transparent";
        c.style.backgroundColor = "";
    });

    card.style.borderColor = "#007bff";
    card.style.backgroundColor = "#f8f9fa";

    selectedMethod = method;
    window.selectedMethod = method; // Make it globally accessible

    const btnNext = document.getElementById("btn-next");
    if (btnNext) btnNext.disabled = false;

    if (typeof window.updateStepIndicator === 'function') {
        window.updateStepIndicator(2, true);
    }
    
    if (typeof window.currentStep !== 'undefined' && window.currentStep >= 4) {
        if (typeof window.setupReceiverFormByShippingType === 'function') {
            window.setupReceiverFormByShippingType();
        }
    }
}

function clearStepData(step) {
    switch (step) {
        case 2:
            selectedMethod = null;
            window.selectedMethod = null;
            if (typeof window.currentStep !== 'undefined' && window.currentStep >= 4) {
                if (typeof window.setupReceiverFormByShippingType === 'function') {
                    window.setupReceiverFormByShippingType();
                }
            }
            break;
    }
}

// Make functions globally accessible immediately
window.showMethodSelection = showMethodSelection;
window.selectMethod = selectMethod;
window.clearStepData = clearStepData;

// Debug logging
console.log('Step2.js loaded successfully');
console.log('Global functions exported:', {
    showMethodSelection: typeof window.showMethodSelection,
    selectMethod: typeof window.selectMethod,
    clearStepData: typeof window.clearStepData
});

// Also add a DOM ready check to ensure the script is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('Step2.js DOM ready check - functions available:', {
        showMethodSelection: typeof window.showMethodSelection,
        selectMethod: typeof window.selectMethod,
        clearStepData: typeof window.clearStepData
    });
});

// Additional immediate availability check
console.log('Step2.js functions immediately available:', {
    showMethodSelection: typeof showMethodSelection,
    selectMethod: typeof selectMethod,
    clearStepData: typeof clearStepData
});
