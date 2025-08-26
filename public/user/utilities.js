let currentStep = 1;

function updateStepIndicator(step) {
    const steps = document.querySelectorAll(".step");
    steps.forEach((s, i) => {
        const bubble = s.querySelector(".step-number");
        if (!bubble) return;
        bubble.classList.remove(
            "is-current",
            "is-done",
            "bg-primary",
            "bg-secondary"
        );
        if (i + 1 < step) bubble.classList.add("is-done", "bg-primary");
        else if (i + 1 === step)
            bubble.classList.add("is-current", "bg-primary");
        else bubble.classList.add("bg-secondary");
    });
}

function validateStep(step) {
    switch (step) {
        case 1:
            return !!window.selectedCompany;
        case 2:
            return !!window.selectedMethod;
        case 3:
            return typeof window.validateStep3Form === "function"
                ? window.validateStep3Form()
                : true;
        case 4:
            return typeof window.canProceedToNextStep === "function"
                ? window.canProceedToNextStep()
                : true;
        case 5:
            return typeof window.validatePackageDetails === "function"
                ? window.validatePackageDetails()
                : true;
        default:
            return true;
    }
}

/* NEW: one place that REALLY enables/disables the Next button for all themes */
function hardEnableNext(ok) {
    const btnNext = document.getElementById("btn-next");
    if (!btnNext) return;

    if (ok) {
        btnNext.disabled = false;
        btnNext.removeAttribute("disabled");
        btnNext.classList.remove("disabled", "btn-secondary");
        btnNext.classList.add("btn-primary");
        btnNext.setAttribute("aria-disabled", "false");
        btnNext.style.pointerEvents = "auto";
        btnNext.style.opacity = "";
    } else {
        btnNext.disabled = true;
        btnNext.setAttribute("disabled", "disabled");
        btnNext.classList.add("disabled", "btn-secondary");
        btnNext.classList.remove("btn-primary");
        btnNext.setAttribute("aria-disabled", "true");
        btnNext.style.pointerEvents = "";
        btnNext.style.opacity = "";
    }
}

function handleNextStep() {
    if (!validateStep(currentStep)) return;
    currentStep += 1;
    showStep(currentStep);
}

function handlePrevStep() {
    if (currentStep > 1) {
        currentStep -= 1;
        showStep(currentStep);
    }
}

function showStep(step) {
    document
        .querySelectorAll(".step-content")
        .forEach((s) => (s.style.display = "none"));
    const el = document.getElementById(`step-${step}`);
    if (el) el.style.display = "block";

    updateStepIndicator(step);

    const btnPrev = document.getElementById("btn-prev");
    const btnNext = document.getElementById("btn-next");

    if (btnPrev) btnPrev.style.display = step === 1 ? "none" : "inline-block";
    if (btnNext) {
        btnNext.style.display = step === 7 ? "none" : "inline-block";
        hardEnableNext(validateStep(step)); // <- use strong enabler every time step changes
    }

    if (step === 2 && typeof window.showMethodSelection === "function")
        window.showMethodSelection();

    if (step === 3) {
        if (typeof window.setupLocationFields === "function")
            window.setupLocationFields();
        if (typeof window.handleCompanyRequirements === "function")
            window.handleCompanyRequirements();
        const inputs = document.querySelectorAll(
            "#step-3 input, #step-3 select, #step-3 textarea"
        );
        inputs.forEach((inp) => {
            if (!inp.dataset.boundStep3) {
                inp.addEventListener("input", () => {
                    hardEnableNext(
                        typeof window.validateStep3Form === "function"
                            ? window.validateStep3Form()
                            : true
                    );
                });
                inp.dataset.boundStep3 = "1";
            }
        });
    }

    if (step === 4) {
        if (typeof window.loadReceivers === "function") window.loadReceivers();
        if (typeof window.setupReceiverFormByShippingType === "function")
            window.setupReceiverFormByShippingType();
        hardEnableNext(validateStep(4));
    }

    if (step === 5 && typeof window.populateShippingFormFields === "function")
        window.populateShippingFormFields();
    if (step === 6 && typeof window.setupPaymentDetails === "function")
        window.setupPaymentDetails();
    if (step === 7 && typeof window.showFinalSummary === "function")
        window.showFinalSummary();
}

/* NEW: keep Next in sync when selections change */
document.addEventListener("shippingCompanySelected", () => {
    if (currentStep === 1) hardEnableNext(true);
});
document.addEventListener("shippingMethodSelected", () => {
    if (currentStep === 2) hardEnableNext(true);
});
document.addEventListener("receiversChanged", () => {
    if (currentStep === 4) hardEnableNext(validateStep(4));
});

function setupReceiverTypeHandling() {
    const existingReceiverRadio = document.getElementById("existing_receiver");
    const newReceiverRadio = document.getElementById("new_receiver");
    const existingSection = document.getElementById(
        "existing_receiver_section"
    );
    const newSection = document.getElementById("new_receiver_section");

    if (existingSection) existingSection.style.display = "none";
    if (newSection) newSection.style.display = "none";

    if (existingReceiverRadio && !existingReceiverRadio.dataset.bound) {
        existingReceiverRadio.addEventListener("change", function () {
            if (this.checked) {
                if (existingSection) existingSection.style.display = "block";
                if (newSection) newSection.style.display = "none";
                if (typeof window.clearReceiverForm === "function")
                    window.clearReceiverForm();
                if (typeof window.showMultipleReceiverControls === "function")
                    window.showMultipleReceiverControls();
                if (
                    typeof window.ensureReceiverStateFieldVisible === "function"
                )
                    window.ensureReceiverStateFieldVisible();
            }
        });
        existingReceiverRadio.dataset.bound = "1";
    }

    if (newReceiverRadio && !newReceiverRadio.dataset.bound) {
        newReceiverRadio.addEventListener("change", function () {
            if (this.checked) {
                if (existingSection) existingSection.style.display = "none";
                if (newSection) newSection.style.display = "block";
                if (typeof window.clearReceiverForm === "function")
                    window.clearReceiverForm();
                if (typeof window.showMultipleReceiverControls === "function")
                    window.showMultipleReceiverControls();
                if (
                    typeof window.ensureReceiverStateFieldVisible === "function"
                )
                    window.ensureReceiverStateFieldVisible();
                if (
                    typeof window.setupReceiverFormByShippingType === "function"
                )
                    window.setupReceiverFormByShippingType();
            }
        });
        newReceiverRadio.dataset.bound = "1";
    }

    const receiverSelect = document.getElementById("receiver_select");
    if (receiverSelect && !receiverSelect.dataset.bound) {
        receiverSelect.addEventListener("change", function () {
            const selectedReceiverId = this.value;
            if (selectedReceiverId) {
                if (typeof window.populateReceiverForm === "function")
                    window.populateReceiverForm(selectedReceiverId);
                if (newSection) newSection.style.display = "block";
            } else {
                if (typeof window.clearReceiverForm === "function")
                    window.clearReceiverForm();
            }
        });
        receiverSelect.dataset.bound = "1";
    }
}

function initShippingForm() {
    currentStep = 1;
    showStep(currentStep);

    const btnNext = document.getElementById("btn-next");
    const btnPrev = document.getElementById("btn-prev");

    if (btnNext && !btnNext.dataset.bound) {
        btnNext.addEventListener("click", handleNextStep);
        btnNext.dataset.bound = "1";
    }
    if (btnPrev && !btnPrev.dataset.bound) {
        btnPrev.addEventListener("click", handlePrevStep);
        btnPrev.dataset.bound = "1";
    }

    /* On load, force the true disabled state for step 1 */
    hardEnableNext(validateStep(1));
    setupReceiverTypeHandling();
}

document.addEventListener("DOMContentLoaded", () => {
    initShippingForm();
});

window.initShippingForm = initShippingForm;
window.showStep = showStep;
window.handleNextStep = handleNextStep;
window.handlePrevStep = handlePrevStep;
window.updateStepIndicator = updateStepIndicator;
window.setupReceiverTypeHandling = setupReceiverTypeHandling;
