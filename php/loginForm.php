<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <title>photoshere - ログイン</title>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h1 class="title">photoshare</h1>
            <p class="subtitle">写真・動画管理アプリ</p>

            <form id="registrationForm" action="login.php" method="post">
                <div class="form-group">
                    <label for="mail">メールアドレス</label>
                    <input id="mail" type="email" name="mail" placeholder="example@photoshere.com" required>
                    <div id="mailError" class="error"></div>
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input id="password" type="password" name="password" placeholder="パスワードを入力" required>
                    <div id="passwordError" class="error"></div>
                </div>

                <button type="submit" class="submit-btn">ログイン</button>

                <div class="login-link">
                    <p>アカウントをお持ちでない方は</p>
                    <div class="signup-link"><a href="index.php">新規登録</a></div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>