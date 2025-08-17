<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('admin');

$coach_id = base64_decode($_GET['id']);

$stmt = $db_con->prepare("SELECT * FROM `users` WHERE `id` = ? AND `role` = 'coach'");
$stmt->bind_param("i", $coach_id);
$stmt->execute();
$result = $stmt->get_result();
$coach = $result->fetch_assoc();
$stmt->close();

?>
<h1 class="text-primary"><i class="fas fa-users"></i> Coach's Members<small class="text-warning"> Members of <?php echo ucwords($coach['full_name']); ?></small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard </a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="index.php?page=all-users">All Users </a></li>
        <li class="breadcrumb-item active" aria-current="page">View Members</li>
    </ol>
</nav>

<table class="table  table-striped table-hover table-bordered" id="data">
    <thead class="thead-dark">
        <tr>
            <th scope="col">SL</th>
            <th scope="col">Player ID</th>
            <th scope="col">Full Name</th>
            <th scope="col">Age</th>
            <th scope="col">Gender</th>
            <th scope="col">Sport</th>
            <th scope="col">Contact</th>
            <th scope="col">Photo</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT p.*, s.name as sport_name FROM `players` p JOIN `sports` s ON p.sport_id = s.id JOIN `coaches` c ON p.team_id = c.team_id WHERE c.user_id = ?";
        $stmt = $db_con->prepare($query);
        $stmt->bind_param("i", $coach_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $i = 1;
        while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['player_id']; ?></td>
                <td><?php echo ucwords($row['full_name']); ?></td>
                <td><?php echo $row['age']; ?></td>
                <td><?php echo $row['gender']; ?></td>
                <td><?php echo $row['sport_name']; ?></td>
                <td><?php echo $row['contact_number']; ?></td>
                <td><img src="images/<?php echo $row['photo']; ?>" height="50px"></td>
            </tr>
        <?php $i++;
        }
        $stmt->close();
        ?>
    </tbody>
</table>
