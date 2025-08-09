document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.querySelector("#select-all-checkbox");
    const checkboxes = document.querySelectorAll(
        'input[name="permission_id[]"]'
    );

    selectAllCheckbox.addEventListener("change", function () {
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
            if (!checkbox.checked) {
                selectAllCheckbox.checked = false;
            } else {
                const allChecked = Array.from(checkboxes).every(function (
                    checkbox
                ) {
                    return checkbox.checked;
                });
                selectAllCheckbox.checked = allChecked;
            }
        });
    });
});
