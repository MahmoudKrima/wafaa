function populateShippingFormFields() {
    // Implementation for populating shipping form fields
    console.log("Populating shipping form fields for step 5");
    
    // Setup package type handling
    setupPackageTypeHandling();
}

function setupPackageTypeHandling() {
    const packageTypeSelect = document.getElementById("package_type");
    if (!packageTypeSelect) return;

    packageTypeSelect.addEventListener("change", function () {
        const selectedValue = this.value;
        if (selectedValue === "boxes") {
            showDimensionsSection();
        } else if (selectedValue === "documents") {
            hideDimensionsSection();
        }
    });
}

function showDimensionsSection() {
    const dimensionsSection = document.getElementById("dimensions_section");
    if (dimensionsSection) {
        dimensionsSection.style.display = "block";
        const lengthField = document.getElementById("length");
        const widthField = document.getElementById("width");
        const heightField = document.getElementById("height");

        if (lengthField) lengthField.required = true;
        if (widthField) widthField.required = true;
        if (heightField) heightField.required = true;
    }
}

function hideDimensionsSection() {
    const dimensionsSection = document.getElementById("dimensions_section");
    if (dimensionsSection) {
        dimensionsSection.style.display = "none";
        const lengthField = document.getElementById("length");
        const widthField = document.getElementById("width");
        const heightField = document.getElementById("height");

        if (lengthField) lengthField.required = false;
        if (widthField) widthField.required = false;
        if (heightField) heightField.required = false;
    }
}

function validatePackageDetails() {
    const packageType = document.getElementById("package_type");
    const packageNumber = document.getElementById("package_number");
    const weight = document.getElementById("weight");

    if (!packageType) {
        showError("Package type element not found");
        return false;
    }

    if (!packageNumber) {
        showError("Package number element not found");
        return false;
    }

    if (!weight) {
        showError("Weight element not found");
        return false;
    }

    if (!packageType.value) {
        showError("Please select a package type (Boxes or Documents)");
        return false;
    }

    if (!packageNumber.value || packageNumber.value < 1) {
        showError("Please enter a valid number (minimum 1)");
        return false;
    }

    if (!weight.value || weight.value <= 0) {
        showError("Please enter a valid weight in kg");
        return false;
    }

    if (packageType.value === "boxes") {
        const length = document.getElementById("length");
        const width = document.getElementById("width");
        const height = document.getElementById("height");

        if (!length || !width || !height) {
            showError("Dimension elements not found");
            return false;
        }

        if (!length.value || !width.value || !height.value) {
            showError("Please enter dimensions (length, width, height) for the boxes");
            return false;
        }

        if (length.value <= 0 || width.value <= 0 || height.value <= 0) {
            showError("Dimensions must be greater than 0");
            return false;
        }
    }

    const acceptTerms = document.getElementById("accept_terms");
    if (!acceptTerms || !acceptTerms.checked) {
        showError("Please accept the terms and conditions to continue");
        return false;
    }

    return true;
}

function showError(message) {
    // Try to use toastr if available
    if (typeof toastr !== "undefined") {
        toastr.error(message);
    } else {
        // Fallback: show error below the form
        const errorContainer = document.getElementById("receiver-error-msg");
        if (errorContainer) {
            errorContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> ${message}
                </div>
            `;
            errorContainer.style.display = "block";

            // Auto-hide after 5 seconds
            setTimeout(() => {
                errorContainer.style.display = "none";
            }, 5000);
        } else {
            alert("Error: " + message);
        }
    }
}
