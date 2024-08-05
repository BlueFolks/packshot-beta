<?php
// public/preview.php

require_once __DIR__ . '/../config/database.php';

$modelId = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM packs WHERE model_id = ?");
$stmt->execute([$modelId]);
$packs = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($packs as $pack) {
    echo '<div class="preview-item">';
    echo '<img src="images/' . $pack['preview_image'] . '" alt="Prévia">';
    echo '<p>' . $pack['num_photos'] . ' Fotos e ' . $pack['num_videos'] . ' Vídeos</p>';
    echo '</div>';
}
?>

<button class="close-popup">Fechar</button>
