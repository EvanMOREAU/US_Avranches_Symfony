document.addEventListener('DOMContentLoaded', function () {
    const userDropdown = document.getElementById('userDropdown');
    
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    userDropdown.addEventListener('change', function () {
        const selectedUserId = this.value;
        const currentUrl = window.location.href;

        const updatedUrl = updateQueryStringParameter(currentUrl, 'userId', selectedUserId);
        window.location.href = updatedUrl;
    });

    function updateQueryStringParameter(uri, key, value) {
        const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        const separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }
});

function cancelTest(cancelUrl, testId) {
    // Envoi de la requête AJAX pour annuler le test
    fetch(cancelUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ testId }),
    })
    .then(response => response.json())
    .then(data => {
        // Gestion de la réponse du serveur
        console.log('Response from server:', data);

        if (data.success) {
            // Actualisez la page ou effectuez d'autres actions nécessaires
            window.location.reload();
        } else {
            alert('Erreur lors de l\'annulation du test : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'annulation du test:', error);
    });
}
function confirmDeletion(deletionUrl, testId) {
    // Envoi de la requête AJAX pour supprimer le test
    fetch(deletionUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ testId }),
    })
    .then(response => response.json())
    .then(data => {
        // Gestion de la réponse du serveur
        if (data.success) {
            window.location.reload();
        } else {
            alert('Erreur lors de la suppression du test : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur lors de la suppression du test:', error);
    });
}

function displayDeletionConfirmationModal(deletionUrl, testId) {
    // Construire le message de confirmation
    const deletionConfirmationMessage = `Voulez-vous vraiment supprimer ce test ?`;

    // Afficher la modal de confirmation Bootstrap pour la suppression
    $('#deletionConfirmationModal').modal('show');

    // Mettre à jour le texte de la modal avec le message de confirmation de suppression
    $('#deletionConfirmationModalBody').text(deletionConfirmationMessage);

    // Gérer l'événement lorsque l'utilisateur confirme la suppression
    $('#confirmDeletionButton').on('click', function () {
        // Appeler la fonction pour supprimer le test
        confirmDeletion(deletionUrl, testId);

        // Cacher la modal après confirmation
        $('#deletionConfirmationModal').modal('hide');
    });
}