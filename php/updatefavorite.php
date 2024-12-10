<?php
session_start();
ini_set("display_errors", 1);
include("../funcs/functions.php");

// セッションチェック
if (!isset($_SESSION["name"])) {
    header("Location: loginForm.php");
    exit();
}

$pdo = db_conn();
$id = $_POST["id"];

$sql = "UPDATE photos SET is_favorite = 1 WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
    sql_error($stmt);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お気に入り追加</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .message {
            color: #4a90e2;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <p class="message">お気に入りに追加しました</p>

    <script>
        setTimeout(() => {
            $('.message').fadeOut('slow', function() {
                history.back();
            });
        }, 1500);
    </script>
</body>
</html>