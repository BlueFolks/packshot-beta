<?php
// public/fetch_preview.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Controller/ModelController.php';

use Controller\ModelController;

$modelController = new ModelController($pdo);

if (isset($_GET['id'])) {
    $modelId = $_GET['id'];
    $previews = $modelController->getPreviews($modelId);

    // Obter contagem de fotos e vídeos
    $stmt = $pdo->prepare("SELECT photos_count, videos_count FROM models WHERE id = :id");
    $stmt->execute(['id' => $modelId]);
    $counts = $stmt->fetch();

    echo json_encode([
        'previews' => $previews,
        'photos_count' => $counts['photos_count'],
        'videos_count' => $counts['videos_count']
    ]);
} else {
    echo json_encode(['error' => 'Model ID not specified']);
}
?>
