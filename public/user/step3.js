function handleCompanyRequirements() {
    if (!window.selectedCompany) return;

    const userStateField = document.getElementById("user_state");
    const userCityField = document.getElementById("user_city");
    const userCountryField = document.getElementById("user_country");

    [userStateField, userCityField, userCountryField].forEach((el) => {
        if (el) {
            el.style.display = "block";
            el.required = true;
        }
    });

    const languageNote = document.getElementById("language-note");
    if (!languageNote) return;

    if (window.selectedCompany.isEnglish === true) {
        languageNote.style.display = "block";
        languageNote.textContent =
            window.translations?.enter_in_english ||
            "Please enter data in English only";
    } else {
        languageNote.style.display = "none";
    }
}

function setupLocationFields() {
    handleCompanyRequirements();
}

function validateStep3Form() {
    const ids = ["user_state", "user_city", "user_country"];
    for (let id of ids) {
        const el = document.getElementById(id);
        if (el && !el.disabled && el.required && !String(el.value || "").trim())
            return false;
    }
    return true;
}

window.validateStep3Form = validateStep3Form;
window.handleCompanyRequirements = handleCompanyRequirements;
window.setupLocationFields = setupLocationFields;
