// Step 2: Method Selection
let selectedMethod = null;

function showMethodSelection() {
    if (!selectedCompany) return;

    const companyName = document.getElementById("selected-company-name");
    if (companyName) companyName.textContent = selectedCompany.name;

    const methodOptions = document.getElementById("method-options");

    let methodsHTML = "";

    if (selectedCompany.shippingMethods && selectedCompany.shippingMethods.includes("local")) {
        methodsHTML += '<div class="col-lg-6 col-md-6 mb-3">';
        methodsHTML += '<div class="card method-option h-100" onclick="selectMethod(this, \'local\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
        methodsHTML += '<div class="card-body text-center">';
        methodsHTML += '<div class="mb-2" style="font-size: 2rem;">🏠</div>';
        methodsHTML += '<h6 class="card-title">' + (translations?.local || "Local") + "</h6>";
        methodsHTML += '<p class="card-text text-muted">' + (translations?.local_delivery || "Local Delivery") + "</p>";
        methodsHTML += "</div></div></div>";
    }

    if (selectedCompany.shippingMethods && selectedCompany.shippingMethods.includes("international")) {
        methodsHTML += '<div class="col-lg-6 col-md-6 mb-3">';
        methodsHTML += '<div class="card method-option h-100" onclick="selectMethod(this, \'international\')" style="cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">';
        methodsHTML += '<div class="card-body text-center">';
        methodsHTML += '<div class="mb-2" style="font-size: 2rem;">🌍</div>';
        methodsHTML += '<h6 class="card-title">' + (translations?.international || "International") + "</h6>";
        methodsHTML += '<p class="card-text text-muted">' + (translations?.worldwide_shipping || "Worldwide Shipping") + "</p>";
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

    const btnNext = document.getElementById("btn-next");
    if (btnNext) btnNext.disabled = false;

    updateStepIndicator(2, true);
    
    if (currentStep >= 4) {
        setupReceiverFormByShippingType();
    }
}

function clearStepData(step) {
    switch (step) {
        case 2:
            selectedMethod = null;
            if (currentStep >= 4) {
                setupReceiverFormByShippingType();
            }
            break;
    }
}
