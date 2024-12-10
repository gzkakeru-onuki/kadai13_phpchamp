<?php
session_start();
$name = $_SESSION["name"];
$id = $_SESSION["id"];

include("../funcs/functions.php");
$pdo = db_conn();

$sql = "SELECT * FROM users WHERE id =:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

$userdetail = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>photoshare - ユーザー情報</title>
    <link rel="stylesheet" href="../css/userdetail.css">
</head>

<body>
    <div class="container">
        <h1>ユーザー情報編集</h1>
        <?php foreach ($userdetail as $uinfo) { ?>
            <form action="update.php" method="post">
                <div class="form-input">
                    <label for="name">ユーザー名</label>
                    <input type="text" id="name" class="name" name="name" value="<?= $uinfo["name"]; ?>" required>
                </div>

                <div class="form-input">
                    <label for="mail">メールアドレス</label>
                    <input type="email" id="mail" class="mail" name="mail" value="<?= $uinfo["mail"]; ?>" required>
                </div>

                <div class="form-input">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" class="password" name="password" value="<?= $uinfo["password"]; ?>" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="editBtn">更新する</button>
                    <button type="button" class="removeBtn">退会する</button>
                </div>
            </form>

            <form id="deleteForm" action="delete.php" method="post" style="display: none;"></form>
            
            <a href="main.php" class="back-link">戻る</a>
        <?php } ?>
    </div>

    <script>
        document.querySelector('.removeBtn').addEventListener('click', function(e) {
            if (confirm('本当に退会しますか？この操作は取り消せません。')) {
                document.getElementById('deleteForm').submit();
            }
        });
    </script>
</body>

</html>