<?php
session_start();
session_destroy();
setcookie("sb_user", "", time() - 3600, "/");
header('Location: account.php');
?>
