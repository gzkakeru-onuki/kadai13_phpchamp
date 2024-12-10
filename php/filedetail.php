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

$sql = "SELECT * FROM photos WHERE id =:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
    sql_error($stmt);
}

$values = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>photoshare - ファイル詳細</title>
    <link rel="stylesheet" href="../css/filedetail.css">
</head>

<body>
    <div class="container">
        <h1>ファイル詳細編集</h1>
        <?php foreach ($values as $value) { ?>
            <form action="updateimage.php" method="post" enctype="multipart/form-data">
                <div class="image-container">
                    <img src="<?= $value["file_path"]; ?>" alt="投稿写真">
                    
                    <div class="form-input">
                        <label for="file">更新したい写真または動画を選択</label>
                        <input type="file" name="file" id="file" accept="image/*,video/*">
                    </div>

                    <div class="form-input">
                        <label for="tag">更新したいタグを入力</label>
                        <input type="text" name="tag" id="tag" value="<?= $value["tag"]; ?>">
                    </div>

                    <div class="form-input">
                        <label for="shooting_at">更新したい撮影日時を入力</label>
                        <input type="date" name="shooting_at" id="shooting_at" value="<?= $value["shooting_at"]; ?>">
                    </div>

                    <div class="button-group">
                        <button type="submit" class="updateBtn">更新する</button>
                        <button type="button" class="removeBtn">削除する</button>
                    </div>

                    <input type="hidden" name="id" value="<?= $id; ?>">
                </div>
            </form>

            <form id="deleteForm" action="deleteimage.php" method="post" style="display: none;">
                <input type="hidden" name="id" value="<?= $id; ?>">
            </form>

            <a href="main.php" class="back-link">戻る</a>
        <?php } ?>
    </div>

    <script>
        document.querySelector('.removeBtn').addEventListener('click', function(e) {
            if (confirm('本当に削除しますか？この操作は取り消せません。')) {
                document.getElementById('deleteForm').submit();
            }
        });
    </script>
</body>

</html>