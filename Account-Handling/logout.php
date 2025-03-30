<?php
require_once 'database/config.php';

session_destroy();

header("Location: login.php");
exit();

?>