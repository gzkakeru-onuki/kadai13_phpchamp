<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require __DIR__ . '/../vendor/autoload.php';
include("../funcs/functions.php");

// セッションチェック
if (!isset($_SESSION["name"])) {
    header("Location: loginForm.php");
    exit();
}

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Translate\V2\TranslateClient;

try {
    // Vision APIクライアントの初期化
    $imageAnnotator = new ImageAnnotatorClient([
        'credentials' => __DIR__ . '/key.json'
    ]);

    // 翻訳クライアントの初期化
    $translate = new TranslateClient([
        'keyFilePath' => __DIR__ . '/key.json'
    ]);



    // ファイルアップロードエラーチェック
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        header('Location: uploadimage.php?result=' . urlencode('ファイルのアップロードに失敗しました。'));
        exit;
    }

    // ファイルの拡張子を取得
    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    // 許可する拡張子の配列
    $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'heic', 'heif', 'webp'];
    $allowed_video_types = ['mp4', 'mov', 'avi', 'wmv'];
    $allowed_types = array_merge($allowed_image_types, $allowed_video_types);

    // ファイルタイプチェック
    if (!in_array($extension, $allowed_types)) {
        header('Location: uploadimage.php?result=' . urlencode('許可されていないファイル形式です。'));
        exit;
    }

    // ファイルサイズの制限（バイト単位）
    $max_image_size = 10 * 1024 * 1024;  // 10MB
    $max_video_size = 100 * 1024 * 1024; // 100MB

    // ファイルサイズチェック
    $file_size = $_FILES['image']['size'];
    if (in_array($extension, $allowed_image_types)) {
        if ($file_size > $max_image_size) {
            header('Location: uploadimage.php?result=' . urlencode('画像ファイルは10MB以下にしてください。'));
            exit;
        }
    } elseif (in_array($extension, $allowed_video_types)) {
        if ($file_size > $max_video_size) {
            header('Location: uploadimage.php?result=' . urlencode('動画ファイルは100MB以下にしてください。'));
            exit;
        }
    }

    // ファイルの存在チェック
    if (!file_exists($_FILES['image']['tmp_name'])) {
        header('Location: uploadimage.php?result=' . urlencode('ファイルが存在しません。'));
        exit;
    }

    // 画像ファイルの場合のみgetimagesizeチェックを行う
    if (in_array($extension, $allowed_image_types) && !getimagesize($_FILES['image']['tmp_name'])) {
        header('Location: uploadimage.php?result=' . urlencode('無効な画像ファイルです。'));
        exit;
    }

    // アップロードされたファイルを保存するディレクトリ
    $uploadDir = '../uploads/';
    $targetFile = $uploadDir . basename($_FILES['image']['name']);

    // ファイルを指定のディレクトリに移動
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        header('Location: uploadimage.php?result=' . urlencode('ファイルの保存に失敗しました。'));
        exit;
    }

    // POSTデータから変数に値を取得
    $shooting_at = $_POST['shooting_at'];
    $tag = $_POST['tag'];

    // 画像データを読み込む
    $imageData = file_get_contents($targetFile);
    
    // 変数の初期化
    $labelsString = null;
    $topFaceEmotion = null;
    $topLandmarkDescription = null;

    // 画像ファイルの場合のみVision APIで解析
    if (in_array($extension, $allowed_image_types)) {
        // ラベル検出
        $response = $imageAnnotator->labelDetection($imageData);
        $labels = $response->getLabelAnnotations();
        $labelsArray = iterator_to_array($labels);
        usort($labelsArray, function($a, $b) {
            return $b->getScore() <=> $a->getScore();
        });
        $topLabels = array_slice($labelsArray, 0, 3);
        $labelDescriptions = array_map(function($label) use ($translate) {
            $description = $label->getDescription();
            $translation = $translate->translate($description, ['target' => 'ja']);
            return $translation['text'];
        }, $topLabels);
        $labelsString = implode(',', $labelDescriptions);

        // 顔検出
        $response = $imageAnnotator->faceDetection($imageData);
        $faces = $response->getFaceAnnotations();
        if (!empty($faces)) {
            $facesArray = iterator_to_array($faces);
            if (!empty($facesArray)) {
                usort($facesArray, function($a, $b) {
                    return $b->getDetectionConfidence() <=> $a->getDetectionConfidence();
                });
                $topFace = $facesArray[0];

                // 顔の感情を取得
                $emotions = [
                    '喜び' => $topFace->getJoyLikelihood(),
                    '悲しみ' => $topFace->getSorrowLikelihood(),
                    '怒り' => $topFace->getAngerLikelihood(),
                    '驚き' => $topFace->getSurpriseLikelihood()
                ];
                arsort($emotions);
                $topFaceEmotion = key($emotions);
            }
        }

        // ランドマーク検出
        $response = $imageAnnotator->landmarkDetection($imageData);
        $landmarks = $response->getLandmarkAnnotations();
        if (!empty($landmarks)) {
            $landmarksArray = iterator_to_array($landmarks);
            if (!empty($landmarksArray)) {
                usort($landmarksArray, function($a, $b) {
                    return $b->getScore() <=> $a->getScore();
                });
                $landmarkDescription = $landmarksArray[0]->getDescription();
                $translation = $translate->translate($landmarkDescription, ['target' => 'ja']);
                $topLandmarkDescription = $translation['text'];
            }
        }
    }

    // データベース接続
    $pdo = db_conn();

    // データベースに保存
    $user_id = $_SESSION["id"];
    $sql = "INSERT INTO photos (file_path, shooting_at, tag, face_emotion, landmark, user_id) 
            VALUES (:file_path, :shooting_at, :tag, :face_emotion, :landmark, :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':file_path', $targetFile, PDO::PARAM_STR);
    $stmt->bindValue(':shooting_at', $shooting_at, PDO::PARAM_STR);
    $stmt->bindValue(':tag', $tag, PDO::PARAM_STR);
    $stmt->bindValue(':face_emotion', $topFaceEmotion, PDO::PARAM_STR);
    $stmt->bindValue(':landmark', $topLandmarkDescription, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status == false) {
        sql_error($stmt);
    } else {
        header('Location: main.php');
    }
    exit;

} catch (Exception $e) {
    header('Location: uploadimage.php?result=' . urlencode('エラー: ' . $e->getMessage()));
}
?>
