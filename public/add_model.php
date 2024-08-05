<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Verifique se o usuário está autenticado e autorizado a acessar esta funcionalidade
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $cover_url = $_POST['cover_url'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $description = $_POST['description'] ?? '';
    $pack_url = $_POST['pack_url'] ?? '';
    $photos_count = $_POST['photos_count'] ?? 0; // Novo campo para quantidade de fotos
    $videos_count = $_POST['videos_count'] ?? 0; // Novo campo para quantidade de vídeos
    $preview_urls = $_POST['preview_urls'] ?? []; // Array de URLs de prévias

    if ($name && $price && $cover_url && !empty($preview_urls)) { // Verifica se os campos obrigatórios estão preenchidos
        try {
            $pdo->beginTransaction(); // Inicia a transação para garantir a integridade dos dados

            // Inserir o modelo no banco de dados
            $stmt = $pdo->prepare('INSERT INTO models (name, price, cover_url, tags, description, pack_url, photos_count, videos_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $price, $cover_url, $tags, $description, $pack_url, $photos_count, $videos_count]);

            $model_id = $pdo->lastInsertId(); // ID do modelo recém inserido

            // Inserir as prévias no banco de dados
            $stmtPreview = $pdo->prepare('INSERT INTO previews (model_id, preview_url) VALUES (?, ?)');
            foreach ($preview_urls as $preview_url) {
                $stmtPreview->execute([$model_id, $preview_url]);
            }

            $pdo->commit(); // Confirma a transação

            // Redireciona de volta para o painel administrativo após adicionar o modelo
            header('Location: admin.php');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack(); // Reverte a transação em caso de erro
            echo 'Erro ao adicionar modelo: ' . $e->getMessage();
        }
    } else {
        echo 'Preencha todos os campos obrigatórios.';
    }
} else {
    header('Location: admin.php');
    exit;
}
?>
