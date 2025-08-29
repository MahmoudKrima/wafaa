(() => {
    let selectedMethod = null;

    function ensureGoToStep() {
        if (typeof window.goToStep === "function") return;
        window.currentStep = 1;
        window.goToStep = function (step) {
            document
                .querySelectorAll(".step-content")
                .forEach((c) => (c.style.display = "none"));
            const target = document.getElementById("step-" + step);
            if (target) target.style.display = "block";
            if (typeof window.updateShippingStepIndicator === "function")
                window.updateShippingStepIndicator(step);
            if (step === 2 && typeof showMethodSelection === "function")
                showMethodSelection();
            if (step === 3) {
                const hiddenMethod = document.getElementById("shipping_method");
                if (hiddenMethod && window.selectedMethod)
                    hiddenMethod.value = window.selectedMethod;
            }
            const btnPrev = document.getElementById("btn-prev");
            if (btnPrev)
                btnPrev.style.display = step > 1 ? "inline-block" : "none";
            window.currentStep = step;
        };
    }

    function showMethodSelection() {
        if (!window.selectedCompany) {
            const mo = document.getElementById("method-options");
            if (mo) mo.innerHTML = "";
            const btnNext = document.getElementById("btn-next");
            if (btnNext) {
                btnNext.disabled = true;
                btnNext.classList.add("btn-secondary");
                btnNext.classList.remove("btn-primary");
            }
            return;
        }

        const companyName = document.getElementById("selected-company-name");
        if (companyName)
            companyName.textContent =
                window.selectedCompany.displayNameEn ||
                window.selectedCompany.name ||
                "";

        const methodOptions = document.getElementById("method-options");
        if (!methodOptions) return;

        const shippingMethods = (
            window.selectedCompany.shippingMethods || []
        ).map((m) => (typeof m === "string" ? m : "").toLowerCase());

        let methodsHTML = "";

        if (shippingMethods.includes("local")) {
            methodsHTML += `
          <div class="col-lg-4 col-md-5 mb-3 mx-auto">
            <div class="card method-option h-100 card_step2" onclick="window.selectMethod(this, 'local')"
                 style="cursor:pointer;border:2px solid transparent;transition:all .3s ease">
              <div class="card-body text-center">
                <div class="mb-2" style="font-size:2rem">🏠</div>
                <h6 class="card-title">${
                    window.translations?.local || "Local"
                }</h6>
                <p class="card-text text-muted">${
                    window.translations?.local_delivery || "Local Delivery"
                }</p>
              </div>
            </div>
          </div>`;
        }
        if (shippingMethods.includes("international")) {
            methodsHTML += `
          <div class="col-lg-4 col-md-5 mb-3 mx-auto">
            <div class="card method-option h-100 card_step2" onclick="window.selectMethod(this, 'international')"
                 style="cursor:pointer;border:2px solid transparent;transition:all .3s ease">
              <div class="card-body text-center">
                <div class="mb-2" style="font-size:2rem">🌍</div>
                <h6 class="card-title">${
                    window.translations?.international || "International"
                }</h6>
                <p class="card-text text-muted">${
                    window.translations?.worldwide_shipping ||
                    "Worldwide Shipping"
                }</p>
              </div>
            </div>
          </div>`;
        }

        // IMPORTANT: no auto-select even if there’s only one method
        methodOptions.innerHTML = methodsHTML;

        const btnNext = document.getElementById("btn-next");
        if (btnNext) {
            const enable = !!window.selectedMethod;
            btnNext.disabled = !enable;
            btnNext.classList.toggle("btn-secondary", !enable);
            btnNext.classList.toggle("btn-primary", enable);
        }

        if (window.selectedMethod) {
            const card = methodOptions.querySelector(
                `[onclick*="'${window.selectedMethod}'"]`
            );
            if (card) {
                card.style.borderColor = "#F6950D";
                card.style.backgroundColor = "#f8f9fa";
            }
        }
    }

    function selectMethod(card, method) {
        document.querySelectorAll(".method-option").forEach((c) => {
            c.style.borderColor = "transparent";
            c.style.backgroundColor = "";
        });
        card.style.borderColor = "#F6950D";
        card.style.backgroundColor = "#f8f9fa";

        selectedMethod = method;
        window.selectedMethod = method;

        const methodInput = document.getElementById("shipping_method");
        if (methodInput) methodInput.value = method;

        const btnNext = document.getElementById("btn-next");
        if (btnNext) {
            btnNext.disabled = false;
            btnNext.classList.remove("btn-secondary");
            btnNext.classList.add("btn-primary");
        }

        document.dispatchEvent(
            new CustomEvent("shippingMethodSelected", { detail: { method } })
        );

        if (typeof window.updateShippingStepIndicator === "function")
            window.updateShippingStepIndicator(2);
        if (
            typeof window.currentStep !== "undefined" &&
            window.currentStep >= 4
        ) {
            if (typeof window.setupReceiverFormByShippingType === "function")
                window.setupReceiverFormByShippingType();
        }
    }

    function clearStepData(step) {
        if (step === 2) {
            selectedMethod = null;
            window.selectedMethod = null;
            const methodInput = document.getElementById("shipping_method");
            if (methodInput) methodInput.value = "";
            const btnNext = document.getElementById("btn-next");
            if (btnNext) {
                btnNext.disabled = true;
                btnNext.classList.add("btn-secondary");
                btnNext.classList.remove("btn-primary");
            }
        }
    }

    document.addEventListener("shippingCompanySelected", () => {
        clearStepData(2);
        if (window.currentStep === 2) showMethodSelection();
    });

    if (!window.goToStep) ensureGoToStep();
    document.addEventListener("DOMContentLoaded", () => {
        if (window.currentStep === 2) showMethodSelection();
    });

    window.showMethodSelection = showMethodSelection;
    window.selectMethod = selectMethod;
    window.clearStepData = clearStepData;
})();
