// JavaScript to add click event listeners to each abs
document.addEventListener('DOMContentLoaded', function () {
    const abss = document.querySelectorAll('.abs');
    const selectedUserIds = [];

    abss.forEach(abs => {
        selectedUserIds.push(abs.closest('.img').getAttribute('data-user-id'));
        abs.addEventListener('click', function () { // Toggle the 'present' class to change the color and selectedUserIds
            abs.classList.toggle('present');

            // Update the selectedUserIds array
            if (abs.classList.contains('present')) {
                selectedUserIds.push(abs.closest('.img').getAttribute('data-user-id'));
            } else {
                const index = selectedUserIds.indexOf(abs.closest('.img').getAttribute('data-user-id'));
                if (index !== -1) {
                    selectedUserIds.splice(index, 1);
                }
            }
        });
    });
});