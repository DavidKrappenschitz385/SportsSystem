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
<h1 class="text-primary"><i class="fas fa-volleyball-ball"></i> All Sports<small class="text-warning"> All Sports List!</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard </a></li>
        <li class="breadcrumb-item active" aria-current="page">All Sports</li>
    </ol>
</nav>
<?php if (isset($_GET['delete']) || isset($_GET['edit'])) { ?>
    <div role="alert" aria-live="assertive" aria-atomic="true" class="toast fade show" data-autohide="true" data-animation="true" data-delay="2000">
        <div class="toast-header">
            <strong class="mr-auto">Sport Alert</strong>
            <small><?php echo date('d-M-Y'); ?></small>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <?php
            if (isset($_GET['delete'])) {
                if ($_GET['delete'] == 'success') {
                    echo "<p style='color: green; font-weight: bold;'>Sport Deleted Successfully!</p>";
                }
            }
            if (isset($_GET['delete'])) {
                if ($_GET['delete'] == 'error') {
                    echo "<p style='color: red'; font-weight: bold;>Sport Not Deleted!</p>";
                }
            }
            if (isset($_GET['edit'])) {
                if ($_GET['edit'] == 'success') {
                    echo "<p style='color: green; font-weight: bold; '>Sport Edited Successfully!</p>";
                }
            }
            if (isset($_GET['edit'])) {
                if ($_GET['edit'] == 'error') {
                    echo "<p style='color: red; font-weight: bold;'>Sport Not Edited!</p>";
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
            <th scope="col">Name</th>
            <th scope="col">Age Bracket</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM `sports` ORDER BY `id` DESC";
        $stmt = $db_con->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $i = 1;
        while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo ucwords($row['name']); ?></td>
                <td><?php echo $row['age_bracket']; ?></td>
                <td>
                    <a class="btn btn-xs btn-warning" href="index.php?page=edit-sport&id=<?php echo base64_encode($row['id']); ?>">
                        <i class="fa fa-edit"></i></a>
                    &nbsp;
                    <form method="POST" action="delete-sport.php" onsubmit="return confirm('Are you sure want to delete this record?');" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="delete_sport" class="btn btn-xs btn-danger">
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
