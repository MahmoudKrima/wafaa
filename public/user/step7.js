// Step 7: Final Summary
function showFinalSummary() {
    const summaryContainer = document.getElementById("final-shipment-summary");
    if (!summaryContainer) return;

    // Create summary HTML
    let summaryHTML = `
        <div class="final-shipment-summary">
            <div class="summary-section">
                <h6>${translations?.shipping_company || 'Shipping Company'}</h6>
                <div class="summary-item">
                    <span class="label">${translations?.company || 'Company'}:</span>
                    <span class="value">${selectedCompany?.name || "N/A"}</span>
                </div>
                <div class="summary-item">
                    <span class="label">${translations?.method || 'Method'}:</span>
                    <span class="value">${selectedMethod === "local" ? (translations?.local || 'Local') : (translations?.international || 'International')}</span>
                </div>
            </div>
        </div>
    `;

    summaryContainer.innerHTML = summaryHTML;
}
