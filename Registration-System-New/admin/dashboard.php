<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('admin');

$corepage = explode('/', $_SERVER['PHP_SELF']);
$corepage = end($corepage);
if ($corepage !== 'index.php') {
    if ($corepage == $corepage) {
        $corepage = explode('.', $corepage);
        header('Location: index.php?page=' . $corepage[0]);
    }
}
?>

<h1><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a> <small>Statistics Overview</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-user"></i> Dashboard</li>
    </ol>
</nav>

<div class="row student">
    <div class="col-sm-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-4">
                        <i class="fa fa-users fa-3x"></i>
                    </div>
                    <div class="col-sm-8">
                        <div class="float-sm-right">&nbsp;<span style="font-size: 30px"><?php $stmt = $db_con->prepare("SELECT * FROM `players`");
                                                                                        $stmt->execute();
                                                                                        $players = $stmt->get_result();
                                                                                        echo $players->num_rows; ?></span></div>
                        <div class="clearfix"></div>
                        <div class="float-sm-right">Total Players</div>
                    </div>
                </div>
            </div>
            <div class="list-group-item-primary list-group-item list-group-item-action">
                <a href="index.php?page=all-players">
                    <div class="row">
                        <div class="col-sm-8">
                            <p class="">All Players</p>
                        </div>
                        <div class="col-sm-4">
                            <i class="fa fa-arrow-right float-sm-right"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-4">
                        <i class="fa fa-chalkboard-teacher fa-3x"></i>
                    </div>
                    <div class="col-sm-8">
                        <div class="float-sm-right">&nbsp;<span style="font-size: 30px"><?php $stmt = $db_con->prepare("SELECT * FROM `coaches`");
                                                                                        $stmt->execute();
                                                                                        $coaches = $stmt->get_result();
                                                                                        echo $coaches->num_rows; ?></span></div>
                        <div class="clearfix"></div>
                        <div class="float-sm-right">Total Coaches</div>
                    </div>
                </div>
            </div>
            <div class="list-group-item-primary list-group-item list-group-item-action">
                <a href="index.php?page=all-coaches">
                    <div class="row">
                        <div class="col-sm-8">
                            <p class="">All Coaches</p>
                        </div>
                        <div class="col-sm-4">
                            <i class="fa fa-arrow-right float-sm-right"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-4">
                        <i class="fa fa-users fa-3x"></i>
                    </div>
                    <div class="col-sm-8">
                        <div class="float-sm-right">&nbsp;<span style="font-size: 30px"><?php $stmt = $db_con->prepare("SELECT * FROM `users`");
                                                                                        $stmt->execute();
                                                                                        $tusers = $stmt->get_result();
                                                                                        echo $tusers->num_rows; ?></span></div>
                        <div class="clearfix"></div>
                        <div class="float-sm-right">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="list-group-item-primary list-group-item list-group-item-action">
                <a href="index.php?page=all-users">
                    <div class="row">
                        <div class="col-sm-8">
                            <p class="">All Users</p>
                        </div>
                        <div class="col-sm-4">
                            <i class="fa fa-arrow-right float-sm-right"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<hr>
<h3>Players</h3>
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
        $query = "SELECT p.*, s.name as sport_name FROM `players` p JOIN `sports` s ON p.sport_id = s.id LEFT JOIN `users` u ON p.user_id = u.id WHERE p.user_id IS NULL OR (u.status = 'approved' AND u.role = 'player') ORDER BY p.`registration_date` DESC";
        $stmt = $db_con->prepare($query);
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
        $stmt->close(); ?>

    </tbody>
</table>
<hr>
<h3>Coaches</h3>
<table class="table  table-striped table-hover table-bordered" id="data">
    <thead class="thead-dark">
        <tr>
            <th scope="col">SL</th>
            <th scope="col">Full Name</th>
            <th scope="col">Sport</th>
            <th scope="col">Team</th>
            <th scope="col">Experience (Years)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query2 = "SELECT c.*, s.name as sport_name, t.name as team_name FROM `coaches` c LEFT JOIN `sports` s ON c.sport_id = s.id LEFT JOIN `teams` t ON c.team_id = t.id LEFT JOIN `users` u ON c.user_id = u.id WHERE c.user_id IS NULL OR (u.status = 'approved' AND u.role = 'coach') ORDER BY c.`id` DESC LIMIT 10";
        $stmt2 = $db_con->prepare($query2);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $j = 1;
        while ($c = $result2->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $j; ?></td>
                <td><?php echo ucwords($c['full_name']); ?></td>
                <td><?php echo ucwords($c['sport_name']); ?></td>
                <td><?php echo ucwords($c['team_name']); ?></td>
                <td><?php echo $c['experience_years']; ?></td>
            </tr>
        <?php $j++;
        }
        $stmt2->close(); ?>

    </tbody>
</table>