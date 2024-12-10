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

$user_id = $_SESSION["id"];
$sql = "SELECT DISTINCT tag 
        FROM photos 
        WHERE user_id = :user_id 
        AND tag IS NOT NULL 
        AND tag != '' 
        AND deleteFlg = 0
        ORDER BY tag";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

// タグごとの写真を取得
$photos_by_tag = [];
foreach ($tags as $tag) {
    $sql_photos = "SELECT *,
                   LOWER(SUBSTRING_INDEX(file_path, '.', -1)) as extension 
                   FROM photos 
                   WHERE tag = ?
                   AND deleteFlg = 0
                   AND user_id = ?
                   ORDER BY created_at DESC";
    $stmt_photos = $pdo->prepare($sql_photos);
    $stmt_photos->execute([$tag['tag'], $user_id]);
    $photos_by_tag[$tag['tag']] = $stmt_photos->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/tags.css">
    <title>photoshare - タグ別写真一覧</title>
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
        
        <div class="tag-container">
            <?php if (empty($photos_by_tag)): ?>
                <p>タグが設定された写真はありません。</p>
            <?php else: ?>
                <?php foreach ($photos_by_tag as $tag => $photos): ?>
                    <div class="tag-section">
                        <h2 class="tag-title"><?= htmlspecialchars($tag) ?></h2>
                        <div class="photo-grid">
                            <?php foreach ($photos as $photo): 
                            
                                $extension = strtolower($photo['extension']);
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg']);
                            ?>
                                <?php if ($isImage): ?>
                                    <div class="image-container">
                                        <img src="<?= htmlspecialchars($photo["file_path"]) ?>" alt="写真">
                                        <p class="image-shooting_at"><?= htmlspecialchars($photo["shooting_at"]) ?></p>
                                        <form action="updatefavorite.php" method="post">
                                            <input type="hidden" value="<?= $photo["id"]; ?>" name="id">
                                            <button class="like-button" type="submit">
                                                <i class="fa-regular fa-star"></i>
                                            </button>
                                        </form>
                                        <form action="filedetail.php" method="post">
                                            <input type="hidden" value="<?= $photo["id"] ?>" name="id">
                                            <button type="submit">詳細を見る</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
