<?php
//【重要】
//insert.phpを修正（関数化）してからselect.phpを開く！！
session_start();
$mail   = $_POST["mail"];
$password  = $_POST["password"];


include("../funcs/functions.php");
$pdo = db_conn();

//２．データ登録SQL作成
$sql = "SELECT * FROM users WHERE mail =:mail AND password =:password";
$stmt = $pdo->prepare($sql);
$stmt ->bindValue(":mail",$mail,PDO::PARAM_STR);
$stmt ->bindValue(":password",$password,PDO::PARAM_STR);
$status = $stmt->execute();

//３．データ表示
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($status==false) {
  sql_error($stmt);
}

if($user){
  $_SESSION['name']= $user['name'];
  $_SESSION["id"]=$user["id"];
  header("Location: main.php");
  exit();
}else {
  echo "メールアドレスかパスワードが間違えています。<br>もしくは、アカウントが登録されていません。";
  exit();
}
?>