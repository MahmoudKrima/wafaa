(() => {
    let selectedCompany = null;
    let selectedMethod = null;

    function loadCompanies() {
        console.log('Loading companies...');
    }

    function selectCompany(company) {
        selectedCompany = company;
        selectedMethod = null; 
        updateCompanyDisplay(company);
        window.enableNext();
    }

    function selectMethod(method) {
        selectedMethod = method;
        updateMethodDisplay(method);
        window.enableNext();
    }

    function updateCompanyDisplay(company) {
        const companyNameElement = document.getElementById('selected-company-name');
        if (companyNameElement) {
            companyNameElement.textContent = company.name;
        }
    }

    function updateMethodDisplay(method) {
        const methodElement = document.getElementById('selected-method');
        if (methodElement) {
            methodElement.textContent = method;
        }
    }

    function validateStep() {
        if (!selectedCompany) {
            alert('Please select a shipping company');
            return false;
        }
        
        if (!selectedMethod) {
            alert('Please select a shipping method');
            return false;
        }
        
        return true;
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadCompanies();
    });

    window.selectCompany = selectCompany;
    window.selectMethod = selectMethod;
    window.validateStep = validateStep;
})();
