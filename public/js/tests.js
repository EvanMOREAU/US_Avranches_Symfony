// $(window).load(function(){
//     $(".col-3 input").val("");
//     $(".input-effect input").focusout(function(){
//       if($(this).val() != ""){
//         $(this).addClass("has-content");
//       }else{
//       $(this).removeClass("has-content");
//       }
//     });
//   });
document.addEventListener('DOMContentLoaded', function () {
    flatpickr(".flatpickr-date", {
        enableTime: false, // Désactive la sélection de l'heure si nécessaire
        dateFormat: "s SSS", // Format pour les secondes et les millisecondes
        allowInput: true, // Permet à l'utilisateur d'entrer le texte directement
        time_24hr: true, // Utilise le format 24 heures
    });
});