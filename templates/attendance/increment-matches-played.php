<?php

$mysqlClient = new PDO('mysql:host=localhost;dbname=us_avranches_symfony;port=3308;charset=utf8', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the request is a POST request.

    // Get the player IDs from the JSON data.
    $requestData = json_decode(file_get_contents('php://input'));
    $playerIdsToIncrement = $requestData->playerIds;

    // Iterate through the player IDs and update the matches_played field.
    foreach ($playerIdsToIncrement as $playerId) {
        // Update the matches_played field in the database for the player with the given ID.
        $stmt = $mysqlClient->prepare('UPDATE tbl_player SET matches_played = matches_played + 1 WHERE id = :playerId');
        $stmt->bindParam(':playerId', $playerId, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Send a success response (you can customize this based on your needs).
    http_response_code(200);
    echo json_encode(['message' => 'Matches played updated successfully']);
} else {
    // Handle other types of requests or invalid requests.
    http_response_code(400);
    echo json_encode(['message' => 'Bad Request']);
}
?>
