function redirectToPage(page) {
    window.location.href = page;
}
document.addEventListener('DOMContentLoaded', function () {
    const returnButton = document.querySelector('.btn-gray');

    if (returnButton) {
        returnButton.addEventListener('click', function () {
            // Utiliser la fonctionnalité de retour du navigateur
            window.history.back();
        });
    }
});