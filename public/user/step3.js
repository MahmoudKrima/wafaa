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
            select.appendChild(opt);
        });
    }

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

        fetch(`/senders-by-company/${companyIdValue}`, {
            headers: {
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            credentials: "same-origin",
        })
            .then((response) => response.json())
            .then((data) => {
                const senders = Array.isArray(data) ? data : [];
                fillSenderSelect(senders);
                select.disabled = false;
            })
            .catch(() => {
                select.innerHTML = `<option value="">${
                    window.translations?.no_senders_found || "No senders found"
                }</option>`;
                select.disabled = false;
            });
    }

    async function populateSenderForm(senderId) {
        const select = $senderSelect();
        if (!select) return;

        try {
            // Fetch full sender details
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
            
            // Populate basic fields
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

            if (companyIdValue) {
                await loadCitiesByCompanyAndCountry(
                    companyIdValue,
                    DEFAULT_KSA_ID,
                    senderCityId
                );
            }
            
            // Preserve the sender selection in the dropdown
            select.value = senderId;
            
            // Update Select2 if it's initialized
            if (typeof $ !== "undefined" && $(select).data('select2')) {
                $(select).val(senderId).trigger('change.select2');
            }
        } catch (error) {
            console.error("Error populating sender form:", error);
            const selectedOption = select.querySelector(
                `option[value="${senderId}"]`
            );
            if (selectedOption) {
                const nameField = document.getElementById("user_name");
                if (nameField) nameField.value = selectedOption.textContent;
            }
            
            // Still preserve the selection even on error
            select.value = senderId;
            if (typeof $ !== "undefined" && $(select).data('select2')) {
                $(select).val(senderId).trigger('change.select2');
            }
        } finally {
            // Trigger validation multiple times to ensure it catches all updates
            if (typeof window.hardEnableNext === "function") {
                // First immediate validation
                setTimeout(() => {
                    const isValid = validateStep3Form();
                    window.hardEnableNext(isValid);
                }, 100);
                
                // Second validation after Select2 updates
                setTimeout(() => {
                    const isValid = validateStep3Form();
                    window.hardEnableNext(isValid);
                }, 400);
                
                // Final validation to be sure
                setTimeout(() => {
                    const isValid = validateStep3Form();
                    window.hardEnableNext(isValid);
                }, 800);
            }
        }
    }

    async function loadCitiesByCompanyAndCountry(
        companyId,
        countryId,
        selectedCityId = ""
    ) {
        const citySelect = $city();
        if (!citySelect) return;

        if (!companyId || !countryId) {
            citySelect.innerHTML = `<option value="">${
                window.translations?.select_city || "Select City"
            }</option>`;
            citySelect.disabled = true;
            
            // Reinitialize Select2 if initialized
            if (typeof $ !== "undefined" && $(citySelect).data('select2')) {
                $(citySelect).select2('destroy');
                $(citySelect).select2({
                    placeholder: window.translations?.select_city || 'Select City',
                    allowClear: true,
                    width: '100%',
                    disabled: true
                });
            }
            return;
        }

        citySelect.innerHTML = `<option value="">${
            window.translations?.loading_cities || "Loading cities..."
        }</option>`;
        citySelect.disabled = true;
        
        // Update Select2 to show loading state
        if (typeof $ !== "undefined" && $(citySelect).data('select2')) {
            // Destroy and reinitialize to show loading text
            $(citySelect).select2('destroy');
            $(citySelect).select2({
                placeholder: window.translations?.loading_cities || 'Loading cities...',
                allowClear: false,
                width: '100%',
                disabled: true
            });
        }

        try {
            const response = await fetch(
                `/cities-by-company-and-country/${companyId}`,
                {
                    headers: {
                        "X-CSRF-TOKEN":
                            document.querySelector('meta[name="csrf-token"]')
                                ?.content || "",
                        Accept: "application/json",
                        "Content-Type": "application/json",
                    },
                    credentials: "same-origin",
                }
            );

            if (!response.ok) {
                throw new Error("Failed to fetch cities");
            }

            const data = await response.json();
            const cities = Array.isArray(data?.results)
                ? data.results
                : Array.isArray(data)
                ? data
                : [];

            citySelect.innerHTML = `<option value="">${
                window.translations?.select_city || "Select City"
            }</option>`;

            cities.forEach((city) => {
                const opt = document.createElement("option");
                opt.value = city.id || city._id || "";
                opt.textContent = labelFrom(city);
                citySelect.appendChild(opt);
            });

            citySelect.disabled = false;

            // Check if Select2 is initialized
            const isSelect2 = typeof $ !== "undefined" && $(citySelect).data('select2');

            // Set the selected city if provided
            if (selectedCityId) {
                // First try exact match
                citySelect.value = selectedCityId;
                
                // If exact match didn't work, try partial matching
                if (citySelect.value !== selectedCityId) {
                    const options = Array.from(citySelect.options);
                    const partialMatch = options.find(
                        (opt) =>
                            opt.value.includes(selectedCityId) ||
                            selectedCityId.includes(opt.value)
                    );
                    if (partialMatch) {
                        citySelect.value = partialMatch.value;
                    }
                }
            }

            // Reinitialize Select2 with new options and selected value
            if (isSelect2) {
                // Destroy and reinitialize Select2 to ensure options are properly loaded
                $(citySelect).select2('destroy');
                $(citySelect).select2({
                    placeholder: window.translations?.choose_city || 'Choose City',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    closeOnSelect: true,
                    cache: true,
                    language: {
                        noResults: function() {
                            return window.translations?.no_cities_available || 'No cities available';
                        },
                        searching: function() {
                            return window.translations?.searching || 'Searching...';
                        }
                    }
                });
                
                // Set the value again after reinitializing
                if (citySelect.value) {
                    $(citySelect).val(citySelect.value).trigger('change.select2');
                }
            }
            
            // Trigger validation after city is set
            setTimeout(() => {
                if (typeof window.hardEnableNext === "function") {
                    const isValid = validateStep3Form();
                    window.hardEnableNext(isValid);
                }
            }, 200);
        } catch (error) {
            console.error("Error loading cities:", error);
            citySelect.innerHTML = `<option value="">${
                window.translations?.error_loading_cities ||
                "Error loading cities"
            }</option>`;
            citySelect.disabled = false;
            
            // Reinitialize Select2 to show error state
            if (typeof $ !== "undefined" && $(citySelect).data('select2')) {
                $(citySelect).select2('destroy');
                $(citySelect).select2({
                    placeholder: window.translations?.error_loading_cities || 'Error loading cities',
                    allowClear: true,
                    width: '100%'
                });
            }
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

            if (method() === "local") {
                const ksaId = findKSAId(items);
                if ([...el.options].some((o) => o.value === ksaId)) {
                    el.value = ksaId;
                    // Load cities directly after setting country
                    const comp = companyId();
                    if (comp) {
                        await loadCitiesByCompanyAndCountry(comp, ksaId);
                    }
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
                if (comp && countryId) {
                    await loadCitiesByCompanyAndCountry(comp, countryId);
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

    async function setupLocationFields() {
        handleCompanyRequirements();
        bindLocationChangeHandlers();
        await loadCountries();
        ensureAdditionalPhoneOptional();
        loadSendersByCompany();
    }

    window.setupLocationFields = setupLocationFields;
    window.handleCompanyRequirements = handleCompanyRequirements;
    window.populateSenderForm = populateSenderForm;
    window.loadSendersByCompany = loadSendersByCompany;

    function validateStep3Form() {
        const root = STEP || document;
        const invalid = root.querySelector("#step-2 :invalid");
        if (invalid) return false;
        const type =
            document.querySelector('input[name="sender_type"]:checked')
                ?.value || "auth";
        if (type === "existing") {
            const sel = document.getElementById("sender_select");
            if (!sel || !sel.value) return false;
        }

        const requiredIds = [
            "user_name",
            "user_phone",
            "user_country",
            "user_city",
            "user_address",
        ];
        for (const id of requiredIds) {
            const el = document.getElementById(id);
            if (!el) continue;
            const val = String(el.value || "").trim();
            if (!val) return false;
        }
        return true;
    }

    window.validateStep3Form = validateStep3Form;
    window.validateStep2Form = validateStep3Form;

    document.addEventListener("DOMContentLoaded", () => {
        ensureAdditionalPhoneOptional();

        const root = STEP || document;
        root.querySelectorAll(
            "#step-2 input, #step-2 select, #step-2 textarea"
        ).forEach((el) => {
            if (!el.dataset.boundStep2Live) {
                el.addEventListener("input", () => {
                    if (typeof window.hardEnableNext === "function")
                        window.hardEnableNext(validateStep3Form());
                });
                el.addEventListener("change", () => {
                    if (typeof window.hardEnableNext === "function")
                        window.hardEnableNext(validateStep3Form());
                });
                el.dataset.boundStep2Live = "1";
            }
        });

        if (typeof window.hardEnableNext === "function")
            window.hardEnableNext(validateStep3Form());
    });

    document.addEventListener("shippingCompanySelected", setupLocationFields);
    document.addEventListener("shippingMethodSelected", setupLocationFields);

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
