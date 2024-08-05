<?php

namespace Controller;

class ModelController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para obter modelos com paginação e ordem
    public function getModelsWithPagination($limit, $offset, $order = 'alphabetical', $tagFilter = '') {
        $orderBy = 'name';
        if ($order == 'popularity') {
            $orderBy = 'popularity DESC';
        } elseif ($order == 'recent') {
            $orderBy = 'created_at DESC';
        }

        $query = "SELECT * FROM models";
        if (!empty($tagFilter)) {
            $query .= " WHERE FIND_IN_SET(:tagFilter, tags)";
        }
        $query .= " ORDER BY $orderBy LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        if (!empty($tagFilter)) {
            $stmt->bindValue(':tagFilter', $tagFilter);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Método para obter todos os modelos (sem paginação)
    public function getModels() {
        $stmt = $this->pdo->query('SELECT * FROM models ORDER BY name');
        return $stmt->fetchAll();
    }

    public function searchModels($term) {
        $stmt = $this->pdo->prepare('SELECT * FROM models WHERE name LIKE :term');
        $stmt->execute(['term' => "%$term%"]);
        return $stmt->fetchAll();
    }

    public function getBannerUrls() {
        $stmt = $this->pdo->query('SELECT * FROM banners');
        return $stmt->fetchAll();
    }

    public function getLogoUrl() {
        $stmt = $this->pdo->query('SELECT * FROM settings WHERE name="logo_url"');
        $row = $stmt->fetch();
        return $row['value'] ?? '';
    }

    public function addModel($data) {
        $stmt = $this->pdo->prepare('INSERT INTO models (name, price, cover_url, preview_url, tags, pack_url) VALUES (:name, :price, :cover_url, :preview_url, :tags, :pack_url)');
        $stmt->execute($data);
    }

    public function editModel($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE models SET name = :name, price = :price, cover_url = :cover_url, preview_url = :preview_url, tags = :tags, pack_url = :pack_url WHERE id = :id');
        $stmt->execute(array_merge($data, ['id' => $id]));
    }

    public function deleteModel($id) {
        $stmt = $this->pdo->prepare('DELETE FROM models WHERE id = :id');
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    // Método para obter o número total de modelos
    public function getTotalModels($tagFilter = '') {
        $query = 'SELECT COUNT(*) FROM models';
        if (!empty($tagFilter)) {
            $query .= " WHERE FIND_IN_SET(:tagFilter, tags)";
        }
        $stmt = $this->pdo->prepare($query);
        if (!empty($tagFilter)) {
            $stmt->bindValue(':tagFilter', $tagFilter);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Método para obter tags distintas (separadas por vírgulas)
    public function getDistinctTags() {
        $stmt = $this->pdo->query('SELECT GROUP_CONCAT(DISTINCT tags) AS tags FROM models');
        $row = $stmt->fetch();
        $tags = explode(',', $row['tags']);
        return array_unique(array_map('trim', $tags));
    }

    // Método para obter prévias
    public function getPreviews($modelId) {
        $stmt = $this->pdo->prepare('SELECT * FROM previews WHERE model_id = :model_id');
        $stmt->execute(['model_id' => $modelId]);
        return $stmt->fetchAll();
    }
}
