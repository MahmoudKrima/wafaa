// ---------- STEP 5: Package details ----------

(function () {
    // internal flag: only show errors when user is actually trying to proceed
    let _submittingStep5 = false;

    // convenience: safe error display (toast/toastr/inline) but only when submitting
    function showErrorStep5(message) {
        if (!_submittingStep5) return; // stay silent during passive validation
        if (typeof toastr !== "undefined") {
            toastr.error(message);
            return;
        }
        const errorContainer = document.getElementById("receiver-error-msg");
        if (errorContainer) {
            errorContainer.innerHTML = `
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> ${message}
          </div>
        `;
            errorContainer.style.display = "block";
            setTimeout(() => (errorContainer.style.display = "none"), 5000);
        } else {
            alert("Error: " + message);
        }
    }

    // keep Next button state in sync while typing/selecting
    function syncNextBtnStep5() {
        if (typeof window.hardEnableNext === "function") {
            window.hardEnableNext(window.validatePackageDetails());
        }
    }

    // called by your step router when step 5 is shown
    window.populateShippingFormFields = function populateShippingFormFields() {
        // set up the package type show/hide rules & input bindings
        setupPackageTypeHandling();

        // Bind inputs to re-validate and update Next button as user edits
        const ids = [
            "package_type",
            "package_number",
            "weight",
            "length",
            "width",
            "height",
            "accept_terms",
        ];
        ids.forEach((id) => {
            const el = document.getElementById(id);
            if (el && !el.dataset.boundStep5) {
                const evt = el.type === "checkbox" ? "change" : "input";
                el.addEventListener(evt, syncNextBtnStep5);
                el.dataset.boundStep5 = "1";
            }
        });

        // Ensure we capture clicks on Next BEFORE the app handler, so we can show errors only then
        const btnNext = document.getElementById("btn-next");
        if (btnNext && !btnNext.dataset.boundStep5Submit) {
            btnNext.addEventListener(
                "click",
                () => {
                    // mark an attempted submission for this click cycle
                    _submittingStep5 = true;
                    // after the current tick, reset so passive validations stay silent
                    setTimeout(() => {
                        _submittingStep5 = false;
                    }, 0);
                },
                true // capture so it runs before the main click handler
            );
            btnNext.dataset.boundStep5Submit = "1";
        }

        // Do an initial passive validation to set Next button correctly (no errors shown)
        syncNextBtnStep5();
    };

    function setupPackageTypeHandling() {
        const packageTypeSelect = document.getElementById("package_type");
        if (!packageTypeSelect) return;

        // initialize dimensions section based on current value
        if (packageTypeSelect.value === "box") {
            showDimensionsSection();
        } else {
            hideDimensionsSection(true);
        }

        if (!packageTypeSelect.dataset.bound) {
            packageTypeSelect.addEventListener("change", function () {
                if (this.value === "box") {
                    showDimensionsSection();
                } else {
                    hideDimensionsSection(true);
                }
                syncNextBtnStep5();
            });
            packageTypeSelect.dataset.bound = "1";
        }
    }

    function showDimensionsSection() {
        const section = document.getElementById("dimensions_section");
        if (!section) return;
        section.style.display = "block";

        const lengthField = document.getElementById("length");
        const widthField = document.getElementById("width");
        const heightField = document.getElementById("height");

        if (lengthField) lengthField.required = true;
        if (widthField) widthField.required = true;
        if (heightField) heightField.required = true;
    }

    // clearValues=true will also wipe any old values when hiding
    function hideDimensionsSection(clearValues = false) {
        const section = document.getElementById("dimensions_section");
        if (!section) return;
        section.style.display = "none";

        const lengthField = document.getElementById("length");
        const widthField = document.getElementById("width");
        const heightField = document.getElementById("height");

        if (lengthField) {
            lengthField.required = false;
            if (clearValues) lengthField.value = "";
        }
        if (widthField) {
            widthField.required = false;
            if (clearValues) widthField.value = "";
        }
        if (heightField) {
            heightField.required = false;
            if (clearValues) heightField.value = "";
        }
    }

    window.validatePackageDetails = function validatePackageDetails() {
        const packageType = document.getElementById("package_type");
        const packageNumber = document.getElementById("package_number");
        const weight = document.getElementById("weight");
        const acceptTerms = document.getElementById("accept_terms");
        if (!packageType || !packageNumber || !weight || !acceptTerms) {
            return false;
        }

        // type required
        if (!packageType.value) {
            showErrorStep5("Please select a package type (Boxes or Documents)");
            return false;
        }

        // number required (>=1)
        const num = Number(packageNumber.value);
        if (!packageNumber.value || isNaN(num) || num < 1) {
            showErrorStep5(
                "Please enter a valid number of packages (minimum 1)"
            );
            return false;
        }

        const w = Number(weight.value);
        if (!weight.value || isNaN(w) || w <= 0) {
            showErrorStep5("Please enter a valid weight in kg");
            return false;
        }

        // dimensions only when boxes
        if (packageType.value === "box") {
            const length = document.getElementById("length");
            const width = document.getElementById("width");
            const height = document.getElementById("height");

            if (!length || !width || !height) {
                showErrorStep5("Dimension fields are missing");
                return false;
            }

            const L = Number(length.value);
            const W = Number(width.value);
            const H = Number(height.value);

            if (!length.value || !width.value || !height.value) {
                showErrorStep5(
                    "Please enter dimensions (length, width, height) for the boxes"
                );
                return false;
            }
            if (
                isNaN(L) ||
                isNaN(W) ||
                isNaN(H) ||
                L <= 0 ||
                W <= 0 ||
                H <= 0
            ) {
                showErrorStep5(
                    "Dimensions must be valid numbers greater than 0"
                );
                return false;
            }
        }

        // terms
        if (!acceptTerms.checked) {
            showErrorStep5(
                "Please accept the terms and conditions to continue"
            );
            return false;
        }
        return true;
    };
})();
