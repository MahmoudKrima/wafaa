function handleCompanyRequirements() {
    if (!selectedCompany) return;

    const userStateField = document.getElementById("user_state");
    const userCityField = document.getElementById("user_city");
    const userCountryField = document.getElementById("user_country");

    if (selectedCompany.hasState) {
        if (userStateField) {
            userStateField.style.display = "block";
            userStateField.required = true;
        }
        if (userCityField) {
            userCityField.style.display = "block";
            userCityField.required = true;
        }
        if (userCountryField) {
            userCountryField.style.display = "block";
            userCountryField.required = true;
        }
    } else {
        if (userStateField) {
            userStateField.style.display = "block";
            userStateField.required = true;
        }
        if (userCityField) {
            userCityField.style.display = "block";
            userCityField.required = true;
        }
        if (userCountryField) {
            userCountryField.style.display = "block";
            userCountryField.required = true;
        }
    }

    if (!selectedCompany.isEnglish) {
        const languageNote = document.getElementById("language-note");
        if (languageNote) {
            languageNote.style.display = "block";
            languageNote.textContent = "يرجى إدخال البيانات باللغة العربية فقط";
        }
    } else {
        const languageNote = document.getElementById("language-note");
        if (languageNote) {
            languageNote.style.display = "none";
        }
    }
}

function setupLocationFields() {
    if (!selectedCompany) return;

    const userStateField = document.getElementById("user_state");
    const userCityField = document.getElementById("user_city");
    const userCountryField = document.getElementById("user_country");

    if (selectedCompany.hasState) {
        if (userStateField) {
            userStateField.style.display = "block";
            userStateField.required = true;
        }
        if (userCityField) {
            userCityField.style.display = "block";
            userCityField.required = true;
        }
        if (userCountryField) {
            userCountryField.style.display = "block";
            userCountryField.required = true;
        }
    } else {
        if (userStateField) {
            userStateField.style.display = "block";
            userStateField.required = true;
        }
        if (userCityField) {
            userCityField.style.display = "block";
            userCityField.required = true;
        }
        if (userCountryField) {
            userCountryField.style.display = "block";
            userCountryField.required = true;
        }
    }

    if (!selectedCompany.isEnglish) {
        const languageNote = document.getElementById("language-note");
        if (languageNote) {
            languageNote.style.display = "block";
            languageNote.textContent = "يرجى إدخال البيانات باللغة العربية فقط";
        }
    } else {
        const languageNote = document.getElementById("language-note");
        languageNote.style.display = "none";
    }
}
