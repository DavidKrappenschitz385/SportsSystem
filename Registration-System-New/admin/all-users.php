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
<h1 class="text-primary"><i class="fas fa-users"></i> All Users<small class="text-warning"> All Users List</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard </a></li>
        <li class="breadcrumb-item active" aria-current="page">All Users</li>
    </ol>
</nav>

<table class="table  table-striped table-hover table-bordered" id="data">
    <thead class="thead-dark">
        <tr>
            <th scope="col">SL</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Username</th>
            <th scope="col">Role</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM `users`";
        $stmt = $db_con->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $i = 1;
        while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo ucwords($row['full_name']); ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo ucwords($row['username']); ?></td>
                <td><?php echo ucwords($row['role']); ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <?php if ($row['role'] == 'coach') : ?>
                        <a class="btn btn-xs btn-primary" href="index.php?page=view-members&id=<?php echo base64_encode($row['id']); ?>">
                            <i class="fa fa-eye"></i> View Members
                        </a>
                    <?php endif; ?>
                    <a class="btn btn-xs btn-warning" href="index.php?page=edit-user&id=<?php echo base64_encode($row['id']); ?>">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <form method="POST" action="delete-user.php" onsubmit="return confirm('Are you sure want to delete this user?');" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="delete_user" class="btn btn-xs btn-danger">
                            <i class="fas fa-trash-alt"></i> Delete
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