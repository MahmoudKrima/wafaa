(() => {
    const SAUDI_ID = "65fd1a1c1fdbc094e3369b29";

    // locale helpers
    let appLocale = (
        document.documentElement?.lang ||
        document.querySelector("[data-app-locale]")?.dataset.appLocale ||
        window.APP_LOCALE ||
        "en"
    ).toLowerCase();

    // global state
    window.selectedReceivers = Array.isArray(window.selectedReceivers)
        ? window.selectedReceivers
        : [];
    window._receiversMap = window._receiversMap || {};

    // keep original fetched receivers so we can add them back if removed
    let initialExistingMap = {};
    let fetchedOnce = false;

    // ----------------- utils -----------------
    const toStr = (v) => (v == null ? "" : String(v));

    function t(k, fb) {
        return (window.translations && window.translations[k]) || fb || k;
    }

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
                box.style.right = "auto";
            } else {
                box.style.right = "20px";
                box.style.left = "auto";
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

    function fetchJson(url, opts) {
        return fetch(url, opts)
            .then((r) => (r.ok ? r.json() : Promise.reject()))
            .catch(() => []);
    }

    // ----------------- validation -----------------
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
        if (missing.length) {
            toast(
                t(
                    "add_receiver_error",
                    "Please fill all required receiver fields"
                ) + `: ${missing.join(", ")}`
            );
            return false;
        }
        return true;
    }

    // ----------------- normalize -----------------
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

    // ----------------- Step 4 data flow -----------------
    function loadReceivers() {
        const select = document.getElementById("receiver_select");
        if (!select) return;

        fetchJson("/receivers")
            .then((data) => {
                const arr = Array.isArray(data)
                    ? data
                    : (data && data.results) || [];
                if (!fetchedOnce) {
                    // preserve original map for adding back after remove
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

        wireUI();
        refreshSelectedReceiversView();
        syncNextButton();
    }

    function populateReceiverSelect(receivers) {
        const select = document.getElementById("receiver_select");
        if (!select) return;

        // Filter out already-selected existing receivers
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
            if (chosenIds.has(id)) return; // hide already selected
            const opt = document.createElement("option");
            opt.value = id;
            opt.textContent = r.name || r.full_name || r.email || id;
            select.appendChild(opt);
        });
    }

    // ----------------- add/remove -----------------
    function canAdd(item) {
        const id = toStr(item.id);
        if (id && window.selectedReceivers.some((r) => toStr(r.id) === id)) {
            toast(t("receiver_already_selected", "Receiver already selected"));
            return false;
        }
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
        if (window.selectedReceivers.some(dup)) {
            toast(t("receiver_already_selected", "Receiver already selected"));
            return false;
        }
        return true;
    }

    function addExisting() {
        const select = document.getElementById("receiver_select");
        const id = toStr(select ? select.value : "");
        if (!id || !window._receiversMap[id]) {
            toast(t("select_receiver", "Please select a receiver"));
            return;
        }
        const raw = window._receiversMap[id];
        const item = normalizeReceiver(raw);
        item.isNew = false;

        if (!validateReceiverFields(item)) return;
        if (!canAdd(item)) return;

        pushReceiver(item);

        // remove from dropdown to avoid duplicates
        const opt = select.querySelector(`option[value="${CSS.escape(id)}"]`);
        if (opt) opt.remove();
        if (select) select.value = "";

        resetReceiverTypeSelection();
        toast(t("receiver_added", "Receiver added"), "success");
    }

    function addNew() {
        const item = {
            id: `tmp_${Date.now()}`,
            isNew: true,
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

        if (!validateReceiverFields(item)) return;
        if (!canAdd(item)) return;

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
    }

    function removeReceiverAt(index) {
        const removed = window.selectedReceivers.splice(index, 1)[0];
        writeHiddenReceivers();
        refreshSelectedReceiversView();
        syncNextButton();
        document.dispatchEvent(new CustomEvent("receiversChanged"));

        // if it was an existing fetched receiver, add back to dropdown
        if (removed && !removed.isNew) {
            const original = initialExistingMap[toStr(removed.id)];
            if (original) {
                const select = document.getElementById("receiver_select");
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
    }

    // ----------------- form helpers -----------------
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

    // ----------------- country/state/city loaders -----------------
    function ensureReceiverStateFieldVisible() {
        const stateField = document.getElementById("state");
        if (stateField) {
            stateField.style.display = "block";
            stateField.style.visibility = "visible";
            stateField.style.opacity = "1";
            stateField.disabled = false;
        }
    }

    function setupReceiverFormByShippingType() {
        const countryField = document.getElementById("country");
        const stateField = document.getElementById("state");
        const cityField = document.getElementById("city");
        if (!countryField || !stateField || !cityField) return;

        const isInternational = window.selectedMethod === "international";

        if (isInternational) {
            loadCountries();
            stateField.innerHTML = `<option value="">${t(
                "select_state",
                "Select State"
            )}</option>`;
            stateField.disabled = true;
            cityField.innerHTML = `<option value="">${t(
                "select_city",
                "Select City"
            )}</option>`;
            cityField.disabled = true;
        } else {
            loadCountriesForLocalShipping();
            stateField.innerHTML = `<option value="">${t(
                "select_state",
                "Select State"
            )}</option>`;
            cityField.innerHTML = `<option value="">${t(
                "select_city",
                "Select City"
            )}</option>`;
            cityField.disabled = true;
            setTimeout(() => {
                if (window.selectedMethod === "local")
                    loadStatesByCountry(SAUDI_ID, null);
            }, 200);
        }
    }

    function loadCountries() {
        const countryField = document.getElementById("country");
        if (!countryField) return;
        fetchJson(
            "https://ghaya-express-staging-af597af07557.herokuapp.com/api/countries?page=0&pageSize=200&orderColumn=createdAt&orderDirection=desc",
            {
                headers: {
                    accept: "*/*",
                    "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                },
            }
        )
            .then((data) => {
                const list = (data && data.results) || [];
                countryField.innerHTML = `<option value="">${t(
                    "select_country",
                    "Select Country"
                )}</option>`;
                list.forEach((c) => {
                    const opt = document.createElement("option");
                    opt.value = c.id || c._id;
                    opt.textContent = displayName(c.name) || c.code || "";
                    countryField.appendChild(opt);
                });
                countryField.disabled = false;
            })
            .catch(() => {
                countryField.innerHTML = `<option value="">${t(
                    "error_loading_countries",
                    "Error loading countries"
                )}</option>`;
                countryField.disabled = true;
            });

        if (!countryField.dataset.bound) {
            countryField.addEventListener("change", function () {
                const v = this.value;
                const stateField = document.getElementById("state");
                const cityField = document.getElementById("city");
                if (stateField) {
                    stateField.disabled = true;
                    stateField.innerHTML = `<option value="">${t(
                        "select_state",
                        "Select State"
                    )}</option>`;
                }
                if (cityField) {
                    cityField.disabled = true;
                    cityField.innerHTML = `<option value="">${t(
                        "select_city",
                        "Select City"
                    )}</option>`;
                }
                if (v) loadStatesByCountry(v, null);
            });
            countryField.dataset.bound = "1";
        }
    }

    function loadCountriesForLocalShipping() {
        const countryField = document.getElementById("country");
        if (!countryField) return;
        fetchJson(
            "https://ghaya-express-staging-af597af07557.herokuapp.com/api/countries?page=0&pageSize=200&orderColumn=createdAt&orderDirection=desc",
            {
                headers: {
                    accept: "*/*",
                    "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                },
            }
        )
            .then((data) => {
                const list = (data && data.results) || [];
                countryField.innerHTML = `<option value="">${t(
                    "select_country",
                    "Select Country"
                )}</option>`;
                list.forEach((c) => {
                    const opt = document.createElement("option");
                    opt.value = c.id || c._id;
                    opt.textContent = displayName(c.name) || c.code || "";
                    if ((c.id || c._id) === SAUDI_ID) {
                        opt.selected = true;
                        setTimeout(
                            () => loadStatesByCountry(SAUDI_ID, null),
                            100
                        );
                    }
                    countryField.appendChild(opt);
                });
                countryField.disabled = true; // local shipping locks to KSA
            })
            .catch(() => {
                countryField.innerHTML = `<option value="">${t(
                    "error_loading_countries",
                    "Error loading countries"
                )}</option>`;
                countryField.disabled = true;
            });
    }

    function loadCountriesForExistingReceiver(selectedCountryId) {
        const countryField = document.getElementById("country");
        if (!countryField) return;
        fetchJson(
            "https://ghaya-express-staging-af597af07557.herokuapp.com/api/countries?page=0&pageSize=200&orderColumn=createdAt&orderDirection=desc",
            {
                headers: {
                    accept: "*/*",
                    "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                },
            }
        )
            .then((data) => {
                const list = (data && data.results) || [];
                countryField.innerHTML = `<option value="">${t(
                    "select_country",
                    "Select Country"
                )}</option>`;
                list.forEach((c) => {
                    const opt = document.createElement("option");
                    opt.value = c.id || c._id;
                    opt.textContent = displayName(c.name) || c.code || "";
                    if ((c.id || c._id) === selectedCountryId)
                        opt.selected = true;
                    countryField.appendChild(opt);
                });
                countryField.disabled = false;
            })
            .catch(() => {
                countryField.innerHTML = `<option value="">${t(
                    "error_loading_countries",
                    "Error loading countries"
                )}</option>`;
                countryField.disabled = true;
            });
    }

    function loadStatesByCountry(countryId, selectedStateId = null) {
        const stateField = document.getElementById("state");
        if (!stateField) return;
        fetchJson(
            `https://ghaya-express-staging-af597af07557.herokuapp.com/api/states?page=0&pageSize=200&orderColumn=createdAt&orderDirection=desc&countryId=${encodeURIComponent(
                countryId
            )}`,
            {
                headers: {
                    accept: "*/*",
                    "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                },
            }
        )
            .then((data) => {
                const list = (data && data.results) || [];
                stateField.innerHTML = `<option value="">${t(
                    "select_state",
                    "Select State"
                )}</option>`;
                list.forEach((st) => {
                    const opt = document.createElement("option");
                    opt.value = st.id || st._id;
                    opt.textContent = displayName(st.name) || "";
                    if (
                        selectedStateId &&
                        (st.id === selectedStateId ||
                            st._id === selectedStateId)
                    )
                        opt.selected = true;
                    stateField.appendChild(opt);
                });
                stateField.disabled = false;
            })
            .catch(() => {
                stateField.innerHTML = `<option value="">${t(
                    "error_loading_states",
                    "Error loading states"
                )}</option>`;
                stateField.disabled = true;
            });

        if (!stateField.dataset.bound) {
            stateField.addEventListener("change", function () {
                const v = this.value;
                const cityField = document.getElementById("city");
                if (cityField) {
                    cityField.disabled = true;
                    cityField.innerHTML = `<option value="">${t(
                        "select_city",
                        "Select City"
                    )}</option>`;
                }
                if (v) loadReceiverCitiesForState(v, null);
            });
            stateField.dataset.bound = "1";
        }
    }

    function loadReceiverCitiesForState(stateId, selectedCityId = null) {
        const cityField = document.getElementById("city");
        if (!cityField) return;

        // do not preview cities when no state selected
        if (!stateId) {
            cityField.disabled = true;
            cityField.innerHTML = `<option value="">${t(
                "select_city",
                "Select City"
            )}</option>`;
            return;
        }

        fetchJson(
            `https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities?page=0&pageSize=200&orderColumn=createdAt&orderDirection=desc&stateId=${encodeURIComponent(
                stateId
            )}`,
            {
                headers: {
                    accept: "*/*",
                    "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                },
            }
        )
            .then((data) => {
                const list = (data && data.results) || [];
                cityField.innerHTML = `<option value="">${t(
                    "select_city",
                    "Select City"
                )}</option>`;
                list.forEach((ci) => {
                    const opt = document.createElement("option");
                    opt.value = ci.id || ci._id;
                    opt.textContent = displayName(ci.name) || "";
                    if (
                        selectedCityId &&
                        (ci.id === selectedCityId || ci._id === selectedCityId)
                    )
                        opt.selected = true;
                    cityField.appendChild(opt);
                });
                cityField.disabled = false;
            })
            .catch(() => {
                cityField.innerHTML = `<option value="">${t(
                    "error_loading_cities",
                    "Error loading cities"
                )}</option>`;
                cityField.disabled = true;
            });
    }

    // ----------------- form population/reset -----------------
    function populateReceiverForm(receiverId) {
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

        if (n.country_id) loadCountriesForExistingReceiver(n.country_id);
        if (n.country_id) loadStatesByCountry(n.country_id, n.state_id);
        if (n.state_id) loadReceiverCitiesForState(n.state_id, n.city_id);

        ensureReceiverStateFieldVisible();
    }

    function clearReceiverForm() {
        const fieldIds = [
            "name",
            "phone",
            "additional_phone",
            "email",
            "address",
            "postal_code",
        ];

        fieldIds.forEach((id) => {
            const el = document.getElementById(id);
            if (el) {
                el.value = "";
                el.textContent = "";
                el.innerHTML = "";
                el.classList.remove("is-invalid", "is-valid");
                el.setAttribute("value", "");
            }
        });
        const countryField = document.getElementById("country");
        const stateField = document.getElementById("state");
        const cityField = document.getElementById("city");

        if (countryField) {
            countryField.innerHTML = `<option value="">${t(
                "select_country",
                "Select Country"
            )}</option>`;
            countryField.value = "";
            countryField.selectedIndex = 0;
        }
        if (stateField) {
            stateField.innerHTML = `<option value="">${t(
                "select_state",
                "Select State"
            )}</option>`;
            stateField.value = "";
            stateField.selectedIndex = 0;
        }
        if (cityField) {
            cityField.innerHTML = `<option value="">${t(
                "select_city",
                "Select City"
            )}</option>`;
            cityField.value = "";
            cityField.selectedIndex = 0;
        }
    }

    function resetFormCompletely() {
        clearReceiverForm();
        const existingRadio = document.getElementById("existing_receiver");
        const newRadio = document.getElementById("new_receiver");
        if (existingRadio) existingRadio.checked = false;
        if (newRadio) newRadio.checked = false;
        const receiverSelect = document.getElementById("receiver_select");
        if (receiverSelect) receiverSelect.value = "";
        const existingSection = document.getElementById(
            "existing_receiver_section"
        );
        const newSection = document.getElementById("new_receiver_section");
        if (existingSection) existingSection.style.display = "none";
        if (newSection) newSection.style.display = "none";
    }

    function resetReceiverTypeSelection() {
        resetFormCompletely();
    }

    function setIf(id, val) {
        const el = document.getElementById(id);
        if (el != null && typeof val !== "undefined" && val !== null)
            el.value = val;
    }

    function wireUI() {
        const addBtn = document.getElementById("add-receiver-btn");
        const receiverSelect = document.getElementById("receiver_select");
        const existingRadio = document.getElementById("existing_receiver");
        const newRadio = document.getElementById("new_receiver");
        const newSection = document.getElementById("new_receiver_section");
        const existingSection = document.getElementById(
            "existing_receiver_section"
        );

        function forceClearNewForm() {
            const ids = [
                "name",
                "phone",
                "additional_phone",
                "email",
                "address",
                "postal_code",
            ];
            ids.forEach((id) => {
                const el = document.getElementById(id);
                if (el) el.value = "";
            });
            const country = document.getElementById("country");
            const state = document.getElementById("state");
            const city = document.getElementById("city");

            if (country) country.value = "";
            if (state) {
                state.innerHTML = `<option value="">${t(
                    "select_state",
                    "Select State"
                )}</option>`;
                state.value = "";
                state.disabled = true;
            }
            if (city) {
                city.innerHTML = `<option value="">${t(
                    "select_city",
                    "Select City"
                )}</option>`;
                city.value = "";
                city.disabled = true;
            }
        }

        function resetExistingSelect() {
            if (receiverSelect) receiverSelect.value = "";
        }

        if (existingRadio && !existingRadio.dataset.bound) {
            existingRadio.addEventListener("change", function () {
                if (!this.checked) return;
                if (existingSection) existingSection.style.display = "block";
                if (newSection) {
                    newSection.style.display = "none";
                    forceClearNewForm(); 
                }
                resetExistingSelect(); 
            });
            existingRadio.dataset.bound = "1";
        }

        if (newRadio && !newRadio.dataset.bound) {
            newRadio.addEventListener("change", function () {
                if (!this.checked) return;
                if (existingSection) existingSection.style.display = "none";
                if (newSection) newSection.style.display = "block";

                forceClearNewForm();
                resetExistingSelect();

                if (typeof setupReceiverFormByShippingType === "function")
                    setupReceiverFormByShippingType();

                if (typeof ensureReceiverStateFieldVisible === "function")
                    ensureReceiverStateFieldVisible();
            });
            newRadio.dataset.bound = "1";
        }

        if (receiverSelect && !receiverSelect.dataset.bound) {
            receiverSelect.addEventListener("change", function () {
                const id = this.value;
                if (newRadio && newRadio.checked) {
                    if (id) {
                        if (typeof populateReceiverForm === "function")
                            populateReceiverForm(id);
                        if (newSection) newSection.style.display = "block";
                    } else {
                        forceClearNewForm();
                    }
                }
            });
            receiverSelect.dataset.bound = "1";
        }

        const onAdd = () => {
            if (existingRadio && existingRadio.checked) return addExisting();
            if (newRadio && newRadio.checked) return addNew();
            toast(t("select_receiver", "Please choose receiver type"), "error");
        };
        if (addBtn && !addBtn.dataset.bound) {
            addBtn.addEventListener("click", onAdd);
            addBtn.dataset.bound = "1";
        }
    }

    function canProceedToNextStep() {
        return (
            Array.isArray(window.selectedReceivers) &&
            window.selectedReceivers.length > 0
        );
    }

    window.loadReceivers = loadReceivers;
    window.populateReceiverForm = populateReceiverForm;
    window.ensureReceiverStateFieldVisible = ensureReceiverStateFieldVisible;
    window.setupReceiverFormByShippingType = setupReceiverFormByShippingType;
    window.canProceedToNextStep = canProceedToNextStep;

    document.addEventListener("DOMContentLoaded", () => {
        resetFormCompletely();
        if (window.selectedMethod) setupReceiverFormByShippingType();
    });

    document.addEventListener("shippingMethodSelected", () => {
        setupReceiverFormByShippingType();
    });
})();
