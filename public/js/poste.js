document.addEventListener("DOMContentLoaded", function() {
    var selection_principale = 0;
    var selection_secondaire = 0;

    const posteSelectionne = document.getElementById("poste-pri"); // Champ de texte du poste principale
    const posteSecondaire = document.getElementById("poste-sec"); // Champ de texte du poste secondaire

    const bouttonPrincipale = document.getElementById("selec-pri"); // Boutton du poste principale
    const bouttonSecondaire = document.getElementById("selec-sec"); // Boutton du poste secondaire

    var url = window.location.pathname; // Récupère le chemin de l'URL, c'est-à-dire "/player/3/poste"
    var parts = url.split('/'); // Divise le chemin en segments en utilisant le caractère "/"
    var id = parts[2]; // L'ID se trouve à la position 2 dans cet exemple

    console.log(id); // Ceci affichera l'ID (dans ce cas, 3) dans la console


    // Recuperation des boutons du terrains
    const gardien = document.getElementById("gardien");
    const defenseurd = document.getElementById("defenseurd");
    const defenseurg = document.getElementById("defenseurg");
    const milieud = document.getElementById("milieud");
    const milieug = document.getElementById("milieug");
    const milieuoff = document.getElementById("milieuoff");
    const milieudef = document.getElementById("milieudef");
    const attaquant = document.getElementById("attaquant");

    bouttonPrincipale.addEventListener("click", function(){
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Aucun";
        }
        posteSelectionne.textContent = "Selectionnez un poste..."
        selection_principale = 1;

    })

    bouttonSecondaire.addEventListener("click", function(){
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Aucun";
        }
        posteSecondaire.textContent = "Selectionnez un poste..."
        selection_secondaire = 1;
    })

    // GARDIEN
    gardien.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Gardien";

            ////////
            var postData = {
                postePrincipal: 'Gardien'
            };

            postePrinc(postData);
            /////////

            if(posteSecondaire.textContent == "Gardien"){
                posteSecondaire.textContent = "Aucun";
                ////////
                var postData = {
                    posteSecondaire: 'Aucun'
                };

                posteSec(postData);
                /////////
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Gardien";

            ////////
            var postData = {
                posteSecondaire: 'Gardien'
            };

            posteSec(postData);
            /////////

            if(posteSelectionne.textContent == "Gardien"){
                posteSelectionne.textContent = "Aucun";
                ////////
                var postData = {
                    postePrincipal: 'Aucun'
                };

                postePrinc(postData);
                /////////
            }
        }
    });

    // DEFENSEUR
    defenseurd.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Defenseur Droit";

            ////////
            var postData = {
                postePrincipal: 'Defenseur Droit'
            };

            postePrinc(postData);
            /////////

            if(posteSecondaire.textContent == "Defenseur Droit"){
                posteSecondaire.textContent = "Aucun";
                ////////
                var postData = {
                    posteSecondaire: 'Aucun'
                };

                posteSec(postData);
                /////////
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Defenseur Droit";

            ////////
            var postData = {
                posteSecondaire: 'Defenseur Droit'
            };

            posteSec(postData);
            /////////

            if(posteSelectionne.textContent == "Defenseur Droit"){
                posteSelectionne.textContent = "Aucun";
                ////////
                var postData = {
                    postePrincipal: 'Aucun'
                };

                postePrinc(postData);
                /////////
            }
        }
    });
    defenseurg.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Defenseur Gauche";

            ////////
            var postData = {
                postePrincipal: 'Defenseur Gauche'
            };

            postePrinc(postData);
            /////////

            if(posteSecondaire.textContent == "Defenseur Gauche"){
                posteSecondaire.textContent = "Aucun";
                ////////
                var postData = {
                    posteSecondaire: 'Aucun'
                };

                posteSec(postData);
                /////////
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Defenseur Gauche";

            ////////
            var postData = {
                posteSecondaire: 'Defenseur Gauche'
            };

            posteSec(postData);
            /////////

            if(posteSelectionne.textContent == "Defenseur Gauche"){
                posteSelectionne.textContent = "Aucun";
                ////////
                var postData = {
                    postePrincipal: 'Aucun'
                };

                postePrinc(postData);
                /////////
            }
        }
    });

    // MILIEU
    milieud.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Milieu Droit";

            ////////
            var postData = {
                postePrincipal: 'Milieu Droit'
            };

            postePrinc(postData);
            /////////

            if(posteSecondaire.textContent == "Milieu Droit"){
                posteSecondaire.textContent = "Aucun";
                ////////
                var postData = {
                    posteSecondaire: 'Aucun'
                };

                posteSec(postData);
                /////////
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Milieu Droit";

            ////////
            var postData = {
                posteSecondaire: 'Milieu Droit'
            };

            posteSec(postData);
            /////////

            if(posteSelectionne.textContent == "Milieu Droit"){
                posteSelectionne.textContent = "Aucun";
                ////////
                var postData = {
                    postePrincipal: 'Aucun'
                };

                postePrinc(postData);
                /////////
            }
        }
    });
    milieug.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Milieu Gauche";

            ////////
            var postData = {
                postePrincipal: 'Milieu Gauche'
            };

            postePrinc(postData);
            /////////

            if(posteSecondaire.textContent == "Milieu Gauche"){
                posteSecondaire.textContent = "Aucun";
                ////////
                var postData = {
                    posteSecondaire: 'Aucun'
                };

                posteSec(postData);
                /////////
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Milieu Gauche";

            ////////
            var postData = {
                posteSecondaire: 'Milieu Gauche'
            };

            posteSec(postData);
            /////////

            if(posteSelectionne.textContent == "Milieu Gauche"){
                posteSelectionne.textContent = "Aucun";
                ////////
                var postData = {
                    postePrincipal: 'Aucun'
                };

                postePrinc(postData);
                /////////
            }
        }
    });
    milieuoff.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Milieu Offensif";

            ////////
            var postData = {
                postePrincipal: 'Milieu Offensif'
            };

            postePrinc(postData);
            /////////

            if(posteSecondaire.textContent == "Milieu Offensif"){
                posteSecondaire.textContent = "Aucun";
                ////////
                var postData = {
                    posteSecondaire: 'Aucun'
                };

                posteSec(postData);
                /////////
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Milieu Offensif";

            ////////
            var postData = {
                posteSecondaire: 'Milieu Offensif'
            };

            posteSec(postData);
            /////////

            if(posteSelectionne.textContent == "Milieu Offensif"){
                posteSelectionne.textContent = "Aucun";
                ////////
                var postData = {
                    postePrincipal: 'Aucun'
                };

                postePrinc(postData);
                /////////
            }
        }
    });
    milieudef.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Milieu Defensif";

            ////////
            var postData = {
                postePrincipal: 'Milieu Defensif'
            };

            postePrinc(postData);
            /////////

            if(posteSecondaire.textContent == "Milieu Defensif"){
                posteSecondaire.textContent = "Aucun";
                ////////
                var postData = {
                    posteSecondaire: 'Aucun'
                };

                posteSec(postData);
                /////////
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Milieu Defensif";

            ////////
            var postData = {
                posteSecondaire: 'Milieu Defensif'
            };

            posteSec(postData);
            /////////

            if(posteSelectionne.textContent == "Milieu Defensif"){
                posteSelectionne.textContent = "Aucun";
                ////////
                var postData = {
                    postePrincipal: 'Aucun'
                };

                postePrinc(postData);
                /////////
            }
        }
    });

    // ATTAQUANT
    attaquant.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Attaquant";

            ////////
            var postData = {
                postePrincipal: 'Attaquant'
            };

            postePrinc(postData);
            /////////

            if(posteSecondaire.textContent == "Attaquant"){
                posteSecondaire.textContent = "Aucun";
                ////////
                var postData = {
                    posteSecondaire: 'Aucun'
                };

                posteSec(postData);
                /////////
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Attaquant";

            ////////
            var postData = {
                posteSecondaire: 'Attaquant'
            };

            posteSec(postData);
            /////////

            if(posteSelectionne.textContent == "Attaquant"){
                posteSelectionne.textContent = "Aucun";
                ////////
                var postData = {
                    postePrincipal: 'Aucun'
                };

                postePrinc(postData);
                /////////
            }
        }
    });

    function postePrinc(postData){
        var options = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams(postData)
        };

        var url = "/player/poste/set-poste-principal/"+id;

        fetch(url, options)
            .then(response => response.json())
            .then(data => {
                console.log('Réponse du serveur :', data);
            })
            .catch(error => {
                console.error('Erreur lors de l\'envoi des données au serveur :', error);
            });
    }

    function posteSec(postData){
        var options = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams(postData)
        };

        var url = "/player/poste/set-poste-secondaire/"+id;

        fetch(url, options)
            .then(response => response.json())
            .then(data => {
                console.log('Réponse du serveur :', data);
            })
            .catch(error => {
                console.error('Erreur lors de l\'envoi des données au serveur :', error);
            });
    }
});
