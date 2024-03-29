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

function sendRequest(url, method, data, successCallback, errorCallback) {
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(data),
    })
    .then(response => response.json())
    .then(data => {
        successCallback(data);
    })
    .catch(error => {
        errorCallback(error);
    });
}

function cancelAction(cancelUrl, entityId) {
    sendRequest(cancelUrl, 'POST', { entityId: entityId }, 
        function(data) {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Erreur lors de l\'annulation : ' + data.message);
            }
        }, 
        function(error) {
            console.error('Erreur lors de l\'annulation :', error);
        }
    );
}

function displayConfirmationModal(deletionUrl, entityId, confirmationMessage) {
    // Afficher la modal de confirmation Bootstrap
    $('#deletionConfirmationModal').modal('show');

    // Mettre à jour le texte de la modal avec le message de confirmation
    $('#deletionConfirmationModalBody').text(confirmationMessage);

    // Gérer l'événement lorsque l'utilisateur confirme l'action
    $('#confirmDeletionButton').on('click', function () {
        // Appeler la fonction correspondante pour confirmer la suppression
        confirmDeletion(deletionUrl, entityId);

        // Cacher la modal après confirmation
        $('#deletionConfirmationModal').modal('hide');
    });
}

function confirmDeletion(deletionUrl, entityId) {
    // Envoi de la requête AJAX pour supprimer le test
    fetch(deletionUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ entityId: entityId }),
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