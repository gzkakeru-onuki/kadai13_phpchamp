<?php
session_start();
ini_set("display_errors", 1);
include("../funcs/functions.php");

// セッションチェック
if (!isset($_SESSION["name"])) {
    header("Location: loginForm.php");
    exit();
}

try {
    $pdo = db_conn();
    $user_id = $_SESSION["id"];
    $sql = "SELECT * FROM photos 
            WHERE user_id = :user_id 
            AND deleteFlg = 0 
            ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

    // executeの引数を削除
    if (!$stmt->execute()) {
        throw new Exception("SQL実行エラー");
    }

    $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "エラー発生: " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <title>photoshare</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><a href="./main.php">photoshare</a></h1>

            <form action="search.php" method="post">
                <div class="search"><input class="search-input" name="word" type="text" placeholder="写真やアルバムを検索"><button type="submit"><i class="fas fa-search"></i>検索</button></div>
            </form>

            <nav>
                <ul class="header-list">
                    <li class="header-item"><?= $_SESSION["name"]; ?>さん</li>
                    <li class="header-item"><a href="./userdetail.php">マイページ</a></li>
                    <li class="header-item"><a href="uploadimage.php">アップロード</a></li>
                    <li class="header-item"><a href="./logout.php">ログアウト</a></li>
                </ul>
            </nav>
        </div>
        <div class="side">
            <div class="search2">
                <p><a href="./search.php?new=new">撮影日時が新しい順</a></p>
                <p><a href="./search.php?old=old">撮影日時が古い順</a></p>
                <p><a href="favorite.php">お気に入り</a></p>
                <p><a href="photo.php">写真</a></p>
                <p style="border-bottom: 1px solid #ddd;"><a href="video.php">動画</a></p>
                <p><a href="tags.php">タグ</a></p>
                <p><a href="shooting.php">撮影日時</a></p>
                <p><a href="vision.php">AI振り分け</a></p>
                <p><a href="garbage.php">ゴミ箱</a></p>
            </div>
            <div class="main">
                <?php if (empty($values)): ?>
                    <p>写真がありません。</p>
                <?php else: ?>
                    <?php foreach ($values as $value) {
                        $value['extension'] = strtolower(pathinfo($value['file_path'], PATHINFO_EXTENSION));
                    ?>
                        <?php if (in_array($value['extension'], ['jpg', 'png', 'gif', 'bmp', 'svg', 'jpeg', 'heic', 'heif', 'webp'])): ?>
                            <div class="image-container">
                                <div>
                                    <img src="<?= $value["file_path"]; ?>" alt="投稿写真">
                                    <p class="image-tag"><?= $value["tag"]; ?></p>
                                    <p class="image-shooting_at"><?= $value["shooting_at"]; ?></p>
                                    <form action="updatefavorite.php" method="post">
                                        <input type="hidden" value="<?= $value["id"]; ?>" name="id">
                                        <button class="like-button" type="submit">
                                            <i class="fa-regular fa-star"></i>
                                        </button>
                                    </form>
                                    <form action="filedetail.php" method="post">
                                        <input type="hidden" value="<?= $value["id"]; ?>" name="id">
                                        <button type="submit">詳細を見る</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php } ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>