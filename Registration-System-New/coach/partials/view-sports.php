<?php
$stmt = $db_con->prepare("SELECT * FROM `sports` ORDER BY `name` ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<h2 class="text-primary">All Sports</h2>
<table class="table table-striped table-hover table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Sport Name</th>
            <th>Age Bracket</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo ucwords($row['name']); ?></td>
                <td><?php echo $row['age_bracket']; ?></td>
                <td>
                    <form action="dashboard.php?page=request-join-sport" method="POST" style="display: inline-block;">
                        <input type="hidden" name="sport_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="request_join_sport" class="btn btn-primary">Request to Join</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
