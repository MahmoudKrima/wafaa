(function () {
    // ----------------- helpers -----------------
    const $$ = (id) => document.getElementById(id);
    const toNum = (v, d = 0) => (isFinite(+v) ? +v : d);
    const t = (k, fb) =>
        (window.translations && window.translations[k]) || fb || "";

    // ----------------- STEP 4: dimensions & validation -----------------
    let _submittingStep5 = false;

    function ensureDimensionDefaults() {
        const lengthField = $$("length");
        const widthField = $$("width");
        const heightField = $$("height");
        [lengthField, widthField, heightField].forEach((f) => {
            if (f && (!f.value || Number(f.value) <= 0)) f.value = "1";
        });
    }

    function showErrorStep5(message, forceShow = false) {
        if (!forceShow && !_submittingStep5) return;
        
        if (typeof toastr !== "undefined") {
            toastr.error(message);
            return;
        }
        const errorContainer = $$("receiver-error-msg");
        if (errorContainer) {
            errorContainer.innerHTML = `
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> ${message}
          </div>`;
            errorContainer.style.display = "block";
            setTimeout(() => (errorContainer.style.display = "none"), 5000);
        } else {
            alert("Error: " + message);
        }
    }

    function syncNextBtnStep5() {
        if (typeof window.hardEnableNext === "function") {
            window.hardEnableNext(window.validatePackageDetails());
        } else {
            const btnNext = document.getElementById("btn-next");
            if (!btnNext) return;
            const ok = window.validatePackageDetails();
            btnNext.disabled = !ok;
            btnNext.classList.toggle("btn-secondary", !ok);
            btnNext.classList.toggle("btn-primary", ok);
        }
    }

    function showDimensionsSection() {
        const section = $$("dimensions_section");
        if (!section) return;
        section.style.display = "block";

        const lengthField = $$("length");
        const widthField = $$("width");
        const heightField = $$("height");
        if (lengthField) lengthField.required = true;
        if (widthField) widthField.required = true;
        if (heightField) heightField.required = true;

        ensureDimensionDefaults();
    }

    function hideDimensionsSection(clearValues = false) {
        const section = $$("dimensions_section");
        if (!section) return;
        section.style.display = "none";

        const lengthField = $$("length");
        const widthField = $$("width");
        const heightField = $$("height");

        if (lengthField) {
            lengthField.required = false;
            if (clearValues) lengthField.value = "";
        }
        if (widthField) {
            widthField.required = false;
            if (clearValues) widthField.value = "";
        }
        if (heightField) {
            heightField.required = false;
            if (clearValues) heightField.value = "";
        }
    }

    function setupPackageTypeHandling() {
        const packageTypeSelect = $$("package_type");
        if (!packageTypeSelect) return;

        packageTypeSelect.required = true;

        const showDim = ["box", "document"].includes(packageTypeSelect.value);
        if (showDim) showDimensionsSection();
        else hideDimensionsSection(true);

        if (!packageTypeSelect.dataset.bound) {
            packageTypeSelect.addEventListener("change", function () {
                if (["box", "document"].includes(this.value)) {
                    showDimensionsSection();
                } else {
                    hideDimensionsSection(true);
                }
                syncNextBtnStep5();
            });
            packageTypeSelect.dataset.bound = "1";
        }
    }

    // Public: used by Next/Submit gating
    window.validatePackageDetails = function validatePackageDetails(showErrors = false) {
        const packageType = $$("package_type");
        const packageNumber = $$("package_number");
        const weight = $$("weight");
        const acceptTerms = $$("accept_terms");
        const packageDescription = $$("package_description");
        if (
            !packageType ||
            !packageNumber ||
            !weight ||
            !acceptTerms ||
            !packageDescription
        ) {
            return false;
        }

        if (!packageType.value) {
            if (showErrors) showErrorStep5(
                t("package_type_required", "Package type is required"),
                true
            );
            return false;
        }

        const num = Number(packageNumber.value);
        if (!packageNumber.value || isNaN(num) || num < 1) {
            if (showErrors) showErrorStep5(
                t("package_number_invalid", "Invalid package number"),
                true
            );
            return false;
        }

        const w = Number(weight.value);
        if (!weight.value || isNaN(w) || w <= 0) {
            if (showErrors) showErrorStep5(t("weight_invalid", "Invalid weight"), true);
            return false;
        }

        const length = $$("length");
        const width = $$("width");
        const height = $$("height");
        if (!length || !width || !height) {
            if (showErrors) showErrorStep5(t("dimensions_missing", "Dimensions missing"), true);
            return false;
        }

        const L = Number(length.value);
        const W = Number(width.value);
        const H = Number(height.value);
        if (!length.value || !width.value || !height.value) {
            if (showErrors) showErrorStep5(t("dimensions_required", "Dimensions required"), true);
            return false;
        }
        if (isNaN(L) || isNaN(W) || isNaN(H) || L <= 0 || W <= 0 || H <= 0) {
            if (showErrors) showErrorStep5(t("dimensions_invalid", "Invalid dimensions"), true);
            return false;
        }

        // Description required
        if (
            !packageDescription.value ||
            packageDescription.value.trim() === ""
        ) {
            if (showErrors) showErrorStep5(
                t(
                    "package_description_required",
                    "Package description is required"
                ),
                true
            );
            return false;
        }

        // If COD selected, ensure amount > 0
        if (window.selectedPaymentMethod === "cod") {
            const codInput = $$("cod-amount-input");
            const v = codInput ? +codInput.value : 0;
            if (!codInput || !isFinite(v) || v <= 0) {
                if (showErrors) showErrorStep5(
                    t("cod_amount_required", "Amount must be greater than 0"),
                    true
                );
                return false;
            }
        }

        if (!acceptTerms.checked) {
            if (showErrors) showErrorStep5(
                t("accept_terms_required", "You must accept the terms"),
                true
            );
            return false;
        }

        return true;
    };

    // Initialize Step 5 fields & listeners
    window.populateShippingFormFields = function populateShippingFormFields() {
        // Reset submitting flag when entering the step
        _submittingStep5 = false;
        
        setupPackageTypeHandling();
        ensureDimensionDefaults(); // on first load

        const ids = [
            "package_type",
            "package_number",
            "weight",
            "length",
            "width",
            "height",
            "accept_terms",
            "package_description",
        ];
        ids.forEach((id) => {
            const el = $$(id);
            if (el && !el.dataset.boundStep5) {
                const evt = el.type === "checkbox" ? "change" : "input";
                el.addEventListener(evt, syncNextBtnStep5);
                el.dataset.boundStep5 = "1";
            }
        });

        const btnNext = $$("btn-next");
        if (btnNext && !btnNext.dataset.boundStep5Submit) {
            btnNext.addEventListener(
                "click",
                () => {
                    _submittingStep5 = true;
                    if (!window.validatePackageDetails(true)) {
                        _submittingStep5 = false;
                        return false;
                    }
                    setTimeout(() => {
                        _submittingStep5 = false;
                    }, 100);
                },
                true
            );
            btnNext.dataset.boundStep5Submit = "1";
        }

        // Call validation without showing errors
        syncNextBtnStep5();
    };

    // ----------------- STEP 4: description toggle (clear/reset) -----------------
    (function setupDescriptionHandling() {
        let userDescriptions = [];
        let descriptionsLoaded = false;

        function loadUserDescriptions() {
            if (descriptionsLoaded) return;
            const url =
                window.API_ENDPOINTS?.userDescriptions ||
                "/user/user-descriptions/getUserDescriptions";
            fetch(url)
                .then((r) => r.json())
                .then((data) => {
                    userDescriptions = Array.isArray(data)
                        ? data
                        : data.descriptions || [];
                    populateDescriptionsDropdown();
                    descriptionsLoaded = true;
                })
                .catch(() => {});
        }

        function populateDescriptionsDropdown() {
            const dropdown = $$("existing_descriptions");
            if (!dropdown) return;
            dropdown.innerHTML = `<option value="">${t(
                "select_description",
                "Select description"
            )}</option>`;
            userDescriptions.forEach((desc) => {
                const option = document.createElement("option");
                option.value = desc.id;
                option.textContent =
                    desc.description || desc.title || "Untitled Description";
                option.dataset.description = desc.description || "";
                dropdown.appendChild(option);
            });
        }

        function clearDescriptionInputs() {
            const textarea = $$("package_description");
            const dropdown = $$("existing_descriptions");
            const descriptionIdInput = $$("description_id");
            const isNewInput = $$("is_new_description");

            if (textarea) {
                textarea.value = "";
                textarea.readOnly = false;
                textarea.disabled = false;
                textarea.style.backgroundColor = "";
                textarea.placeholder = t(
                    "enter_package_description",
                    "Enter package description"
                );
            }
            if (dropdown) dropdown.value = "";
            if (descriptionIdInput) descriptionIdInput.value = "";
            if (isNewInput) isNewInput.value = "1";

            syncNextBtnStep5();
        }

        function handleTypeChange() {
            const newRadio = $$("description_new");
            const existingRadio = $$("description_existing");
            const container = $$("existing_descriptions_container");
            const textarea = $$("package_description");
            const isNewInput = $$("is_new_description");

            if (
                !newRadio ||
                !existingRadio ||
                !container ||
                !textarea ||
                !isNewInput
            )
                return;

            if (newRadio.checked) {
                // switching to new: hide dropdown, enable textarea, clear everything
                container.style.display = "none";
                textarea.readOnly = false;
                textarea.disabled = false;
                textarea.style.backgroundColor = "";
                textarea.value = "";
                $$("description_id") && ($$("description_id").value = "");
                isNewInput.value = "1";
            } else if (existingRadio.checked) {
                // switching to existing: show dropdown, disable typing, clear textarea & id
                container.style.display = "block";
                textarea.readOnly = true;
                textarea.disabled = true;
                textarea.style.backgroundColor = "#f8f9fa";
                textarea.value = "";
                $$("existing_descriptions") &&
                    ($$("existing_descriptions").value = "");
                $$("description_id") && ($$("description_id").value = "");
                isNewInput.value = "0";
            }
            syncNextBtnStep5();
        }

        function handleDropdownChange() {
            const dropdown = $$("existing_descriptions");
            const textarea = $$("package_description");
            const descriptionIdInput = $$("description_id");
            if (!dropdown || !textarea || !descriptionIdInput) return;

            const opt = dropdown.options[dropdown.selectedIndex];
            if (opt && opt.value) {
                textarea.value = opt.dataset.description || "";
                descriptionIdInput.value = opt.value;
            } else {
                textarea.value = "";
                descriptionIdInput.value = "";
            }
            syncNextBtnStep5();
        }

        document.addEventListener("DOMContentLoaded", function () {
            const newRadio = $$("description_new");
            const existingRadio = $$("description_existing");
            const dropdown = $$("existing_descriptions");
            const textarea = $$("package_description");

            if (newRadio && !newRadio.dataset.bound) {
                newRadio.addEventListener("change", () => {
                    handleTypeChange();
                    clearDescriptionInputs();
                });
                newRadio.dataset.bound = "1";
            }
            if (existingRadio && !existingRadio.dataset.bound) {
                existingRadio.addEventListener("change", () => {
                    handleTypeChange();
                    clearDescriptionInputs();
                });
                existingRadio.dataset.bound = "1";
            }
            if (dropdown && !dropdown.dataset.bound) {
                dropdown.addEventListener("change", handleDropdownChange);
                dropdown.dataset.bound = "1";
            }
            if (textarea && !textarea.dataset.bound) {
                textarea.addEventListener("input", syncNextBtnStep5);
                textarea.dataset.bound = "1";
            }

            handleTypeChange();
            loadUserDescriptions();
        });

        document.addEventListener("stepChanged", function (e) {
            if (e && e.detail && e.detail.currentStep === 4) {
                loadUserDescriptions();
            }
        });
    })();

    // ----------------- STEP 5: payment (COD min=1 & >0) -----------------
    function companySupportsCOD(company) {
        if (!company) return false;
        if (
            company.hasCod === true ||
            company.supports_cod === true ||
            company.cash_on_delivery === true ||
            company?.cash_on_delivery?.enabled === true ||
            company?.cod?.enabled === true
        )
            return true;

        const methods = Array.isArray(company?.shippingMethods)
            ? company.shippingMethods.map((m) => String(m).toLowerCase().trim())
            : [];
        const aliases = new Set([
            "cashondelivery",
            "cash_on_delivery",
            "cash-on-delivery",
            "cod",
            "cashondelivary",
            "cash on delivery",
        ]);
        return methods.some((m) => aliases.has(m));
    }

    function getAdminCodFee() {
        const c = window.selectedCompany || {};
        const globalAdmin = window.ADMIN_SETTINGS?.cod_fee_per_receiver;
        const candidates = [c._adminCodFee, c.adminCodFee, globalAdmin];
        for (const v of candidates) if (isFinite(+v)) return +v;
        return 0;
    }

    function getCurrencySymbol() {
        const c = window.selectedCompany || {};
        const p = window.companyPricing || window.pricingSummary || {};
        return (
            c.currency_symbol ||
            c.currencySymbol ||
            p.currency_symbol ||
            p.currencySymbol ||
            t("currency_symbol", "SAR")
        );
    }

    function selectedReceiversCount() {
        return Array.isArray(window.selectedReceivers)
            ? window.selectedReceivers.length
            : 0;
    }

    function ensureStyles() {
        if ($$("pay-cards-style")) return;
        const css = document.createElement("style");
        css.id = "pay-cards-style";
        css.textContent = `
        .payment-grid{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}
        .pay-card{border:2px solid #8181814d;border-radius:10px;padding:15px;cursor:pointer;background:#fff}
        .pay-card:hover{box-shadow:0 6px 18px rgb(128 128 128 / 27%)}
        .pay-card.active{border-color:#F6950D;}
        .pay-card .title{display:flex;align-items:center;gap:10px;font-weight:700;margin-bottom:6px}
        .pay-card .muted{color:#6c757d;font-size:.9rem}
        .pay-card input[type="radio"]{display:none}
        .cod-pill{display:inline-block;padding:3px 8px;border-radius:999px;background:#e7f5e8;color:#198754;font-size:.75rem;margin-inline-start:6px}
        .cod-extra{display:none;margin-top:10px}
        .cod-extra.show{display:block}
      `;
        document.head.appendChild(css);
    }

    function ensureHiddenPaymentInput() {
        const form =
            document.querySelector('form[enctype="multipart/form-data"]') ||
            document.querySelector("form");
        let hidden = $$("payment_method_hidden");
        if (!hidden) {
            hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = "payment_method";
            hidden.id = "payment_method_hidden";
            (form || document.body).appendChild(hidden);
        }
        return hidden;
    }

    function buildCard({ value, icon, title, badge, extraHtml }) {
        const badgeHTML = badge ? `<span class="cod-pill">${badge}</span>` : "";
        return `
        <label class="pay-card" data-value="${value}">
          <input type="radio" name="payment_method" value="${value}">
          <div class="title">
            <i class="${icon}"></i>
            <span>${title}${badgeHTML}</span>
          </div>
          ${
              value === "cod"
                  ? `<div id="cod-note-line" class="mt-1 small text-muted"></div>`
                  : ""
          }
          ${extraHtml || ""}
        </label>
      `;
    }

    function renderPaymentOptions() {
        const container = document.querySelector(".payment-options-container");
        if (!container) return;

        ensureStyles();

        // clear previous
        container
            .querySelectorAll(".payment-grid, .pay-card")
            .forEach((el) => el.remove());

        const supportsCOD = companySupportsCOD(window.selectedCompany);
        const adminCodFee = getAdminCodFee();
        const cur = getCurrencySymbol();

        let html = `<div class="payment-grid">`;

        // Wallet card
        html += buildCard({
            value: "wallet",
            icon: "fas fa-wallet text-primary",
            title: t("normal_shipment", "شحنة عادية"),
        });

        // COD card (if supported)
        if (supportsCOD) {
            const codExtraHtml = `
          <div id="cod-extra" class="cod-extra">
            <label for="cod-amount-input" class="form-label small mb-1">
              ${t("cod_amount", "مبلغ التحصيل")}
            </label>
            <div class="input-group">
              <input type="number" min="1" step="0.01" class="form-control" id="cod-amount-input" placeholder="1.00" autocomplete="off" inputmode="decimal" required>
              <span class="input-group-text" id="cod-currency">${cur}</span>
            </div>
            <input type="hidden" name="cod_amount" id="cod-amount-hidden" value="">
          </div>
        `;

            html += buildCard({
                value: "cod",
                icon: "fas fa-money-bill-wave text-success",
                title: t("cash_on_delivery_shippment", "الدفع عند الاستلام"),
                badge: t("cash_on_delivery_available", "متاح"),
                extraHtml: codExtraHtml,
            });
        } else {
            const codLegacy = $$("cash_on_delivery")?.closest(".form-check");
            if (codLegacy) codLegacy.style.display = "none";
        }

        html += `</div>`;
        container.insertAdjacentHTML("afterbegin", html);

        // Wire up behavior
        const grid = container.querySelector(".payment-grid");
        const cards = grid.querySelectorAll(".pay-card");
        const radios = grid.querySelectorAll('input[name="payment_method"]');
        const hiddenMethod = ensureHiddenPaymentInput();

        function toggleCodDetails(show) {
            const box = $$("cod-extra");
            if (!box) return;
            box.classList.toggle("show", !!show);

            const input = $$("cod-amount-input");
            const hidden = $$("cod-amount-hidden");
            if (input) {
                if (show) {
                    input.min = "1";
                    input.required = true;
                    input.setCustomValidity(
                        input.value && +input.value > 0
                            ? ""
                            : t(
                                  "cod_amount_required",
                                  "Amount must be greater than 0"
                              )
                    );
                    if (!input.value) input.focus();
                } else {
                    input.required = false;
                    input.setCustomValidity("");
                    input.value = "";
                    if (hidden) hidden.value = "";
                }
            }
            syncNextBtnStep5();
        }

        function updateCodNote() {
            const noteEl = $$("cod-note-line");
            if (!noteEl) return;
            const isCodSelected = grid.querySelector(
                'input[name="payment_method"][value="cod"]'
            )?.checked;
            if (!isCodSelected) {
                noteEl.textContent = "";
                return;
            }
            const count = Math.max(1, selectedReceiversCount());
            const fee = getAdminCodFee();
            const total = (fee * count).toFixed(2);
            noteEl.textContent = `${count} × ${t(
                "cod_fee_per_receiver",
                "رسوم الدفع عند الاستلام (لكل مستلم)"
            )} = ${total} ${getCurrencySymbol()}`;
        }

        function select(value) {
            cards.forEach((c) =>
                c.classList.toggle("active", c.dataset.value === value)
            );
            radios.forEach((r) => (r.checked = r.value === value));
            window.selectedPaymentMethod = value;
            hiddenMethod.value = value;
            toggleCodDetails(value === "cod");

            const codCheckbox = $$("cash_on_delivery"); // legacy (if present)
            if (codCheckbox) codCheckbox.checked = value === "cod";

            updateCodNote();

            document.dispatchEvent(
                new CustomEvent("paymentMethodChanged", {
                    detail: { method: value },
                })
            );
        }

        // restore selected method if any
        const initial =
            (window.OLD_INPUT && window.OLD_INPUT.payment_method) ||
            hiddenMethod.value ||
            "wallet";
        select(initial);

        // click binding
        cards.forEach((card) =>
            card.addEventListener("click", () => select(card.dataset.value))
        );

        // legacy checkbox binding (optional)
        const codCheckbox = $$("cash_on_delivery");
        if (codCheckbox && !codCheckbox.dataset.bound) {
            codCheckbox.addEventListener("change", () =>
                select(codCheckbox.checked ? "cod" : "wallet")
            );
            codCheckbox.dataset.bound = "1";
        }

        // COD input validation (enforce > 0)
        const codInput = $$("cod-amount-input");
        if (codInput && !codInput.dataset.bound) {
            const sync = () => {
                const v = isFinite(+codInput.value) ? +codInput.value : 0;
                const hidden = $$("cod-amount-hidden");
                if (v > 0) {
                    codInput.setCustomValidity("");
                    if (hidden) hidden.value = v;
                    window.codAmount = v;
                } else {
                    codInput.setCustomValidity(
                        t(
                            "cod_amount_required",
                            "Amount must be greater than 0"
                        )
                    );
                    if (hidden) hidden.value = "";
                    window.codAmount = undefined;
                }
                syncNextBtnStep5();
            };
            codInput.addEventListener("input", sync);
            codInput.addEventListener("change", sync);
            codInput.dataset.bound = "1";
        }

        updateCodNote();
        document.addEventListener("receiversChanged", () => {
            updateCodNote();
            syncNextBtnStep5();
        });
    }

    window.setupPaymentDetails = function setupPaymentDetails() {
        renderPaymentOptions();
    };

    document.addEventListener("DOMContentLoaded", () => {
        const hasStep6 = document.querySelector(
            "#step-6 .payment-options-container"
        );
        if (hasStep6) renderPaymentOptions();
        // Don't call populateShippingFormFields on page load
        // It will be called when step 4 is shown via utilities.js
    });
})();
