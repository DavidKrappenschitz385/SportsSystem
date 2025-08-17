<?php
$stmt = $db_con->prepare("SELECT t.*, s.name as sport_name FROM `teams` t JOIN `sports` s ON t.sport_id = s.id ORDER BY t.`name` ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<h2 class="text-primary">All Teams</h2>
<table class="table table-striped table-hover table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Team Name</th>
            <th>Sport</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo ucwords($row['name']); ?></td>
                <td><?php echo ucwords($row['sport_name']); ?></td>
                <td>
                    <form action="dashboard.php?page=request-join-team" method="POST" style="display: inline-block;">
                        <input type="hidden" name="team_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="request_join_team" class="btn btn-primary">Request to Join</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
