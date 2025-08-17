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
<h1 class="text-primary"><i class="fas fa-users"></i> All Teams<small class="text-warning"> All Teams List!</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard </a></li>
        <li class="breadcrumb-item active" aria-current="page">All Teams</li>
    </ol>
</nav>
<?php if (isset($_GET['delete']) || isset($_GET['edit'])) { ?>
    <div role="alert" aria-live="assertive" aria-atomic="true" class="toast fade show" data-autohide="true" data-animation="true" data-delay="2000">
        <div class="toast-header">
            <strong class="mr-auto">Team Alert</strong>
            <small><?php echo date('d-M-Y'); ?></small>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <?php
            if (isset($_GET['delete'])) {
                if ($_GET['delete'] == 'success') {
                    echo "<p style='color: green; font-weight: bold;'>Team Deleted Successfully!</p>";
                }
            }
            if (isset($_GET['delete'])) {
                if ($_GET['delete'] == 'error') {
                    echo "<p style='color: red'; font-weight: bold;>Team Not Deleted!</p>";
                }
            }
            if (isset($_GET['edit'])) {
                if ($_GET['edit'] == 'success') {
                    echo "<p style='color: green; font-weight: bold; '>Team Edited Successfully!</p>";
                }
            }
            if (isset($_GET['edit'])) {
                if ($_GET['edit'] == 'error') {
                    echo "<p style='color: red; font-weight: bold;'>Team Not Edited!</p>";
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
            <th scope="col">Team Name</th>
            <th scope="col">Sport</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT t.*, s.name as sport_name FROM `teams` t JOIN `sports` s ON t.sport_id = s.id ORDER BY t.`id` DESC";
        $stmt = $db_con->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $i = 1;
        while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo ucwords($row['name']); ?></td>
                <td><?php echo ucwords($row['sport_name']); ?></td>
                <td>
                    <a class="btn btn-xs btn-warning" href="index.php?page=edit-team&id=<?php echo base64_encode($row['id']); ?>">
                        <i class="fa fa-edit"></i></a>
                    &nbsp;
                    <form method="POST" action="delete-team.php" onsubmit="return confirm('Are you sure want to delete this record?');" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="delete_team" class="btn btn-xs btn-danger">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </td>
            </tr>
        <?php $i++;
        }
        $stmt->close();
        ?>
    </tbody>
</table>
