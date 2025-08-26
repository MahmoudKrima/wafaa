(() => {
    const SAUDI_ID = "65fd1a1c1fdbc094e3369b29";
  
    let appLocale = (
      document.documentElement?.lang ||
      document.querySelector("[data-app-locale]")?.dataset.appLocale ||
      window.APP_LOCALE ||
      "en"
    ).toLowerCase();
  
    window.selectedReceivers = Array.isArray(window.selectedReceivers)
      ? window.selectedReceivers
      : [];
    window._receiversMap = window._receiversMap || {};
    let initialExistingMap = {};
  
    function t(k, fallback) {
      return (window.translations && window.translations[k]) || fallback || k;
    }
  
    function labelByLocale(nameObj) {
      if (!nameObj) return "";
      if (typeof nameObj === "string") return nameObj;
      if (appLocale === "ar" && (nameObj.ar || nameObj.AR))
        return nameObj.ar || nameObj.AR;
      if (nameObj.en || nameObj.EN) return nameObj.en || nameObj.EN;
      const vals = Object.values(nameObj);
      return typeof vals[0] === "string" ? vals[0] : "";
    }
  
    function displayName(maybeLocalized) {
      if (!maybeLocalized) return "";
      if (typeof maybeLocalized === "object") return labelByLocale(maybeLocalized);
      if (typeof maybeLocalized === "string") {
        const s = maybeLocalized.trim();
        if (s.startsWith("{") && s.endsWith("}")) {
          try {
            return labelByLocale(JSON.parse(s));
          } catch {}
        }
        return s;
      }
      return String(maybeLocalized);
    }
  
    function toast(msg, type = "success") {
      let box = document.getElementById("toast-box");
      if (!box) {
        box = document.createElement("div");
        box.id = "toast-box";
        box.style.position = "fixed";
        box.style.zIndex = "9999";
        box.style.top = "20px";
        if (appLocale === "ar") {
          box.style.left = "20px";
          box.style.right = "auto";
        } else {
          box.style.right = "20px";
          box.style.left = "auto";
        }
        document.body.appendChild(box);
      }
      const el = document.createElement("div");
      el.style.minWidth = "240px";
      el.style.marginBottom = "10px";
      el.style.padding = "10px 14px";
      el.style.borderRadius = "6px";
      el.style.color = type === "error" ? "#842029" : "#0f5132";
      el.style.background = type === "error" ? "#f8d7da" : "#d1e7dd";
      el.style.border = "1px solid " + (type === "error" ? "#f5c2c7" : "#badbcc");
      el.textContent = msg;
      box.appendChild(el);
      setTimeout(() => el.remove(), 2200);
    }
  
    function fetchJson(url, opts) {
      return fetch(url, opts)
        .then((r) => (r.ok ? r.json() : Promise.reject()))
        .catch(() => []);
    }
  
    function loadReceivers() {
      const select = document.getElementById("receiver_select");
      if (!select) return;
      fetchJson("/receivers")
        .then((data) => {
          const arr = Array.isArray(data) ? data : (data && data.results) || [];
          populateReceiverSelect(arr);
          initialExistingMap = { ...window._receiversMap };
        })
        .catch(() => {
          select.innerHTML = `<option value="">${t(
            "no_receivers_found",
            "No receivers found"
          )}</option>`;
        });
      wireUI();
      refreshSelectedReceiversView();
      syncNextButton();
      hideDevButtons();
    }
  
    function hideDevButtons() {
      document
        .querySelectorAll('#existing_receiver_section button[onclick*="test"]')
        .forEach((btn) => (btn.style.display = "none"));
    }
  
    function populateReceiverSelect(receivers) {
      const select = document.getElementById("receiver_select");
      if (!select) return;
      select.innerHTML = `<option value="">${t(
        "choose_receiver",
        "Choose receiver"
      )}</option>`;
      window._receiversMap = {};
      receivers.forEach((r) => {
        const id = r.id || r._id || String(Math.random());
        window._receiversMap[id] = r;
      });
      const alreadyChosenIds = new Set(
        window.selectedReceivers.filter((x) => !x.isNew).map((x) => x.id)
      );
      Object.keys(window._receiversMap).forEach((id) => {
        if (alreadyChosenIds.has(id)) return;
        const rec = window._receiversMap[id];
        const opt = document.createElement("option");
        opt.value = id;
        opt.textContent = rec.name || rec.full_name || rec.email || id;
        select.appendChild(opt);
      });
    }
  
    function wireUI() {
      const addBtn = document.getElementById("add-receiver-btn");
      const receiverSelect = document.getElementById("receiver_select");
      const existingRadio = document.getElementById("existing_receiver");
      const newRadio = document.getElementById("new_receiver");
      const newSection = document.getElementById("new_receiver_section");
      const existingSection = document.getElementById("existing_receiver_section");
  
      if (receiverSelect && !receiverSelect.dataset.bound) {
        receiverSelect.addEventListener("change", () => {});
        receiverSelect.dataset.bound = "1";
      }
  
      const onAdd = () => {
        if (existingRadio && existingRadio.checked) return addExisting();
        if (newRadio && newRadio.checked) return addNew();
        toast(t("select_receiver", "Please choose receiver type"), "error");
      };
  
      if (addBtn && !addBtn.dataset.bound) {
        addBtn.addEventListener("click", onAdd);
        addBtn.dataset.bound = "1";
      }
  
      if (existingRadio && !existingRadio.dataset.bound) {
        existingRadio.addEventListener("change", function () {
          if (this.checked) {
            if (existingSection) existingSection.style.display = "block";
            if (newSection) {
              clearReceiverForm();
              newSection.style.display = "none";
            }
          }
        });
        existingRadio.dataset.bound = "1";
      }
  
      if (newRadio && !newRadio.dataset.bound) {
        newRadio.addEventListener("change", function () {
          if (this.checked) {
            if (existingSection) existingSection.style.display = "none";
            if (newSection) newSection.style.display = "block";
            clearReceiverForm();
            setupReceiverFormByShippingType();
          }
        });
        newRadio.dataset.bound = "1";
      }
    }
  
    function addExisting() {
      const select = document.getElementById("receiver_select");
      const id = select ? select.value : "";
      if (!id || !window._receiversMap[id]) {
        toast(t("select_receiver", "Please select a receiver"), "error");
        return;
      }
      const raw = window._receiversMap[id];
      const item = normalizeReceiver(raw);
      item.isNew = false;
      if (!canAdd(item)) return;
      pushReceiver(item);
      const opt = select.querySelector(`option[value="${id}"]`);
      if (opt) opt.remove();
      if (select) select.value = "";
      resetReceiverTypeSelection();
      toast(t("receiver_added", "Receiver added"));
    }
  
    function addNew() {
      const item = {
        id: `tmp_${Date.now()}`,
        isNew: true,
        name: valueOf("name"),
        phone: valueOf("phone"),
        additional_phone: valueOf("additional_phone"),
        email: valueOf("email"),
        address: valueOf("address"),
        postal_code: valueOf("postal_code"),
        country_id: valueOf("country"),
        state_id: valueOf("state"),
        city_id: valueOf("city"),
        country_name: displayName(textOf("country")),
        state_name: displayName(textOf("state")),
        city_name: displayName(textOf("city")),
      };
      if (
        !item.name ||
        !item.phone ||
        !item.address ||
        !item.country_id ||
        !item.state_id ||
        !item.city_id
      ) {
        toast(
          t("add_receiver_error", "Please fill all required receiver fields"),
          "error"
        );
        return;
      }
      if (!canAdd(item)) return;
      pushReceiver(item);
      clearReceiverForm();
      resetReceiverTypeSelection();
      toast(t("receiver_added", "Receiver added"));
    }
  
    function canAdd(item) {
      const phone = (item.phone || "").trim();
      const email = (item.email || "").trim().toLowerCase();
      const name = (item.name || "").trim().toLowerCase();
      const dup = (r) => {
        const rp = (r.phone || "").trim();
        const re = (r.email || "").trim().toLowerCase();
        const rn = (r.name || "").trim().toLowerCase();
        if (phone && rp && phone === rp) return true;
        if (email && re && email === re) return true;
        if (name && rn && phone && rp && name === rn && phone === rp) return true;
        return false;
      };
      if (window.selectedReceivers.some(dup)) {
        toast(t("receiver_already_selected", "Receiver already selected"), "error");
        return false;
      }
      return true;
    }
  
    function valueOf(id) {
      const el = document.getElementById(id);
      return el ? el.value : "";
    }
  
    function textOf(id) {
      const el = document.getElementById(id);
      if (!el) return "";
      if (el.tagName === "SELECT") {
        const opt = el.options[el.selectedIndex];
        return opt ? opt.textContent : "";
      }
      return el.value || "";
    }
  
    function normalizeReceiver(r) {
      const countryName = r.country?.name || r.country_name;
      const stateName = r.state?.name || r.state_name;
      const cityName = r.city?.name || r.city_name;
      return {
        id: r.id || r._id || `tmp_${Date.now()}`,
        name: r.name || r.full_name || "",
        phone: r.phone || "",
        additional_phone: r.additional_phone || r.alt_phone || "",
        email: r.email || "",
        address: r.address || "",
        postal_code: r.postal_code || "",
        country_id:
          r.country_id || r.countryId || r.country || (r.country && r.country.id) || "",
        state_id:
          r.state_id || r.stateId || r.state || (r.state && r.state.id) || "",
        city_id: r.city_id || r.cityId || r.city || (r.city && r.city.id) || "",
        country_name: displayName(countryName),
        state_name: displayName(stateName),
        city_name: displayName(cityName),
      };
    }
  
    function pushReceiver(item) {
      window.selectedReceivers.push(item);
      writeHiddenReceivers();
      refreshSelectedReceiversView();
      syncNextButton();
      document.dispatchEvent(new CustomEvent("receiversChanged"));
    }
  
    function writeHiddenReceivers() {
      const hidden = document.getElementById("selected_receivers_hidden");
      if (hidden) hidden.value = JSON.stringify(window.selectedReceivers || []);
    }
  
    function refreshSelectedReceiversView() {
      const box = document.getElementById("receivers-container");
      if (!box) return;
      if (!window.selectedReceivers.length) {
        box.style.display = "none";
        box.innerHTML = "";
        return;
      }
      box.style.display = "block";
      const titleR = t("receiver", "Receiver");
      const phoneL = t("phone", "Phone");
      const cityL = t("city", "City");
      const addrL = t("address", "Address");
      let html = "";
      window.selectedReceivers.forEach((r, idx) => {
        const cityTxt = displayName(r.city_name);
        html += `
          <div class="card mb-2">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                  <div><strong>${titleR} #${idx + 1}:</strong> ${r.name || ""}${
          r.isNew ? " • NEW" : ""
        }</div>
                  <div><strong>${phoneL}:</strong> ${r.phone || ""}</div>
                  <div><strong>${cityL}:</strong> ${cityTxt}</div>
                  <div><strong>${addrL}:</strong> ${r.address || ""}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" data-index="${idx}">&times;</button>
              </div>
            </div>
          </div>`;
      });
      box.innerHTML = html;
      box.querySelectorAll("button.btn-outline-danger").forEach((btn) => {
        if (!btn.dataset.bound) {
          btn.addEventListener("click", function () {
            const i = parseInt(this.dataset.index, 10);
            if (isNaN(i)) return;
            const removed = window.selectedReceivers.splice(i, 1)[0];
            if (removed && !removed.isNew && initialExistingMap[removed.id]) {
              const sel = document.getElementById("receiver_select");
              if (sel && !sel.querySelector(`option[value="${removed.id}"]`)) {
                const opt = document.createElement("option");
                opt.value = removed.id;
                const rec = initialExistingMap[removed.id];
                opt.textContent =
                  (rec && (rec.name || rec.full_name || rec.email)) ||
                  removed.name ||
                  removed.id;
                sel.appendChild(opt);
              }
            }
            writeHiddenReceivers();
            refreshSelectedReceiversView();
            syncNextButton();
            document.dispatchEvent(new CustomEvent("receiversChanged"));
          });
          btn.dataset.bound = "1";
        }
      });
    }
  
    function canProceedToNextStep() {
      return Array.isArray(window.selectedReceivers) && window.selectedReceivers.length > 0;
    }
  
    function syncNextButton() {
      const btnNext = document.getElementById("btn-next");
      if (!btnNext) return;
      const ok = canProceedToNextStep();
      btnNext.disabled = !ok;
      btnNext.classList.toggle("btn-secondary", !ok);
      btnNext.classList.toggle("btn-primary", ok);
    }
  
    function ensureReceiverStateFieldVisible() {
      const stateField = document.getElementById("state");
      if (stateField) {
        stateField.style.display = "block";
        stateField.style.visibility = "visible";
        stateField.style.opacity = "1";
        stateField.disabled = false;
      }
    }
  
    function setupReceiverFormByShippingType() {
      const countryField = document.getElementById("country");
      const stateField = document.getElementById("state");
      const cityField = document.getElementById("city");
      if (!countryField || !stateField || !cityField) return;
  
      const isInternational = window.selectedMethod === "international";
  
      if (isInternational) {
        loadCountries();
        stateField.innerHTML = `<option value="">${t("select_state", "Select State")}</option>`;
        stateField.disabled = true;
        cityField.innerHTML = `<option value="">${t("select_city", "Select City")}</option>`;
        cityField.disabled = true;
      } else {
        loadCountriesForLocalShipping();
        stateField.innerHTML = `<option value="">${t("select_state", "Select State")}</option>`;
        cityField.innerHTML = `<option value="">${t("select_city", "Select City")}</option>`;
        cityField.disabled = true;
        setTimeout(() => {
          if (window.selectedMethod === "local") {
            loadStatesByCountry(SAUDI_ID, null);
          }
        }, 200);
      }
    }
  
    function populateReceiverForm(receiverId) {
      const r =
        (window._receiversMap && window._receiversMap[receiverId]) ||
        (window.selectedReceivers || []).find((x) => x.id === receiverId);
      if (!r) return;
      setIf("name", r.name);
      setIf("phone", r.phone);
      setIf("additional_phone", r.additional_phone);
      setIf("email", r.email);
      setIf("address", r.address);
      setIf("postal_code", r.postal_code);
      const countryId = r.country_id || r.countryId;
      const stateId = r.state_id || r.stateId;
      const cityId = r.city_id || r.cityId;
      if (countryId) loadCountriesForExistingReceiver(countryId);
      if (countryId) loadStatesByCountry(countryId, stateId);
      if (stateId) loadReceiverCitiesForState(stateId, cityId);
      ensureReceiverStateFieldVisible();
    }
  
    function clearReceiverForm() {
      ["name", "phone", "additional_phone", "email", "address", "postal_code"].forEach(
        (id) => {
          const el = document.getElementById(id);
          if (el) el.value = "";
        }
      );
      setupReceiverFormByShippingType();
    }
  
    function resetReceiverTypeSelection() {
      const existingRadio = document.getElementById("existing_receiver");
      const newRadio = document.getElementById("new_receiver");
      const existingSection = document.getElementById("existing_receiver_section");
      const newSection = document.getElementById("new_receiver_section");
      if (existingRadio) existingRadio.checked = false;
      if (newRadio) newRadio.checked = false;
      if (existingSection) existingSection.style.display = "none";
      if (newSection) newSection.style.display = "none";
      const select = document.getElementById("receiver_select");
      if (select) select.value = "";
      clearReceiverForm();
    }
  
    function setIf(id, val) {
      const el = document.getElementById(id);
      if (el != null && typeof val !== "undefined" && val !== null) el.value = val;
    }
  
    function loadCountries() {
      const countryField = document.getElementById("country");
      if (!countryField) return;
      fetchJson(
        "https://ghaya-express-staging-af597af07557.herokuapp.com/api/countries?page=0&pageSize=200&orderColumn=createdAt&orderDirection=desc",
        {
          headers: { accept: "*/*", "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu" },
        }
      )
        .then((data) => {
          const list = (data && data.results) || [];
          countryField.innerHTML = `<option value="">${t(
            "select_country",
            "Select Country"
          )}</option>`;
          list.forEach((c) => {
            const opt = document.createElement("option");
            opt.value = c.id || c._id;
            opt.textContent = displayName(c.name) || c.code || "";
            countryField.appendChild(opt);
          });
          countryField.disabled = false;
        })
        .catch(() => {
          countryField.innerHTML = `<option value="">${t(
            "error_loading_countries",
            "Error loading countries"
          )}</option>`;
          countryField.disabled = true;
        });
      if (!countryField.dataset.bound) {
        countryField.addEventListener("change", function () {
          const v = this.value;
          const stateField = document.getElementById("state");
          const cityField = document.getElementById("city");
          if (stateField) {
            stateField.disabled = true;
            stateField.innerHTML = `<option value="">${t(
              "select_state",
              "Select State"
            )}</option>`;
          }
          if (cityField) {
            cityField.disabled = true;
            cityField.innerHTML = `<option value="">${t(
              "select_city",
              "Select City"
            )}</option>`;
          }
          if (v) loadStatesByCountry(v, null);
        });
        countryField.dataset.bound = "1";
      }
    }
  
    function loadCountriesForLocalShipping() {
      const countryField = document.getElementById("country");
      if (!countryField) return;
      fetchJson(
        "https://ghaya-express-staging-af597af07557.herokuapp.com/api/countries?page=0&pageSize=200&orderColumn=createdAt&orderDirection=desc",
        {
          headers: { accept: "*/*", "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu" },
        }
      )
        .then((data) => {
          const list = (data && data.results) || [];
          countryField.innerHTML = `<option value="">${t(
            "select_country",
            "Select Country"
          )}</option>`;
          list.forEach((c) => {
            const opt = document.createElement("option");
            opt.value = c.id || c._id;
            opt.textContent = displayName(c.name) || c.code || "";
            if ((c.id || c._id) === SAUDI_ID) {
              opt.selected = true;
              setTimeout(() => loadStatesByCountry(SAUDI_ID, null), 100);
            }
            countryField.appendChild(opt);
          });
          countryField.disabled = true;
        })
        .catch(() => {
          countryField.innerHTML = `<option value="">${t(
            "error_loading_countries",
            "Error loading countries"
          )}</option>`;
          countryField.disabled = true;
        });
    }
  
    function loadCountriesForExistingReceiver(selectedCountryId) {
      const countryField = document.getElementById("country");
      if (!countryField) return;
      fetchJson(
        "https://ghaya-express-staging-af597af07557.herokuapp.com/api/countries?page=0&pageSize=200&orderColumn=createdAt&orderDirection=desc",
        {
          headers: { accept: "*/*", "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu" },
        }
      )
        .then((data) => {
          const list = (data && data.results) || [];
          countryField.innerHTML = `<option value="">${t(
            "select_country",
            "Select Country"
          )}</option>`;
          list.forEach((c) => {
            const opt = document.createElement("option");
            opt.value = c.id || c._id;
            opt.textContent = displayName(c.name) || c.code || "";
            if ((c.id || c._id) === selectedCountryId) opt.selected = true;
            countryField.appendChild(opt);
          });
          countryField.disabled = false;
        })
        .catch(() => {
          countryField.innerHTML = `<option value="">${t(
            "error_loading_countries",
            "Error loading countries"
          )}</option>`;
          countryField.disabled = true;
        });
    }
  
    function loadStatesByCountry(countryId, selectedStateId = null) {
      const stateField = document.getElementById("state");
      if (!stateField) return;
      fetchJson(
        `https://ghaya-express-staging-af597af07557.herokuapp.com/api/states?page=0&pageSize=200&orderColumn=createdAt&orderDirection=desc&countryId=${encodeURIComponent(
          countryId
        )}`,
        {
          headers: { accept: "*/*", "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu" },
        }
      )
        .then((data) => {
          const list = (data && data.results) || [];
          stateField.innerHTML = `<option value="">${t(
            "select_state",
            "Select State"
          )}</option>`;
          list.forEach((st) => {
            const opt = document.createElement("option");
            opt.value = st.id || st._id;
            opt.textContent = displayName(st.name) || "";
            if (
              selectedStateId &&
              (st.id === selectedStateId || st._id === selectedStateId)
            )
              opt.selected = true;
            stateField.appendChild(opt);
          });
          stateField.disabled = false;
        })
        .catch(() => {
          stateField.innerHTML = `<option value="">${t(
            "error_loading_states",
            "Error loading states"
          )}</option>`;
          stateField.disabled = true;
        });
  
      if (!stateField.dataset.bound) {
        stateField.addEventListener("change", function () {
          const v = this.value;
          const cityField = document.getElementById("city");
          if (cityField) {
            cityField.disabled = true;
            cityField.innerHTML = `<option value="">${t(
              "select_city",
              "Select City"
            )}</option>`;
          }
          if (v) loadReceiverCitiesForState(v, null);
        });
        stateField.dataset.bound = "1";
      }
    }
  
    function loadReceiverCitiesForState(stateId, selectedCityId = null) {
      const cityField = document.getElementById("city");
      if (!cityField) return;
  
      if (!stateId) {
        cityField.disabled = true;
        cityField.innerHTML = `<option value="">${t(
          "select_city",
          "Select City"
        )}</option>`;
        return;
      }
  
      fetchJson(
        `https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities?page=0&pageSize=200&orderColumn=createdAt&orderDirection=desc&stateId=${encodeURIComponent(
          stateId
        )}`,
        {
          headers: { accept: "*/*", "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu" },
        }
      )
        .then((data) => {
          const list = (data && data.results) || [];
          cityField.innerHTML = `<option value="">${t(
            "select_city",
            "Select City"
          )}</option>`;
          list.forEach((ci) => {
            const opt = document.createElement("option");
            opt.value = ci.id || ci._id;
            opt.textContent = displayName(ci.name) || "";
            if (
              selectedCityId &&
              (ci.id === selectedCityId || ci._id === selectedCityId)
            )
              opt.selected = true;
            cityField.appendChild(opt);
          });
          cityField.disabled = false;
        })
        .catch(() => {
          cityField.innerHTML = `<option value="">${t(
            "error_loading_cities",
            "Error loading cities"
          )}</option>`;
          cityField.disabled = true;
        });
    }
  
    window.loadReceivers = loadReceivers;
    window.populateReceiverForm = populateReceiverForm;
    window.ensureReceiverStateFieldVisible = ensureReceiverStateFieldVisible;
    window.setupReceiverFormByShippingType = setupReceiverFormByShippingType;
    window.canProceedToNextStep = canProceedToNextStep;
  
    document.addEventListener("DOMContentLoaded", () => {
      if (window.selectedMethod) setupReceiverFormByShippingType();
    });
  
    document.addEventListener("shippingMethodSelected", () => {
      setupReceiverFormByShippingType();
    });
  })();
  