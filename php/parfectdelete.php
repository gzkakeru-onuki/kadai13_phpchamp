<?php
session_start();
ini_set("display_errors", 1);
include("../funcs/functions.php");

$pdo = db_conn();

$id = $_POST["id"];

$sql = "DELETE FROM photos WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
    sql_error($stmt);
} else {
    head("garbage.php");
}

?>