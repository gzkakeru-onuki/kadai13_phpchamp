<?php
session_start();
ini_set("display_errors", 1);


$name = $_POST["name"];
$mail = $_POST["mail"];
$password = $_POST["password"];


include("../funcs/functions.php");
$pdo = db_conn();

//３．データ登録SQL作成

$sql = "INSERT INTO users(name, password, mail, created_at) VALUES (:name,:password,:mail,sysdate());";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':password', $password, PDO::PARAM_STR);
$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
$status = $stmt->execute(); // SQL実行

$sql = "SELECT * FROM users WHERE mail =:mail AND password =:password";
$stmt = $pdo->prepare($sql);
$stmt ->bindValue(":mail",$mail,PDO::PARAM_STR);
$stmt ->bindValue(":password",$password,PDO::PARAM_STR);
$status = $stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

//４．データ登録処理後
if ($status == false) { // 登録処理にエラーがあれば
    sql_error($stmt);
} else {
    $_SESSION["name"]=$name;
    $_SESSION["id"] =$user["id"];
    head("main.php");
}
