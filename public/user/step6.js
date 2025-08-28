// STEP 6 — Payment details (idempotent)
(function () {
    const $$ = (id) => document.getElementById(id);
    const toNum = (v, d = 0) => (isFinite(+v) ? +v : d);

    function companySupportsCOD(company) {
        if (!company) return false;
        if (
            Array.isArray(company.shippingMethods) &&
            company.shippingMethods.includes("cashOnDelivery")
        )
            return true;
        if (company.supports_cod === true) return true;
        if (company.cash_on_delivery === true) return true;
        if (company.cash_on_delivery && company.cash_on_delivery.enabled)
            return true;
        if (company.cod && company.cod.enabled) return true;
        return false;
    }

    function getCodFee() {
        const c = window.selectedCompany || {};
        const p =
            window.companyPricing ||
            window.pricingSummary ||
            window.currentQuote ||
            {};
        const candidates = [
            c.codPrice, // <-- your object
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
            "﷼"
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
        .pay-card{border:2px solid #F6950D;border-radius:14px;padding:14px;transition:.2s;cursor:pointer;background:#fff}
        .pay-card:hover{box-shadow:0 6px 18px rgba(0,0,0,.06);transform:translateY(-1px)}
        .pay-card.active{border-color:#F6950D;box-shadow:0 0 0 3px rgba(13,110,253,.15)}
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
        </label>
      `;
    }

    function renderPaymentOptions() {
        const container = document.querySelector(".payment-options-container");
        if (!container) return;

        ensureStyles();

        // Remove any previous grid/cards to avoid duplicates
        container
            .querySelectorAll(".payment-grid, .pay-card")
            .forEach((el) => el.remove());

        const supportsCOD = companySupportsCOD(window.selectedCompany);
        const codFee = getCodFee();
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
                codFee > 0
                    ? `${t(
                          "cod_fee",
                          "رسوم الدفع عند الاستلام"
                      )}: ${codFee} ${cur}`
                    : t("cod_information", "الدفع عند الاستلام متاح");
            html += buildCard({
                value: "cod",
                icon: "fas fa-money-bill-wave text-success",
                title: t("cash_on_delivery", "الدفع عند الاستلام"),
                desc,
                badge: t("cash_on_delivery_available", "متاح"),
            });
        } else {
            // hide legacy checkbox if no COD
            const codLegacy = $$("cash_on_delivery")?.closest(".form-check");
            if (codLegacy) codLegacy.style.display = "none";
        }

        html += `</div>`;
        // Replace content (not append) to prevent duplication
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
        }

        cards.forEach((card) =>
            card.addEventListener("click", () => select(card.dataset.value))
        );

        // default to wallet
        select("wallet");

        // keep legacy checkbox in sync if present
        const codCheckbox = $$("cash_on_delivery");
        if (codCheckbox && !codCheckbox.dataset.bound) {
            codCheckbox.addEventListener("change", () => {
                select(codCheckbox.checked ? "cod" : "wallet");
            });
            codCheckbox.dataset.bound = "1";
        }
    }

    function toggleCodDetails(show) {
        const wrap = $$("cod_details");
        if (!wrap) return;
        wrap.style.display = show ? "block" : "none";
        if (show) updateCodDisplay();
    }

    function updateCodDisplay() {
        const codPriceEl = $$("cod_price_display");
        const totalWithCodEl = $$("total_with_cod_display");
        if (!codPriceEl || !totalWithCodEl) return;

        const cur = getCurrencySymbol();
        const fee = getCodFee();
        const count = Math.max(1, selectedReceiversCount());
        const perReceiver = true; // flip to false if your fee is flat per shipment
        const baseTotal = getBaseTotal();
        const codTotalFee = perReceiver ? fee * count : fee;

        codPriceEl.textContent = perReceiver
            ? `${fee} ${cur} × ${count}`
            : `${fee} ${cur}`;
        totalWithCodEl.textContent = `${(baseTotal + codTotalFee).toFixed(
            2
        )} ${cur}`;
    }

    // Exposed to your flow
    window.setupPaymentDetails = function setupPaymentDetails() {
        renderPaymentOptions();
        const codCheckbox = $$("cash_on_delivery");
        toggleCodDetails(!!(codCheckbox && codCheckbox.checked));
        document.addEventListener("receiversChanged", () => {
            const codOn = $$("cash_on_delivery")?.checked;
            if (codOn) updateCodDisplay();
        });
    };
})();
