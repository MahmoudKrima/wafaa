(function () {
    let _submittingStep5 = false;
    const t = window.step5Translations || window.translations || {};

    function ensureDimensionDefaults() {
        const lengthField = document.getElementById("length");
        const widthField = document.getElementById("width");
        const heightField = document.getElementById("height");
        [lengthField, widthField, heightField].forEach((f) => {
            if (f && (!f.value || Number(f.value) <= 0)) f.value = "1";
        });
    }

    function showErrorStep5(message) {
        if (!_submittingStep5) return;
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

    function syncNextBtnStep5() {
        if (typeof window.hardEnableNext === "function") {
            window.hardEnableNext(window.validatePackageDetails());
        }
    }
    window.syncNextBtnStep5 = syncNextBtnStep5;


    window.populateShippingFormFields = function populateShippingFormFields() {
        setupPackageTypeHandling();

        ensureDimensionDefaults();

        const ids = [
            "package_type",
            "package_number",
            "weight",
            "length",
            "width",
            "height",
            "accept_terms",
            "package_description",
        ];
        ids.forEach((id) => {
            const el = document.getElementById(id);
            if (el && !el.dataset.boundStep5) {
                const evt = el.type === "checkbox" ? "change" : "input";
                el.addEventListener(evt, syncNextBtnStep5);
                el.dataset.boundStep5 = "1";
            }
        });

        const btnNext = document.getElementById("btn-next");
        if (btnNext && !btnNext.dataset.boundStep5Submit) {
            btnNext.addEventListener(
                "click",
                () => {
                    _submittingStep5 = true;
                    if (!window.validatePackageDetails(true)) {
                        _submittingStep5 = false;
                        return false;
                    }
                    setTimeout(() => {
                        _submittingStep5 = false;
                    }, 100);
                },
                true
            );
            btnNext.dataset.boundStep5Submit = "1";
        }

        syncNextBtnStep5();
    };

    function setupPackageTypeHandling() {
        const packageTypeSelect = document.getElementById("package_type");
        if (!packageTypeSelect) return;
        packageTypeSelect.required = true;
        if (["box", "document"].includes(packageTypeSelect.value)) {
            showDimensionsSection();
        } else {
            hideDimensionsSection(true);
        }
        if (!packageTypeSelect.dataset.bound) {
            packageTypeSelect.addEventListener("change", function () {
                if (["box", "document"].includes(this.value)) {
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

        ensureDimensionDefaults();
    }

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

    window.validatePackageDetails = function validatePackageDetails(showErrors = false) {
        const packageType = document.getElementById("package_type");
        const packageNumber = document.getElementById("package_number");
        const weight = document.getElementById("weight");
        const acceptTerms = document.getElementById("accept_terms");
        const packageDescription = document.getElementById(
            "package_description"
        );
        if (
            !packageType ||
            !packageNumber ||
            !weight ||
            !acceptTerms ||
            !packageDescription
        ) {
            return false;
        }

        if (!packageType.value) {
            if (showErrors || _submittingStep5) showErrorStep5(t.package_type_required || "Please select a package type (Boxes or Documents).");
            return false;
        }

        const num = Number(packageNumber.value);
        if (!packageNumber.value || isNaN(num) || num < 1) {
            if (showErrors || _submittingStep5) showErrorStep5(t.package_number_invalid);
            return false;
        }

        const w = Number(weight.value);
        if (!weight.value || isNaN(w) || w <= 0) {
            if (showErrors || _submittingStep5) showErrorStep5(t.weight_invalid);
            return false;
        }

        const length = document.getElementById("length");
        const width = document.getElementById("width");
        const height = document.getElementById("height");
        if (!length || !width || !height) {
            if (showErrors || _submittingStep5) showErrorStep5(t.dimensions_missing);
            return false;
        }

        const L = Number(length.value);
        const W = Number(width.value);
        const H = Number(height.value);

        if (!length.value || !width.value || !height.value) {
            if (showErrors || _submittingStep5) showErrorStep5(t.dimensions_required);
            return false;
        }
        if (isNaN(L) || isNaN(W) || isNaN(H) || L <= 0 || W <= 0 || H <= 0) {
            if (showErrors || _submittingStep5) showErrorStep5(t.dimensions_invalid);
            return false;
        }

        if (!acceptTerms.checked) {
            if (showErrors || _submittingStep5) showErrorStep5(t.accept_terms_required);
            return false;
        }

        if (
            !packageDescription.value ||
            packageDescription.value.trim() === ""
        ) {
            if (showErrors || _submittingStep5) showErrorStep5(
                t.package_description_required ||
                    "Package description is required"
            );
            return false;
        }

        return true;
    };
})();
