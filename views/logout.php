<?php
$_SESSION = [];

session_destroy();

header("Location: index.php?action=main");
exit;
?>