var url = window.location.pathname; // Récupère le chemin de l'URL, c'est-à-dire "/player/3/poste"
var parts = url.split('/'); // Divise le chemin en segments en utilisant le caractère "/"
var id = parts[2]; // L'ID se trouve à la position 2 dans cet exemple

console.log(id); // Ceci affichera l'ID (dans ce cas, 3) dans la console

function handleClick(event) {
    var windowWidth = window.innerWidth;
    var windowHeight = window.innerHeight;

    var x = (event.clientX / windowWidth) * 100;
    var y = (event.clientY / windowHeight) * 100;

    // Affichage des coordonnées en pourcentage dans la console
    console.log('Coordonnées du Clic :', 'X:', x.toFixed(2) + '%', 'Y:', y.toFixed(2) + '%');

    var coord = {
        coordX: x.toFixed(2)
    };
    
    coordX(coord);

    var coord2 = {
        coordY: y.toFixed(2)
    };
    
    coordY(coord2);
}

document.getElementById('terrain').addEventListener('click', handleClick);

function coordX(coord){
    var options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(coord)
    };

    var url = "/player/poste/set-poste-cache-x/"+id;

    fetch(url, options)
        .then(response => response.json())
        .then(data => {
            console.log('Réponse du serveur :', data);
        })
        .catch(error => {
            console.error('Erreur lors de l\'envoi des données au serveur :', error);
        });
}

function coordY(coord){
    var options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(coord)
    };

    var url = "/player/poste/set-poste-cache-y/"+id;

    fetch(url, options)
        .then(response => response.json())
        .then(data => {
            console.log('Réponse du serveur :', data);
        })
        .catch(error => {
            console.error('Erreur lors de l\'envoi des données au serveur :', error);
        });
}