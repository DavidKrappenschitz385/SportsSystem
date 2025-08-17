<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('admin');

$user = $_SESSION['user_login'];
$corepage = explode('/', $_SERVER['PHP_SELF']);
$corepage = end($corepage);
if ($corepage !== 'index.php') {
    if ($corepage == $corepage) {
        $corepage = explode('.', $corepage);
        header('Location: index.php?page=' . $corepage[0]);
    }
}
?>
<h1 class="text-primary"><i class="fas fa-user"></i> Admin User Profile</h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard </a></li>
        <li class="breadcrumb-item active" aria-current="page">User Profile</li>
    </ol>
</nav>
<?php
$stmt = $db_con->prepare("SELECT * FROM `users` WHERE `username` = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
?>
<div class="row">
    <div class="col-sm-6">
        <table class="table table-bordered">
            <tr>
                <td>User ID</td>
                <td><?php echo $row['id']; ?></td>
            </tr>
            <tr>
                <td>Name</td>
                <td><?php echo ucwords($row['full_name']); ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?php echo $row['email']; ?></td>
            </tr>
            <tr>
                <td>Username</td>
                <td><?php echo ucwords($row['username']); ?></td>
            </tr>
            <tr>
                <td>Role</td>
                <td><?php echo ucwords($row['role']); ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td><?php echo ucwords($row['status']); ?></td>
            </tr>
        </table>
        <a class="btn btn-warning pull-right" href="index.php?page=edit-user&id=<?php echo base64_encode($row['id']); ?>">Edit Profile</a>
    </div>
    <div class="col-sm-6">
        <h3>Profile Picture</h3>
        <?php if (!empty($row['photo'])) { ?>
            <a href="images/<?php echo $row['photo']; ?>">
                <img class="img-thumbnail" id="imguser" src="images/<?php echo $row['photo']; ?>" width="200px">
            </a>
        <?php } ?>
        <?php
        if (isset($_POST['upphoto'])) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                die('CSRF token validation failed.');
            }
            if (!empty($row['photo']) && file_exists('images/' . $row['photo'])) {
                unlink('images/' . $row['photo']);
            }
            $photofile = $_FILES['userphoto']['tmp_name'];
            $ext = pathinfo($_FILES['userphoto']['name'], PATHINFO_EXTENSION);
            $upphoto = $user . date('YmdHis') . '.' . $ext;

            $stmt = $db_con->prepare("UPDATE `users` SET `photo` = ? WHERE `users`.`username` = ?");
            $stmt->bind_param("ss", $upphoto, $user);
            if ($stmt->execute()) {
                move_uploaded_file($photofile, 'images/' . $upphoto);
                redirect('index.php?page=user-profile');
            } else {
                echo "Profile Picture Not Uploaded";
            }
            $stmt->close();
        }
        ?><br>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="file" name="userphoto" required="" id="photo"><br>
            <input class="btn btn-info" type="submit" name="upphoto" value="Upload Photo">
        </form>
    </div>
</div>
