<?php
session_start();
session_unset(); // セッションの変数を全て削除
session_destroy(); // セッションを破棄

header("Location: index.php");
exit;
?>