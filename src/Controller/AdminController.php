<?php

namespace Controller;

class AdminController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getModels() {
        $stmt = $this->pdo->query('SELECT * FROM models');
        return $stmt->fetchAll();
    }

    public function getBanners() {
        $stmt = $this->pdo->query('SELECT * FROM banners');
        return $stmt->fetchAll();
    }

    public function getLogoUrl() {
        $stmt = $this->pdo->query('SELECT * FROM settings WHERE name="logo_url"');
        $row = $stmt->fetch();
        return $row['value'] ?? '';
    }

    public function updateLogo($url) {
        $stmt = $this->pdo->prepare('UPDATE settings SET value=:value WHERE name="logo_url"');
        $stmt->execute(['value' => $url]);
    }

    public function updateBanners($urls) {
        $this->pdo->exec('TRUNCATE TABLE banners');
        $stmt = $this->pdo->prepare('INSERT INTO banners (url) VALUES (:url)');
        foreach ($urls as $url) {
            $stmt->execute(['url' => $url]);
        }
    }
}

?>
