(() => {
    const SAUDI_ID = "65fd1a1c1fdbc094e3369b29";

    let appLocale = (
        document.documentElement?.lang ||
        document.querySelector("[data-app-locale]")?.dataset.appLocale ||
        window.APP_LOCALE ||
        "en"
    ).toLowerCase();

    window.selectedReceivers = Array.isArray(window.selectedReceivers)
        ? window.selectedReceivers
        : [];
    window._receiversMap = window._receiversMap || {};

    let initialExistingMap = {};
    let fetchedOnce = false;

    const API_KEY =
        document.querySelector('meta[name="ghaya-api-key"]')?.content ||
        (window.GHAYA_API_KEY ?? "qp4dz7u6m8ro8jx0txg9eqh7mcu5vvg0");

    const API_BASE =
        document.querySelector('meta[name="ghaya-api-base"]')?.content ||
        (window.GHAYA_API_BASE ??
            "https://ghaya-express-api-server-74ddc24b4e63.herokuapp.com/api");

    const API = {
        countries:
            window.API_ENDPOINTS?.countries ||
            `${API_BASE}/countries?page=0&pageSize=500`,
        states: (countryId, companyId) =>
            window.API_ENDPOINTS?.states?.(countryId, companyId) ||
            `${API_BASE}/states?pageSize=500&page=0&countryId=${encodeURIComponent(
                countryId
            )}&shippingCompanyId=${encodeURIComponent(companyId)}`,
        cities: (countryId, stateId, companyId) =>
            window.API_ENDPOINTS?.cities?.(countryId, stateId, companyId) ||
            `${API_BASE}/cities?pageSize=500&page=0&countryId=${encodeURIComponent(
                countryId
            )}&stateId=${encodeURIComponent(
                stateId
            )}&shippingCompanyId=${encodeURIComponent(companyId)}`,
    };

    const $country = () => document.getElementById("country");
    const $state = () => document.getElementById("state");
    const $city = () => document.getElementById("city");
    const $addBtn = () => document.getElementById("add-receiver-btn");
    const $existingRadio = () => document.getElementById("existing_receiver");
    const $newRadio = () => document.getElementById("new_receiver");
    const $receiverSelect = () => document.getElementById("receiver_select");
    const $newSection = () => document.getElementById("new_receiver_section");
    const $existingSection = () =>
        document.getElementById("existing_receiver_section");

    const toStr = (v) => (v == null ? "" : String(v));
    const t = (k, fb) =>
        (window.translations && window.translations[k]) || fb || k;

    function labelByLocale(nameObj) {
        if (!nameObj) return "";
        if (typeof nameObj === "string") return nameObj;
        if (appLocale === "ar" && (nameObj.ar || nameObj.AR))
            return nameObj.ar || nameObj.AR;
        if (nameObj.en || nameObj.EN) return nameObj.en || nameObj.EN;
        const vals = Object.values(nameObj);
        return typeof vals[0] === "string" ? vals[0] : "";
    }
    function displayName(maybeLocalized) {
        if (!maybeLocalized) return "";
        if (typeof maybeLocalized === "object")
            return labelByLocale(maybeLocalized);
        if (typeof maybeLocalized === "string") {
            const s = maybeLocalized.trim();
            if (s.startsWith("{") && s.endsWith("}")) {
                try {
                    return labelByLocale(JSON.parse(s));
                } catch {}
            }
            return s;
        }
        return String(maybeLocalized);
    }

    function toast(msg, type = "error") {
        if (typeof toastr !== "undefined") {
            (type === "error" ? toastr.error : toastr.success)(msg);
            return;
        }
        let box = document.getElementById("toast-box");
        if (!box) {
            box = document.createElement("div");
            box.id = "toast-box";
            box.style.position = "fixed";
            box.style.zIndex = "9999";
            box.style.top = "20px";
            if (appLocale === "ar") {
                box.style.left = "20px";
            } else {
                box.style.right = "20px";
            }
            document.body.appendChild(box);
        }
        const el = document.createElement("div");
        el.style.minWidth = "240px";
        el.style.marginBottom = "10px";
        el.style.padding = "10px 14px";
        el.style.borderRadius = "6px";
        el.style.color = type === "error" ? "#842029" : "#0f5132";
        el.style.background = type === "error" ? "#f8d7da" : "#d1e7dd";
        el.style.border =
            "1px solid " + (type === "error" ? "#f5c2c7" : "#badbcc");
        el.textContent = msg;
        box.appendChild(el);
        setTimeout(() => el.remove(), 2200);
    }

    function getJSON(url) {
        return fetch(url, {
            headers: { accept: "*/*", "x-api-key": API_KEY },
            credentials: "same-origin",
        }).then((r) => (r.ok ? r.json() : Promise.reject()));
    }

    function fillSelect(selectEl, items, { placeholder } = {}) {
        if (!selectEl) return;
        selectEl.innerHTML = "";
        if (placeholder) {
            const opt = document.createElement("option");
            opt.value = "";
            opt.textContent = placeholder;
            selectEl.appendChild(opt);
        }
        (items || []).forEach((it) => {
            const opt = document.createElement("option");
            opt.value = it.id || it._id || it.value || it.code || "";
            const label = it.label || it.name || it.title || "";
            opt.textContent = displayName(label) || String(label) || opt.value;
            selectEl.appendChild(opt);
        });
    }

    const REQUIRED_KEYS = [
        "name",
        "phone",
        "email",
        "address",
        "country_id",
        "state_id",
        "city_id",
    ];
    function validateReceiverFields(r) {
        const missing = REQUIRED_KEYS.filter(
            (k) => !String(r?.[k] ?? "").trim()
        );
        return missing.length === 0;
    }

    function normalizeReceiver(r) {
        const countryName = r.country?.name || r.country_name;
        const stateName = r.state?.name || r.state_name;
        const cityName = r.city?.name || r.city_name;
        const id =
            r.id ||
            r._id ||
            r.receiver_id ||
            r.receiverId ||
            `tmp_${Date.now()}`;
        const country_id =
            r.country_id ||
            r.countryId ||
            r.country ||
            (r.country && r.country.id) ||
            "";
        const state_id =
            r.state_id || r.stateId || r.state || (r.state && r.state.id) || "";
        const city_id =
            r.city_id || r.cityId || r.city || (r.city && r.city.id) || "";
        return {
            id: toStr(id),
            name: r.name || r.full_name || "",
            phone: r.phone || "",
            additional_phone: r.additional_phone || r.alt_phone || "",
            email: r.email || "",
            address: r.address || "",
            postal_code: r.postal_code || "",
            country_id: toStr(country_id),
            state_id: toStr(state_id),
            city_id: toStr(city_id),
            country_name: displayName(countryName),
            state_name: displayName(stateName),
            city_name: displayName(cityName),
        };
    }

    function getCompanyId() {
        return String(
            window.selectedCompany?.id || window.selectedCompany?._id || ""
        );
    }
    function getMethod() {
        return (window.selectedMethod || "").toLowerCase();
    }

    async function loadCountries(selectedId = "") {
        const el = $country();
        if (!el) return;
        fillSelect(el, [], {
            placeholder:
                window.translations?.loading_countries ||
                "Loading countries...",
        });
        el.disabled = true;
        const data = await getJSON(API.countries).catch(() => null);
        const items = Array.isArray(data?.results)
            ? data.results
            : Array.isArray(data)
            ? data
            : [];
        fillSelect(el, items, {
            placeholder:
                window.translations?.select_country || "Select Country",
        });
        el.disabled = false;
        if (selectedId) el.value = selectedId;
        else if (getMethod() === "local") el.value = SAUDI_ID;
        if (!el.dataset.bound) {
            el.addEventListener("change", async () => {
                $state().innerHTML = `<option value="">${t(
                    "select_state",
                    "Select State"
                )}</option>`;
                $city().innerHTML = `<option value="">${t(
                    "select_city",
                    "Select City"
                )}</option>`;
                $state().disabled = true;
                $city().disabled = true;
                if (el.value) await loadStates(el.value);
                updateAddButtonState();
            });
            el.dataset.bound = "1";
        }
        if (el.value) await loadStates(el.value);
    }

    async function loadStates(countryId, selectedId = "") {
        const el = $state();
        if (!el) return;
        const companyId = getCompanyId();
        if (!countryId || !companyId) {
            el.innerHTML = `<option value="">${t(
                "select_state",
                "Select State"
            )}</option>`;
            el.disabled = true;
            return;
        }
        el.innerHTML = `<option value="">${t(
            "loading_states",
            "Loading states..."
        )}</option>`;
        el.disabled = true;
        const data = await getJSON(API.states(countryId, companyId)).catch(
            () => null
        );
        const items = Array.isArray(data?.results)
            ? data.results
            : Array.isArray(data)
            ? data
            : [];
        el.innerHTML = `<option value="">${t(
            "select_state",
            "Select State"
        )}</option>`;
        items.forEach((s) => {
            const opt = document.createElement("option");
            opt.value = s.id || s._id;
            opt.textContent = displayName(s.name) || "";
            el.appendChild(opt);
        });
        el.disabled = false;
        el.value = selectedId || "";
        if (!el.dataset.bound) {
            el.addEventListener("change", async () => {
                const cityEl = $city();
                if (cityEl) {
                    cityEl.innerHTML = `<option value="">${t(
                        "select_city",
                        "Select City"
                    )}</option>`;
                    cityEl.disabled = true;
                }
                if (el.value) await loadCities($country().value, el.value);
                updateAddButtonState();
            });
            el.dataset.bound = "1";
        }
        if (el.value) await loadCities(countryId, el.value);
    }

    async function loadCities(countryId, stateId, selectedId = "") {
        const el = $city();
        if (!el) return;
        const companyId = getCompanyId();
        if (!countryId || !stateId || !companyId) {
            el.innerHTML = `<option value="">${t(
                "select_city",
                "Select City"
            )}</option>`;
            el.disabled = true;
            return;
        }
        el.innerHTML = `<option value="">${t(
            "loading_cities",
            "Loading cities..."
        )}</option>`;
        el.disabled = true;
        const data = await getJSON(
            API.cities(countryId, stateId, companyId)
        ).catch(() => null);
        const items = Array.isArray(data?.results)
            ? data.results
            : Array.isArray(data)
            ? data
            : [];
        el.innerHTML = `<option value="">${t(
            "select_city",
            "Select City"
        )}</option>`;
        items.forEach((c) => {
            const opt = document.createElement("option");
            opt.value = c.id || c._id;
            opt.textContent = displayName(c.name) || "";
            el.appendChild(opt);
        });
        el.disabled = false;
        el.value = selectedId || "";
    }

    async function setupReceiverFormByShippingType() {
        const c = $country(),
            s = $state(),
            ci = $city();
        if (!c || !s || !ci) return;
        c.required = s.required = ci.required = true;
        await loadCountries();
        if (getMethod() === "local") await loadStates(SAUDI_ID);
        ensureAdditionalPhoneOptional();
        updateAddButtonState();
    }

    function ensureReceiverStateFieldVisible() {
        const stateField = $state();
        if (stateField) {
            stateField.style.display = "block";
            stateField.style.visibility = "visible";
            stateField.style.opacity = "1";
            stateField.disabled = false;
        }
    }

    function populateReceiverSelect(receivers) {
        const select = $receiverSelect();
        if (!select) return;
        const chosenIds = new Set(
            (window.selectedReceivers || [])
                .filter((x) => !x.isNew)
                .map((x) => toStr(x.id))
        );
        select.innerHTML = `<option value="">${t(
            "choose_receiver",
            "Choose receiver"
        )}</option>`;
        window._receiversMap = {};
        receivers.forEach((r) => {
            const id = toStr(r.id || r._id);
            if (!id) return;
            window._receiversMap[id] = r;
            if (chosenIds.has(id)) return;
            const opt = document.createElement("option");
            opt.value = id;
            opt.textContent = r.name || r.full_name || r.email || id;
            select.appendChild(opt);
        });
    }

    function fetchJson(url, opts) {
        return fetch(url, opts)
            .then((r) => (r.ok ? r.json() : Promise.reject()))
            .catch(() => []);
    }

    function loadReceivers() {
        const select = $receiverSelect();
        if (!select) return;
        fetchJson("/receivers")
            .then((data) => {
                const arr = Array.isArray(data)
                    ? data
                    : (data && data.results) || [];
                if (!fetchedOnce) {
                    initialExistingMap = {};
                    arr.forEach((r) => {
                        const id = toStr(r.id || r._id);
                        if (id) initialExistingMap[id] = r;
                    });
                    fetchedOnce = true;
                }
                populateReceiverSelect(arr);
            })
            .catch(() => {
                select.innerHTML = `<option value="">${t(
                    "no_receivers_found",
                    "No receivers found"
                )}</option>`;
            });

        refreshSelectedReceiversView();
        syncNextButton();
        updateAddButtonState();
    }

    function canAdd(item) {
        const id = toStr(item.id);
        if (id && window.selectedReceivers.some((r) => toStr(r.id) === id))
            return false;
        const phone = (item.phone || "").trim();
        const email = (item.email || "").trim().toLowerCase();
        const name = (item.name || "").trim().toLowerCase();
        const dup = (r) => {
            const rp = (r.phone || "").trim();
            const re = (r.email || "").trim().toLowerCase();
            const rn = (r.name || "").trim().toLowerCase();
            if (phone && rp && phone === rp) return true;
            if (email && re && email === re) return true;
            if (name && rn && phone && rp && name === rn && phone === rp)
                return true;
            return false;
        };
        if (window.selectedReceivers.some(dup)) return false;
        return true;
    }

    function collectReceiverFromForm(existingId = "") {
        return {
            id: existingId || `tmp_${Date.now()}`,
            isNew: !existingId,
            name: valueOf("name"),
            phone: valueOf("phone"),
            additional_phone: valueOf("additional_phone"),
            email: valueOf("email"),
            address: valueOf("address"),
            postal_code: valueOf("postal_code"),
            country_id: valueOf("country"),
            state_id: valueOf("state"),
            city_id: valueOf("city"),
            country_name: displayName(textOf("country")),
            state_name: displayName(textOf("state")),
            city_name: displayName(textOf("city")),
        };
    }

    function addExisting() {
        const select = $receiverSelect();
        const existingId = toStr(select ? select.value : "");
        if (!existingId || !window._receiversMap[existingId]) {
            toast(t("select_receiver", "Please select a receiver"));
            return;
        }
        const item = collectReceiverFromForm(existingId);
        if (!validateReceiverFields(item)) {
            toast(
                t(
                    "add_receiver_error",
                    "Please fill all required receiver fields"
                )
            );
            updateAddButtonState();
            return;
        }
        if (!canAdd(item)) {
            toast(t("receiver_already_selected", "Receiver already selected"));
            return;
        }
        pushReceiver(item);
        const opt = select.querySelector(
            `option[value="${CSS.escape(existingId)}"]`
        );
        if (opt) opt.remove();
        select.value = "";
        resetReceiverTypeSelection();
        toast(t("receiver_added", "Receiver added"), "success");
    }

    function addNew() {
        const item = collectReceiverFromForm("");
        if (!validateReceiverFields(item)) {
            toast(
                t(
                    "add_receiver_error",
                    "Please fill all required receiver fields"
                )
            );
            updateAddButtonState();
            return;
        }
        if (!canAdd(item)) {
            toast(t("receiver_already_selected", "Receiver already selected"));
            return;
        }
        pushReceiver(item);
        clearReceiverForm();
        resetReceiverTypeSelection();
        toast(t("receiver_added", "Receiver added"), "success");
    }

    function pushReceiver(item) {
        window.selectedReceivers.push(item);
        writeHiddenReceivers();
        refreshSelectedReceiversView();
        syncNextButton();
        document.dispatchEvent(new CustomEvent("receiversChanged"));
        updateAddButtonState();
    }

    function removeReceiverAt(index) {
        const removed = window.selectedReceivers.splice(index, 1)[0];
        writeHiddenReceivers();
        refreshSelectedReceiversView();
        syncNextButton();
        document.dispatchEvent(new CustomEvent("receiversChanged"));
        if (removed && !removed.isNew) {
            const original = initialExistingMap[toStr(removed.id)];
            if (original) {
                const select = $receiverSelect();
                if (
                    select &&
                    !select.querySelector(
                        `option[value="${CSS.escape(removed.id)}"]`
                    )
                ) {
                    const opt = document.createElement("option");
                    opt.value = toStr(removed.id);
                    opt.textContent =
                        original.name ||
                        original.full_name ||
                        original.email ||
                        removed.id;
                    select.appendChild(opt);
                }
            }
        }
        updateAddButtonState();
    }

    function valueOf(id) {
        const el = document.getElementById(id);
        return el ? el.value : "";
    }
    function textOf(id) {
        const el = document.getElementById(id);
        if (!el) return "";
        if (el.tagName === "SELECT") {
            const opt = el.options[el.selectedIndex];
            return opt ? opt.textContent : "";
        }
        return el.value || "";
    }

    function writeHiddenReceivers() {
        const hidden = document.getElementById("selected_receivers_hidden");
        if (hidden)
            hidden.value = JSON.stringify(window.selectedReceivers || []);
    }

    function refreshSelectedReceiversView() {
        const box = document.getElementById("receivers-container");
        if (!box) return;
        if (!window.selectedReceivers.length) {
            box.style.display = "none";
            box.innerHTML = "";
            return;
        }
        box.style.display = "block";
        const titleR = t("receiver", "Receiver");
        const phoneL = t("phone", "Phone");
        const cityL = t("city", "City");
        const addrL = t("address", "Address");
        let html = "";
        window.selectedReceivers.forEach((r, idx) => {
            const cityTxt = displayName(r.city_name);
            html += `
        <div class="card mb-2">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
              <div>
                <div><strong>${titleR} #${idx + 1}:</strong> ${r.name || ""}${
                r.isNew ? " • NEW" : ""
            }</div>
                <div><strong>${phoneL}:</strong> ${r.phone || ""}</div>
                <div><strong>${cityL}:</strong> ${cityTxt}</div>
                <div><strong>${addrL}:</strong> ${r.address || ""}</div>
              </div>
              <button type="button" class="btn btn-sm btn-outline-danger" data-index="${idx}">&times;</button>
            </div>
          </div>
        </div>`;
        });
        box.innerHTML = html;
        box.querySelectorAll("button.btn-outline-danger").forEach((btn) => {
            if (!btn.dataset.bound) {
                btn.addEventListener("click", function () {
                    const i = parseInt(this.dataset.index, 10);
                    if (!isNaN(i)) removeReceiverAt(i);
                });
                btn.dataset.bound = "1";
            }
        });
    }

    function canProceedToNextStep() {
        return (
            Array.isArray(window.selectedReceivers) &&
            window.selectedReceivers.length > 0
        );
    }

    function syncNextButton() {
        const btnNext = document.getElementById("btn-next");
        if (!btnNext) return;
        const ok = canProceedToNextStep();
        btnNext.disabled = !ok;
        btnNext.classList.toggle("btn-secondary", !ok);
        btnNext.classList.toggle("btn-primary", ok);
    }

    async function populateReceiverForm(receiverId) {
        const r =
            (window._receiversMap && window._receiversMap[receiverId]) ||
            (window.selectedReceivers || []).find(
                (x) => toStr(x.id) === toStr(receiverId)
            );
        if (!r) return;
        const n = normalizeReceiver(r);
        setIf("name", n.name);
        setIf("phone", n.phone);
        setIf("additional_phone", n.additional_phone);
        setIf("email", n.email);
        setIf("address", n.address);
        setIf("postal_code", n.postal_code);
        await loadCountries(n.country_id || "");
        if (n.country_id) {
            await loadStates(n.country_id, n.state_id || "");
            if (n.state_id)
                await loadCities(n.country_id, n.state_id, n.city_id || "");
        } else if (getMethod() === "local") {
            await loadStates(SAUDI_ID);
        }
        ensureReceiverStateFieldVisible();
        ensureAdditionalPhoneOptional();
        const formSection = $newSection();
        if (formSection) formSection.style.display = "block";
        updateAddButtonState();
    }

    function clearReceiverForm() {
        [
            "name",
            "phone",
            "additional_phone",
            "email",
            "address",
            "postal_code",
        ].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.value = "";
        });
        const c = $country(),
            s = $state(),
            ci = $city();
        if (c) {
            c.innerHTML = `<option value="">${t(
                "select_country",
                "Select Country"
            )}</option>`;
            c.value = "";
        }
        if (s) {
            s.innerHTML = `<option value="">${t(
                "select_state",
                "Select State"
            )}</option>`;
            s.value = "";
            s.disabled = true;
        }
        if (ci) {
            ci.innerHTML = `<option value="">${t(
                "select_city",
                "Select City"
            )}</option>`;
            ci.value = "";
            ci.disabled = true;
        }
        ensureAdditionalPhoneOptional();
        updateAddButtonState();
    }

    function resetFormCompletely() {
        clearReceiverForm();
        const existingRadio = $existingRadio();
        const newRadio = $newRadio();
        if (existingRadio) existingRadio.checked = false;
        if (newRadio) newRadio.checked = false;
        const receiverSelect = $receiverSelect();
        if (receiverSelect) receiverSelect.value = "";
        const existingSection = $existingSection();
        const newSection = $newSection();
        if (existingSection) existingSection.style.display = "none";
        if (newSection) newSection.style.display = "none";
        updateAddButtonState();
        syncNextButton();
    }
    function resetReceiverTypeSelection() {
        resetFormCompletely();
    }

    function setIf(id, val) {
        const el = document.getElementById(id);
        if (el != null) el.value = val ?? "";
    }

    function isFormComplete() {
        const requiredIds = [
            "name",
            "phone",
            "email",
            "address",
            "country",
            "state",
            "city",
        ];
        return requiredIds.every((id) => String(valueOf(id) || "").trim());
    }

    function updateAddButtonState() {
        const btn = $addBtn();
        if (!btn) return;
        const modeExisting = $existingRadio()?.checked;
        const modeNew = $newRadio()?.checked;
        let enable = false;
        if (modeExisting) {
            const selectedId = $receiverSelect()?.value || "";
            enable = !!selectedId && isFormComplete();
        } else if (modeNew) {
            enable = isFormComplete();
        } else {
            enable = false;
        }
        btn.disabled = !enable;
        btn.classList.toggle("btn-secondary", !enable);
        btn.classList.toggle("btn-success", enable);
        syncNextButton();
    }

    function bindFormFieldListeners() {
        const root = $newSection() || document;
        ["input", "select", "textarea"].forEach((tag) => {
            root.querySelectorAll(tag).forEach((el) => {
                if (!el.dataset.receiverBind) {
                    el.addEventListener("input", updateAddButtonState);
                    el.addEventListener("change", updateAddButtonState);
                    el.dataset.receiverBind = "1";
                }
            });
        });
        const rsel = $receiverSelect();
        if (rsel && !rsel.dataset.receiverBind) {
            rsel.addEventListener("change", () => {
                const id = rsel.value;
                if (id && $existingRadio()?.checked) {
                    const formSection = $newSection();
                    if (formSection) formSection.style.display = "block";
                    populateReceiverForm(id);
                }
                updateAddButtonState();
            });
            rsel.dataset.receiverBind = "1";
        }
        ensureAdditionalPhoneOptional();
    }

    function ensureAdditionalPhoneOptional() {
        const ap = document.getElementById("additional_phone");
        if (ap) {
            ap.required = false;
            ap.removeAttribute("required");
        }
    }

    function wireUI() {
        const addBtn = $addBtn();
        const receiverSelect = $receiverSelect();
        const existingRadio = $existingRadio();
        const newRadio = $newRadio();
        const newSection = $newSection();
        const existingSection = $existingSection();

        function forceClearNewForm() {
            clearReceiverForm();
        }
        function resetExistingSelect() {
            if (receiverSelect) receiverSelect.value = "";
        }

        if (existingRadio && !existingRadio.dataset.bound) {
            existingRadio.addEventListener("change", function () {
                if (!this.checked) return;
                if (existingSection) existingSection.style.display = "block";
                if (newSection) newSection.style.display = "none";
                forceClearNewForm();
                resetExistingSelect();
                loadReceivers(); // <-- load list when choosing "Existing"
                ensureAdditionalPhoneOptional();
                updateAddButtonState();
            });
            existingRadio.dataset.bound = "1";
        }

        if (newRadio && !newRadio.dataset.bound) {
            newRadio.addEventListener("change", async function () {
                if (!this.checked) return;
                if (existingSection) existingSection.style.display = "none";
                if (newSection) newSection.style.display = "block";
                forceClearNewForm();
                resetExistingSelect();
                await setupReceiverFormByShippingType();
                ensureReceiverStateFieldVisible();
                ensureAdditionalPhoneOptional();
                updateAddButtonState();
            });
            newRadio.dataset.bound = "1";
        }

        if (addBtn && !addBtn.dataset.bound) {
            addBtn.addEventListener("click", () => {
                if ($existingRadio()?.checked) return addExisting();
                if ($newRadio()?.checked) return addNew();
                toast(
                    t("select_receiver", "Please choose receiver type"),
                    "error"
                );
            });
            addBtn.dataset.bound = "1";
        }

        bindFormFieldListeners();
        ensureAdditionalPhoneOptional();
        updateAddButtonState();
    }

    window.loadReceivers = loadReceivers;
    window.populateReceiverForm = populateReceiverForm;
    window.ensureReceiverStateFieldVisible = ensureReceiverStateFieldVisible;
    window.setupReceiverFormByShippingType = setupReceiverFormByShippingType;
    window.canProceedToNextStep = () =>
        Array.isArray(window.selectedReceivers) &&
        window.selectedReceivers.length > 0;

    // Bind immediately so radios work as soon as Step 3 is shown
    document.addEventListener("DOMContentLoaded", () => {
        wireUI(); // <-- crucial: bind the radio handlers now
        resetFormCompletely();
        if (window.selectedMethod) setupReceiverFormByShippingType();
        ensureAdditionalPhoneOptional();
        updateAddButtonState();
        syncNextButton();
    });

    // Also (optionally) re-bind when step changes to 3
    document.addEventListener("stepChanged", (e) => {
        if (e?.detail?.currentStep === 3 || e?.detail?.currentStep === "3") {
            wireUI();
            updateAddButtonState();
            syncNextButton();
        }
    });

    document.addEventListener("shippingMethodSelected", () => {
        setupReceiverFormByShippingType();
        ensureAdditionalPhoneOptional();
        updateAddButtonState();
        syncNextButton();
    });
})();
