document.addEventListener("DOMContentLoaded", function() {
    var selection_principale = 0;
    var selection_secondaire = 0;

    const posteSelectionne = document.getElementById("poste-pri"); // Champ de texte du poste principale
    const posteSecondaire = document.getElementById("poste-sec"); // Champ de texte du poste secondaire

    const bouttonPrincipale = document.getElementById("selec-pri"); // Boutton du poste principale
    const bouttonSecondaire = document.getElementById("selec-sec"); // Boutton du poste secondaire

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
            if(posteSecondaire.textContent == "Gardien"){
                posteSecondaire.textContent = "Aucun";
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Gardien";
            if(posteSelectionne.textContent == "Gardien"){
                posteSelectionne.textContent = "Aucun";
            }
        }
    });

    // DEFENSEUR
    defenseurd.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Defenseur Droit";
            if(posteSecondaire.textContent == "Defenseur Droit"){
                posteSecondaire.textContent = "Aucun";
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Defenseur Droit";
            if(posteSelectionne.textContent == "Defenseur Droit"){
                posteSelectionne.textContent = "Aucun";
            }
        }
    });
    defenseurg.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Defenseur Gauche";
            if(posteSecondaire.textContent == "Defenseur Gauche"){
                posteSecondaire.textContent = "Aucun";
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Defenseur Gauche";
            if(posteSelectionne.textContent == "Defenseur Gauche"){
                posteSelectionne.textContent = "Aucun";
            }
        }
    });

    // MILIEU
    milieud.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Milieu Droit";
            if(posteSecondaire.textContent == "Milieu Droit"){
                posteSecondaire.textContent = "Aucun";
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Milieu Droit";
            if(posteSelectionne.textContent == "Milieu Droit"){
                posteSelectionne.textContent = "Aucun";
            }
        }
    });
    milieug.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Milieu Gauche";
            if(posteSecondaire.textContent == "Milieu Gauche"){
                posteSecondaire.textContent = "Aucun";
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Milieu Gauche";
            if(posteSelectionne.textContent == "Milieu Gauche"){
                posteSelectionne.textContent = "Aucun";
            }
        }
    });
    milieuoff.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Milieu Offensif";
            if(posteSecondaire.textContent == "Milieu Offensif"){
                posteSecondaire.textContent = "Aucun";
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Milieu Offensif";
            if(posteSelectionne.textContent == "Milieu Offensif"){
                posteSelectionne.textContent = "Aucun";
            }
        }
    });
    milieudef.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Milieu Defensif";
            if(posteSecondaire.textContent == "Milieu Defensif"){
                posteSecondaire.textContent = "Aucun";
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Milieu Defensif";
            if(posteSelectionne.textContent == "Milieu Defensif"){
                posteSelectionne.textContent = "Aucun";
            }
        }
    });

    // ATTAQUANT
    attaquant.addEventListener("click", function() {
        if(selection_principale == 1){
            selection_principale = 0;
            posteSelectionne.textContent = "Attaquant";
            if(posteSecondaire.textContent == "Attaquant"){
                posteSecondaire.textContent = "Aucun";
            }
        }
        if(selection_secondaire == 1){
            selection_secondaire = 0;
            posteSecondaire.textContent = "Attaquant";
            if(posteSelectionne.textContent == "Attaquant"){
                posteSelectionne.textContent = "Aucun";
            }
        }
    });
});
