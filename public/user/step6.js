// STEP 6 — Payment details (idempotent)
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

    // --- NEW: always prefer ADMIN fee (company._adminCodFee), fallback to global admin settings
    function getAdminCodFee() {
        const c = window.selectedCompany || {};
        const globalAdmin = window.ADMIN_SETTINGS?.cod_fee_per_receiver;
        const candidates = [
            c._adminCodFee, // set in step1 on selection
            c.adminCodFee, // just in case
            globalAdmin,
        ];
        for (const v of candidates) if (isFinite(+v)) return +v;
        return 0;
    }

    // kept for completeness (if you still want to show company fee somewhere)
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
            window.translations?.currency_symbol ||
            "SAR"
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
        .pay-card{border:1px solid #e9ecef;border-radius:14px;padding:14px;transition:.2s;cursor:pointer;background:#fff}
        .pay-card:hover{box-shadow:0 6px 18px rgba(0,0,0,.06);transform:translateY(-1px)}
        .pay-card.active{border-color:#0d6efd;box-shadow:0 0 0 3px rgba(13,110,253,.15)}
        .pay-card .title{display:flex;align-items:center;gap:10px;font-weight:700;margin-bottom:6px}
        .pay-card .muted{color:#6c757d;font-size:.9rem}
        .pay-card input[type="radio"]{display:none}
        .cod-pill{display:inline-block;padding:3px 8px;border-radius:999px;background:#e7f5e8;color:#198754;font-size:.75rem;margin-inline-start:6px}
      `;
        document.head.appendChild(css);
    }

    function buildCard({ value, icon, title, desc, badge }) {
        const badgeHTML = badge ? `<span class="cod-pill">${badge}</span>` : "";
        return `
        <label class="pay-card" data-value="${value}">
          <input type="radio" name="payment_method" value="${value}">
          <div class="title">
            <i class="${icon}"></i>
            <span>${title}${badgeHTML}</span>
          </div>
          <div class="muted">${desc}</div>
          ${
              value === "cod"
                  ? `<div id="cod-note-line" class="mt-1 small text-muted"></div>`
                  : ""
          }
        </label>
      `;
    }

    function renderPaymentOptions() {
        const container = document.querySelector(".payment-options-container");
        if (!container) return;

        ensureStyles();
        container
            .querySelectorAll(".payment-grid, .pay-card")
            .forEach((el) => el.remove());

        const supportsCOD = companySupportsCOD(window.selectedCompany);
        const adminCodFee = getAdminCodFee();
        const cur = getCurrencySymbol();
        const t = (k, fb) =>
            (window.translations && window.translations[k]) || fb;

        let html = `<div class="payment-grid">`;

        html += buildCard({
            value: "wallet",
            icon: "fas fa-wallet text-primary",
            title: t("wallet", "المحفظة"),
            desc: t("payment_wallet_desc", "يمكنك الدفع بالمحفظة"),
        });

        if (supportsCOD) {
            const desc =
                adminCodFee > 0
                    ? `${t(
                          "cod_fee_per_receiver",
                          "رسوم الدفع عند الاستلام (لكل مستلم)"
                      )} : ${adminCodFee} ${cur}`
                    : t("cod_information", "الدفع عند الاستلام متاح");
            html += buildCard({
                value: "cod",
                icon: "fas fa-money-bill-wave text-success",
                title: t("cash_on_delivery", "الدفع عند الاستلام"),
                desc,
                badge: t("cash_on_delivery_available", "متاح"),
            });
        } else {
            const codLegacy = $$("cash_on_delivery")?.closest(".form-check");
            if (codLegacy) codLegacy.style.display = "none";
        }

        html += `</div>`;
        container.insertAdjacentHTML("afterbegin", html);

        const grid = container.querySelector(".payment-grid");
        const cards = grid.querySelectorAll(".pay-card");
        const radios = grid.querySelectorAll('input[name="payment_method"]');

        function select(value) {
            cards.forEach((c) =>
                c.classList.toggle("active", c.dataset.value === value)
            );
            radios.forEach((r) => (r.checked = r.value === value));
            toggleCodDetails(value === "cod");
            const codCheckbox = $$("cash_on_delivery");
            if (codCheckbox) codCheckbox.checked = value === "cod";
            updateCodNote(); // NEW: refresh the note when switching
        }

        cards.forEach((card) =>
            card.addEventListener("click", () => select(card.dataset.value))
        );
        select("wallet");

        const codCheckbox = $$("cash_on_delivery");
        if (codCheckbox && !codCheckbox.dataset.bound) {
            codCheckbox.addEventListener("change", () => {
                select(codCheckbox.checked ? "cod" : "wallet");
            });
            codCheckbox.dataset.bound = "1";
        }

        // initial note render (in case COD is pre-checked externally)
        updateCodNote();
    }

    function toggleCodDetails(show) {
        // COD details are now shown in step 7 payment details card
        // No need to toggle visibility in step 6
        return;
    }

    function updateCodNote() {
        const noteEl = $$("cod-note-line");
        if (!noteEl) return;
        const isCodSelected = document.querySelector(
            'input[name="payment_method"][value="cod"]'
        )?.checked;
        if (!isCodSelected) {
            noteEl.textContent = "";
            return;
        }

        const count = Math.max(1, selectedReceiversCount());
        const fee = getAdminCodFee();
        const cur = getCurrencySymbol();
        const total = (fee * count).toFixed(2);
        const t = (k, fb) =>
            (window.translations && window.translations[k]) || fb;

        // Example: "3 × COD fee (per receiver) = 45 ﷼"
        noteEl.textContent = `${count} × ${t(
            "cod_fee_per_receiver",
            "رسوم الدفع عند الاستلام (لكل مستلم)"
        )} = ${total} ${cur}`;
    }

    function updateCodDisplay() {
        // COD display is now handled in step 7 payment details card
        // No need to update elements in step 6
        return;
    }

    // expose
    window.setupPaymentDetails = function setupPaymentDetails() {
        renderPaymentOptions();
        const codCheckbox = $$("cash_on_delivery");
        // toggleCodDetails(!!(codCheckbox && codCheckbox.checked)); // No longer needed

        document.addEventListener("receiversChanged", () => {
            // keep COD totals + note in sync with # of receivers
            const codOn =
                $$("cash_on_delivery")?.checked ||
                document.querySelector('.pay-card[data-value="cod"].active');
            updateCodNote();
            // if (codOn) updateCodDisplay(); // No longer needed
        });
    };
})();
