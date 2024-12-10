<?php
session_start();
ini_set("display_errors", 1);
include("../funcs/functions.php");

$pdo = db_conn();

$id = $_POST["id"];

$sql = "UPDATE photos SET deleteFlg =1 WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

//４．データ削除処理後
if ($status == false) { // 登録処理にエラーがあれば
    sql_error($stmt);
} else {
    head("main.php");
}
