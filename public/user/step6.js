(function () {
    const $$ = (id) => document.getElementById(id);
    const toNum = (v, d = 0) => (isFinite(+v) ? +v : d);

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

        const methods = Array.isArray(company.shippingMethods)
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

    function getCompanyCodFee() {
        const c = window.selectedCompany || {};
        const p =
            window.companyPricing ||
            window.pricingSummary ||
            window.currentQuote ||
            {};
        const candidates = [
            c.codPrice,
            c.cod_fee,
            c.codFee,
            c.cash_on_delivery_fee,
            c.cashOnDeliveryFee,
            c?.cash_on_delivery?.fee,
            c?.cod?.fee,
            p.cod_fee,
            p.codFee,
            p?.fees?.cod,
        ];
        for (const v of candidates) if (isFinite(+v)) return +v;
        return 0;
    }

    function getBaseTotal() {
        const p =
            window.companyPricing ||
            window.pricingSummary ||
            window.currentQuote ||
            {};
        const candidates = [
            p.total_without_cod,
            p.totalWithoutCod,
            p.base_total,
            p.baseTotal,
            p.total,
        ];
        for (const v of candidates) if (isFinite(+v)) return +v;
        const n = Array.isArray(window.selectedReceivers)
            ? window.selectedReceivers.length
            : 0;
        const perRec = toNum(p?.per_receiver_fee, 0);
        const base = toNum(p?.base, 0);
        return base + perRec * n;
    }

    function getCurrencySymbol() {
        const c = window.selectedCompany || {};
        const p = window.companyPricing || window.pricingSummary || {};
        return (
            c.currency_symbol ||
            c.currencySymbol ||
            p.currency_symbol ||
            p.currencySymbol ||
            (window.translations && window.translations.currency_symbol) ||
            "SAR"
        );
    }

    function selectedReceiversCount() {
        return Array.isArray(window.selectedReceivers)
            ? window.selectedReceivers.length
            : 0;
    }

    // ---- styles ---------------------------------------------------------------
    function ensureStyles() {
        if ($$("pay-cards-style")) return;
        const css = document.createElement("style");
        css.id = "pay-cards-style";
        css.textContent = `
        .payment-grid{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}
        .pay-card{border:2px solid #fe94001a;border-radius:14px;padding:14px;transition:.2s;cursor:pointer;background:#fff}
        .pay-card:hover{box-shadow:0 6px 18px rgb(254 148 0 / 11%);transform:translateY(-1px)}
        .pay-card.active{border-color:#F6950D;box-shadow:0 0 0 3px rgba(13,110,253,.15)}
        .pay-card .title{display:flex;align-items:center;gap:10px;font-weight:700;margin-bottom:6px}
        .pay-card .muted{color:#6c757d;font-size:.9rem}
        .pay-card input[type="radio"]{display:none}
        .cod-pill{display:inline-block;padding:3px 8px;border-radius:999px;background:#e7f5e8;color:#198754;font-size:.75rem;margin-inline-start:6px}
        .cod-extra{display:none;margin-top:10px}
        .cod-extra.show{display:block}
      `;
        document.head.appendChild(css);
    }

    // Ensure a hidden field exists to mirror the chosen method
    function ensureHiddenPaymentInput() {
        const form =
            document.querySelector('form[enctype="multipart/form-data"]') ||
            document.querySelector("form");
        let hidden = document.getElementById("payment_method_hidden");
        if (!hidden) {
            hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = "payment_method";
            hidden.id = "payment_method_hidden";
            (form || document.body).appendChild(hidden);
        }
        return hidden;
    }

    // ---- UI builders ----------------------------------------------------------
    function buildCard({ value, icon, title, desc, badge, extraHtml }) {
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

    // ---- behavior -------------------------------------------------------------
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
        const t = (k, fb) =>
            (window.translations && window.translations[k]) || fb;

        let html = `<div class="payment-grid">`;

        // Wallet
        html += buildCard({
            value: "wallet",
            icon: "fas fa-wallet text-primary",
            title: t("normal_shipment", "شحنة عادية"),
        });

        // COD
        if (supportsCOD) {
            const desc =
                adminCodFee > 0
                    ? `${t(
                          "cod_fee_per_receiver",
                          "رسوم الدفع عند الاستلام (لكل مستلم)"
                      )} : ${adminCodFee} ${cur}`
                    : t("cod_information", "الدفع عند الاستلام متاح");

            const codExtraHtml = `
          <div id="cod-extra" class="cod-extra">
            <label for="cod-amount-input" class="form-label small mb-1">
              ${
                  (window.translations && window.translations.cod_amount) ||
                  "مبلغ التحصيل"
              }
            </label>
            <div class="input-group">
              <input type="number" min="0" step="0.01" class="form-control" id="cod-amount-input" placeholder="0.00" autocomplete="off" inputmode="decimal">
              <span class="input-group-text" id="cod-currency">${cur}</span>
            </div>
            <!-- submit-friendly hidden field -->
            <input type="hidden" name="cod_amount" id="cod-amount-hidden" value="">
          </div>
        `;

            html += buildCard({
                value: "cod",
                icon: "fas fa-money-bill-wave text-success",
                title: t("cash_on_delivery_shippment", "الدفع عند الاستلام"),
                desc,
                badge: t("cash_on_delivery_available", "متاح"),
                extraHtml: codExtraHtml,
            });
        } else {
            const codLegacy = $$("cash_on_delivery")?.closest(".form-check");
            if (codLegacy) codLegacy.style.display = "none";
        }

        html += `</div>`;
        container.insertAdjacentHTML("afterbegin", html);

        // Wire up
        const grid = container.querySelector(".payment-grid");
        const cards = grid.querySelectorAll(".pay-card");
        const radios = grid.querySelectorAll('input[name="payment_method"]');
        const hiddenMethod = ensureHiddenPaymentInput();

        function toggleCodDetails(show) {
            const box = $$("cod-extra");
            if (!box) return;
            box.classList.toggle("show", !!show);
            if (show) {
                const input = $$("cod-amount-input");
                if (input && !input.value) input.focus();
            }
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
            const cur = getCurrencySymbol();
            const t = (k, fb) =>
                (window.translations && window.translations[k]) || fb;
            const total = (fee * count).toFixed(2);
            noteEl.textContent = `${count} × ${t(
                "cod_fee_per_receiver",
                "رسوم الدفع عند الاستلام (لكل مستلم)"
            )} = ${total} ${cur}`;
        }

        function select(value) {
            cards.forEach((c) =>
                c.classList.toggle("active", c.dataset.value === value)
            );
            radios.forEach((r) => (r.checked = r.value === value));
            window.selectedPaymentMethod = value;
            hiddenMethod.value = value;
            toggleCodDetails(value === "cod");
            const codCheckbox = $$("cash_on_delivery"); // legacy (if exists)
            if (codCheckbox) codCheckbox.checked = value === "cod";
            updateCodNote();

            document.dispatchEvent(
                new CustomEvent("paymentMethodChanged", {
                    detail: { method: value },
                })
            );
        }

        cards.forEach((card) =>
            card.addEventListener("click", () => select(card.dataset.value))
        );

        // ---------- NEW: Restore previous COD amount on render ----------
        const restoreCodAmount = () => {
            const codInput = $$("cod-amount-input");
            const codHidden = $$("cod-amount-hidden");
            if (!codInput || !codHidden) return;

            const existing = toNum(
                (window.OLD_INPUT && window.OLD_INPUT.cod_amount) ??
                    (codHidden.value !== "" ? codHidden.value : undefined) ??
                    window.codAmount,
                0
            );

            if (!isNaN(existing) && existing >= 0) {
                codInput.value = existing;
                codHidden.value = existing;
                window.codAmount = existing;
            }
        };
        restoreCodAmount();
        const initial =
            (window.OLD_INPUT && window.OLD_INPUT.payment_method) ||
            hiddenMethod.value ||
            "wallet";
        select(initial);

        const codCheckbox = $$("cash_on_delivery");
        if (codCheckbox && !codCheckbox.dataset.bound) {
            codCheckbox.addEventListener("change", () => {
                select(codCheckbox.checked ? "cod" : "wallet");
            });
            codCheckbox.dataset.bound = "1";
        }

        const codInput = $$("cod-amount-input");
        if (codInput && !codInput.dataset.bound) {
            const sync = () => {
                const v = isFinite(+codInput.value) ? +codInput.value : 0;
                const hidden = $$("cod-amount-hidden");
                if (hidden) hidden.value = v;
                window.codAmount = v;
            };
            codInput.addEventListener("input", sync);
            codInput.addEventListener("change", sync);
            codInput.dataset.bound = "1";
        }

        updateCodNote();
        document.addEventListener("receiversChanged", () => {
            updateCodNote();
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
    });
})();
