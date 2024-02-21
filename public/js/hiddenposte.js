var url2 = window.location.pathname; // Récupère le chemin de l'URL, c'est-à-dire "/player/3/poste"
var parts = url2.split('/'); // Divise le chemin en segments en utilisant le caractère "/"
var id = parts[2]; // L'ID se trouve à la position 2 dans cet exemple

console.log(id);

function handleClick(event) {
    var terrain = document.querySelector('.terrain');

    var existingCircles = terrain.querySelectorAll('.cercle');
    existingCircles.forEach(function (circle) {
        terrain.removeChild(circle);
    });

    var terrainRect = terrain.getBoundingClientRect();
    var x = ((event.clientX - terrainRect.left) / terrainRect.width) * 100;
    var y = ((event.clientY - terrainRect.top) / terrainRect.height) * 100;

    // Affichage des coordonnées en pourcentage dans la console
    console.log('Coordonnées du Clic :', 'X:', x.toFixed(2) + '%', 'Y:', y.toFixed(2) + '%');

    var newCircle = document.createElement('img');
    newCircle.src = '/images/cercle.png';
    newCircle.alt = 'cercle';
    newCircle.classList.add('cercle');

    newCircle.style.position = 'absolute';
    newCircle.style.maxWidth = '2%';
    newCircle.style.zIndex = '2';
    newCircle.style.left = x.toFixed(2) -1 + '%';
    newCircle.style.top = y.toFixed(2) -0.75 + '%';

    terrain.appendChild(newCircle);

    var coord = {
        coordX: x.toFixed(2)
    };

    coordX(coord);

    var coord2 = {
        coordY: y.toFixed(2)
    };

    coordY(coord2);
}

function handleConfirmButtonClick() {
    window.location.reload();
}

document.querySelector('.terrain').addEventListener('click', handleClick);
document.getElementById('confirmerButton').addEventListener('click', handleConfirmButtonClick);


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
