(() => {
    window.selectedUser = null;
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
        return `${(+n).toFixed(2).replace(/\.00$/, "")} ${
            window.translations?.currency_symbol || "SAR"
        }`;
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

        const userId = window.selectedUser?.id;
        if (!userId) {
            container.innerHTML =
                '<div class="alert alert-warning">No user selected</div>';
            return;
        }

        fetch(`/admin/admin-shipping/user-companies?user_id=${userId}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.results && data.results.length > 0) {
                    displayCompanies(data.results);
                } else {
                    container.innerHTML =
                        '<div class="alert alert-info">No shipping companies available for this user</div>';
                }
            })
            .catch((error) => {
                console.error("Error fetching shipping companies:", error);
                container.innerHTML =
                    '<div class="alert alert-danger">Error loading shipping companies</div>';
            });
    }

    function displayCompanies(companies) {
        const container = document.getElementById("companies-container");
        if (!container) return;

        const companiesHtml = companies
            .map((company) => {
                const methods = getNormalizedMethods(company.shippingMethods);
                const methodBadges = buildMethodBadges(methods);
                const methodText = prettyMethods(methods);

                return `
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <img src="${
                                        company.logoUrl ||
                                        "/default-company-logo.png"
                                    }" 
                                         alt="Company Logo" 
                                         class="me-3" 
                                         style="width:60px;height:60px;object-fit:contain;">
                                    <div>
                                        <h6 class="mb-1">${company.name}</h6>
                                        <small class="text-muted">${
                                            company.serviceName ||
                                            "Shipping Service"
                                        }</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Methods:</strong> ${methodText}
                                    ${
                                        methodBadges
                                            ? `<div class="mt-2">${methodBadges}</div>`
                                            : ""
                                    }
                                </div>
                                <div class="mb-3">
                                    <strong>Max Weight:</strong> ${
                                        company.maxWeight || "N/A"
                                    } kg
                                </div>
                                <div class="mb-3">
                                    <strong>Local Price:</strong> ${
                                        company.userLocalPrice
                                            ? formatMoney(
                                                  company.userLocalPrice
                                              )
                                            : "N/A"
                                    }
                                </div>
                                <div class="mb-3">
                                    <strong>International Price:</strong> ${
                                        company.userInternationalPrice
                                            ? formatMoney(
                                                  company.userInternationalPrice
                                              )
                                            : "N/A"
                                    }
                                </div>
                                <div class="mb-3">
                                    <strong>Extra Weight Price:</strong> ${formatMoney(
                                        company.adminExtraWeightPrice
                                    )} per kg
                                </div>
                                <div class="mb-3">
                                    <strong>COD Fee:</strong> ${formatMoney(
                                        company.adminCodFee
                                    )} per receiver
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" 
                                        class="btn btn-primary btn-sm w-100"
                                        onclick="selectCompany('${
                                            company.id
                                        }')">
                                    Select Company
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            })
            .join("");

        container.innerHTML = companiesHtml;
    }

    function selectCompany(companyId) {
        const company = shippingCompaniesData.find((c) => c.id === companyId);
        if (!company) return;

        window.selectedCompany = company;
        window.selectedCompanyId = companyId;
        localStorage.setItem("selectedCompany", JSON.stringify(company));
        enableNext();
        const container = document.getElementById("companies-container");
        if (container) {
            container.innerHTML = `
                <div class="alert alert-success">
                    <h6>Company Selected!</h6>
                    <p><strong>${company.name}</strong> has been selected.</p>
                    <p>Click "Next" to continue to the next step.</p>
                </div>
            `;
        }
    }

    // Initialize when DOM is ready
    document.addEventListener("DOMContentLoaded", function () {
        // Load user data from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get("user_id");

        if (userId) {
            // Fetch user data and companies
            fetchUserData(userId);
        }
    });

    function fetchUserData(userId) {
        // Fetch user information and shipping companies
        Promise.all([
            fetch(
                `/admin/admin-shipping/user-companies?user_id=${userId}`
            ).then((r) => r.json()),
            fetch(
                `/admin/admin-shipping/user-receivers?user_id=${userId}`
            ).then((r) => r.json()),
            fetch(`/admin/admin-shipping/user-wallet?user_id=${userId}`).then(
                (r) => r.json()
            ),
        ])
            .then(([companiesData, receiversData, walletData]) => {
                // Store data globally
                window.companiesData = companiesData;
                window.receiversData = receiversData;
                window.walletData = walletData;

                // Load companies
                if (companiesData.results && companiesData.results.length > 0) {
                    displayCompanies(companiesData.results);
                }
            })
            .catch((error) => {
                console.error("Error fetching data:", error);
            });
    }

    // Make functions globally available
    window.selectCompany = selectCompany;
    window.fetchShippingCompanies = fetchShippingCompanies;
})();
