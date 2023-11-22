// // JavaScript to add click event listeners to each abs
// document.addEventListener('DOMContentLoaded', function () {
//     const abss = document.querySelectorAll('.abs');
//     const selectedUserIds = [];

//     abss.forEach(abs => {
//         selectedUserIds.push(abs.closest('.img').getAttribute('data-user-id'));
//         abs.addEventListener('click', function () { // Toggle the 'present' class to change the color and selectedUserIds
//             abs.classList.toggle('present');

//             // Update the selectedUserIds array
//             if (abs.classList.contains('present')) {
//                 selectedUserIds.push(abs.closest('.img').getAttribute('data-user-id'));
//             } else {
//                 const index = selectedUserIds.indexOf(abs.closest('.img').getAttribute('data-user-id'));
//                 if (index !== -1) {
//                     selectedUserIds.splice(index, 1);
//                 }
//             }
//         });
//     });

//     // Function to finalize attendance and send a POST request
//     function finalizeAttendanceAndRedirect() {
//         const updateMatchesPlayedURL = '{{ path('update_matches_played', {'category': category}) }}';
//         fetch(updateMatchesPlayedURL, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json'
//             },
//             body: JSON.stringify({ selectedUserIds: selectedUserIds })
//         }).then(response => {
//             if (response.ok) {
//                 alert('Appel effectué avec succès!');
//                 // Redirect to the desired route after the action is completed
//                 window.location.href = '{{ path('app_attendance', {'category': category}) }}';
//             } else {
//                 alert('Erreur, l\'appel n\'a pas pu être effectué !');
//             }
//         });
//     }

//     // Attach the finalizeAttendanceAndRedirect function to the button click event
//     document.getElementById('finaliserAppel').addEventListener('click', finalizeAttendanceAndRedirect);
// });