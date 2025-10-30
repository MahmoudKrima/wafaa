(() => {
    const STEP = document.getElementById("step-2");
    const LOCALE = (STEP?.dataset?.appLocale || "en").toLowerCase();
    const DEFAULT_KSA_ID = "65fd1a1c1fdbc094e3369b29";

    const API_KEY =
        document.querySelector('meta[name="ghaya-api-key"]')?.content ||
        (window.GHAYA_API_KEY ?? "qp4dz7u6m8ro8jx0txg9eqh7mcu5vvg0");

    const API_BASE =
        document.querySelector('meta[name="ghaya-api-base"]')?.content ||
        (window.GHAYA_API_BASE ??
            "https://ghaya-express-api-server-74ddc24b4e63.herokuapp.com/api");

    const API = {
        countries:
            (window.API_ENDPOINTS && window.API_ENDPOINTS.countries) ||
            `${API_BASE}/countries?page=0&pageSize=500`,

        states: (countryId, shippingCompanyId) =>
            (window.API_ENDPOINTS &&
                window.API_ENDPOINTS.states?.(countryId, shippingCompanyId)) ||
            `${API_BASE}/states?pageSize=500&page=0&countryId=${encodeURIComponent(
                countryId
            )}&shippingCompanyId=${encodeURIComponent(shippingCompanyId)}`,

        cities: (countryId, stateId, shippingCompanyId) =>
            (window.API_ENDPOINTS &&
                window.API_ENDPOINTS.cities?.(
                    countryId,
                    stateId,
                    shippingCompanyId
                )) ||
            `${API_BASE}/cities?pageSize=500&page=0&countryId=${encodeURIComponent(
                countryId
            )}&stateId=${encodeURIComponent(
                stateId
            )}&shippingCompanyId=${encodeURIComponent(shippingCompanyId)}`,
    };

    // Performance optimization: City data cache
    const cityCache = new Map();
    const loadingPromises = new Map();
    const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes cache
    const MAX_CACHE_SIZE = 50; // Maximum number of cached entries

    // Performance optimization: Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Performance optimization: Cache management
    function manageCache() {
        if (cityCache.size > MAX_CACHE_SIZE) {
            const entries = Array.from(cityCache.entries());
            // Remove oldest entries (simple LRU)
            const toRemove = entries.slice(0, entries.length - MAX_CACHE_SIZE);
            toRemove.forEach(([key]) => cityCache.delete(key));
        }
    }

    // Performance optimization: Get cache key
    function getCacheKey(companyId, countryId) {
        return `${companyId || "no-company"}-${countryId}`;
    }

    // Performance optimization: Check if cache is valid
    function isCacheValid(timestamp) {
        return Date.now() - timestamp < CACHE_DURATION;
    }

    const $country = () => document.getElementById("user_country");
    const $city = () => document.getElementById("user_city");
    const $senderSelect = () => document.getElementById("sender_select");

    const requiredIds = [
        "user_name",
        "user_phone",
        "user_country",
        "user_city",
        "user_address",
    ];

    function enableInputs() {
        (STEP || document)
            .querySelectorAll("input,select,textarea")
            .forEach((el) => (el.disabled = false));
    }

    async function getJSON(url) {
        const res = await fetch(url, {
            credentials: "same-origin",
            headers: { accept: "*/*", "x-api-key": API_KEY },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
    }

    function labelFrom(item) {
        const n = item?.name;
        if (n && typeof n === "object") return n[LOCALE] || n.en || n.ar || "";
        return item?.englishName || item?.name || "";
    }

    function fillSelect(select, list, { placeholder, valueKey = "id" } = {}) {
        if (!select) return;
        const prev = select.value;
        select.innerHTML = "";

        const opt0 = document.createElement("option");
        opt0.value = "";
        opt0.textContent =
            placeholder ||
            (select.id === "user_country"
                ? window.translations?.select_country || "Select country"
                : select.id === "user_state"
                ? window.translations?.select_state || "Select state"
                : window.translations?.select_city || "Select city");
        select.appendChild(opt0);

        list.forEach((item) => {
            const opt = document.createElement("option");
            opt.value = String(item[valueKey] ?? item.id ?? "");
            opt.textContent =
                labelFrom(item) || String(item.code || item.id || "");
            select.appendChild(opt);
        });

        const preset = select.getAttribute("data-selected");
        const canUsePreset =
            preset &&
            Array.from(select.options).some((o) => o.value === preset);
        const canUsePrev =
            prev && Array.from(select.options).some((o) => o.value === prev);

        if (canUsePrev) select.value = prev;
        else if (canUsePreset) select.value = preset;
        else select.value = "";
    }

    function findKSAId(countries) {
        const byCode = countries.find(
            (c) => String(c.code || "").toUpperCase() === "SA"
        );
        if (byCode) return String(byCode.id);
        const byName = countries.find((c) => {
            const nm = (labelFrom(c) || "").toLowerCase();
            return nm.includes("saudi");
        });
        return byName ? String(byName.id) : DEFAULT_KSA_ID;
    }

    function companyId() {
        return String(
            window.selectedCompany?.id || window.selectedCompany?._id || ""
        );
    }
    function method() {
        return (window.selectedMethod || "").toLowerCase();
    }

    function fillSenderSelect(senders) {
        const select = $senderSelect();
        if (!select) return;
        select.innerHTML = `<option value="">${
            window.translations?.choose_sender || "Choose sender"
        }</option>`;
        senders.forEach((sender) => {
            const opt = document.createElement("option");
            opt.value = sender.id || sender._id || "";
            opt.textContent = sender.name || sender.full_name || "";

            // Store additional data as data attributes for instant population
            if (sender.phone) opt.dataset.phone = sender.phone;
            if (sender.additional_phone)
                opt.dataset.additionalPhone = sender.additional_phone;
            if (sender.address) opt.dataset.address = sender.address;

            select.appendChild(opt);
        });
    }

    // Optimized sender loading with immediate execution
    function loadSendersByCompany() {
        const select = $senderSelect();
        if (!select) return;

        const companyIdValue = companyId();
        if (!companyIdValue) {
            select.innerHTML = `<option value="">${
                window.translations?.select_company_first ||
                "Please select a shipping company first"
            }</option>`;
            select.disabled = true;
            return;
        }

        // Show loading state
        select.innerHTML = `<option value="">${
            window.translations?.loading || "Loading..."
        }</option>`;
        select.disabled = true;

        // Load senders immediately without waiting for other operations
        loadSendersImmediately(companyIdValue, select);
    }

    // Immediate sender loading function with caching
    const senderCache = new Map();
    const senderLoadingPromises = new Map();

    async function loadSendersImmediately(companyIdValue, select) {
        // Check cache first
        if (senderCache.has(companyIdValue)) {
            const cachedSenders = senderCache.get(companyIdValue);
            fillSenderSelect(cachedSenders);
            select.disabled = false;
            return;
        }

        // Check if already loading
        if (senderLoadingPromises.has(companyIdValue)) {
            try {
                const senders = await senderLoadingPromises.get(companyIdValue);
                fillSenderSelect(senders);
                select.disabled = false;
            } catch (error) {
                console.error("Error loading cached senders:", error);
                select.innerHTML = `<option value="">${
                    window.translations?.no_senders_found || "No senders found"
                }</option>`;
                select.disabled = false;
            }
            return;
        }

        // Create loading promise
        const loadingPromise = fetchSendersData(companyIdValue);
        senderLoadingPromises.set(companyIdValue, loadingPromise);

        try {
            const senders = await loadingPromise;
            senderCache.set(companyIdValue, senders);
            fillSenderSelect(senders);
            select.disabled = false;
        } catch (error) {
            console.error("Error loading senders:", error);
            select.innerHTML = `<option value="">${
                window.translations?.no_senders_found || "No senders found"
            }</option>`;
            select.disabled = false;
        } finally {
            senderLoadingPromises.delete(companyIdValue);
        }
    }

    // Separate function for fetching sender data
    async function fetchSendersData(companyIdValue) {
        const response = await fetch(`/senders-by-company/${companyIdValue}`, {
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
            throw new Error(`Failed to fetch senders: ${response.status}`);
        }

        const data = await response.json();
        return Array.isArray(data) ? data : [];
    }

    // Sender form cache for instant population
    const senderFormCache = new Map();

    // Function to preserve sender selection during operations
    function preserveSenderSelection() {
        const select = $senderSelect();
        if (!select) return null;

        const selectedValue = select.value;
        const selectedText =
            select.options[select.selectedIndex]?.textContent || "";

        return { value: selectedValue, text: selectedText };
    }

    // Function to restore sender selection after operations
    function restoreSenderSelection(selection) {
        if (!selection || !selection.value) return;

        const select = $senderSelect();
        if (!select) return;

        // Restore the value immediately
        select.value = selection.value;

        // Update Select2 with a small delay to ensure it's fully initialized
        setTimeout(() => {
            if (typeof $ !== "undefined" && $(select).data("select2")) {
                $(select).val(selection.value).trigger("change.select2");
            }
        }, 50);
    }

    async function populateSenderForm(senderId) {
        const select = $senderSelect();
        if (!select) return;

        // Set selection immediately for instant feedback
        select.value = senderId;
        if (typeof $ !== "undefined" && $(select).data("select2")) {
            $(select).val(senderId).trigger("change.select2");
        }

        // Check cache first for instant population
        if (senderFormCache.has(senderId)) {
            const cachedSender = senderFormCache.get(senderId);
            populateFormFields(cachedSender);
            // Load location data in background without blocking
            loadLocationDataInBackground(cachedSender);
            return;
        }

        // Populate basic fields from dropdown option immediately
        const selectedOption = select.querySelector(
            `option[value="${senderId}"]`
        );
        if (selectedOption) {
            const nameField = document.getElementById("user_name");
            if (nameField) nameField.value = selectedOption.textContent;

            // Try to populate additional fields from data attributes if available
            const phoneField = document.getElementById("user_phone");
            const additionalPhoneField = document.getElementById(
                "user_additional_phone"
            );
            const addressField = document.getElementById("user_address");

            if (phoneField && selectedOption.dataset.phone) {
                phoneField.value = selectedOption.dataset.phone;
            }
            if (
                additionalPhoneField &&
                selectedOption.dataset.additionalPhone
            ) {
                additionalPhoneField.value =
                    selectedOption.dataset.additionalPhone;
            }
            if (addressField && selectedOption.dataset.address) {
                addressField.value = selectedOption.dataset.address;
            }
        }

        // Trigger validation immediately after basic population
        if (typeof window.hardEnableNext === "function") {
            const isValid = window.smartValidateStep();
            window.hardEnableNext(isValid);
        }

        // Fetch full details in background without blocking UI
        fetchSenderDetailsInBackground(senderId);
    }

    // Background sender details fetching - non-blocking
    async function fetchSenderDetailsInBackground(senderId) {
        try {
            const response = await fetch(`/user/senders/${senderId}`, {
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
                throw new Error("Failed to fetch sender details");
            }

            const sender = await response.json();

            // Cache the sender data
            senderFormCache.set(senderId, sender);

            // Populate all form fields
            populateFormFields(sender);

            // Load location data in background without blocking
            loadLocationDataInBackground(sender);
        } catch (error) {
            console.error("Error populating sender form:", error);
            // Form already has basic data from dropdown option
        }
    }

    // Instant form field population
    function populateFormFields(sender) {
        const nameField = document.getElementById("user_name");
        const phoneField = document.getElementById("user_phone");
        const additionalPhoneField = document.getElementById(
            "user_additional_phone"
        );
        const addressField = document.getElementById("user_address");

        if (nameField) nameField.value = sender.name || "";
        if (phoneField) phoneField.value = sender.phone || "";
        if (additionalPhoneField)
            additionalPhoneField.value = sender.additional_phone || "";
        if (addressField) addressField.value = sender.address || "";
    }

    // Background location data loading - completely non-blocking
    function loadLocationDataInBackground(sender) {
        // Run all location loading in background without blocking UI
        setTimeout(async () => {
            try {
                await loadCountries();

                const countrySelect = $country();
                if (countrySelect) {
                    countrySelect.value = DEFAULT_KSA_ID;
                }

                const companyIdValue = companyId();
                let senderCityId = null;

                if (
                    sender.shipping_companies &&
                    Array.isArray(sender.shipping_companies)
                ) {
                    const matchingCompany = sender.shipping_companies.find(
                        (sc) =>
                            String(sc.shipping_company_id) ===
                            String(companyIdValue)
                    );

                    if (matchingCompany && matchingCompany.city_id) {
                        senderCityId = matchingCompany.city_id;
                    }
                }

                // Load cities for Saudi Arabia
                if (companyIdValue) {
                    await loadCitiesByCompanyAndCountry(
                        companyIdValue,
                        DEFAULT_KSA_ID,
                        senderCityId
                    );
                } else {
                    await preloadSaudiArabiaCities();
                }
            } catch (error) {
                console.error("Error loading location data:", error);
            } finally {
                // Trigger validation after background loading completes
                if (typeof window.hardEnableNext === "function") {
                    const isValid = window.smartValidateStep();
                    window.hardEnableNext(isValid);
                }
            }
        }, 0); // Run immediately but asynchronously
    }

    // Ultra-optimized city loading with instant switching
    function loadCitiesByCompanyAndCountry(
        companyId,
        countryId,
        selectedCityId = ""
    ) {
        const citySelect = $city();
        if (!citySelect) return;

        if (!countryId) {
            resetCitySelect(citySelect, true);
            return;
        }

        // Check cache first for instant loading (highest priority)
        const cacheKey = `${companyId}-${countryId}`;
        if (cityCache.has(cacheKey)) {
            const cachedData = cityCache.get(cacheKey);
            if (isCacheValid(cachedData.timestamp)) {
                // Instant loading from cache - update UI immediately
                populateCitySelect(
                    citySelect,
                    cachedData.cities,
                    selectedCityId
                );
                console.log(
                    `⚡ INSTANT city loading from cache for company ${companyId}`
                );
                return;
            }
        }

        // Use global city management system (second priority)
        if (window.currentCities && window.currentCities().length > 0) {
            populateCitySelect(
                citySelect,
                window.currentCities(),
                selectedCityId
            );
            console.log(
                `⚡ INSTANT city loading from global cache for company ${companyId}`
            );
            return;
        }

        // Check if cities are being preloaded for this company
        if (
            window.currentCompanyId &&
            window.currentCompanyId() === companyId
        ) {
            // Wait a moment for preloading to complete, then try again
            setTimeout(() => {
                if (cityCache.has(cacheKey)) {
                    const cachedData = cityCache.get(cacheKey);
                    if (isCacheValid(cachedData.timestamp)) {
                        populateCitySelect(
                            citySelect,
                            cachedData.cities,
                            selectedCityId
                        );
                        console.log(
                            `⚡ DELAYED INSTANT city loading from cache for company ${companyId}`
                        );
                        return;
                    }
                }
                // If still not cached, fall back to background loading
                loadCitiesInBackground(companyId, countryId, selectedCityId);
            }, 100);
            return;
        }

        // If cities not loaded globally, try to load them in background
        if (window.loadCitiesForCurrentCompany) {
            window
                .loadCitiesForCurrentCompany()
                .then((cities) => {
                    if (cities.length > 0) {
                        populateCitySelect(citySelect, cities, selectedCityId);
                        console.log(
                            `City loading from global system for company ${companyId}`
                        );
                    } else {
                        loadCitiesInBackground(
                            companyId,
                            countryId,
                            selectedCityId
                        );
                    }
                })
                .catch((error) => {
                    console.error(
                        "Error loading cities from global system:",
                        error
                    );
                    loadCitiesInBackground(
                        companyId,
                        countryId,
                        selectedCityId
                    );
                });
            return;
        }

        // Fallback to background loading
        loadCitiesInBackground(companyId, countryId, selectedCityId);
    }

    // Background city loading that doesn't block UI
    async function loadCitiesInBackground(
        companyId,
        countryId,
        selectedCityId = ""
    ) {
        const citySelect = $city();
        if (!citySelect) return;

        // Show loading state
        showCityLoadingState(citySelect);

        try {
            const cities = await fetchCitiesData(companyId, countryId);

            // Cache the cities for future use
            const cacheKey = `${companyId}-${countryId}`;
            cityCache.set(cacheKey, {
                cities: cities,
                timestamp: Date.now(),
            });

            populateCitySelect(citySelect, cities, selectedCityId);
        } catch (error) {
            console.error("Error loading cities:", error);
            showCityErrorState(citySelect);
        }
    }

    // Optimized function to fetch cities data
    async function fetchCitiesData(companyId, countryId) {
        let url, headers;

        if (companyId) {
            url = `/cities-by-company-and-country/${companyId}`;
        } else {
            url = `/cities-by-country/${countryId}`;
        }

        headers = {
            "X-CSRF-TOKEN":
                document.querySelector('meta[name="csrf-token"]')?.content ||
                "",
            Accept: "application/json",
            "Content-Type": "application/json",
        };

        const response = await fetch(url, {
            headers,
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

    // Optimized function to populate city select
    function populateCitySelect(citySelect, cities, selectedCityId = "") {
        // Use DocumentFragment for better performance
        const fragment = document.createDocumentFragment();

        // Add default option
        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent =
            window.translations?.select_city || "Select City";
        fragment.appendChild(defaultOption);

        // Add city options with state information stored in data attributes
        cities.forEach((city) => {
            const opt = document.createElement("option");
            opt.value = city.id || city._id || "";
            opt.textContent = labelFrom(city);

            // Store state information in data attributes for later retrieval
            if (city.state_id) {
                opt.dataset.stateId = city.state_id;
            }
            if (city.state_name) {
                opt.dataset.stateName = city.state_name;
            }

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

        // Reinitialize Select2 efficiently
        reinitializeCitySelect2(citySelect);

        // Trigger validation
        setTimeout(() => {
            if (typeof window.hardEnableNext === "function") {
                const isValid = window.smartValidateStep();
                window.hardEnableNext(isValid);
            }
        }, 100);
    }

    // Optimized function to show loading state
    function showCityLoadingState(citySelect) {
        // Preserve sender selection
        const senderSelection = preserveSenderSelection();

        citySelect.innerHTML = `<option value="">${
            window.translations?.loading_cities || "Loading cities..."
        }</option>`;
        citySelect.disabled = true;

        if (typeof $ !== "undefined" && $(citySelect).data("select2")) {
            $(citySelect).select2("destroy");
            $(citySelect).select2({
                placeholder:
                    window.translations?.loading_cities || "Loading cities...",
                allowClear: false,
                width: "100%",
                disabled: true,
            });
        }

        // Restore sender selection
        if (senderSelection) {
            restoreSenderSelection(senderSelection);
        }
    }

    // Optimized function to show error state
    function showCityErrorState(citySelect) {
        // Preserve sender selection
        const senderSelection = preserveSenderSelection();

        citySelect.innerHTML = `<option value="">${
            window.translations?.error_loading_cities || "Error loading cities"
        }</option>`;
        citySelect.disabled = false;

        if (typeof $ !== "undefined" && $(citySelect).data("select2")) {
            $(citySelect).select2("destroy");
            $(citySelect).select2({
                placeholder:
                    window.translations?.error_loading_cities ||
                    "Error loading cities",
                allowClear: true,
                width: "100%",
            });
        }

        // Restore sender selection
        if (senderSelection) {
            restoreSenderSelection(senderSelection);
        }
    }

    // Optimized function to reset city select
    function resetCitySelect(citySelect, disabled = false) {
        // Preserve sender selection
        const senderSelection = preserveSenderSelection();

        citySelect.innerHTML = `<option value="">${
            window.translations?.select_city || "Select City"
        }</option>`;
        citySelect.disabled = disabled;

        if (typeof $ !== "undefined" && $(citySelect).data("select2")) {
            $(citySelect).select2("destroy");
            $(citySelect).select2({
                placeholder: window.translations?.select_city || "Select City",
                allowClear: true,
                width: "100%",
                disabled: disabled,
            });
        }

        // Restore sender selection
        if (senderSelection) {
            restoreSenderSelection(senderSelection);
        }
    }

    // Function to handle city selection and capture state information
    function handleCitySelection(citySelect) {
        const selectedOption = citySelect.options[citySelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const stateId = selectedOption.dataset.stateId;
            const stateName = selectedOption.dataset.stateName;

            // Update hidden form fields for state information
            const stateIdField = document.getElementById(
                "sender_state_id_hidden"
            );
            const stateNameField = document.getElementById(
                "sender_state_name_hidden"
            );

            if (stateIdField) {
                stateIdField.value = stateId || "";
            }
            if (stateNameField) {
                stateNameField.value = stateName || "";
            }

            console.log("City selected:", {
                cityId: selectedOption.value,
                cityName: selectedOption.textContent,
                stateId: stateId,
                stateName: stateName,
            });
        } else {
            // Clear state information if no city selected
            const stateIdField = document.getElementById(
                "sender_state_id_hidden"
            );
            const stateNameField = document.getElementById(
                "sender_state_name_hidden"
            );

            if (stateIdField) {
                stateIdField.value = "";
            }
            if (stateNameField) {
                stateNameField.value = "";
            }
        }
    }

    // Optimized function to reinitialize Select2 for city select only
    function reinitializeCitySelect2(citySelect) {
        if (!citySelect) return;

        // Preserve sender selection before any Select2 operations
        const senderSelection = preserveSenderSelection();

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

            // Add event listener for city selection
            $(citySelect).on("change", function () {
                handleCitySelection(citySelect);
            });

            if (citySelect.value) {
                $(citySelect).val(citySelect.value).trigger("change.select2");
            }
        }

        // Restore sender selection after city Select2 reinitialization
        if (senderSelection) {
            restoreSenderSelection(senderSelection);
        }
    }

    async function loadCountries() {
        const el = $country();
        if (!el) return;
        el.required = true;

        fillSelect(el, [], {
            placeholder:
                window.translations?.loading_countries ||
                "Loading countries...",
        });
        el.disabled = true;

        try {
            const data = await getJSON(API.countries);
            const items = Array.isArray(data?.results)
                ? data.results
                : Array.isArray(data)
                ? data
                : [];
            fillSelect(el, items, {
                placeholder: window.translations?.select_country,
            });

            // Always set Saudi Arabia as default for sender section
            const ksaId = findKSAId(items);
            if ([...el.options].some((o) => o.value === ksaId)) {
                el.value = ksaId;
                // Load cities directly after setting country
                const comp = companyId();
                if (comp) {
                    await loadCitiesByCompanyAndCountry(comp, ksaId);
                }
            }
        } catch {
            fillSelect(el, [], {
                placeholder:
                    window.translations?.no_countries_found || "No countries",
            });
        } finally {
            el.disabled = false;
        }
    }

    function bindLocationChangeHandlers() {
        const c = $country();
        if (c && !c.dataset.bound) {
            c.addEventListener("change", async () => {
                const comp = companyId();
                const countryId = c.value;
                if (countryId) {
                    // Use debounced loading to prevent rapid API calls
                    debouncedLoadCities(comp, countryId);
                }
            });
            c.dataset.bound = "1";
        }
    }

    function ensureAdditionalPhoneOptional() {
        const ap = document.getElementById("user_additional_phone");
        if (ap) {
            ap.required = false;
            ap.removeAttribute("required");
            ap.setAttribute("aria-required", "false");
        }
    }

    function handleCompanyRequirements() {
        enableInputs();
        [$country(), $city()].forEach((el) => {
            if (el) {
                el.required = true;
                el.disabled = false;
            }
        });
        ensureAdditionalPhoneOptional();

        const note = document.getElementById("language-note");
        if (note) {
            if (window.selectedCompany?.isEnglish === true) {
                note.style.display = "block";
                note.textContent =
                    window.translations?.enter_in_english ||
                    "Please enter data in English only";
            } else {
                note.style.display = "none";
            }
        }
    }

    // Optimized preloading function using global city management
    async function preloadSaudiArabiaCities() {
        const citySelect = $city();
        if (!citySelect) return;

        const countrySelect = $country();
        if (!countrySelect || !countrySelect.value) return;

        // Use global city management system
        if (window.currentCities && window.currentCities().length > 0) {
            populateCitySelect(citySelect, window.currentCities());
            return;
        }

        // If cities not loaded globally, try to load them
        if (window.loadCitiesForCurrentCompany) {
            try {
                const cities = await window.loadCitiesForCurrentCompany();
                if (cities.length > 0) {
                    populateCitySelect(citySelect, cities);
                    return;
                }
            } catch (error) {
                console.error(
                    "Error preloading cities from global system:",
                    error
                );
            }
        }

        // Fallback to local loading
        const ksaId = countrySelect.value;
        const comp = companyId();
        await loadCitiesByCompanyAndCountry(comp, ksaId);
    }

    // Debounced city loading to prevent rapid API calls
    const debouncedLoadCities = debounce(loadCitiesByCompanyAndCountry, 300);

    // Ultra-optimized setup with non-blocking loading
    function setupLocationFields() {
        // Only run if we're on the correct step to avoid unnecessary work
        if (window.currentStep !== 2) return;

        handleCompanyRequirements();
        bindLocationChangeHandlers();
        ensureAdditionalPhoneOptional();

        // Load senders immediately without waiting for other operations
        loadSendersByCompany();

        // Load countries and cities in background without blocking UI
        loadCountriesAndCitiesInBackground();
    }

    // Non-blocking background loading for better company switching
    async function loadCountriesAndCitiesInBackground() {
        try {
            // Load countries first
            await loadCountries();

            // Then load cities in background without blocking
            preloadSaudiArabiaCities().catch((error) => {
                console.warn("City preload failed:", error);
            });
        } catch (error) {
            console.warn("Failed to load countries, but continuing:", error);
            // Still try to preload cities even if countries fail
            preloadSaudiArabiaCities().catch((cityError) => {
                console.warn("City preload also failed:", cityError);
            });
        }
    }

    // Immediate sender loading for better UX
    function immediateSenderLoad() {
        const companyIdValue = companyId();
        if (!companyIdValue) return;

        const select = $senderSelect();
        if (!select) return;

        // Load senders immediately when company is selected
        loadSendersImmediately(companyIdValue, select);
    }

    // Preload senders for instant access
    function preloadSendersForCompany(companyIdValue) {
        if (!companyIdValue || senderCache.has(companyIdValue)) return;

        // Start loading in background
        fetchSendersData(companyIdValue)
            .then((senders) => {
                senderCache.set(companyIdValue, senders);
            })
            .catch((error) => {
                console.error("Error preloading senders:", error);
            });
    }

    // Ultra-aggressive city preloading for instant switching
    function preloadCitiesForAllCompanies() {
        // Get all company elements
        const companyElements = document.querySelectorAll(
            'input[name="shipping_company_id"]'
        );

        console.log(
            `🚀 Starting aggressive preloading for ${companyElements.length} companies...`
        );

        // Preload cities for all companies in parallel
        const preloadPromises = [];

        companyElements.forEach((companyElement) => {
            const companyId = companyElement.value;
            if (companyId && !cityCache.has(`${companyId}-${DEFAULT_KSA_ID}`)) {
                // Start preloading immediately
                const preloadPromise = fetchCitiesData(
                    companyId,
                    DEFAULT_KSA_ID
                )
                    .then((cities) => {
                        const cacheKey = `${companyId}-${DEFAULT_KSA_ID}`;
                        cityCache.set(cacheKey, {
                            cities: cities,
                            timestamp: Date.now(),
                        });
                        console.log(
                            `✅ Preloaded cities for company ${companyId}: ${cities.length} cities`
                        );
                    })
                    .catch((error) => {
                        console.warn(
                            `❌ Failed to preload cities for company ${companyId}:`,
                            error
                        );
                    });

                preloadPromises.push(preloadPromise);
            } else if (companyId) {
                console.log(`⚡ Company ${companyId} cities already cached`);
            }
        });

        // Log when all preloading is complete
        Promise.allSettled(preloadPromises).then(() => {
            console.log(
                `🎉 City preloading complete. Cached ${cityCache.size} company-city combinations.`
            );
        });
    }

    // Immediate city preloading when companies become visible
    function preloadCitiesOnCompanyHover() {
        const companyElements = document.querySelectorAll(
            'input[name="shipping_company_id"]'
        );

        console.log(
            `🎯 Setting up hover preloading for ${companyElements.length} companies...`
        );

        companyElements.forEach((companyElement) => {
            const companyId = companyElement.value;
            if (companyId && !cityCache.has(`${companyId}-${DEFAULT_KSA_ID}`)) {
                // Preload on hover for even faster switching
                companyElement.addEventListener("mouseenter", () => {
                    if (!cityCache.has(`${companyId}-${DEFAULT_KSA_ID}`)) {
                        console.log(
                            `🔄 Hover preloading cities for company ${companyId}...`
                        );
                        fetchCitiesData(companyId, DEFAULT_KSA_ID)
                            .then((cities) => {
                                const cacheKey = `${companyId}-${DEFAULT_KSA_ID}`;
                                cityCache.set(cacheKey, {
                                    cities: cities,
                                    timestamp: Date.now(),
                                });
                                console.log(
                                    `✅ Hover preloaded cities for company ${companyId}: ${cities.length} cities`
                                );
                            })
                            .catch(() => {
                                // Silent fail for hover preloading
                            });
                    }
                });
            }
        });
    }

    // Immediate city preloading for better UX
    async function immediateCityPreload() {
        // Only preload if we have Saudi Arabia set as country
        const countrySelect = $country();
        if (countrySelect && countrySelect.value === DEFAULT_KSA_ID) {
            await preloadSaudiArabiaCities();
        }
    }

    // Cache management functions
    function clearCityCache() {
        cityCache.clear();
        loadingPromises.clear();
    }

    function clearSenderCache() {
        senderCache.clear();
        senderFormCache.clear();
        senderLoadingPromises.clear();
    }

    function getCacheStats() {
        return {
            cityCacheSize: cityCache.size,
            loadingPromises: loadingPromises.size,
            senderCacheSize: senderCache.size,
            senderFormCacheSize: senderFormCache.size,
            cacheKeys: Array.from(cityCache.keys()),
        };
    }

    window.setupLocationFields = setupLocationFields;
    window.handleCompanyRequirements = handleCompanyRequirements;
    window.populateSenderForm = populateSenderForm;
    window.loadSendersByCompany = loadSendersByCompany;
    window.loadSendersImmediately = loadSendersImmediately;
    window.immediateSenderLoad = immediateSenderLoad;
    window.preloadSendersForCompany = preloadSendersForCompany;
    window.preloadCitiesForAllCompanies = preloadCitiesForAllCompanies;
    window.preserveSenderSelection = preserveSenderSelection;
    window.restoreSenderSelection = restoreSenderSelection;
    window.clearCityCache = clearCityCache;
    window.clearSenderCache = clearSenderCache;
    window.getCacheStats = getCacheStats;
    window.immediateCityPreload = immediateCityPreload;

    function validateStep3Form() {
        console.log(
            `🔍 validateStep3Form called - currentStep: ${window.currentStep}`
        );

        // GUARD: If we're on step 2, don't run step 3 validation
        if (window.currentStep === 2) {
            console.log(
                `🚫 BLOCKED: validateStep3Form called on step 2 - returning true to prevent interference`
            );
            return true;
        }

        const root = STEP || document;
        const invalid = root.querySelector("#step-2 :invalid");
        if (invalid) {
            console.log(
                `❌ validateStep3Form: Found invalid element:`,
                invalid
            );
            return false;
        }
        const type =
            document.querySelector('input[name="sender_type"]:checked')
                ?.value || "auth";
        if (type === "existing") {
            const sel = document.getElementById("sender_select");
            if (!sel || !sel.value) {
                console.log(`❌ validateStep3Form: No sender selected`);
                return false;
            }
        }

        const requiredIds = [
            "user_name",
            "user_phone",
            "user_city",
            "user_address",
        ];
        for (const id of requiredIds) {
            const el = document.getElementById(id);
            if (!el) continue;
            const val = String(el.value || "").trim();
            if (!val) {
                console.log(
                    `❌ validateStep3Form: Required field ${id} is empty`
                );
                return false;
            }
        }
        console.log(`✅ validateStep3Form: All validations passed`);
        return true;
    }

    window.validateStep3Form = validateStep3Form;
    // Step 2 validation should only check company and method selection
    window.validateStep2Form = () => {
        const result = !!(window.selectedCompany && window.selectedMethod);
        console.log(
            `🔍 validateStep2Form: ${result}, company: ${!!window.selectedCompany}, method: ${!!window.selectedMethod}`
        );
        return result;
    };

    // Smart validation function that uses the correct validation based on current step
    window.smartValidateStep = () => {
        const currentStep = window.currentStep || 1;
        console.log(`🎯 smartValidateStep called for step ${currentStep}`);

        if (currentStep === 2) {
            // Step 2 validation is completely isolated - only check company and method
            const result = !!(window.selectedCompany && window.selectedMethod);
            console.log(`🔒 STEP 2 ISOLATED VALIDATION: ${result}`);
            return result;
        } else if (currentStep === 3) {
            return validateStep3Form();
        } else {
            return true; // Other steps are always valid
        }
    };

    document.addEventListener("DOMContentLoaded", () => {
        ensureAdditionalPhoneOptional();

        const root = STEP || document;
        root.querySelectorAll(
            "#step-2 input, #step-2 select, #step-2 textarea"
        ).forEach((el) => {
            if (!el.dataset.boundStep2Live) {
                el.addEventListener("input", () => {
                    if (typeof window.hardEnableNext === "function") {
                        const isValid = window.smartValidateStep();
                        window.hardEnableNext(isValid);
                    }
                });
                el.addEventListener("change", () => {
                    if (typeof window.hardEnableNext === "function") {
                        const isValid = window.smartValidateStep();
                        window.hardEnableNext(isValid);
                    }
                });
                el.dataset.boundStep2Live = "1";
            }
        });

        if (typeof window.hardEnableNext === "function") {
            const isValid = window.smartValidateStep();
            window.hardEnableNext(isValid);
        }
    });

    // Ultra-fast sender and city loading when company is selected
    document.addEventListener("shippingCompanySelected", () => {
        // Load senders immediately without any blocking operations
        immediateSenderLoad();

        // Load cities immediately for instant switching
        const companyIdValue = companyId();
        const countrySelect = $country();
        const countryIdValue = countrySelect ? countrySelect.value : null;
        if (companyIdValue && countryIdValue) {
            // Try to load cities instantly from cache
            loadCitiesByCompanyAndCountry(companyIdValue, countryIdValue);
        }

        // Setup location fields in background (completely non-blocking)
        if (window.requestIdleCallback) {
            requestIdleCallback(
                () => {
                    setupLocationFields();
                },
                { timeout: 100 }
            );
        } else {
            setTimeout(() => {
                setupLocationFields();
            }, 0);
        }
    });

    document.addEventListener("shippingMethodSelected", setupLocationFields);

    // Listen for global cities loaded event
    document.addEventListener("citiesLoaded", () => {
        // Populate sender city select if it exists and is visible
        const citySelect = $city();
        if (citySelect && citySelect.offsetParent !== null) {
            populateCitySelect(citySelect, window.currentCities());
        }
    });

    // Ultra-optimized loading when step 2 is shown
    document.addEventListener("stepChanged", (e) => {
        if (
            e &&
            e.detail &&
            (e.detail.currentStep === 2 || e.detail.currentStep === "2")
        ) {
            // Trigger immediate sender and city preload with minimal delay
            setTimeout(() => {
                immediateSenderLoad();
                immediateCityPreload();
                // Ultra-aggressive preloading for instant switching
                preloadCitiesForAllCompanies();
                preloadCitiesOnCompanyHover();
            }, 50); // Reduced delay for faster response
        }
    });

    // Ultra-aggressive preloading when page loads
    document.addEventListener("DOMContentLoaded", () => {
        console.log("🚀 DOM loaded, starting immediate city preloading...");

        // Start preloading immediately
        preloadCitiesForAllCompanies();
        preloadCitiesOnCompanyHover();

        // Also preload after a short delay to catch any dynamically loaded companies
        setTimeout(() => {
            console.log("🔄 Second wave of city preloading...");
            preloadCitiesForAllCompanies();
        }, 200);

        // Third wave after a longer delay to ensure everything is loaded
        setTimeout(() => {
            console.log("🔄 Final wave of city preloading...");
            preloadCitiesForAllCompanies();
        }, 1000);
    });

    document.addEventListener("DOMContentLoaded", () => {
        const senderSelect = $senderSelect();
        if (senderSelect) {
            senderSelect.addEventListener("change", function () {
                if (this.value) {
                    populateSenderForm(this.value);
                }
            });
        }
    });
})();
