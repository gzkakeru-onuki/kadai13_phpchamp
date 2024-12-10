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
$sql = "SELECT DISTINCT face_emotion 
        FROM photos 
        WHERE user_id = :user_id 
        AND face_emotion IS NOT NULL 
        AND face_emotion != '' 
        AND deleteFlg = 0
        ORDER BY face_emotion";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$emotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ユニークなlandmarkの取得
$sql_landmarks = "SELECT DISTINCT landmark FROM photos WHERE landmark IS NOT NULL AND landmark != '' AND deleteFlg = 0 AND user_id = ? ORDER BY landmark";
$stmt_landmarks = $pdo->prepare($sql_landmarks);
$stmt_landmarks->execute([$user_id]);
$landmarks = $stmt_landmarks->fetchAll(PDO::FETCH_ASSOC);

// face_emotionごとの写真を取得
$photos_by_emotion = [];
foreach ($emotions as $emotion) {
    $sql_photos = "SELECT *, 
                   LOWER(SUBSTRING_INDEX(file_path, '.', -1)) as extension 
                   FROM photos 
                   WHERE face_emotion = ?
                   AND deleteFlg = 0
                   AND user_id = ?
                   ORDER BY created_at DESC";
    $stmt_photos = $pdo->prepare($sql_photos);
    $stmt_photos->execute([$emotion['face_emotion'], $user_id]);
    $photos_by_emotion[$emotion['face_emotion']] = $stmt_photos->fetchAll(PDO::FETCH_ASSOC);
}

// landmarkごとの写真を取得
$photos_by_landmark = [];
foreach ($landmarks as $landmark) {
    $sql_photos = "SELECT *, 
                   LOWER(SUBSTRING_INDEX(file_path, '.', -1)) as extension 
                   FROM photos 
                   WHERE landmark = ?
                   AND deleteFlg = 0
                   AND user_id = ?
                   ORDER BY created_at DESC";
    $stmt_photos = $pdo->prepare($sql_photos);
    $stmt_photos->execute([$landmark['landmark'], $user_id]);
    $photos_by_landmark[$landmark['landmark']] = $stmt_photos->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/vision.css">
    <title>photoshare - AI振り分け</title>
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

        <div class="emotion-container">
            <h2>感情別写真一覧</h2>
            <?php if (empty($photos_by_emotion)): ?>
                <p>感情が検出された写真はありません。</p>
            <?php else: ?>
                <?php foreach ($photos_by_emotion as $emotion => $photos): ?>
                    <div class="emotion-section">
                        <h3 class="emotion-title"><?= htmlspecialchars($emotion) ?></h3>
                        <div class="photo-grid">
                            <?php foreach ($photos as $photo):
                                $extension = strtolower($photo['extension']);
                                $isImage = in_array($extension, ['jpg', 'png', 'gif', 'bmp', 'svg', 'jpeg', 'heic', 'heif', 'webp']);
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

        <div class="landmark-container">
            <h2>ランドマーク別写真一覧</h2>
            <?php if (empty($photos_by_landmark)): ?>
                <p>ランドマークが検出された写真はありません。</p>
            <?php else: ?>
                <?php foreach ($photos_by_landmark as $landmark => $photos): ?>
                    <div class="landmark-section">
                        <h3 class="landmark-title"><?= htmlspecialchars($landmark) ?></h3>
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