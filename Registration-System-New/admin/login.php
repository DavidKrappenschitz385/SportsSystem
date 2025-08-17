<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

if (isset($_SESSION['user_login'])) {
    redirect_to_dashboard();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $input_arr = array();

    if (empty($username)) {
        $input_arr['input_user_error'] = "Username Is Required!";
    }

    if (empty($password)) {
        $input_arr['input_pass_error'] = "Password Is Required!";
    }

    if (count($input_arr) == 0) {
        $user = get_user_by_username($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_login'] = $username;
            $_SESSION['user_role'] = $user['role'];
            redirect_to_dashboard();
        } else {
            $worngpass = "Invalid username or password!";
        }
    }
}

$page_title = "Login";
require_once '../templates/header.php';
?>

<div class="container">
    <br>
    <h1 class="text-center">Login</h1>
    <hr>
    <br>
    <div class="row">
        <div class="col-md-4 offset-md-4">
            <?php if (isset($worngpass)) { ?>
                <div class="alert alert-danger"><?php echo $worngpass; ?></div>
            <?php } ?>
            <?php if (isset($status_error)) { ?>
                <div class="alert alert-warning"><?php echo $status_error; ?></div>
            <?php } ?>
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" class="form-control" name="username" value="<?php echo isset($username) ? $username : ''; ?>" placeholder="Username" required>
                    <?php if (isset($input_arr['input_user_error'])) {
                        echo '<label class="error">' . $input_arr['input_user_error'] . '</label>';
                    } ?>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <?php if (isset($input_arr['input_pass_error'])) {
                        echo '<label class="error">' . $input_arr['input_pass_error'] . '</label>';
                    } ?>
                </div>
                <div class="text-center">
                    <button type="submit" name="login" class="btn btn-primary">Sign in</button>
                </div>
                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="../register.php">Register here</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../templates/footer.php'; ?>