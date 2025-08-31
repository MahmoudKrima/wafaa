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

function isStep1Valid() {
    return !!window.selectedCompany;
}
function isStep2Valid() {
    return !!window.selectedMethod;
}
function isStep3Valid() {
    return typeof window.validateStep3Form === "function"
        ? window.validateStep3Form()
        : true;
}

function setNextForStep(step) {
    if (step === 1) return hardEnableNext(isStep1Valid());
    if (step === 2) return hardEnableNext(isStep2Valid());
    if (step === 3) return hardEnableNext(isStep3Valid());
    return hardEnableNext(true);
}

function hardEnableNext(ok) {
    const btnNext = document.getElementById("btn-next");
    if (!btnNext) return;
    if (ok) {
        btnNext.disabled = false;
        btnNext.removeAttribute("disabled");
        btnNext.classList.remove("disabled", "btn-secondary");
        btnNext.classList.add("btn-primary");
        btnNext.setAttribute("aria-disabled", "false");
    } else {
        btnNext.disabled = true;
        btnNext.setAttribute("disabled", "disabled");
        btnNext.classList.add("disabled", "btn-secondary");
        btnNext.classList.remove("btn-primary");
        btnNext.setAttribute("aria-disabled", "true");
    }
}

function handleNextStep() {
    if (currentStep === 1 && !isStep1Valid()) return;
    if (currentStep === 2 && !isStep2Valid()) return;
    if (currentStep === 3 && !isStep3Valid()) return;
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
        setNextForStep(step);
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
                const sync = () => hardEnableNext(isStep3Valid());
                inp.addEventListener("input", sync);
                inp.addEventListener("change", sync);
                inp.dataset.boundStep3 = "1";
            }
        });
    }

    if (step === 4) {
        if (typeof window.loadReceivers === "function") window.loadReceivers();
        if (typeof window.setupReceiverFormByShippingType === "function")
            window.setupReceiverFormByShippingType();
        setNextForStep(4);
    }

    if (step === 5 && typeof window.populateShippingFormFields === "function")
        window.populateShippingFormFields();
    if (step === 6 && typeof window.setupPaymentDetails === "function")
        window.setupPaymentDetails();
    if (step === 7 && typeof window.setupStep7 === "function")
        window.setupStep7();

    document.dispatchEvent(
        new CustomEvent("stepChanged", { detail: { currentStep: step } })
    );
}

document.addEventListener("shippingCompanySelected", () => {
    if (currentStep === 1) setNextForStep(1);
});
document.addEventListener("shippingMethodSelected", () => {
    if (currentStep === 2) setNextForStep(2);
});
document.addEventListener("receiversChanged", () => {
    if (currentStep === 4) hardEnableNext(true);
});

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

    hardEnableNext(isStep1Valid());

    if (typeof window.setupReceiverTypeHandling === "function")
        window.setupReceiverTypeHandling();
}

document.addEventListener("DOMContentLoaded", initShippingForm);

window.initShippingForm = initShippingForm;
window.showStep = showStep;
window.handleNextStep = handleNextStep;
window.handlePrevStep = handlePrevStep;
window.updateStepIndicator = updateStepIndicator;
