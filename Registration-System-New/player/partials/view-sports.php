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
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo ucwords($row['name']); ?></td>
                <td><?php echo $row['age_bracket']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
