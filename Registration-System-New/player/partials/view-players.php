<?php
$stmt = $db_con->prepare("SELECT p.*, s.name as sport_name, t.name as team_name FROM `players` p LEFT JOIN `sports` s ON p.sport_id = s.id LEFT JOIN `teams` t ON p.team_id = t.id WHERE p.user_id != ? ORDER BY p.`full_name` ASC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2 class="text-primary">All Players</h2>
<table class="table table-striped table-hover table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Player ID</th>
            <th>Full Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Sport</th>
            <th>Team</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $row['player_id']; ?></td>
                <td><?php echo ucwords($row['full_name']); ?></td>
                <td><?php echo $row['age']; ?></td>
                <td><?php echo $row['gender']; ?></td>
                <td><?php echo ucwords($row['sport_name']); ?></td>
                <td><?php echo ucwords($row['team_name']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
