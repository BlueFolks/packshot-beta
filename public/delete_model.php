<?php
// public/delete_model.php

session_start();
require_once __DIR__ . '/../config/database.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    if (!empty($id)) {
        try {
            $pdo->beginTransaction();

            // Excluir packs relacionados ao modelo
            $stmt = $pdo->prepare("DELETE FROM packs WHERE model_id = ?");
            $stmt->execute([$id]);

            // Excluir o modelo
            $stmt = $pdo->prepare("DELETE FROM models WHERE id = ?");
            $stmt->execute([$id]);

            $pdo->commit();
            echo 'Modelo excluído com sucesso.';
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo 'Erro ao excluir o modelo: ' . $e->getMessage();
        }
    } else {
        echo 'ID inválido ou não fornecido.';
    }
} else {
    echo 'Método de requisição inválido.';
}
?>
