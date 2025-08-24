(() => {
  // Make selectedCompany globally accessible
  window.selectedCompany = null;
  let shippingCompaniesData = [];
  let companiesLoaded = false;
  let companiesFetchController = null;

  function updateStepIndicator(step) {
    const steps = document.querySelectorAll(".step");
    steps.forEach((s, i) => {
      const bubble = s.querySelector(".step-number");
      if (!bubble) return;
      bubble.classList.remove("is-current", "is-done");
      if (i + 1 < step) bubble.classList.add("is-done");
      else if (i + 1 === step) bubble.classList.add("is-current");
    });
  }

  function formatMoney(n) {
    if (n === null || n === undefined || isNaN(n)) return "—";
    return `$${(+n).toFixed(2).replace(/\.00$/,'')}`;
  }

  function getNormalizedMethods(methodsRaw) {
    const arr = Array.isArray(methodsRaw) ? methodsRaw : [];
    return [...new Set(arr.filter(Boolean).map(m => m.toString().trim().toLowerCase()))];
  }

  function buildMethodBadges(tokens) {
    const t = new Set(tokens);
    const badges = [];
    if (t.has('local')) {
      badges.push(`<span class="badge outline-success">${window.translations?.local || 'Local'}</span>`);
    }
    if (t.has('international')) {
      badges.push(`<span class="badge outline-info">${window.translations?.international || 'International'}</span>`);
    }
    return badges.join('');
  }

  function prettyMethods(tokens) {
    const labels = [];
    const t = new Set(tokens);
    if (t.has('local')) labels.push(window.translations?.local || 'Local');
    if (t.has('international')) labels.push(window.translations?.international || 'International');
    return labels.join(', ') || '—';
  }

  function fetchShippingCompanies(force = false) {
    const container = document.getElementById("companies-container");
    if (!container) {
      console.error("Companies container not found");
      return;
    }
    if (companiesLoaded && !force) {
      console.log("Companies already loaded, skipping fetch");
      return;
    }
    
    console.log("Fetching shipping companies...");
    companiesLoaded = true;
    if (companiesFetchController) { try { companiesFetchController.abort(); } catch {} }
    companiesFetchController = new AbortController();

    container.innerHTML = `
      <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status" style="width:2rem;height:2rem"></div>
        <p class="mt-2">${window.translations?.loading_companies || "Loading shipping companies..."}</p>
      </div>
    `;

    const url = (typeof window.API_ENDPOINTS !== "undefined" && window.API_ENDPOINTS.shippingCompanies)
      ? window.API_ENDPOINTS.shippingCompanies
      : "/user/shipping-companies";

    console.log("Fetching from URL:", url);

    fetch(url, { signal: companiesFetchController.signal })
      .then(r => { 
        console.log("Response status:", r.status);
        if (!r.ok) throw new Error(`HTTP ${r.status}`); 
        return r.json(); 
      })
      .then(data => {
        console.log("Companies data received:", data);
        const results = data?.results || [];
        console.log("Companies results:", results);
        shippingCompaniesData = results;
        if (!results.length) {
          container.innerHTML = `
            <div class="alert alert-warning text-center mb-0">
              ${window.translations?.no_companies_available || "No active shipping companies."}
            </div>`;
          return;
        }
        displayCompanies(results);
      })
      .catch(err => {
        if (err.name === "AbortError") return;
        console.error("Error fetching companies:", err);
        container.innerHTML = `
          <div class="alert alert-danger text-center mb-0">
            ${window.translations?.error_loading_companies || "Error loading companies. Please try again."}
          </div>`;
      })
      .finally(() => { companiesFetchController = null; });
  }

  function displayCompanies(companies) {
    const container = document.getElementById("companies-container");
    if (!container) return;

    const html = ['<div class="companies-grid">'];

    companies.forEach((c, i) => {
      const logoUrl = c.logoUrl || c.logo || "/default-logo.png";
      const methods = getNormalizedMethods(c.shippingMethods);
      const localPrice = +c.localPrice || 0;
      const maxWeight  = +c.maxWeight || 0;
      const intlPrice  = +c.internationalPrice || 0;
      const extraW     = +c.extraWeightPrice || 0;
      const cod        = +c.codPrice || 0;
      const shipFee    = +c.shipmentFees || 0;
      const badges = buildMethodBadges(methods);

      html.push(`
        <div class="company-card" data-index="${i}" tabindex="0" aria-label="${c.name}">
          <div class="card">
            <div class="card-body">
              <div class="company-logo"><img src="${logoUrl}" alt="${c.name} logo"></div>
              <h5 class="card-title">${c.name}</h5>
              ${badges ? `<div class="meta-row">${badges}</div>` : ''}
              <div class="preview">
                ${localPrice > 0 ? `
                  <div class="preview-box">
                    <small>${window.translations?.local_shipping_price || 'Local Price'}</small>
                    <strong>${formatMoney(localPrice)}</strong>
                  </div>` : ''}
                ${maxWeight > 0 ? `
                  <div class="preview-box">
                    <small>${window.translations?.max_weight || 'Max Weight'}</small>
                    <strong>${maxWeight}${window.translations?.kg ? ' ' + window.translations.kg : ' kg'}</strong>
                  </div>` : ''}
              </div>
              <div class="card-hint">
                <small><i class="fas fa-info-circle me-1"></i>${window.translations?.click_for_details || 'Click for details'}</small>
              </div>
              <div class="company-details-card" id="company-details-${i}">
                <div class="card border-0 shadow-sm mt-2">
                  <div class="card-body">
                    <h6><i class="fas fa-info-circle"></i> ${(window.translations?.pricing_information || 'Pricing information')}</h6>
                    <div class="details-grid">
                      ${intlPrice > 0 ? `
                        <div class="details-tile">
                          <small>${window.translations?.international_shipping_price || 'International Price'}</small>
                          <strong>${formatMoney(intlPrice)}</strong>
                        </div>` : ''}
                      ${extraW > 0 ? `
                        <div class="details-tile">
                          <small>${window.translations?.extra_weight_price || 'Extra Weight Price'}</small>
                          <strong>${formatMoney(extraW)}/kg</strong>
                        </div>` : ''}
                      ${cod > 0 ? `
                        <div class="details-tile">
                          <small>${window.translations?.cod_fee || 'COD Fee'}</small>
                          <strong>${formatMoney(cod)}</strong>
                        </div>` : ''}
                      ${shipFee > 0 ? `
                        <div class="details-tile">
                          <small>${window.translations?.shipment_fees || 'Shipment Fees'}</small>
                          <strong>${formatMoney(shipFee)}</strong>
                        </div>` : ''}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `);
    });

    html.push('</div>');
    container.innerHTML = html.join('');

    // Add event listeners to company cards
    container.querySelectorAll('.company-card').forEach(card => {
      console.log('Adding event listeners to card:', card);
      card.addEventListener('click', onCardClick);
      card.addEventListener('keyup', (e)=>{
        if(e.key==='Enter' || e.key===' ') {
          e.preventDefault();
          onCardClick.call(card, e);
        }
      });
      
      // Add keyboard navigation
      card.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
          e.preventDefault();
          const cards = Array.from(container.querySelectorAll('.company-card'));
          const currentIndex = cards.indexOf(card);
          let nextIndex;
          
          if (e.key === 'ArrowRight') {
            nextIndex = (currentIndex + 1) % cards.length;
          } else {
            nextIndex = (currentIndex - 1 + cards.length) % cards.length;
          }
          
          cards[nextIndex].focus();
        }
      });
    });

    requestAnimationFrame(()=>{
      container.querySelectorAll('.company-card').forEach((card, idx)=>{
        card.style.opacity='0';
        card.style.transform='translateY(18px)';
        setTimeout(()=>{
          card.style.transition='all .35s ease';
          card.style.opacity='1';
          card.style.transform='translateY(0)';
        }, idx*60);
      });
    });
  }

  function onCardClick() {
    console.log('Card clicked, processing selection...');
    const idx = parseInt(this.dataset.index, 10);
    const company = shippingCompaniesData[idx];
    console.log('Company data:', company, 'Index:', idx);
    
    if (!company) {
      console.error('Company data not found for index:', idx);
      return;
    }

    // Clear previous selection
    document.querySelectorAll('.company-card').forEach(c => {
      c.classList.remove('selected');
      c.setAttribute('aria-selected','false');
      // Remove any previous selection indicators
      const card = c.querySelector('.card');
      if (card) {
        card.style.borderColor = '';
        card.style.boxShadow = '';
        card.style.background = '';
      }
    });
    
    // Set new selection
    this.classList.add('selected');
    this.setAttribute('aria-selected','true');
    
    // Add visual selection indicator
    const selectedCard = this.querySelector('.card');
    if (selectedCard) {
      selectedCard.style.borderColor = '#0d6efd';
      selectedCard.style.boxShadow = '0 0 0 3px rgba(13, 110, 253, 0.25), 0 8px 25px rgba(0, 0, 0, 0.15)';
      selectedCard.style.background = '#f8f9ff';
    }

    // Show details for selected company
    document.querySelectorAll('.company-details-card').forEach((el, i) => {
      if (i === idx) { 
        el.classList.add('active'); 
      } else { 
        el.classList.remove('active'); 
      }
    });

    // Update both local and global selectedCompany
    selectedCompany = company;
    window.selectedCompany = company;
    console.log('Selected company set:', window.selectedCompany);

    // Update company name display
    const nameEl = document.getElementById('selected-company-name');
    if (nameEl) {
      nameEl.textContent = company.name || '';
      console.log('Company name updated in display');
    }

    // Update hidden input
    const idInput = document.getElementById('shipping_company_id');
    if (idInput) {
      idInput.value = company.id || company._id || '';
      console.log('Hidden input updated with company ID:', idInput.value);
    }

    // Enable next button with animation
    const btnNext = document.getElementById('btn-next');
    if (btnNext) {
      btnNext.disabled = false;
      btnNext.removeAttribute('disabled');
      btnNext.classList.remove('btn-secondary');
      btnNext.classList.add('btn-primary');
      
      // Add a subtle animation to draw attention
      btnNext.style.transform = 'scale(1.05)';
      setTimeout(() => {
        btnNext.style.transform = 'scale(1)';
      }, 200);
      console.log('Next button enabled');
    } else {
      console.error('Next button not found');
    }

    // Show company summary
    showSelectedCompanySummary(company);
    
    // Update step indicator
    if (typeof updateStepIndicator === 'function') {
      updateStepIndicator(1);
      console.log('Step indicator updated');
    }

    // Dispatch custom event
    document.dispatchEvent(new CustomEvent('shippingCompanySelected', { 
      detail: { company } 
    }));
    console.log('Custom event dispatched');

    // Log selection for debugging
    console.log('Company selection completed:', company.name, 'ID:', company.id || company._id);
  }

  function showSelectedCompanySummary(company) {
    const box = document.getElementById('company-selected-summary');
    if (!box) return;

    const tokens = getNormalizedMethods(company.shippingMethods);
    const methodsPretty = prettyMethods(tokens);

    box.innerHTML = `
      <div class="selected-summary">
        <div class="selected-summary__title">
          ${window.translations?.shipping_company || 'Shipping Company'}:
          <strong>${company.name || ''}</strong>
        </div>
        <div class="selected-summary__grid">
          <div class="selected-summary__item">
            <small>${window.translations?.method || 'Method(s)'}</small>
            <strong>${methodsPretty}</strong>
          </div>
          <div class="selected-summary__item">
            <small>${window.translations?.local_shipping_price || 'Local Price'}</small>
            <strong>${formatMoney(company.localPrice)}</strong>
          </div>
          <div class="selected-summary__item">
            <small>${window.translations?.international_shipping_price || 'International Price'}</small>
            <strong>${company.internationalPrice ? formatMoney(company.internationalPrice) : '—'}</strong>
          </div>
          <div class="selected-summary__item">
            <small>${window.translations?.max_weight || 'Max Weight'}</small>
            <strong>${company.maxWeight ? company.maxWeight + (window.translations?.kg ? ' ' + window.translations.kg : ' kg') : '—'}</strong>
          </div>
          <div class="selected-summary__item">
            <small>${window.translations?.extra_weight_price || 'Extra Weight Price'}</small>
            <strong>${company.extraWeightPrice ? formatMoney(company.extraWeightPrice) + '/kg' : '—'}</strong>
          </div>
          <div class="selected-summary__item">
            <small>${window.translations?.cod_fee || 'COD Fee'}</small>
            <strong>${company.codPrice ? formatMoney(company.codPrice) : '—'}</strong>
          </div>
        </div>
      </div>
    `;
    box.style.display = 'block';
    
    const btnNext = document.getElementById('btn-next');
    if (btnNext) {
      btnNext.disabled = false;
      btnNext.removeAttribute('disabled');
      btnNext.classList.remove('btn-secondary');
      btnNext.classList.add('btn-primary');
    }
  }

  function clearStepData(step) {
    if (step === 1) {
      selectedCompany = null;
      window.selectedCompany = null;
      const idInput = document.getElementById('shipping_company_id');
      if (idInput) idInput.value = '';
      const box = document.getElementById('company-selected-summary');
      if (box) box.innerHTML = '';
      
      // Reset next button state
      const btnNext = document.getElementById('btn-next');
      if (btnNext) {
        btnNext.disabled = true;
        btnNext.classList.remove('btn-primary');
        btnNext.classList.add('btn-secondary');
      }
    }
  }

  // Add function to check if company is selected
  function isCompanySelected() {
    return window.selectedCompany !== null;
  }

  // Add function to get selected company info
  function getSelectedCompanyInfo() {
    if (!window.selectedCompany) {
      return null;
    }
    
    return {
      id: window.selectedCompany.id || window.selectedCompany._id,
      name: window.selectedCompany.name,
      localPrice: window.selectedCompany.localPrice,
      internationalPrice: window.selectedCompany.internationalPrice,
      maxWeight: window.selectedCompany.maxWeight,
      extraWeightPrice: window.selectedCompany.extraWeightPrice,
      codPrice: window.selectedCompany.codPrice,
      shipmentFees: window.selectedCompany.shipmentFees,
      shippingMethods: window.selectedCompany.shippingMethods
    };
  }

  // Add function to clear company selection
  function clearCompanySelection() {
    selectedCompany = null;
    window.selectedCompany = null;
    
    // Clear visual selection
    document.querySelectorAll('.company-card').forEach(c => {
      c.classList.remove('selected');
      c.setAttribute('aria-selected','false');
      const card = c.querySelector('.card');
      if (card) {
        card.style.borderColor = '';
        card.style.boxShadow = '';
        card.style.background = '';
      }
    });
    
    // Hide company details
    document.querySelectorAll('.company-details-card').forEach(el => {
      el.classList.remove('active');
    });
    
    // Clear hidden input
    const idInput = document.getElementById('shipping_company_id');
    if (idInput) idInput.value = '';
    
    // Clear company name display
    const nameEl = document.getElementById('selected-company-name');
    if (nameEl) nameEl.textContent = '';
    
    // Disable next button
    const btnNext = document.getElementById('btn-next');
    if (btnNext) {
      btnNext.disabled = true;
      btnNext.classList.remove('btn-primary');
      btnNext.classList.add('btn-secondary');
    }
    
    // Hide company summary
    const box = document.getElementById('company-selected-summary');
    if (box) {
      box.innerHTML = '';
      box.style.display = 'none';
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    console.log('Step1.js loaded - DOM ready');
    if (document.getElementById('companies-container')) {
      console.log('Companies container found, fetching companies...');
      fetchShippingCompanies();
    } else {
      console.log('Companies container not found');
    }
  }, { once: true });

  // Make functions globally accessible
  window.initShippingCompanies = function () {
    console.log('initShippingCompanies called');
    companiesLoaded = false;
    fetchShippingCompanies(true);
  };

  window.clearShippingStepData = clearStepData;
  window.updateShippingStepIndicator = updateStepIndicator;
  window.getSelectedCompany = function() { return window.selectedCompany; };
  window.isCompanySelected = isCompanySelected;
  window.getSelectedCompanyInfo = getSelectedCompanyInfo;
  window.clearCompanySelection = clearCompanySelection;
})();
