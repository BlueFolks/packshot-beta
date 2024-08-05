<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Verifique se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        try {
            // Prepare a declaração para selecionar o usuário pelo email
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifique se o usuário existe e a senha está correta
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['id']; // Salve o ID do usuário na sessão
                header('Location: index.php'); // Redirecione para a página inicial
                exit;
            } else {
                echo 'Email ou senha incorretos.';
            }
        } catch (PDOException $e) {
            echo 'Erro ao verificar login: ' . $e->getMessage();
        }
    } else {
        echo 'Por favor, preencha todos os campos.';
    }
} else {
    header('Location: index.php');
    exit;
}
?>
