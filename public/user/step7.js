(() => {
    function t(key, fallback = "") {
        if (window.translations && window.translations[key])
            return window.translations[key];
        return fallback || key;
    }
    function num(v, def = 0) {
        if (v == null) return def;
        if (typeof v === "number" && isFinite(v)) return v;
        const s = String(v);
        const m = s.replace(",", ".").match(/-?\d+(\.\d+)?/);
        return m ? parseFloat(m[0]) : def;
    }
    function _val(id) {
        const el = document.getElementById(id);
        return el ? String(el.value || "") : "";
    }
    function _textOfSelect(id) {
        const el = document.getElementById(id);
        if (!el || el.tagName !== "SELECT") return "";
        const opt = el.options[el.selectedIndex];
        return opt ? String(opt.textContent || "") : "";
    }
    function setConfirmEnabled(enabled) {
        const btn = document.getElementById("btn-confirm-shipping");
        if (!btn) return;
        btn.disabled = !enabled;
        if (enabled) {
            btn.removeAttribute("disabled");
            btn.classList.remove("disabled");
            btn.setAttribute("aria-disabled", "false");
            btn.style.pointerEvents = "auto";
            btn.style.opacity = "";
        } else {
            btn.setAttribute("disabled", "disabled");
            btn.classList.add("disabled");
            btn.setAttribute("aria-disabled", "true");
        }
    }
    function extractNumericValue(elementId) {
        const element = document.getElementById(elementId);
        if (!element) return 0;
        const text = (element.textContent || "").replace(",", "");
        const match = text.match(/-?\d+(\.\d+)?/);
        return match ? parseFloat(match[0]) : 0;
    }

    /* =============== state restore (old input) =============== */
    function restoreFormStateFromValidationErrors() {
        const oldInput = window.OLD_INPUT || {};
        const oldState = window.OLD_STATE || {};
        if (oldState.selectedCompany)
            window.selectedCompany = oldState.selectedCompany;
        if (oldState.companyPricing)
            window.companyPricing = oldState.companyPricing;
        if (oldState.selectedMethod)
            window.selectedMethod = oldState.selectedMethod;

        const fieldsToRestore = {
            package_type: oldInput.package_type,
            package_number: oldInput.package_number,
            length: oldInput.length,
            width: oldInput.width,
            height: oldInput.height,
            weight: oldInput.weight,
            package_description: oldInput.package_description,
            package_notes: oldInput.package_notes,
        };
        Object.entries(fieldsToRestore).forEach(([fieldId, value]) => {
            if (value !== undefined && value !== null) {
                const field = document.getElementById(fieldId);
                if (field) {
                    if (field.type === "checkbox")
                        field.checked = Boolean(value);
                    else field.value = value;
                }
            }
        });

        if (oldInput.accept_terms) {
            const termsField = document.getElementById("accept_terms");
            if (termsField) termsField.checked = true;
        }
        if (oldInput.payment_method) {
            const paymentMethodField = document.querySelector(
                `input[name="payment_method"][value="${oldInput.payment_method}"]`
            );
            if (paymentMethodField) paymentMethodField.checked = true;
            window.selectedPaymentMethod = oldInput.payment_method;
            const hidden = document.getElementById("payment_method_hidden");
            if (hidden) hidden.value = oldInput.payment_method;
        }
        if (oldInput.cod_amount != null) {
            const codHidden = document.getElementById("cod-amount-hidden");
            if (codHidden) codHidden.value = oldInput.cod_amount;
            window.codAmount = num(oldInput.cod_amount, 0);
        }
        if (oldInput.selected_receivers) {
            try {
                const receivers = JSON.parse(oldInput.selected_receivers);
                if (Array.isArray(receivers))
                    window.selectedReceivers = receivers;
            } catch {}
        }
        if (typeof window.populateAllSummaries === "function")
            window.populateAllSummaries();
    }

    /* =============== wallet =============== */
    async function fetchUserWalletBalance() {
        try {
            const response = await fetch("/wallet/balance", {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
            });
            if (response.ok) {
                const data = await response.json();
                const balance = parseFloat(data.balance) || 0;
                window.userWalletBalance = balance;
                return balance;
            }
        } catch {}
        return 0;
    }

    /* =============== getters =============== */
    function _receivers() {
        return Array.isArray(window.selectedReceivers)
            ? window.selectedReceivers
            : [];
    }
    function _currency() {
        const c = window.selectedCompany || {};
        const p = window.companyPricing || window.pricingSummary || {};
        return (
            c.currency_symbol ||
            c.currencySymbol ||
            p.currency_symbol ||
            p.currencySymbol ||
            window.translations?.currency_symbol ||
            "SAR"
        );
    }
    function _method() {
        return window.selectedMethod;
    }
    function _userPricePerReceiver() {
        const company = window.selectedCompany || {};
        const m = _method();
        if (m === "local")
            return num(
                company.effectiveLocalPrice ??
                    company.userLocalPrice ??
                    company.localPrice
            );
        if (m === "international")
            return num(
                company.effectiveInternationalPrice ??
                    company.userInternationalPrice ??
                    company.internationalPrice
            );
        return 0;
    }
    function _adminCodPerReceiver() {
        const c = window.selectedCompany || {};
        const g = window.ADMIN_SETTINGS?.cod_fee_per_receiver;
        return num(c._adminCodFee ?? c.adminCodFee ?? g);
    }
    function _adminExtraPerKg() {
        const c = window.selectedCompany || {};
        const g = window.ADMIN_SETTINGS?.extra_weight_price_per_kg;
        return num(
            c._adminExtraWeightPrice ??
                c._adminExtraKg ??
                c.adminExtraWeightPrice ??
                g
        );
    }
    function _maxWeight() {
        const c = window.selectedCompany || {};
        return num(c.maxWeight, 7);
    }
    function _enteredWeight() {
        const el = document.getElementById("weight");
        return el ? num(el.value) : 0;
    }
    function _isCodSelected() {
        const fromGlobal =
            window.selectedPaymentMethod ||
            document.getElementById("payment_method_hidden")?.value;
        if (fromGlobal) return String(fromGlobal).toLowerCase() === "cod";
        const r = document.querySelector(
            'input[name="payment_method"]:checked'
        );
        return !!(r && r.value === "cod");
    }
    function _codAmount() {
        const ids = [
            document.getElementById("cod-amount-hidden")?.value,
            document.querySelector('input[name="cod_amount"]')?.value,
            window.codAmount,
        ];
        for (const v of ids) {
            const n = num(v, NaN);
            if (!isNaN(n)) return Math.max(0, n);
        }
        return 0;
    }

    /* =============== UI summary fillers =============== */
    function populateShippingCompanySummary() {
        const company = window.selectedCompany;
        if (!company) return;
        const logoPreview = document.getElementById("company-logo-preview");
        const namePreview = document.getElementById("company-name-preview");
        const servicePreview = document.getElementById(
            "company-service-preview"
        );
        if (logoPreview && company.logoUrl) {
            logoPreview.src = company.logoUrl;
            logoPreview.style.display = "block";
        } else if (logoPreview) {
            logoPreview.style.display = "none";
        }
        if (namePreview) namePreview.textContent = company.name || "N/A";
        if (servicePreview)
            servicePreview.textContent = company.serviceName || "N/A";

        const methodPreview = document.getElementById(
            "shipping-method-preview"
        );
        if (methodPreview) {
            const m = _method();
            if (m === "local") {
                methodPreview.textContent = t(
                    "local_shipping",
                    "Local Shipping"
                );
                methodPreview.className = "badge bg-success fs-6";
            } else if (m === "international") {
                methodPreview.textContent = t(
                    "international_shipping",
                    "International Shipping"
                );
                methodPreview.className = "badge bg-info fs-6";
            } else {
                methodPreview.textContent = t(
                    "shipping_method",
                    "Shipping Method"
                );
                methodPreview.className = "badge bg-secondary fs-6";
            }
        }
    }
    function populateUserInformationSummary() {
        const m = (id) => document.getElementById(id);
        const out = {
            name: m("sender-name-preview"),
            phone: m("sender-phone-preview"),
            email: m("sender-email-preview"),
            address: m("sender-address-preview"),
            city: m("sender-city-preview"),
            postal: m("sender-postal-preview"),
        };
        const dom = {
            name: _val("user_name"),
            phone: _val("user_phone"),
            email: _val("user_email"),
            address: _val("user_address"),
            city: _textOfSelect("user_city") || _val("user_city"),
            postal: _val("user_postal_code"),
        };
        const O = window.OLD_INPUT || {};
        const fb = {
            name: t("user_name", "Name"),
            phone: t("user_phone", "Phone"),
            email: t("user_email", "Email"),
            address: t("user_address", "Address"),
            city: t("user_city", "City"),
            postal: t("user_postal", "Postal Code"),
        };
        const firstNonEmpty = (...vals) =>
            vals.find((v) => typeof v === "string" && v.trim().length) ?? "";
        const mapOld = {
            name: O.sender_name,
            phone: O.sender_phone,
            email: O.sender_email,
            address: O.sender_address,
            city: O.sender_city_name || O.sender_city,
            postal: O.sender_postal_code,
        };
        if (out.name)
            out.name.textContent = firstNonEmpty(
                mapOld.name,
                dom.name,
                fb.name
            );
        if (out.phone)
            out.phone.textContent = firstNonEmpty(
                mapOld.phone,
                dom.phone,
                fb.phone
            );
        if (out.email)
            out.email.textContent = firstNonEmpty(
                mapOld.email,
                dom.email,
                fb.email
            );
        if (out.address)
            out.address.textContent = firstNonEmpty(
                mapOld.address,
                dom.address,
                fb.address
            );
        if (out.city)
            out.city.textContent = firstNonEmpty(
                mapOld.city,
                dom.city,
                fb.city
            );
        if (out.postal)
            out.postal.textContent = firstNonEmpty(
                mapOld.postal,
                dom.postal,
                fb.postal
            );
    }
    function populateReceiversSummary() {
        const box = document.getElementById("receivers-summary-container");
        const countEl = document.getElementById("receivers-count-preview");
        if (!box || !countEl) return;
        const list = _receivers();
        countEl.textContent = list.length;
        if (!list.length) {
            box.innerHTML = `<p class="text-muted">${t(
                "no_receivers_selected",
                "No receivers selected"
            )}</p>`;
            return;
        }
        let html = "";
        list.forEach((r, i) => {
            const isNew = r.isNew
                ? ` (${t("new", "New")})`
                : ` (${t("existing", "Existing")})`;
            const phone = r.phone || t("not_specified", "Not specified");
            const email = r.email || t("not_specified", "Not specified");
            const city = r.city_name || t("not_specified", "Not specified");
            const addr = r.address || t("not_specified", "Not specified");
            html += `
          <div class="receiver-summary-item border-bottom pb-3 mb-3">
            <h6 class="mb-2"><i class="fas fa-user me-2"></i>${t(
                "receiver",
                "Receiver"
            )} #${i + 1}: ${r.name || ""}${isNew}</h6>
            <div class="row">
              <div class="col-md-6">
                <p class="mb-1"><strong>${t(
                    "phone",
                    "Phone"
                )}:</strong> ${phone}</p>
                <p class="mb-1"><strong>${t(
                    "email",
                    "Email"
                )}:</strong> ${email}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>${t(
                    "city",
                    "City"
                )}:</strong> ${city}</p>
                <p class="mb-1"><strong>${t(
                    "address",
                    "Address"
                )}:</strong> ${addr}</p>
              </div>
            </div>
          </div>`;
        });
        box.innerHTML = html;
    }
    function populatePackageDetailsSummary() {
        const set = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = (val ?? "").toString();
        };
        const pkgType = _textOfSelect("package_type") || _val("package_type");
        const pkgCount = _val("package_number") || "1";
        const weight = _val("weight") || "0";
        const length = _val("length") || "0";
        const width = _val("width") || "0";
        const height = _val("height") || "0";
        const notes =
            _val("package_notes") || t("no_special_notes", "No special notes");

        set(
            "package-type-preview",
            pkgType || t("package_type", "Package Type")
        );
        set(
            "package-count-preview",
            pkgCount || t("package_count", "Package Count")
        );
        set("package-weight-preview", weight || t("weight_kg", "Weight (KG)"));
        set("package-length-preview", length || t("length_cm", "Length (cm)"));
        set("package-width-preview", width || t("width_cm", "Width (cm)"));
        set("package-height-preview", height || t("height_cm", "Height (cm)"));

        const notesEl = document.getElementById("package-notes-preview");
        if (notesEl)
            notesEl.textContent = notes.trim()
                ? notes
                : t("no_special_notes", "No special notes");
    }

    /* =============== totals & balance =============== */
    function ensureCodAmountRow() {
        const codFeesEl = document.getElementById("cod-fees-preview");
        if (!codFeesEl) return null;
        let row = document.getElementById("cod-amount-row");
        if (!row) {
            const parentRow = codFeesEl.closest(".row");
            row = document.createElement("div");
            row.className = "row";
            row.id = "cod-amount-row";
            row.innerHTML = `
                <div class="col-md-12" style="display:flex;justify-content:space-between;">
                    <strong class="mb-3 text-black">${t("cod_amount", "COD Amount")}:</strong>
                    <div class="mb-0 text-muted" id="cod-amount-display"></div>
                </div>`;
            if (parentRow && parentRow.parentNode)
                parentRow.parentNode.insertBefore(row, parentRow.nextSibling);
        }
        return row;
    }
    function populatePaymentDetailsCard(perShip, perExtra, perCod, isCOD, cur) {
        const methodEl = document.getElementById("payment-method-preview");
        if (methodEl)
            methodEl.textContent = isCOD
                ? t("cash_on_delivery", "Cash on Delivery")
                : t("wallet", "Wallet");
        const shipEl = document.getElementById("shipping-fee-preview");
        const extraEl = document.getElementById("extra-fees-preview");
        const totalEl = document.getElementById("total-amount-preview");
        const codFeesEl = document.getElementById("cod-fees-preview");
        const receiversCountEl = document.getElementById(
            "receivers-count-display"
        );
        const perReceiverTotalEl =
            document.getElementById("per-receiver-total");

        const receiversCount = _receivers().length || 1;
        const totalShipping = perShip * receiversCount;
        const totalExtra = perExtra * receiversCount;
        const totalCod = isCOD ? perCod * receiversCount : 0;
        const totalPerReceiver = perShip + perExtra + (isCOD ? perCod : 0);
        const grandTotal = totalShipping + totalExtra + totalCod;

        if (shipEl) shipEl.textContent = `${totalShipping.toFixed(2)} ${cur}`;
        if (extraEl) extraEl.textContent = `${totalExtra.toFixed(2)} ${cur}`;
        if (codFeesEl) codFeesEl.textContent = `${totalCod.toFixed(2)} ${cur}`;
        if (totalEl) totalEl.textContent = `${grandTotal.toFixed(2)} ${cur}`;
        if (receiversCountEl) receiversCountEl.textContent = receiversCount;
        if (perReceiverTotalEl)
            perReceiverTotalEl.textContent = `${totalPerReceiver.toFixed(
                2
            )} ${cur}`;

        const codRow = ensureCodAmountRow();
        const codAmount = _codAmount();
        if (codRow) {
            if (isCOD && codAmount > 0) {
                codRow.style.display = "";
                const disp = document.getElementById("cod-amount-display");
                if (disp) disp.textContent = `${codAmount.toFixed(2)} ${cur}`;
            } else {
                codRow.style.display = "none";
            }
        }
        return grandTotal;
    }
    function updateWalletBalanceDisplay() {
        const method = (
            window.selectedPaymentMethod ||
            document.getElementById("payment_method_hidden")?.value ||
            document.querySelector('input[name="payment_method"]:checked')
                ?.value ||
            "wallet"
        ).toLowerCase();

        const walletBalanceSection = document.getElementById(
            "wallet-balance-section"
        );
        const cur = _currency();
        if (walletBalanceSection) walletBalanceSection.style.display = "block";

        const ensureBalanceThenCheck = async () => {
            if (typeof window.userWalletBalance !== "number")
                await fetchUserWalletBalance();
            const numericBalance =
                parseFloat(window.userWalletBalance || 0) || 0;
            const walletBalanceDisplay = document.getElementById(
                "wallet-balance-display"
            );
            const walletBalanceWarning = document.getElementById(
                "wallet-balance-warning"
            );
            const totalAmount = extractNumericValue("total-amount-preview");

            if (walletBalanceDisplay)
                walletBalanceDisplay.textContent = `${numericBalance.toFixed(
                    2
                )} ${cur}`;

            // For both WALLET and COD: must have enough balance
            if (method === "wallet" || method === "cod") {
                if (numericBalance < totalAmount) {
                    if (walletBalanceWarning) {
                        walletBalanceWarning.style.display = "block";
                        walletBalanceWarning.innerHTML = `<small><i class="fa fa-warning"></i> ${t(
                            "insufficient_balance",
                            "Insufficient balance! Please recharge your wallet."
                        )}</small>`;
                    }
                    setConfirmEnabled(false);
                } else {
                    if (walletBalanceWarning)
                        walletBalanceWarning.style.display = "none";
                    setConfirmEnabled(true);
                }
            } else {
                if (walletBalanceSection)
                    walletBalanceSection.style.display = "none";
                setConfirmEnabled(true);
            }
        };
        ensureBalanceThenCheck();
    }
    function populatePerReceiverPaymentSummary() {
        const cur = _currency();
        const perShip = _userPricePerReceiver();
        const extraKg = Math.max(0, _enteredWeight() - _maxWeight());
        const perExtra = extraKg * _adminExtraPerKg();
        const perCod = _adminCodPerReceiver();
        const isCOD = _isCodSelected();

        const baseEl = document.getElementById("price-base-per-receiver");
        const extraEl = document.getElementById("price-extra-per-receiver");
        const codEl = document.getElementById("price-cod-per-receiver");
        if (baseEl) baseEl.textContent = `${perShip.toFixed(2)} ${cur}`;
        if (extraEl) extraEl.textContent = `${perExtra.toFixed(2)} ${cur}`;
        if (codEl) codEl.textContent = `${perCod.toFixed(2)} ${cur}`;

        const note = document.getElementById("extra-weight-note");
        if (note) {
            if (extraKg > 0) {
                note.textContent = `${t(
                    "extra_weight_note",
                    "Extra"
                )} : ${extraKg.toFixed(2)} ${t(
                    "kg",
                    "kg"
                )} × ${_adminExtraPerKg().toFixed(2)} ${cur}/${t(
                    "kg",
                    "kg"
                )} (${t(
                    "company_max_weight",
                    "Company max"
                )} = ${_maxWeight()} ${t("kg", "kg")}, ${t(
                    "entered_weight",
                    "Entered"
                )} = ${_enteredWeight()} ${t("kg", "kg")})`;
            } else {
                note.textContent = t("no_extra_weight", "No extra weight");
            }
        }
        const grandTotal = populatePaymentDetailsCard(
            perShip,
            perExtra,
            perCod,
            isCOD,
            cur
        );
        updateWalletBalanceDisplay();
        return grandTotal;
    }

    /* =============== hidden fields helpers =============== */
    function ensureHidden(form, id, name) {
        let el = form.querySelector(`#${id}`);
        if (!el) {
            el = document.createElement("input");
            el.type = "hidden";
            el.id = id;
            el.name = name || id;
            form.appendChild(el);
        }
        return el;
    }

    // Mirrors country/state/city IDs + NAMES to hidden inputs so the server always receives them.
    function mirrorLocationToForm(form) {
        // IDs from selects used in Step 3
        const countryId = _val("user_country");
        const stateId = _val("user_state");
        const cityId = _val("user_city");

        // Visible (label) names from selected <option>
        const countryName = _textOfSelect("user_country");
        const stateName = _textOfSelect("user_state");
        const cityName = _textOfSelect("user_city");

        // Your existing sender_* fields (already in Blade)
        ensureHidden(
            form,
            "sender_country_id_hidden",
            "sender_country_id"
        ).value = countryId;
        ensureHidden(
            form,
            "sender_country_name_hidden",
            "sender_country_name"
        ).value = countryName;
        ensureHidden(form, "sender_state_id_hidden", "sender_state_id").value =
            stateId;
        ensureHidden(
            form,
            "sender_state_name_hidden",
            "sender_state_name"
        ).value = stateName;
        ensureHidden(form, "sender_city_id_hidden", "sender_city_id").value =
            cityId;
        ensureHidden(
            form,
            "sender_city_name_hidden",
            "sender_city_name"
        ).value = cityName;

        // Also provide raw names used by your Step 3 form (if your controller expects these too)
        ensureHidden(form, "country_id_hidden", "country_id").value = countryId;
        ensureHidden(form, "state_id_hidden", "state_id").value = stateId;
        ensureHidden(form, "city_id_hidden", "city_id").value = cityId;

        ensureHidden(form, "country_name_hidden", "country_name").value =
            countryName;
        ensureHidden(form, "state_name_hidden", "state_name").value = stateName;
        ensureHidden(form, "city_name_hidden", "city_name").value = cityName;
    }

    /* =============== terms & actions =============== */
    function setupTermsValidation() {
        // do not auto-disable the confirm button here; wallet check will govern it
        setConfirmEnabled(true);
    }
    function ensureCodHiddenInForm(form, value) {
        let hidden = form.querySelector(
            '#cod-amount-hidden[name="cod_amount"]'
        );
        if (!hidden) {
            hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = "cod_amount";
            hidden.id = "cod-amount-hidden";
            form.appendChild(hidden);
        }
        hidden.value = value != null ? value : "";
    }
    function ensurePaymentMethodHiddenInForm(form, value) {
        let hidden = form.querySelector("#payment_method_hidden");
        if (!hidden) {
            hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = "payment_method";
            hidden.id = "payment_method_hidden";
            form.appendChild(hidden);
        }
        hidden.value = value || "";
    }

    function setupActionButtons() {
        const prev = document.getElementById("btn-prev-step7");
        if (prev)
            prev.addEventListener("click", () => {
                if (typeof window.showStep === "function") window.showStep(6);
            });

        const confirm = document.getElementById("btn-confirm-shipping");
        if (confirm && !confirm.dataset.boundConfirm) {
            confirm.addEventListener("click", (e) => {
                e.preventDefault();

                // Basic required fields
                const requiredFields = {
                    user_name: "Sender Name",
                    user_phone: "Sender Phone",
                    user_email: "Sender Email",
                    user_address: "Sender Address",
                    user_city: "Sender City",
                    package_type: "Package Type",
                    weight: "Package Weight",
                };
                const missing = [];
                for (const [id, label] of Object.entries(requiredFields)) {
                    const field = document.getElementById(id);
                    if (!field || !String(field.value || "").trim())
                        missing.push(label);
                }
                if (missing.length > 0) {
                    alert(
                        `Please fill in the following required fields:\n${missing.join(
                            "\n"
                        )}`
                    );
                    return;
                }
                if (!window.selectedCompany) {
                    alert("Please select a shipping company first.");
                    return;
                }
                if (
                    !window.selectedReceivers ||
                    window.selectedReceivers.length === 0
                ) {
                    alert("Please add at least one receiver.");
                    return;
                }

                const paymentMethod = (
                    window.selectedPaymentMethod ||
                    document.getElementById("payment_method_hidden")?.value ||
                    document.querySelector(
                        'input[name="payment_method"]:checked'
                    )?.value ||
                    "wallet"
                ).toLowerCase();

                const totalAmount = extractNumericValue("total-amount-preview");
                const walletBalance = parseFloat(window.userWalletBalance || 0);

                // For Wallet or COD, ensure enough balance
                if (
                    (paymentMethod === "wallet" || paymentMethod === "cod") &&
                    walletBalance < totalAmount
                ) {
                    // warning already shown in UI
                    return;
                }

                // Sender location (IDs + names)
                const sender_country_id = _val("user_country");
                const sender_country_name = _textOfSelect("user_country");
                const sender_state_id = _val("user_state");
                const sender_state_name = _textOfSelect("user_state");
                const sender_city_id = _val("user_city");
                const sender_city_name = _textOfSelect("user_city");

                const shippingData = {
                    company_id: window.selectedCompany?.id || null,
                    shipping_method: window.selectedMethod || null,

                    sender_name: _val("user_name"),
                    sender_phone: _val("user_phone"),
                    sender_email: _val("user_email"),
                    sender_address: _val("user_address"),
                    sender_postal_code: _val("user_postal_code"),

                    // full location
                    sender_country_id,
                    sender_country_name,
                    sender_state_id,
                    sender_state_name,
                    sender_city_id,
                    sender_city_name,

                    // legacy/compat
                    sender_city: sender_city_id,

                    receivers: window.selectedReceivers || [],

                    package_type: _val("package_type"),
                    package_count: _val("package_number") || "1",
                    weight: _val("weight") || "0",
                    length: _val("length") || "0",
                    width: _val("width") || "0",
                    height: _val("height") || "0",
                    package_description: _val("package_description") || "",
                    package_notes: _val("package_notes") || "",

                    payment_method: paymentMethod,

                    shipping_price_per_receiver: extractNumericValue(
                        "price-base-per-receiver"
                    ),
                    extra_weight_per_receiver: extractNumericValue(
                        "price-extra-per-receiver"
                    ),
                    cod_price_per_receiver: extractNumericValue(
                        "price-cod-per-receiver"
                    ),
                    total_per_receiver:
                        extractNumericValue("per-receiver-total"),
                    total_amount: totalAmount,
                    receivers_count: window.selectedReceivers?.length || 0,
                    currency: window.translations?.currency_symbol || "SAR",
                    max_weight: parseFloat(
                        window.selectedCompany?.maxWeight || "7"
                    ),
                    entered_weight: parseFloat(_val("weight") || "0"),
                    extra_kg: Math.max(
                        0,
                        parseFloat(_val("weight") || "0") -
                            parseFloat(window.selectedCompany?.maxWeight || "7")
                    ),
                    _token:
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                };

                const form =
                    document.querySelector(
                        'form[enctype="multipart/form-data"]'
                    ) || document.querySelector("form");

                if (form) {
                    // Core hiddens already present in Blade
                    const companyIdField = form.querySelector(
                        "#shipping_company_id"
                    );
                    const methodField = form.querySelector("#shipping_method");
                    const receiversField = form.querySelector(
                        "#selected_receivers_hidden"
                    );
                    if (companyIdField)
                        companyIdField.value = shippingData.company_id || "";
                    if (methodField)
                        methodField.value = shippingData.shipping_method || "";
                    if (receiversField)
                        receiversField.value = JSON.stringify(
                            shippingData.receivers || []
                        );

                    // Make sure all hidden values are present/updated
                    const hiddenFields = {
                        // sender
                        sender_name: shippingData.sender_name,
                        sender_phone: shippingData.sender_phone,
                        sender_email: shippingData.sender_email,
                        sender_address: shippingData.sender_address,
                        sender_postal_code: shippingData.sender_postal_code,

                        // location
                        sender_country_id: shippingData.sender_country_id,
                        sender_country_name: shippingData.sender_country_name,
                        sender_state_id: shippingData.sender_state_id,
                        sender_state_name: shippingData.sender_state_name,
                        sender_city_id: shippingData.sender_city_id,
                        sender_city_name: shippingData.sender_city_name,
                        sender_city: shippingData.sender_city, // legacy

                        // pricing
                        payment_method: shippingData.payment_method,
                        shipping_price_per_receiver:
                            shippingData.shipping_price_per_receiver,
                        extra_weight_per_receiver:
                            shippingData.extra_weight_per_receiver,
                        cod_price_per_receiver:
                            shippingData.cod_price_per_receiver,
                        total_per_receiver: shippingData.total_per_receiver,
                        total_amount: shippingData.total_amount,
                        receivers_count: shippingData.receivers_count,
                        currency: shippingData.currency,
                        max_weight: shippingData.max_weight,
                        entered_weight: shippingData.entered_weight,
                        extra_kg: shippingData.extra_kg,

                        // package convenience
                        package_type: shippingData.package_type,
                        package_count: shippingData.package_count,
                        length: shippingData.length,
                        width: shippingData.width,
                        height: shippingData.height,
                        weight: shippingData.weight,
                        package_description: shippingData.package_description,
                        package_notes: shippingData.package_notes,
                    };
                    Object.entries(hiddenFields).forEach(([key, value]) => {
                        let field = form.querySelector(`#${key}_hidden`);
                        if (!field) {
                            field = document.createElement("input");
                            field.type = "hidden";
                            field.id = `${key}_hidden`;
                            field.name = key;
                            form.appendChild(field);
                        }
                        field.value = value != null ? value : "";
                    });

                    // Explicitly mirror location IDs & names to dedicated hiddens as well
                    mirrorLocationToForm(form);

                    ensurePaymentMethodHiddenInForm(form, paymentMethod);
                    ensureCodHiddenInForm(form, _codAmount());

                    const originalText = confirm.innerHTML;
                    confirm.innerHTML =
                        '<i class="fas fa-spinner fa-spin me-2"></i>Creating Shipment...';
                    confirm.disabled = true;

                    // ensure validateForm exists (form has onsubmit="return validateForm()")
                    if (typeof window.validateForm !== "function") {
                        window.validateForm = () => true;
                    }

                    form.submit();
                } else {
                    alert(
                        "Form not found. Please refresh the page and try again."
                    );
                }
            });
            confirm.dataset.boundConfirm = "1";
        }
    }

    function populateAllSummaries() {
        populateShippingCompanySummary();
        populateUserInformationSummary();
        populateReceiversSummary();
        populatePackageDetailsSummary();
        populatePerReceiverPaymentSummary();
    }
    function setupStep7() {
        populateAllSummaries();
        setupTermsValidation();
        setupActionButtons();
        updateWalletBalanceDisplay();
    }

    window.setupStep7 = setupStep7;
    window.populateAllSummaries = populateAllSummaries;

    document.addEventListener("DOMContentLoaded", () => {
        // guarantee validateForm exists so form onsubmit won't block
        if (typeof window.validateForm !== "function")
            window.validateForm = () => true;

        fetchUserWalletBalance();
        if (window.OLD_INPUT && Object.keys(window.OLD_INPUT).length > 0) {
            restoreFormStateFromValidationErrors();
        }

        // step navigation hooks
        document.addEventListener("stepChanged", (e) => {
            const step = e.detail?.currentStep;

            // Entering Step 7: build UI and recompute totals
            if (step === 7) {
                if (
                    window.OLD_INPUT &&
                    Object.keys(window.OLD_INPUT).length > 0
                ) {
                    restoreFormStateFromValidationErrors();
                }
                setupStep7();
                setTimeout(() => populatePerReceiverPaymentSummary(), 0);
            }

            // Entering Step 6: keep COD amount persisted (mirror hidden -> input)
            if (step === 6) {
                const codInput = document.getElementById("cod-amount-input");
                const codHidden = document.getElementById("cod-amount-hidden");
                const v =
                    codHidden && codHidden.value !== ""
                        ? codHidden.value
                        : typeof window.codAmount === "number"
                        ? window.codAmount
                        : "";
                if (codInput && v !== "") codInput.value = v;
            }

            if (step === 4) {
                const form =
                    document.querySelector(
                        'form[enctype="multipart/form-data"]'
                    ) || document.querySelector("form");
                if (form) mirrorLocationToForm(form);
            }
        });

        [
            "weight",
            "package_type",
            "package_number",
            "length",
            "width",
            "height",
            "package_notes",
            "package_description",
        ].forEach((id) => {
            const el = document.getElementById(id);
            if (el && !el.dataset.boundStep7Recalc) {
                el.addEventListener(
                    el.type === "checkbox" ? "change" : "input",
                    () => {
                        populatePackageDetailsSummary();
                        populatePerReceiverPaymentSummary();
                    }
                );
                el.dataset.boundStep7Recalc = "1";
            }
        });
        document
            .querySelectorAll('input[name="payment_method"]')
            .forEach((r) => {
                if (!r.dataset.boundStep7Pay) {
                    r.addEventListener("change", () => {
                        window.selectedPaymentMethod = r.value;
                        const hidden = document.getElementById(
                            "payment_method_hidden"
                        );
                        if (hidden) hidden.value = r.value;
                        populatePerReceiverPaymentSummary();
                        updateWalletBalanceDisplay();
                    });
                    r.dataset.boundStep7Pay = "1";
                }
            });
        const codInput = document.getElementById("cod-amount-input");
        if (codInput && !codInput.dataset.boundStep7Cod) {
            const syncCod = () => {
                const v = isFinite(+codInput.value) ? +codInput.value : 0;
                const hidden = document.getElementById("cod-amount-hidden");
                if (hidden) hidden.value = v;
                window.codAmount = v;
                populatePerReceiverPaymentSummary();
            };
            codInput.addEventListener("input", syncCod);
            codInput.addEventListener("change", syncCod);
            codInput.dataset.boundStep7Cod = "1";
        }

        document.addEventListener("paymentMethodChanged", () => {
            populatePerReceiverPaymentSummary();
            updateWalletBalanceDisplay();
        });
    });
})();
