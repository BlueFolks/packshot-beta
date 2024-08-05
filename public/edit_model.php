<?php
// public/edit_model.php

session_start();
require_once __DIR__ . '/../src/Controller/ModelController.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM models WHERE id = ?");
$stmt->execute([$id]);
$model = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $coverImage = $_POST['cover_image'];
    $tags = $_POST['tags'];

    $stmt = $pdo->prepare("UPDATE models SET name = ?, price = ?, cover_image = ?, tags = ? WHERE id = ?");
    $stmt->execute([$name, $price, $coverImage, $tags, $id]);

    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Modelo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>Editar Modelo</h1>
        <div class="user-options">
            <a href="logout.php">Sair</a>
        </div>
    </div>

    <div class="form-container">
        <form action="edit_model.php?id=<?php echo $model['id']; ?>" method="post">
            <input type="text" name="name" value="<?php echo $model['name']; ?>" required>
            <input type="text" name="price" value="<?php echo $model['price']; ?>" required>
            <input type="text" name="cover_image" value="<?php echo $model['cover_image']; ?>" required>
            <input type="text" name="tags" value="<?php echo $model['tags']; ?>">
            <button type="submit">Atualizar</button>
        </form>
    </div>
</body>
</html>
