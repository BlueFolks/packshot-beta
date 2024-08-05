<?php
session_start();
require_once __DIR__ . '/../src/Controller/AdminController.php';

use Controller\AdminController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=packshot', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $adminController = new AdminController($pdo);

        if (isset($_POST['logoUrl'])) {
            $logoUrl = $_POST['logoUrl'];
            $adminController->updateLogo($logoUrl); // Corrigido para updateLogo
        }        

        header('Location: admin.php');
        exit;
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
}
?>
