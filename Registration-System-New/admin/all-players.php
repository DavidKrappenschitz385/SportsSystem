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
<h1 class="text-primary"><i class="fas fa-users"></i> All Players<small class="text-warning"> All Players List!</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard </a></li>
        <li class="breadcrumb-item active" aria-current="page">All Players</li>
    </ol>
</nav>
<?php if (isset($_GET['delete']) || isset($_GET['edit'])) { ?>
    <div role="alert" aria-live="assertive" aria-atomic="true" class="toast fade show" data-autohide="true" data-animation="true" data-delay="2000">
        <div class="toast-header">
            <strong class="mr-auto">Player Insert Alert</strong>
            <small><?php echo date('d-M-Y'); ?></small>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <?php
            if (isset($_GET['delete'])) {
                if ($_GET['delete'] == 'success') {
                    echo "<p style='color: green; font-weight: bold;'>Player Deleted Successfully!</p>";
                }
            }
            if (isset($_GET['delete'])) {
                if ($_GET['delete'] == 'error') {
                    echo "<p style='color: red'; font-weight: bold;>Player Not Deleted!</p>";
                }
            }
            if (isset($_GET['edit'])) {
                if ($_GET['edit'] == 'success') {
                    echo "<p style='color: green; font-weight: bold; '>Player Edited Successfully!</p>";
                }
            }
            if (isset($_GET['edit'])) {
                if ($_GET['edit'] == 'error') {
                    echo "<p style='color: red; font-weight: bold;'>Player Not Edited!</p>";
                }
            }
            ?>
        </div>
    </div>
<?php } ?>
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
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = 'SELECT p.*, s.name as sport_name FROM `players` p JOIN `sports` s ON p.sport_id = s.id LEFT JOIN `users` u ON p.user_id = u.id WHERE p.user_id IS NULL OR (u.status = \'approved\' AND u.role = \'player\') ORDER BY p.`registration_date` DESC';
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
                <td>
                    <a class="btn btn-xs btn-warning" href="index.php?page=edit-player&id=<?php echo base64_encode($row['id']); ?>&photo=<?php echo base64_encode($row['photo']); ?>">
                        <i class="fa fa-edit"></i></a>
                    &nbsp;
                    <form method="POST" action="delete-player.php" onsubmit="return confirm('Are you sure want to delete this record?');" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="photo" value="<?php echo $row['photo']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="delete_player" class="btn btn-xs btn-danger">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                    &nbsp;
                    <a class="btn btn-xs btn-info" href="index.php?page=generate-id&id=<?php echo base64_encode($row['id']); ?>" target="_blank">
                        <i class="fas fa-id-card"></i>
                    </a>
                </td>
            </tr>
        <?php $i++;
        }
        $stmt->close();
        ?>

    </tbody>
</table>