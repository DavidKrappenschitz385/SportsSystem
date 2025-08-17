<?php
$stmt = $db_con->prepare("SELECT c.*, s.name as sport_name, t.name as team_name FROM `coaches` c LEFT JOIN `sports` s ON c.sport_id = s.id LEFT JOIN `teams` t ON c.team_id = t.id WHERE c.user_id != ? ORDER BY c.`full_name` ASC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2 class="text-primary">All Coaches</h2>
<table class="table table-striped table-hover table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Full Name</th>
            <th>Sport</th>
            <th>Team</th>
            <th>Experience (Years)</th>
            <th>Certifications</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo ucwords($row['full_name']); ?></td>
                <td><?php echo ucwords($row['sport_name']); ?></td>
                <td><?php echo ucwords($row['team_name']); ?></td>
                <td><?php echo $row['experience_years']; ?></td>
                <td><?php echo htmlspecialchars($row['certifications']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
