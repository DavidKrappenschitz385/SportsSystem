<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('admin');

if (isset($_GET['id'])) {
    $player_id = base64_decode($_GET['id']);
    $stmt = $db_con->prepare("SELECT p.*, s.name as sport_name FROM `players` p JOIN `sports` s ON p.sport_id = s.id WHERE p.id = ?");
    $stmt->bind_param("i", $player_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $player = $result->fetch_assoc();
    $stmt->close();
} else {
    redirect('index.php?page=all-players');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Player ID Card</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/id-card.css">
</head>
<body>
    <div class="container">
        <div class="id-card">
            <div class="id-card-header">
                <h2>Player ID Card</h2>
            </div>
            <div class="id-card-body">
                <div class="id-card-photo">
                    <img src="images/<?php echo $player['photo']; ?>" alt="Player Photo">
                </div>
                <div class="id-card-info">
                    <p><strong>Name:</strong> <?php echo ucwords($player['full_name']); ?></p>
                    <p><strong>Player ID:</strong> <?php echo $player['player_id']; ?></p>
                    <p><strong>Sport:</strong> <?php echo ucwords($player['sport_name']); ?></p>
                </div>
            </div>
            <div class="id-card-footer">
                <p>Barangay Sports Registration System</p>
            </div>
        </div>
        <div class="text-center mt-3">
            <button class="btn btn-primary" onclick="window.print()">Print ID Card</button>
            <a href="index.php?page=all-players" class="btn btn-secondary">Back to All Players</a>
        </div>
    </div>
</body>
</html>
