<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/signup.css">
    <title>photoshare - 会員登録</title>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h1 class="title">photoshare</h1>
            <p class="subtitle">写真・動画管理アプリ</p>
            
            <form id="registrationForm" action="createaccount.php" method="post">
                <div class="form-group">
                    <label for="name">ユーザー名</label>
                    <input id="name" type="text" name="name" minlength="3" maxlength="16" placeholder="photo太郎" required>
                    <div id="nameError" class="error"></div>
                </div>

                <div class="form-group">
                    <label for="mail">メールアドレス</label>
                    <input id="mail" type="email" name="mail" placeholder="example@photoshare.com" required>
                    <div id="mailError" class="error"></div>
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input id="password" type="password" name="password" minlength="8" maxlength="16" placeholder="8文字以上で入力" required>
                    <div id="passwordError" class="error"></div>
                </div>

                <button type="submit" class="submit-btn">アカウント登録</button>

                <div class="login-link">
                    <p>すでにアカウントをお持ちの方は</p>
                    <a href="loginForm.php">ログイン</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>