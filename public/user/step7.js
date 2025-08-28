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
    function restoreFormStateFromValidationErrors() {
        const oldInput = window.OLD_INPUT || {};
        const oldState = window.OLD_STATE || {};
        if (oldState.selectedCompany) {
            window.selectedCompany = oldState.selectedCompany;
        }
        if (oldState.companyPricing) {
            window.companyPricing = oldState.companyPricing;
        }
        if (oldState.selectedMethod) {
            window.selectedMethod = oldState.selectedMethod;
        }
        const fieldsToRestore = {
            package_type: oldInput.package_type,
            package_number: oldInput.package_number,
            length: oldInput.length,
            width: oldInput.width,
            height: oldInput.height,
            weight: oldInput.weight,
            package_description: oldInput.package_description,
        };

        Object.entries(fieldsToRestore).forEach(([fieldId, value]) => {
            if (value !== undefined && value !== null) {
                const field = document.getElementById(fieldId);
                if (field) {
                    if (field.type === "checkbox") {
                        field.checked = Boolean(value);
                    } else if (field.tagName === "SELECT") {
                        field.value = value;
                    } else {
                        field.value = value;
                    }
                }
            }
        });
        if (oldInput.accept_terms) {
            const termsField = document.getElementById("accept_terms");
            if (termsField) {
                termsField.checked = true;
            }
        }
        if (oldInput.payment_method) {
            const paymentMethodField = document.querySelector(
                `input[name="payment_method"][value="${oldInput.payment_method}"]`
            );
            if (paymentMethodField) {
                paymentMethodField.checked = true;
            }
        }
        if (oldInput.selected_receivers) {
            try {
                const receivers = JSON.parse(oldInput.selected_receivers);
                if (Array.isArray(receivers)) {
                    window.selectedReceivers = receivers;
                }
            } catch (e) {}
        }
        if (typeof window.populateAllSummaries === "function") {
            window.populateAllSummaries();
        }
    }
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
        } catch (error) {}
        return 0;
    }

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
        if (m === "local") {
            return num(
                company.effectiveLocalPrice ??
                    company.userLocalPrice ??
                    company.localPrice
            );
        } else if (m === "international") {
            return num(
                company.effectiveInternationalPrice ??
                    company.userInternationalPrice ??
                    company.internationalPrice
            );
        }
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
        const r = document.querySelector(
            'input[name="payment_method"]:checked'
        );
        return !!(r && r.value === "cod");
    }
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
            name: m("user_name")?.value ?? "",
            phone: m("user_phone")?.value ?? "",
            email: m("user_email")?.value ?? "",
            address: m("user_address")?.value ?? "",
            city: m("user_city")?.value ?? "",
            postal: m("user_postal_code")?.value ?? "",
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
            city: O.sender_city,
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
        const g = (id) => document.getElementById(id);
        const out = {
            type: g("package-type-preview"),
            cnt: g("package-count-preview"),
            w: g("package-weight-preview"),
            L: g("package-length-preview"),
            W: g("package-width-preview"),
            H: g("package-height-preview"),
            notes: g("package-notes-preview"),
        };
        const s5 = {
            type: g("package_type"),
            cnt: g("package_number"),
            w: g("weight"),
            L: g("length"),
            W: g("width"),
            H: g("height"),
            notes: g("package_description"),
        };

        if (out.type)
            out.type.textContent = s5.type
                ? s5.type.value
                : t("package_type", "Type");
        if (out.cnt) out.cnt.textContent = s5.cnt ? s5.cnt.value : "1";
        if (out.w) out.w.textContent = s5.w ? s5.w.value : "0";
        if (out.L) out.L.textContent = s5.L ? s5.L.value : "0";
        if (out.W) out.W.textContent = s5.W ? s5.W.value : "0";
        if (out.H) out.H.textContent = s5.H ? s5.H.value : "0";
        if (out.notes)
            out.notes.textContent = s5.notes
                ? s5.notes.value
                : t("no_special_notes", "No special notes");
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
        const taxEl = document.getElementById("tax-amount-preview");
        const discEl = document.getElementById("discount-amount-preview");
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
        if (taxEl) taxEl.textContent = `0 ${cur}`;
        if (discEl) discEl.textContent = `0 ${cur}`;
        if (totalEl) totalEl.textContent = `${grandTotal.toFixed(2)} ${cur}`;
        if (receiversCountEl) receiversCountEl.textContent = receiversCount;
        if (perReceiverTotalEl)
            perReceiverTotalEl.textContent = `${totalPerReceiver.toFixed(
                2
            )} ${cur}`;
        return grandTotal;
    }

    function updateWalletBalanceDisplay() {
        const paymentMethod =
            document.querySelector('input[name="payment_method"]:checked')
                ?.value || "wallet";
        const walletBalanceSection = document.getElementById(
            "wallet-balance-section"
        );
        const cur = _currency();

        if (paymentMethod === "wallet") {
            if (walletBalanceSection)
                walletBalanceSection.style.display = "block";

            fetchUserWalletBalance().then((balance) => {
                const numericBalance = parseFloat(balance) || 0;
                const walletBalanceDisplay = document.getElementById(
                    "wallet-balance-display"
                );
                const walletBalanceWarning = document.getElementById(
                    "wallet-balance-warning"
                );
                const totalAmount = extractNumericValue("total-amount-preview"); // grand total

                if (walletBalanceDisplay)
                    walletBalanceDisplay.textContent = `${numericBalance.toFixed(
                        2
                    )} ${cur}`;

                if (numericBalance < totalAmount) {
                    if (walletBalanceWarning) {
                        walletBalanceWarning.style.display = "block";
                        walletBalanceWarning.innerHTML = `<small><i class="fas fa-exclamation-triangle me-1"></i> ${t(
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
            });
        } else {
            if (walletBalanceSection)
                walletBalanceSection.style.display = "none";
            setConfirmEnabled(true);
        }
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
                note.textContent =
                    `${t("extra_weight_note", "Extra")} : ${extraKg.toFixed(
                        2
                    )} ${t("kg", "kg")} × ${_adminExtraPerKg().toFixed(
                        2
                    )} ${cur}/${t("kg", "kg")} ` +
                    `(${t(
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

    function setupTermsValidation() {
        setConfirmEnabled(true);
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

                const requiredFields = {
                    user_name: "Sender Name",
                    user_phone: "Sender Phone",
                    user_email: "Sender Email",
                    user_address: "Sender Address",
                    user_city: "Sender City",
                    package_type: "Package Type",
                    weight: "Package Weight",
                };
                const missingFields = [];
                for (const [fieldId, fieldName] of Object.entries(
                    requiredFields
                )) {
                    const field = document.getElementById(fieldId);
                    if (!field || !String(field.value || "").trim())
                        missingFields.push(fieldName);
                }
                if (missingFields.length > 0) {
                    alert(
                        `Please fill in the following required fields:\n${missingFields.join(
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

                const paymentMethod =
                    document.querySelector(
                        'input[name="payment_method"]:checked'
                    )?.value || "wallet";
                if (paymentMethod === "wallet") {
                    const totalAmount = extractNumericValue(
                        "total-amount-preview"
                    );
                    const userWalletBalance = parseFloat(
                        window.userWalletBalance || 0
                    );
                    if (userWalletBalance < totalAmount) {
                        return;
                    }
                }

                const shippingData = {
                    company_id: window.selectedCompany?.id || null,
                    shipping_method: window.selectedMethod || null,

                    sender_name:
                        document.getElementById("user_name")?.value || "",
                    sender_phone:
                        document.getElementById("user_phone")?.value || "",
                    sender_email:
                        document.getElementById("user_email")?.value || "",
                    sender_address:
                        document.getElementById("user_address")?.value || "",
                    sender_city:
                        document.getElementById("user_city")?.value || "",
                    sender_postal_code:
                        document.getElementById("user_postal_code")?.value ||
                        "",

                    receivers: window.selectedReceivers || [],
                    package_type:
                        document.getElementById("package_type")?.value || "",
                    package_count:
                        document.getElementById("package_number")?.value || "1",
                    weight: document.getElementById("weight")?.value || "0",
                    length: document.getElementById("length")?.value || "0",
                    width: document.getElementById("width")?.value || "0",
                    height: document.getElementById("height")?.value || "0",
                    package_description:
                        document.getElementById("package_description")?.value ||
                        "",

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
                    total_amount: extractNumericValue("total-amount-preview"),
                    receivers_count: window.selectedReceivers?.length || 0,

                    currency: window.translations?.currency_symbol || "SAR",
                    max_weight: parseFloat(
                        window.selectedCompany?.maxWeight || "7"
                    ),
                    entered_weight: parseFloat(
                        document.getElementById("weight")?.value || "0"
                    ),
                    extra_kg: Math.max(
                        0,
                        parseFloat(
                            document.getElementById("weight")?.value || "0"
                        ) - parseFloat(window.selectedCompany?.maxWeight || "7")
                    ),

                    _token:
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                };

                const form = document.querySelector(
                    'form[enctype="multipart/form-data"]'
                );
                if (form) {
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

                    const hiddenFields = {
                        sender_name: shippingData.sender_name,
                        sender_phone: shippingData.sender_phone,
                        sender_email: shippingData.sender_email,
                        sender_address: shippingData.sender_address,
                        sender_city: shippingData.sender_city,
                        sender_postal_code: shippingData.sender_postal_code,
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
                    };

                    Object.entries(hiddenFields).forEach(([key, value]) => {
                        const field = form.querySelector(`#${key}_hidden`);
                        if (field) field.value = value;
                    });

                    const originalText = confirm.innerHTML;
                    confirm.innerHTML =
                        '<i class="fas fa-spinner fa-spin me-2"></i>Creating Shipment...';
                    confirm.disabled = true;

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
        fetchUserWalletBalance();

        if (window.OLD_INPUT && Object.keys(window.OLD_INPUT).length > 0) {
            restoreFormStateFromValidationErrors();
        }

        document.addEventListener("stepChanged", (e) => {
            if (e.detail && e.detail.currentStep === 7) {
                if (
                    window.OLD_INPUT &&
                    Object.keys(window.OLD_INPUT).length > 0
                ) {
                    restoreFormStateFromValidationErrors();
                }

                setupStep7();
                setTimeout(() => populatePerReceiverPaymentSummary(), 0);
            }
        });

        [
            "weight",
            "package_type",
            "package_number",
            "length",
            "width",
            "height",
        ].forEach((id) => {
            const el = document.getElementById(id);
            if (el && !el.dataset.boundStep7Recalc) {
                el.addEventListener(
                    el.type === "checkbox" ? "change" : "input",
                    () => {
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
                        populatePerReceiverPaymentSummary();
                        updateWalletBalanceDisplay();
                    });
                    r.dataset.boundStep7Pay = "1";
                }
            });
    });
})();
