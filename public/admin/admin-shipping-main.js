(() => {
    let currentStep = 1;
    const totalSteps = 7;

    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach((content, index) => {
            if (index + 1 === step) {
                content.style.display = 'block';
            } else {
                content.style.display = 'none';
            }
        });

        // Update step indicator
        updateStepIndicator(step);
    }

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
            if (i + 1 < step) {
                bubble.classList.add("is-done", "bg-primary");
            } else if (i + 1 === step) {
                bubble.classList.add("is-current", "bg-primary");
            } else {
                bubble.classList.add("bg-secondary");
            }
        });
    }

    function enableNext() {
        const btnNext = document.getElementById("btn-next");
        if (!btnNext) return;
        btnNext.disabled = false;
        btnNext.removeAttribute("disabled");
        btnNext.classList.remove("btn-secondary");
        btnNext.classList.add("btn-primary");
    }

    function disableNext() {
        const btnNext = document.getElementById("btn-next");
        if (!btnNext) return;
        btnNext.disabled = true;
        btnNext.setAttribute("disabled", "disabled");
        btnNext.classList.add("btn-secondary");
        btnNext.classList.remove("btn-primary");
    }

    function showStepContent(stepNumber) {
        // Hide all step content
        document.querySelectorAll('.step-content').forEach((content, index) => {
            content.style.display = 'none';
        });

        // Show the selected step
        const selectedStep = document.getElementById(`step-${stepNumber}`);
        if (selectedStep) {
            selectedStep.style.display = 'block';
        }

        // Update step indicator
        updateStepIndicator(stepNumber);
    }

    function nextStep() {
        if (currentStep < totalSteps) {
            currentStep++;
            showStepContent(currentStep);
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStepContent(currentStep);
        }
    }

    function validateForm() {
        // Add validation logic here
        return true;
    }

    // Initialize when DOM is ready
    document.addEventListener("DOMContentLoaded", function() {
        // Show first step by default
        showStep(1);

        // Add event listeners for navigation
        const btnNext = document.getElementById("btn-next");
        const btnPrev = document.getElementById("btn-prev");

        if (btnNext) {
            btnNext.addEventListener("click", function() {
                if (currentStep < totalSteps) {
                    nextStep();
                }
            });
        }

        if (btnPrev) {
            btnPrev.addEventListener("click", function() {
                if (currentStep > 1) {
                    prevStep();
                }
            });
        }
    });

    // Make functions globally available
    window.showStep = showStep;
    window.nextStep = nextStep;
    window.prevStep = prevStep;
    window.updateStepIndicator = updateStepIndicator;
    window.enableNext = enableNext;
    window.disableNext = disableNext;
})();
