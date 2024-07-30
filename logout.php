<?php
session_start();
include 'log_activity.php';

if (isset($_SESSION["username"])) {
    // Log logout activity
    $activity = "Logged out";
    log_activity($_SESSION["user_id"], $activity);

    session_unset();
    session_destroy();
}

header("Location: login.php");
exit();
?>
