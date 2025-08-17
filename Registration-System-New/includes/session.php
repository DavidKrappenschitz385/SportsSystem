<?php
session_start();

require_once 'db_con.php';

function check_session() {
    if (!isset($_SESSION['user_login'])) {
        header('Location: ../login.php');
        exit;
    }
}

function check_role($role) {
    if ($_SESSION['user_role'] !== $role) {
        header('Location: ../unauthorized.php');
        exit;
    }
}

function redirect_to_dashboard() {
    if (isset($_SESSION['user_login'])) {
        $role = $_SESSION['user_role'];
        if ($role === 'admin') {
            header('Location: ../admin/dashboard.php');
        } elseif ($role === 'coach') {
            header('Location: ../coach/dashboard.php');
        } elseif ($role === 'player') {
            header('Location: ../player/dashboard.php');
        }
        exit;
    }
}
