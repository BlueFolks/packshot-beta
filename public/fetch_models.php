<?php
// public/fetch_models.php

require_once __DIR__ . '/../config/database.php';

$query = "SELECT * FROM models";
$params = [];
$conditions = [];

// Implementação da Paginação
$limit = 4; // Limite de modelos por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if (isset($_GET['tag'])) {
    $tag = '%' . $_GET['tag'] . '%';
    $conditions[] = "tags LIKE ?";
    $params[] = $tag;
}

if ($conditions) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

if (isset($_GET['order'])) {
    switch ($_GET['order']) {
        case 'alphabetical':
            $query .= " ORDER BY name ASC";
            break;
        case 'popularity':
            $query = "SELECT models.*, COUNT(transactions.id) as popularity 
                      FROM models 
                      LEFT JOIN transactions ON models.id = transactions.model_id 
                      GROUP BY models.id 
                      ORDER BY popularity DESC";
            break;
        case 'recent':
            $query .= " ORDER BY created_at DESC";
            break;
    }
}

$query .= " LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute($params);
$models = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($models);
