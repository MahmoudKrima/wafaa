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
    // Step 2 requires company, method selection AND sender form validation
    const hasCompany = !!window.selectedCompany;
    const hasMethod = !!window.selectedMethod;

    // Check sender form validation
    let senderFormValid = false;

    // Check if we're using existing sender or new sender
    const senderType =
        document.querySelector('input[name="sender_type"]:checked')?.value ||
        "auth";

    if (senderType === "existing") {
        // For existing sender, check if a sender is selected
        const senderSelect = document.getElementById("sender_select");
        senderFormValid = !!(senderSelect && senderSelect.value);
    } else {
        // For new sender, check required fields (excluding hidden country field)
        const requiredIds = [
            "user_name",
            "user_phone",
            "user_city",
            "user_address",
        ];
        senderFormValid = requiredIds.every((id) => {
            const el = document.getElementById(id);
            if (!el) return false;
            const val = String(el.value || "").trim();
            return val !== "";
        });
    }

    const result = hasCompany && hasMethod && senderFormValid;
    console.log(
        `🔍 isStep2Valid: ${result}, company: ${hasCompany}, method: ${hasMethod}, senderForm: ${senderFormValid}, type: ${senderType}`
    );
    return result;
}

function isStep3Valid() {
    return typeof window.canProceedToNextStep === "function"
        ? window.canProceedToNextStep()
        : true;
}

function isStep4Valid() {
    return typeof window.validatePackageDetails === "function"
        ? window.validatePackageDetails()
        : false;
}

function isStep5Valid() {
    return true; // Payment step - always valid
}

function setNextForStep(step) {
    console.log(`🎯 setNextForStep called for step ${step}`);
    if (step === 1) return hardEnableNext(isStep1Valid());
    if (step === 2) return hardEnableNext(isStep2Valid());
    if (step === 3) return hardEnableNext(isStep3Valid());
    if (step === 4) return hardEnableNext(isStep4Valid());
    if (step === 5) return hardEnableNext(isStep5Valid());
    return hardEnableNext(true);
}

function hardEnableNext(ok) {
    const btnNext = document.getElementById("btn-next");
    if (!btnNext) return;

    // Debug logging
    console.log(
        `🔘 hardEnableNext called: ${ok}, currentStep: ${
            window.currentStep
        }, selectedCompany: ${!!window.selectedCompany}, selectedMethod: ${!!window.selectedMethod}`
    );

    // SIMPLE OVERRIDE: For step 2, only check company and method - ignore everything else
    if (window.currentStep === 2) {
        const step2Valid = !!(window.selectedCompany && window.selectedMethod);
        console.log(
            `🎯 STEP 2 OVERRIDE: ${step2Valid}, company: ${!!window.selectedCompany}, method: ${!!window.selectedMethod}`
        );

        if (step2Valid) {
            btnNext.disabled = false;
            btnNext.removeAttribute("disabled");
            btnNext.classList.remove("disabled", "btn-secondary");
            btnNext.classList.add("btn-primary");
            btnNext.setAttribute("aria-disabled", "false");
            console.log("✅ Next button ENABLED (Step 2 Override)");
        } else {
            btnNext.disabled = true;
            btnNext.setAttribute("disabled", "disabled");
            btnNext.classList.add("disabled", "btn-secondary");
            btnNext.classList.remove("btn-primary");
            btnNext.setAttribute("aria-disabled", "true");
            console.log("❌ Next button DISABLED (Step 2 Override)");
        }
        return;
    }

    if (ok) {
        btnNext.disabled = false;
        btnNext.removeAttribute("disabled");
        btnNext.classList.remove("disabled", "btn-secondary");
        btnNext.classList.add("btn-primary");
        btnNext.setAttribute("aria-disabled", "false");
        console.log("✅ Next button ENABLED");
    } else {
        btnNext.disabled = true;
        btnNext.setAttribute("disabled", "disabled");
        btnNext.classList.add("disabled", "btn-secondary");
        btnNext.classList.remove("btn-primary");
        btnNext.setAttribute("aria-disabled", "true");
        console.log("❌ Next button DISABLED");
    }
}

function handleNextStep() {
    const btnNext = document.getElementById("btn-next");
    if (
        btnNext &&
        (btnNext.disabled || btnNext.getAttribute("aria-disabled") === "true")
    )
        return;
    if (currentStep === 1 && !isStep1Valid()) return;
    if (currentStep === 2 && !isStep2Valid()) return;
    if (currentStep === 3 && !isStep3Valid()) return;
    if (currentStep === 4 && !isStep4Valid()) {
        return;
    }
    if (currentStep === 5 && !isStep5Valid()) return;
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
        btnNext.style.display = step === 6 ? "none" : "inline-block";
        setNextForStep(step);
    }
    if (step === 2 && typeof window.showMethodSelection === "function")
        window.showMethodSelection();
    if (step === 2) {
        if (typeof window.setupLocationFields === "function")
            window.setupLocationFields();
        if (typeof window.handleCompanyRequirements === "function")
            window.handleCompanyRequirements();
        const inputs = document.querySelectorAll(
            "#step-2 input, #step-2 select, #step-2 textarea"
        );
        inputs.forEach((inp) => {
            if (!inp.dataset.boundStep2) {
                const sync = () => hardEnableNext(isStep2Valid());
                inp.addEventListener("input", sync);
                inp.addEventListener("change", sync);
                inp.dataset.boundStep2 = "1";
            }
        });
    }
    if (step === 3) {
        if (typeof window.loadReceivers === "function") window.loadReceivers();
        if (typeof window.setupReceiverFormByShippingType === "function")
            window.setupReceiverFormByShippingType();
        setNextForStep(3);
    }
    if (step === 4) {
        if (typeof window.populateShippingFormFields === "function")
            window.populateShippingFormFields();
        setTimeout(() => {
            setNextForStep(4);
        }, 100);
    }
    if (step === 5 && typeof window.setupPaymentDetails === "function")
        window.setupPaymentDetails();
    if (step === 6 && typeof window.setupStep7 === "function")
        window.setupStep7();
    window.currentStep = step;
    document.dispatchEvent(
        new CustomEvent("stepChanged", { detail: { currentStep: step } })
    );
}

document.addEventListener("shippingCompanySelected", () => {
    console.log(
        `🏢 Company selected - currentStep: ${currentStep}, window.currentStep: ${window.currentStep}`
    );
    if (currentStep === 1) setNextForStep(1);

    // Direct step 2 button control
    if (window.currentStep === 2) {
        setTimeout(() => {
            const step2Valid = !!(
                window.selectedCompany && window.selectedMethod
            );
            console.log(`🎯 DIRECT STEP 2 CONTROL: ${step2Valid}`);
            forceButtonState(step2Valid);
        }, 100);
    }
});

document.addEventListener("shippingMethodSelected", () => {
    console.log(
        `🚚 Method selected - currentStep: ${currentStep}, window.currentStep: ${window.currentStep}`
    );
    if (currentStep === 2) setNextForStep(2);

    // Direct step 2 button control
    if (window.currentStep === 2) {
        setTimeout(() => {
            const step2Valid = !!(
                window.selectedCompany && window.selectedMethod
            );
            console.log(`🎯 DIRECT STEP 2 CONTROL: ${step2Valid}`);
            forceButtonState(step2Valid);
        }, 100);
    }
});

document.addEventListener("receiversChanged", () => {
    if (currentStep === 3) hardEnableNext(isStep3Valid());
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

// COMPLETELY SEPARATE BUTTON CONTROL SYSTEM
function forceButtonState(shouldEnable) {
    const btnNext = document.getElementById("btn-next");
    if (!btnNext) return;

    console.log(
        `🔧 FORCE BUTTON STATE: ${shouldEnable ? "ENABLE" : "DISABLE"}`
    );

    if (shouldEnable) {
        btnNext.disabled = false;
        btnNext.removeAttribute("disabled");
        btnNext.classList.remove("disabled", "btn-secondary");
        btnNext.classList.add("btn-primary");
        btnNext.setAttribute("aria-disabled", "false");
        console.log("✅ BUTTON FORCE ENABLED");
    } else {
        btnNext.disabled = true;
        btnNext.setAttribute("disabled", "disabled");
        btnNext.classList.add("disabled", "btn-secondary");
        btnNext.classList.remove("btn-primary");
        btnNext.setAttribute("aria-disabled", "true");
        console.log("❌ BUTTON FORCE DISABLED");
    }
}

// Override the hardEnableNext function completely
const originalHardEnableNext = window.hardEnableNext;
window.hardEnableNext = function (ok) {
    console.log(
        `🚨 hardEnableNext OVERRIDE called with: ${ok}, currentStep: ${window.currentStep}`
    );

    // For step 2, completely ignore the parameter and use our own logic
    if (window.currentStep === 2) {
        const step2Valid = !!(window.selectedCompany && window.selectedMethod);
        console.log(
            `🎯 STEP 2 OVERRIDE: ignoring parameter ${ok}, using ${step2Valid}`
        );
        forceButtonState(step2Valid);
        return;
    }

    // For other steps, use original logic
    if (originalHardEnableNext) {
        originalHardEnableNext(ok);
    }
};

// Simple periodic check to ensure step 2 button state is correct
setInterval(() => {
    if (window.currentStep === 2) {
        const step2Valid = !!(window.selectedCompany && window.selectedMethod);
        const btnNext = document.getElementById("btn-next");
        if (btnNext) {
            const shouldBeEnabled = step2Valid;
            const isEnabled = !btnNext.disabled;

            if (shouldBeEnabled !== isEnabled) {
                console.log(
                    `🔄 PERIODIC FIX: Step 2 button state mismatch - fixing to ${shouldBeEnabled}`
                );
                forceButtonState(shouldBeEnabled);
            }
        }
    }
}, 200); // Check every 200ms for faster response

// Global City Management System
(() => {
    const SAUDI_ID = "65fd1a1c1fdbc094e3369b29";
    const cityCache = new Map();
    const loadingPromises = new Map();
    const CACHE_DURATION = 10 * 60 * 1000; // 10 minutes cache
    let currentCompanyId = null;
    let currentCities = [];

    // Get cache key for company
    function getCityCacheKey(companyId) {
        return `cities-${companyId || "no-company"}`;
    }

    // Check if cache is valid
    function isCacheValid(timestamp) {
        return Date.now() - timestamp < CACHE_DURATION;
    }

    // Fetch cities for a company
    async function fetchCitiesForCompany(companyId) {
        const cacheKey = getCityCacheKey(companyId);

        // Check cache first
        const cachedData = cityCache.get(cacheKey);
        if (cachedData && isCacheValid(cachedData.timestamp)) {
            return cachedData.cities;
        }

        // Check if already loading
        if (loadingPromises.has(cacheKey)) {
            return await loadingPromises.get(cacheKey);
        }

        // Create loading promise
        const loadingPromise = loadCitiesFromAPI(companyId);
        loadingPromises.set(cacheKey, loadingPromise);

        try {
            const cities = await loadingPromise;

            // Cache the result
            cityCache.set(cacheKey, {
                cities,
                timestamp: Date.now(),
            });

            return cities;
        } finally {
            loadingPromises.delete(cacheKey);
        }
    }

    // Load cities from API
    async function loadCitiesFromAPI(companyId) {
        const url = companyId
            ? `/cities-by-company-and-country/${companyId}`
            : `/cities-by-country/${SAUDI_ID}`;

        const response = await fetch(url, {
            headers: {
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            credentials: "same-origin",
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch cities: ${response.status}`);
        }

        const data = await response.json();
        return Array.isArray(data?.results)
            ? data.results
            : Array.isArray(data)
            ? data
            : [];
    }

    // Populate city select with cached cities
    function populateCitySelect(citySelect, cities, selectedCityId = "") {
        if (!citySelect) return;

        // Use DocumentFragment for better performance
        const fragment = document.createDocumentFragment();

        // Add default option
        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent =
            window.translations?.select_city || "Select City";
        fragment.appendChild(defaultOption);

        // Add city options
        cities.forEach((city) => {
            const opt = document.createElement("option");
            opt.value = city.id || city._id || "";
            opt.textContent = getCityDisplayName(city);
            fragment.appendChild(opt);
        });

        // Clear and populate in one operation
        citySelect.innerHTML = "";
        citySelect.appendChild(fragment);
        citySelect.disabled = false;

        // Set selected city if provided
        if (selectedCityId) {
            citySelect.value = selectedCityId;
        }

        // Reinitialize Select2 if needed
        reinitializeCitySelect2(citySelect);
    }

    // Get city display name
    function getCityDisplayName(city) {
        if (!city) return "";
        if (typeof city.name === "object") {
            const locale = (window.APP_LOCALE || "en").toLowerCase();
            return city.name[locale] || city.name.en || city.name.ar || "";
        }
        return city.name || city.englishName || "";
    }

    // Reinitialize Select2
    function reinitializeCitySelect2(citySelect) {
        if (typeof $ !== "undefined" && $(citySelect).data("select2")) {
            $(citySelect).select2("destroy");
        }

        if (typeof $ !== "undefined") {
            $(citySelect).select2({
                placeholder: window.translations?.choose_city || "Choose City",
                allowClear: true,
                width: "100%",
                minimumInputLength: 0,
                closeOnSelect: true,
                cache: true,
                language: {
                    noResults: function () {
                        return (
                            window.translations?.no_cities_available ||
                            "No cities available"
                        );
                    },
                    searching: function () {
                        return window.translations?.searching || "Searching...";
                    },
                },
            });

            if (citySelect.value) {
                $(citySelect).val(citySelect.value).trigger("change.select2");
            }
        }
    }

    // Global function to load cities for current company
    async function loadCitiesForCurrentCompany() {
        const companyId =
            window.selectedCompany?.id || window.selectedCompany?._id;
        if (!companyId) return [];

        try {
            const cities = await fetchCitiesForCompany(companyId);
            currentCompanyId = companyId;
            currentCities = cities;
            return cities;
        } catch (error) {
            console.error("Error loading cities for company:", error);
            return [];
        }
    }

    // Global function to populate any city select
    function populateAnyCitySelect(citySelectId, selectedCityId = "") {
        const citySelect = document.getElementById(citySelectId);
        if (!citySelect || currentCities.length === 0) return;

        populateCitySelect(citySelect, currentCities, selectedCityId);
    }

    // Clear city cache
    function clearCityCache() {
        cityCache.clear();
        loadingPromises.clear();
        currentCities = [];
        currentCompanyId = null;
    }

    // Get cache stats
    function getCityCacheStats() {
        return {
            cacheSize: cityCache.size,
            loadingPromises: loadingPromises.size,
            currentCompanyId,
            currentCitiesCount: currentCities.length,
            cacheKeys: Array.from(cityCache.keys()),
        };
    }

    // Listen for company selection to preload cities (non-blocking)
    document.addEventListener("shippingCompanySelected", () => {
        console.log("Company selected, preloading cities...");

        // Load cities in background without blocking
        loadCitiesForCurrentCompany()
            .then(() => {
                // Dispatch event that cities are loaded
                document.dispatchEvent(
                    new CustomEvent("citiesLoaded", {
                        detail: {
                            companyId: currentCompanyId,
                            cities: currentCities,
                        },
                    })
                );
            })
            .catch((error) => {
                console.error("Error loading cities for company:", error);
            });
    });

    // Expose global functions
    window.loadCitiesForCurrentCompany = loadCitiesForCurrentCompany;
    window.populateAnyCitySelect = populateAnyCitySelect;
    window.clearCityCache = clearCityCache;
    window.getCityCacheStats = getCityCacheStats;
    window.currentCities = () => currentCities;
    window.currentCompanyId = () => currentCompanyId;
})();

window.initShippingForm = initShippingForm;
window.showStep = showStep;
window.handleNextStep = handleNextStep;
window.handlePrevStep = handlePrevStep;
window.updateStepIndicator = updateStepIndicator;
window.hardEnableNext = hardEnableNext;
