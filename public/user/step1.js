(() => {
    window.selectedCompany = null;
    let shippingCompaniesData = [];
    let companiesLoaded = false;
    let companiesFetchController = null;

    function updateStepIndicator(step) {
        const steps = document.querySelectorAll(".step");
        steps.forEach((s, i) => {
            const bubble = s.querySelector(".step-number");
            if (!bubble) return;
            bubble.classList.remove(
                "is-current",
                "is-done",
                "bg-primary",
                "bg-secondary"
            );
            if (i + 1 < step) bubble.classList.add("is-done", "bg-primary");
            else if (i + 1 === step)
                bubble.classList.add("is-current", "bg-primary");
            else bubble.classList.add("bg-secondary");
        });
    }

    function formatMoney(n) {
        if (n === null || n === undefined || isNaN(n)) return "—";
        return `$${(+n).toFixed(2).replace(/\.00$/, "")}`;
    }

    function getNormalizedMethods(methodsRaw) {
        const arr = Array.isArray(methodsRaw) ? methodsRaw : [];
        return [
            ...new Set(
                arr
                    .filter(Boolean)
                    .map((m) => m.toString().trim().toLowerCase())
            ),
        ];
    }

    function buildMethodBadges(tokens) {
        const t = new Set(tokens);
        const badges = [];
        if (t.has("local"))
            badges.push(
                `<span class="badge outline-success">${
                    window.translations?.local || "Local"
                }</span>`
            );
        if (t.has("international"))
            badges.push(
                `<span class="badge outline-info">${
                    window.translations?.international || "International"
                }</span>`
            );
        return badges.join("");
    }

    function prettyMethods(tokens) {
        const labels = [];
        const t = new Set(tokens);
        if (t.has("local")) labels.push(window.translations?.local || "Local");
        if (t.has("international"))
            labels.push(window.translations?.international || "International");
        return labels.join(", ") || "—";
    }

    function enableNext() {
        const btnNext = document.getElementById("btn-next");
        if (!btnNext) return;
        btnNext.disabled = false;
        btnNext.removeAttribute("disabled");
        btnNext.classList.remove("btn-secondary");
        btnNext.classList.add("btn-primary");
    }

    function disableNext() {
        const btnNext = document.getElementById("btn-next");
        if (!btnNext) return;
        btnNext.disabled = true;
        btnNext.setAttribute("disabled", "disabled");
        btnNext.classList.add("btn-secondary");
        btnNext.classList.remove("btn-primary");
    }

    function fetchShippingCompanies(force = false) {
        const container = document.getElementById("companies-container");
        if (!container) return;
        if (companiesLoaded && !force) return;

        companiesLoaded = true;
        if (companiesFetchController) {
            try {
                companiesFetchController.abort();
            } catch {}
        }
        companiesFetchController = new AbortController();

        container.innerHTML = `
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status" style="width:2rem;height:2rem"></div>
          <p class="mt-2">${
              window.translations?.loading_companies ||
              "Loading shipping companies..."
          }</p>
        </div>`;

        const url =
            typeof window.API_ENDPOINTS !== "undefined" &&
            window.API_ENDPOINTS.shippingCompanies
                ? window.API_ENDPOINTS.shippingCompanies
                : "/user/shipping-companies";

        fetch(url, { signal: companiesFetchController.signal })
            .then((r) => {
                if (!r.ok) throw new Error();
                return r.json();
            })
            .then((data) => {
                window.ADMIN_SETTINGS = data?.admin_settings || null;
                const results = data?.results || [];
                shippingCompaniesData = results;
                if (!results.length) {
                    container.innerHTML = `<div class="alert alert-warning text-center mb-0">${
                        window.translations?.no_companies_available ||
                        "No active shipping companies."
                    }</div>`;
                    return;
                }
                displayCompanies(results);
            })
            .catch(() => {
                container.innerHTML = `<div class="alert alert-danger text-center mb-0">${
                    window.translations?.error_loading_companies ||
                    "Error loading companies. Please try again."
                }</div>`;
            })
            .finally(() => {
                companiesFetchController = null;
            });
    }

    function displayCompanies(companies) {
        const container = document.getElementById("companies-container");
        if (!container) return;

        const globalAdminCOD =
            window.ADMIN_SETTINGS?.cod_fee_per_receiver ?? null;
        const globalAdminExtra =
            window.ADMIN_SETTINGS?.extra_weight_price_per_kg ?? null;

        const kgLabel = window.translations?.kg || "kg";
        const maxWeightLabel = window.translations?.max_weight || "Max Weight";

        const html = ['<div class="companies-grid">'];

        companies.forEach((c, i) => {
            const logoUrl = c.logoUrl || c.logo || "/default-logo.png";
            const methods = getNormalizedMethods(c.shippingMethods);

            const localUser =
                c.effectiveLocalPrice ??
                c.userLocalPrice ??
                c.localPrice ??
                null;
            const intlUser =
                c.effectiveInternationalPrice ??
                c.userInternationalPrice ??
                c.internationalPrice ??
                null;

            const adminCodFee = c.adminCodFee ?? globalAdminCOD ?? null;
            const adminExtraPerKg =
                c.adminExtraWeightPrice ?? globalAdminExtra ?? null;

            const hasLocal = methods.includes("local");
            const hasIntl = methods.includes("international");
            const hasCOD =
                c.hasCod === true || methods.includes("cashondelivery");

            const badges = buildMethodBadges(methods);

            html.push(`
          <div class="company-card" data-index="${i}" tabindex="0" aria-label="${
                c.name
            }">
            <div class="card">
              <div class="card-body">
                <div class="company-logo"><img src="${logoUrl}" alt="${
                c.name
            } logo"></div>
                <h5 class="card-title">${c.name}</h5>
                ${badges ? `<div class="meta-row">${badges}</div>` : ""}
                <div class="preview">
                  ${
                      hasIntl && intlUser != null
                          ? `<div class="preview-box"><small>${
                                window.translations
                                    ?.international_shipping_price ||
                                "International Price"
                            }</small><strong>${formatMoney(
                                intlUser
                            )}</strong></div>`
                          : ""
                  }
                  ${
                      hasLocal && localUser != null
                          ? `<div class="preview-box"><small>${
                                window.translations?.local_shipping_price ||
                                "Local Price"
                            }</small><strong>${formatMoney(
                                localUser
                            )}</strong></div>`
                          : ""
                  }
                  ${
                      adminExtraPerKg != null
                          ? `<div class="preview-box"><small>${
                                window.translations?.extra_weight_price ||
                                "Extra Weight Price"
                            }</small><strong>${kgLabel}/${formatMoney(
                                adminExtraPerKg
                            )}</strong></div>`
                          : ""
                  }
                  ${
                      hasCOD && adminCodFee != null
                          ? `<div class="preview-box"><small>${
                                window.translations?.cod_fee_per_receiver ||
                                "COD fee (per receiver)"
                            }</small><strong>${formatMoney(
                                adminCodFee
                            )}</strong></div>`
                          : ""
                  }
                  ${
                      c.maxWeight
                          ? `<div class="preview-box"><small>${maxWeightLabel}</small><strong>${c.maxWeight} ${kgLabel}</strong></div>`
                          : ""
                  }
                </div>
              </div>
            </div>
          </div>`);
        });

        html.push("</div>");
        container.innerHTML = html.join("");

        container.querySelectorAll(".company-card").forEach((card) => {
            card.addEventListener("click", onCardClick);
            card.addEventListener("keyup", (e) => {
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    onCardClick.call(card, e);
                }
            });
            card.addEventListener("keydown", (e) => {
                if (e.key === "ArrowRight" || e.key === "ArrowLeft") {
                    e.preventDefault();
                    const cards = Array.from(
                        container.querySelectorAll(".company-card")
                    );
                    const currentIndex = cards.indexOf(card);
                    const nextIndex =
                        e.key === "ArrowRight"
                            ? (currentIndex + 1) % cards.length
                            : (currentIndex - 1 + cards.length) % cards.length;
                    cards[nextIndex].focus();
                }
            });
        });
    }

    function onCardClick() {
        const idx = parseInt(this.dataset.index, 10);
        const company = shippingCompaniesData[idx];
        if (!company) return;

        document.querySelectorAll(".company-card").forEach((c) => {
            c.classList.remove("selected");
            c.setAttribute("aria-selected", "false");
            const card = c.querySelector(".card");
            if (card) {
                card.style.borderColor = "";
                card.style.boxShadow = "";
                card.style.background = "";
            }
        });

        this.classList.add("selected");
        this.setAttribute("aria-selected", "true");
        const selectedCard = this.querySelector(".card");
        if (selectedCard) {
            selectedCard.style.boxShadow =
                "0 0 0 3px rgba(13, 110, 253, 0.25), 0 8px 25px rgba(0, 0, 0, 0.15)";
            selectedCard.style.background = "#f8f9ff";
        }

        window.selectedCompany = company;

        if (company.shippingMethods) {
            if (Array.isArray(company.shippingMethods))
                company.shippingMethods = company.shippingMethods.map((m) =>
                    typeof m === "string" ? m.toLowerCase() : m
                );
        } else {
            company.shippingMethods = ["local", "international"];
        }

        const globalAdminCOD =
            window.ADMIN_SETTINGS?.cod_fee_per_receiver ?? null;
        const globalAdminExtra =
            window.ADMIN_SETTINGS?.extra_weight_price_per_kg ?? null;

        company._localUserPrice =
            company.effectiveLocalPrice ??
            company.userLocalPrice ??
            company.localPrice ??
            null;
        company._intlUserPrice =
            company.effectiveInternationalPrice ??
            company.userInternationalPrice ??
            company.internationalPrice ??
            null;
        company._adminCodFee = company.adminCodFee ?? globalAdminCOD ?? null;
        company._adminExtraKg =
            company.adminExtraWeightPrice ?? globalAdminExtra ?? null;
        company.displayNameEn = company.displayNameEn || company.name;

        const nameEl = document.getElementById("selected-company-name");
        if (nameEl)
            nameEl.textContent = company.displayNameEn || company.name || "";

        const idInput = document.getElementById("shipping_company_id");
        if (idInput) idInput.value = company.id || company._id || "";

        const methodInput = document.getElementById("shipping_method");
        if (methodInput) methodInput.value = "";
        window.selectedMethod = null;

        enableNext();

        showSelectedCompanySummary(company);
        if (typeof updateStepIndicator === "function") updateStepIndicator(1);
        document.dispatchEvent(
            new CustomEvent("shippingCompanySelected", { detail: { company } })
        );
    }

    function showSelectedCompanySummary(company) {
        const box = document.getElementById("company-selected-summary");
        if (!box) return;

        const tokens = getNormalizedMethods(company.shippingMethods);
        const methodsPretty = prettyMethods(tokens);
        const adminCOD = company._adminCodFee;
        const adminExtra = company._adminExtraKg;
        const kgLabel = window.translations?.kg || "kg";

        box.innerHTML = `
        <div class="selected-summary">
          <div class="selected-summary__title">
            ${window.translations?.shipping_company || "Shipping Company"}:
            <strong>${company.displayNameEn || company.name || ""}</strong>
          </div>
          <div class="selected-summary__grid">
            <div class="selected-summary__item"><small>${
                window.translations?.method || "Method(s)"
            }</small><strong>${methodsPretty}</strong></div>
            <div class="selected-summary__item"><small>${
                window.translations?.international_shipping_price ||
                "International Price"
            }</small><strong>${
            company._intlUserPrice != null
                ? formatMoney(company._intlUserPrice)
                : "—"
        }</strong></div>
            <div class="selected-summary__item"><small>${
                window.translations?.local_shipping_price || "Local Price"
            }</small><strong>${
            company._localUserPrice != null
                ? formatMoney(company._localUserPrice)
                : "—"
        }</strong></div>
            <div class="selected-summary__item"><small>${
                window.translations?.cod_fee_per_receiver ||
                "COD fee (per receiver)"
            }</small><strong>${
            (tokens.includes("cashondelivery") || company.hasCod) &&
            adminCOD != null
                ? formatMoney(adminCOD)
                : "—"
        }</strong></div>
            <div class="selected-summary__item"><small>${
                window.translations?.extra_weight_price || "Extra Weight Price"
            }</small><strong>${
            adminExtra != null ? `${kgLabel}/${formatMoney(adminExtra)}` : "—"
        }</strong></div>
            <div class="selected-summary__item"><small>${
                window.translations?.max_weight || "Max Weight"
            }</small><strong>${
            company.maxWeight ? `${company.maxWeight} ${kgLabel}` : "—"
        }</strong></div>
          </div>
        </div>`;
        box.style.display = "block";
    }

    function clearStepData(step) {
        if (step === 1) {
            window.selectedCompany = null;
            const idInput = document.getElementById("shipping_company_id");
            if (idInput) idInput.value = "";
            const box = document.getElementById("company-selected-summary");
            if (box) box.innerHTML = "";
            disableNext();
        }
    }

    document.addEventListener(
        "DOMContentLoaded",
        () => {
            if (document.getElementById("companies-container"))
                fetchShippingCompanies();
            const btnPrev = document.getElementById("btn-prev");
            if (btnPrev && !btnPrev.dataset.boundNav) {
                btnPrev.addEventListener("click", () => {
                    if (
                        window.currentStep > 1 &&
                        typeof window.goToStep === "function"
                    )
                        window.goToStep(window.currentStep - 1);
                });
                btnPrev.dataset.boundNav = "1";
            }
        },
        { once: true }
    );

    window.initShippingCompanies = function () {
        companiesLoaded = false;
        fetchShippingCompanies(true);
    };
    window.clearShippingStepData = clearStepData;
    window.updateShippingStepIndicator = updateStepIndicator;
})();
