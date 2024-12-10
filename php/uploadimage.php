<?php
session_start();
ini_set("display_errors", 1);
include("../funcs/functions.php");

// セッションチェック
if (!isset($_SESSION["name"])) {
    header("Location: loginForm.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>photoshare - アップロード</title>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        form { display: flex; flex-direction: column; gap: 16px; }
        label { margin-bottom: 8px; display: block; }
        input { padding: 8px; margin-bottom: 16px; }
        button { padding: 12px; background-color: #4a90e2; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #357abd; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: #4a90e2;">photoshare</h1>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="file">写真または動画を選択</label>
                <input type="file" name="image" id="file" accept="image/*,video/*" required>
            </div>
            <div>
                <label for="shooting_at">撮影日時</label>
                <input type="date" name="shooting_at" id="shooting_at">
            </div>
            <div>
                <label for="tag">タグ</label>
                <input type="text" name="tag" id="tag" placeholder="タグを入力（複数の場合はカンマ区切り）">
            </div>
            <button type="submit">アップロード</button>
        </form>
        <div style="margin-top: 20px;">
            <a href="main.php">戻る</a>
        </div>
    </div>
</body>
</html> 