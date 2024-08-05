<?php
// public/my_packs.php

session_start();
require_once __DIR__ . '/../config/database.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Recupera packs comprados pelo usuário
$stmt = $pdo->prepare("
    SELECT packs.*, models.name AS model_name 
    FROM transactions 
    JOIN packs ON transactions.pack_id = packs.id 
    JOIN models ON packs.model_id = models.id 
    WHERE transactions.user_id = ? AND transactions.status = 'completed'
");
$stmt->execute([$userId]);
$purchasedPacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meus Packs</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>Meus Packs</h1>
        <div class="user-options">
            <a href="logout.php">Sair</a>
        </div>
    </div>

    <div class="model-list">
        <?php foreach ($purchasedPacks as $pack): ?>
            <div class="model-item">
                <img src="<?php echo $pack['preview_image']; ?>" alt="<?php echo $pack['model_name']; ?>">
                <h2><?php echo $pack['model_name']; ?></h2>
                <p><?php echo $pack['num_photos']; ?> Fotos e <?php echo $pack['num_videos']; ?> Vídeos</p>
                <button onclick="window.location.href='<?php echo $pack['pack_url']; ?>'">Ver Pack</button>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
