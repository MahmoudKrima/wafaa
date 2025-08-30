(() => {
    let receivers = [];
    let selectedReceivers = [];

    function loadReceivers(userId) {
        fetch(`/admin/admin-shipping/user-receivers?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                receivers = data;
                displayReceivers(data);
            })
            .catch(error => {
                console.error('Error loading receivers:', error);
            });
    }

    function displayReceivers(receiversData) {
        const container = document.getElementById('receivers-container');
        if (!container) return;

        if (receiversData.length === 0) {
            container.innerHTML = '<div class="alert alert-info">No receivers found for this user</div>';
            return;
        }

        const receiversHtml = receiversData
            .map((receiver, index) => `
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h6>${receiver.name}</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Phone:</strong> ${receiver.phone || 'N/A'}</p>
                            <p><strong>Email:</strong> ${receiver.email || 'N/A'}</p>
                            <p><strong>Address:</strong> ${receiver.address || 'N/A'}</p>
                            <p><strong>City:</strong> ${receiver.city || 'N/A'}</p>
                        </div>
                        <div class="card-footer">
                            <button type="button" 
                                    class="btn btn-primary btn-sm"
                                    onclick="selectReceiver(${receiver.id})">
                                Select Receiver
                            </button>
                        </div>
                    </div>
                </div>
            `)
            .join("");

        container.innerHTML = receiversHtml;
    }

    function selectReceiver(receiverId) {
        const receiver = receivers.find(r => r.id === receiverId);
        if (!receiver) return;

        if (!selectedReceivers.find(r => r.id === receiverId)) {
            selectedReceivers.push(receiver);
        }

        updateSelectedReceiversDisplay();
        
        if (selectedReceivers.length > 0) {
            window.enableNext();
        }
    }

    function updateSelectedReceiversDisplay() {
        const container = document.getElementById('selected-receivers-display');
        if (!container) return;

        if (selectedReceivers.length === 0) {
            container.innerHTML = '<p class="text-muted">No receivers selected</p>';
            return;
        }

        const receiversHtml = selectedReceivers
            .map((receiver, index) => `
                <div class="alert alert-success">
                    <strong>${receiver.name}</strong> - ${receiver.phone || 'N/A'}
                    <button type="button" 
                            class="btn btn-danger btn-sm float-end"
                            onclick="removeReceiver(${index})">
                        Remove
                    </button>
                </div>
            `)
            .join("");

        container.innerHTML = receiversHtml;
    }

    function removeReceiver(index) {
        selectedReceivers.splice(index, 1);
        updateSelectedReceiversDisplay();
        if (selectedReceivers.length === 0) {
            window.disableNext();
        }
    }

    function validateStep() {
        if (selectedReceivers.length === 0) {
            alert('Please select at least one receiver');
            return false;
        }
        
        return true;
    }

    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        
        if (userId) {
            loadReceivers(userId);
        }
    });
    window.loadReceivers = loadReceivers;
    window.selectReceiver = selectReceiver;
    window.removeReceiver = removeReceiver;
    window.validateStep = validateStep;
})();
