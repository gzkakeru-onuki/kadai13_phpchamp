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

$targetFile = $_FILES["file"]["name"];
$file_path = "../uploads/" . basename($targetFile); //ファイル名を取得

$tag = $_POST["tag"];
$shooting_at = $_POST["shooting_at"];
$id = $_POST["id"];

if (move_uploaded_file($_FILES["file"]["tmp_name"], $file_path)) {
    $sql = "UPDATE photos SET file_path = :file_path, shooting_at = :shooting_at, tag=:tag WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":file_path", $file_path, PDO::PARAM_STR);
    $stmt->bindValue(":tag", $tag, PDO::PARAM_STR);
    $stmt->bindValue(":shooting_at", $shooting_at, PDO::PARAM_STR);
    $stmt->bindValue(":id", $id, PDO::PARAM_STR);
    $status = $stmt->execute();
}
//４．データ登録処理後
if ($status == false) { // 登録処理にエラーがあれば
    sql_error($stmt);
} else {
    head("main.php");
}
