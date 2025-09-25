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
    const $state = () => document.getElementById("user_state");
    const $city = () => document.getElementById("user_city");

    const requiredIds = [
        "user_name",
        "user_phone",
        "user_email",
        "user_country",
        "user_state",
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
                    await loadStates();
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

    async function loadStates() {
        const el = $state(),
            countryEl = $country();
        if (!el || !countryEl) return;

        el.required = true;
        const cid = String(countryEl.value || "");
        const comp = companyId();

        if (!cid || !comp) {
            fillSelect(el, [], {
                placeholder: window.translations?.select_state,
            });
            return;
        }

        // ⬇️ show loading
        fillSelect(el, [], {
            placeholder:
                window.translations?.loading_states ||
                (LOCALE === "ar"
                    ? "جاري تحميل المناطق..."
                    : "Loading states..."),
        });
        el.disabled = true;

        try {
            const data = await getJSON(API.states(cid, comp));
            const items = Array.isArray(data?.results)
                ? data.results
                : Array.isArray(data)
                ? data
                : [];
            fillSelect(el, items, {
                placeholder: window.translations?.select_state,
            });
            if (items.length === 1) el.value = String(items[0].id);
            await loadCities();
        } catch {
            fillSelect(el, [], {
                placeholder:
                    window.translations?.no_states_found || "No states",
            });
            fillSelect($city(), [], {
                placeholder:
                    window.translations?.no_cities_available || "No cities",
            });
        } finally {
            el.disabled = false;
        }
    }

    async function loadCities() {
        const el = $city(),
            s = $state(),
            c = $country();
        if (!el || !s || !c) return;

        el.required = true;
        const cid = String(c.value || "");
        const sid = String(s.value || "");
        const comp = companyId();

        if (!cid || !sid || !comp) {
            fillSelect(el, [], {
                placeholder: window.translations?.select_city,
            });
            return;
        }

        // ⬇️ show loading
        fillSelect(el, [], {
            placeholder:
                window.translations?.loading_cities ||
                (LOCALE === "ar" ? "جاري تحميل المدن..." : "Loading cities..."),
        });
        el.disabled = true;

        try {
            const data = await getJSON(API.cities(cid, sid, comp));
            const items = Array.isArray(data?.results)
                ? data.results
                : Array.isArray(data)
                ? data
                : [];
            fillSelect(el, items, {
                placeholder: window.translations?.select_city,
            });
            if (items.length === 1) el.value = String(items[0].id);
        } catch {
            fillSelect(el, [], {
                placeholder:
                    window.translations?.no_cities_available || "No cities",
            });
        } finally {
            el.disabled = false;
        }
    }

    function bindLocationChangeHandlers() {
        const c = $country(),
            s = $state();
        if (c && !c.dataset.bound) {
            c.addEventListener("change", loadStates);
            c.dataset.bound = "1";
        }
        if (s && !s.dataset.bound) {
            s.addEventListener("change", loadCities);
            s.dataset.bound = "1";
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
        [$country(), $state(), $city()].forEach((el) => {
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
        if ($country()?.value) await loadStates();
        if ($state()?.value) await loadCities();
        ensureAdditionalPhoneOptional();
    }

    window.setupLocationFields = setupLocationFields;
    window.handleCompanyRequirements = handleCompanyRequirements;

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
            "user_email",
            "user_country",
            "user_state",
            "user_city",
            "user_address",
        ];
        for (const id of requiredIds) {
            const el = document.getElementById(id);
            if (!el) return false;
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

        // Initial validation
        if (typeof window.hardEnableNext === "function")
            window.hardEnableNext(validateStep3Form());
    });

    document.addEventListener("shippingCompanySelected", setupLocationFields);
    document.addEventListener("shippingMethodSelected", setupLocationFields);
})();
