/* Step 1 – Companies: fetch, render, select, and show concise summary */

let selectedCompany = null;
let shippingCompaniesData = [];

/* Optional: calmer step indicator */
function updateStepIndicator(step) {
  const steps = document.querySelectorAll(".step");
  steps.forEach((s, i) => {
    const bubble = s.querySelector(".step-number");
    bubble.classList.remove("is-current", "is-done");
    if (i + 1 < step) bubble.classList.add("is-done");
    else if (i + 1 === step) bubble.classList.add("is-current");
  });
}

function formatMoney(n){
  if (n === null || n === undefined || isNaN(n)) return "—";
  return `$${(+n).toFixed(2).replace(/\.00$/,'')}`;
}

function fetchShippingCompanies() {
  const container = document.getElementById("companies-container");
  if (!container) return;

  container.innerHTML = `
    <div class="text-center py-4">
      <div class="spinner-border text-primary" role="status" style="width:2rem;height:2rem"></div>
      <p class="mt-2">${translations?.loading_companies || "Loading shipping companies..."}</p>
    </div>
  `;

  const url = (typeof API_ENDPOINTS !== "undefined" && API_ENDPOINTS.shippingCompanies)
    ? API_ENDPOINTS.shippingCompanies
    : "/user/shipping-companies";

  fetch(url)
    .then(r => {
      if (!r.ok) throw new Error(`HTTP ${r.status}`);
      return r.json();
    })
    .then(data => {
      const results = data?.results || [];
      shippingCompaniesData = results;
      if (!results.length) {
        container.innerHTML = `
          <div class="alert alert-warning text-center mb-0">
            ${translations?.no_companies_available || "No active shipping companies."}
          </div>`;
        return;
      }
      displayCompanies(results);
    })
    .catch(err => {
      console.error(err);
      container.innerHTML = `
        <div class="alert alert-danger text-center mb-0">
          ${translations?.error_loading_companies || "Error loading companies. Please try again."}
        </div>`;
    });
}

function displayCompanies(companies) {
  const container = document.getElementById("companies-container");
  if (!container) return;

  const html = ['<div class="companies-grid">'];
  companies.forEach((c, i) => {
    const logoUrl = c.logoUrl || c.logo || "/default-logo.png";
    const methods = c.shippingMethods || [];
    const localPrice = c.localPrice || 0;
    const maxWeight  = c.maxWeight || 0;
    const intlPrice  = c.internationalPrice || 0;
    const extraW     = c.extraWeightPrice || 0;
    const cod        = c.codPrice || 0;
    const shipFee    = c.shipmentFees || 0;

    html.push(`
      <div class="company-card" data-index="${i}" tabindex="0" aria-label="${c.name}">
        <div class="card-body">
          <div class="company-logo"><img src="${logoUrl}" alt="${c.name} logo"></div>
          <h5 class="card-title">${c.name}</h5>

          ${methods.length ? `
            <div class="meta-row">
              ${methods.map(m => `<span class="badge ${m==='local'?'outline-success':'outline-info'}">
                ${m==='local' ? (translations?.local || 'Local') : (translations?.international || 'International')}
              </span>`).join('')}
            </div>` : ''}

          <div class="preview">
            ${localPrice>0 ? `
              <div class="preview-box">
                <small>${translations?.local_shipping_price || 'Local Price'}</small>
                <strong>${formatMoney(localPrice)}</strong>
              </div>` : ''}
            ${maxWeight>0 ? `
              <div class="preview-box">
                <small>${translations?.max_weight || 'Max Weight'}</small>
                <strong>${maxWeight}${translations?.kg ? ' '+translations.kg : ' kg'}</strong>
              </div>` : ''}
          </div>

          <div class="card-hint"><small><i class="fas fa-info-circle me-1"></i>${translations?.click_for_details || 'Click for details'}</small></div>

          <div class="company-details-card" id="company-details-${i}">
            <div class="card border-0 shadow-sm mt-2">
              <div class="card-body">
                <h6><i class="fas fa-info-circle"></i> ${(translations?.pricing_information || 'Pricing information')}</h6>
                <div class="details-grid">
                  ${intlPrice>0 ? `
                    <div class="details-tile">
                      <small>${translations?.international_shipping_price || 'International Price'}</small>
                      <strong>${formatMoney(intlPrice)}</strong>
                    </div>` : ''}
                  ${extraW>0 ? `
                    <div class="details-tile">
                      <small>${translations?.extra_weight_price || 'Extra Weight Price'}</small>
                      <strong>${formatMoney(extraW)}/kg</strong>
                    </div>` : ''}
                  ${cod>0 ? `
                    <div class="details-tile">
                      <small>${translations?.cod_fee || 'COD Fee'}</small>
                      <strong>${formatMoney(cod)}</strong>
                    </div>` : ''}
                  ${shipFee>0 ? `
                    <div class="details-tile">
                      <small>${translations?.shipment_fees || 'Shipment Fees'}</small>
                      <strong>${formatMoney(shipFee)}</strong>
                    </div>` : ''}
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

  // Wire interactions
  document.querySelectorAll('.company-card').forEach(card => {
    card.addEventListener('click', onCardClick);
    card.addEventListener('keyup', (e)=>{ if(e.key==='Enter' || e.key===' ') onCardClick.call(card, e); });
  });

  // Subtle fade-in
  requestAnimationFrame(()=>{
    container.querySelectorAll('.company-card').forEach((card, idx)=>{
      card.style.opacity='0'; card.style.transform='translateY(18px)';
      setTimeout(()=>{
        card.style.transition='all .35s ease'; card.style.opacity='1'; card.style.transform='translateY(0)';
      }, idx*60);
    });
  });
}

function onCardClick(e){
  const idx = Number(this.dataset.index);
  const company = shippingCompaniesData[idx];

  // Select visual
  document.querySelectorAll('.company-card').forEach(c=>c.classList.remove('selected'));
  this.classList.add('selected');

  // Toggle details (open this, close others)
  document.querySelectorAll('.company-details-card').forEach((el, i)=>{
    if (i===idx){ el.classList.add('active'); }
    else{ el.classList.remove('active'); }
  });

  // Persist selection
  selectedCompany = company;
  const nameEl = document.getElementById('selected-company-name');
  if (nameEl) nameEl.textContent = company.name || '';

  const idInput = document.getElementById('shipping_company_id');
  if (idInput) idInput.value = company.id || company._id || '';

  // Enable Next button
  const btnNext = document.getElementById('btn-next');
  if (btnNext){ btnNext.disabled = false; btnNext.classList.remove('btn-secondary'); btnNext.classList.add('btn-primary'); }

  // Show simple summary above Next button
  showSelectedCompanySummary(company);

  // Step indicator
  if (typeof updateStepIndicator === 'function') updateStepIndicator(1);
}

function showSelectedCompanySummary(company){
  const box = document.getElementById('company-selected-summary');
  if (!box) return;

  const methods = (company.shippingMethods || []).map(m => m==='local' ? (translations?.local || 'Local') : (translations?.international || 'International')).join(', ') || '—';

  box.innerHTML = `
    <div class="selected-summary">
      <div class="selected-summary__title">
        ${translations?.shipping_company || 'Shipping Company'}: <strong>${company.name || ''}</strong>
      </div>
      <div class="selected-summary__grid">
        <div class="selected-summary__item">
          <small>${translations?.method || 'Method(s)'}</small>
          <strong>${methods}</strong>
        </div>
        <div class="selected-summary__item">
          <small>${translations?.local_shipping_price || 'Local Price'}</small>
          <strong>${formatMoney(company.localPrice)}</strong>
        </div>
        <div class="selected-summary__item">
          <small>${translations?.international_shipping_price || 'International Price'}</small>
          <strong>${company.internationalPrice ? formatMoney(company.internationalPrice) : '—'}</strong>
        </div>
        <div class="selected-summary__item">
          <small>${translations?.max_weight || 'Max Weight'}</small>
          <strong>${company.maxWeight ? company.maxWeight + (translations?.kg ? ' ' + translations.kg : ' kg') : '—'}</strong>
        </div>
        <div class="selected-summary__item">
          <small>${translations?.extra_weight_price || 'Extra Weight Price'}</small>
          <strong>${company.extraWeightPrice ? formatMoney(company.extraWeightPrice) + '/kg' : '—'}</strong>
        </div>
        <div class="selected-summary__item">
          <small>${translations?.cod_fee || 'COD Fee'}</small>
          <strong>${company.codPrice ? formatMoney(company.codPrice) : '—'}</strong>
        </div>
      </div>
    </div>
  `;

  // Make sure it’s visible
  const summaryBlock = box.querySelector('.selected-summary');
  if (summaryBlock) summaryBlock.style.display = 'block';
}

/* Utility: clear step data if needed */
function clearStepData(step){
  if (step === 1){
    selectedCompany = null;
    const idInput = document.getElementById('shipping_company_id');
    if (idInput) idInput.value = '';
    const box = document.getElementById('company-selected-summary');
    if (box) box.innerHTML = '';
  }
}

/* Call this once on load (or from your existing init) */
document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('companies-container')) {
    fetchShippingCompanies();
  }
});
