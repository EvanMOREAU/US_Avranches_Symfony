var url2 = window.location.pathname; // Récupère le chemin de l'URL, c'est-à-dire "/player/3/poste"
var parts = url2.split('/'); // Divise le chemin en segments en utilisant le caractère "/"
var id = parts[2]; // L'ID se trouve à la position 2 dans cet exemple

console.log(id); // Ceci affichera l'ID (dans ce cas, 3) dans la console

function handleClick(event) {
    var terrain = document.querySelector('.terrain');

    var terrainRect = terrain.getBoundingClientRect();
    var x = ((event.clientX - terrainRect.left) / terrainRect.width) * 100;
    var y = ((event.clientY - terrainRect.top) / terrainRect.height) * 100;

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

    window.location.reload();
}

document.querySelector('.terrain').addEventListener('click', handleClick);

// ... (le reste de votre script)


function coordX(coord){
    var options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(coord)
    };

    var url = "/user/poste/set-poste-cache-x/"+id;

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

    var url = "/user/poste/set-poste-cache-y/"+id;

    fetch(url, options)
        .then(response => response.json())
        .then(data => {
            console.log('Réponse du serveur :', data);
        })
        .catch(error => {
            console.error('Erreur lors de l\'envoi des données au serveur :', error);
        });
}
