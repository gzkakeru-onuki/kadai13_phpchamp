<?php

//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

//db接続
function db_conn(){
    
    // try {
    //     $db_name = "pioneer-mind_nikinabi";  // データベース名
    //     $db_id   = "pioneer-mind_nikinabi"; // アカウント名
    //     $db_pw   = "Ka12183002";           // パスワード
    //     $db_host = "mysql80.pioneer-mind.sakura.ne.jp"; // DBホスト
    //     return new PDO('mysql:dbname='.$db_name.';charset=utf8;host='.$db_host, $db_id, $db_pw);
    // } catch (PDOException $e) {
    //     exit('DB Connection Error:'.$e->getMessage());
    // }

    try {
        $db_name = "photoshere";  // データベース名
        $db_id   = "root"; // アカウント名
        $db_pw   = "";           // パスワード
        $db_host = "localhost"; // DBホスト
        return new PDO('mysql:dbname='.$db_name.';charset=utf8;host='.$db_host, $db_id, $db_pw);
    } catch (PDOException $e) {
        exit('DB Connection Error:'.$e->getMessage());
    }
}   

//SQLエラー関数：sql_error($stmt)
function sql_error($stmt){
    $error = $stmt->errorInfo();
    exit("SQLError:".$error[2]);
}

//リダイレクト関数: redirect($filename)
function redirect($filename){
    redirect("Location: ".$filename);
    exit();
}

//ページ遷移関数：header($filename)
function head($filename){
    header("Location: ".$filename);
    exit();
}