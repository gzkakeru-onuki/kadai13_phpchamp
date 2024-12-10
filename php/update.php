<?php
session_start();
ini_set("display_errors", 1);
include("../funcs/functions.php");

$pdo = db_conn();

$name = $_POST["name"];
$mail = $_POST["mail"];
$password = $_POST["password"];
$id=$_SESSION["id"];

$sql = "UPDATE users SET name = :name, mail = :mail, password = :password WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt ->bindValue(":name",$name,PDO::PARAM_STR);
$stmt ->bindValue(":mail",$mail,PDO::PARAM_STR);
$stmt ->bindValue(":password",$password,PDO::PARAM_STR);
$stmt ->bindValue(":id",$id,PDO::PARAM_INT);
$status = $stmt->execute();

//４．データ登録処理後
if ($status == false) { // 登録処理にエラーがあれば
    sql_error($stmt);
}else {
    head("main.php");
}

?>