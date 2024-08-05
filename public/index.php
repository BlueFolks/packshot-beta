<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Controller/ModelController.php';

use Controller\ModelController;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=packshot', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $modelController = new ModelController($pdo);
    $order = $_POST['order'] ?? 'alphabetical'; // Default to alphabetical order
    $tagFilter = $_POST['tag'] ?? '';

    // Get models with pagination and filter
    $limit = 4;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch models based on current filter and order
    $models = $modelController->getModelsWithPagination($limit, $offset, $order, $tagFilter);

    $totalModels = $modelController->getTotalModels($tagFilter);
    $totalPages = ceil($totalModels / $limit);

    $tags = $modelController->getDistinctTags();
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    $models = []; // Ensure $models is always defined
    $tags = [];
    $totalPages = 1;
    $page = 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Peta:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <title>Packs de Modelos</title>
</head>
<body>

<div class="header">
    <img src="<?php echo $modelController->getLogoUrl(); ?>" class="logo" alt="Logo">
    <div class="user-options">
        <?php if (isset($_SESSION['user'])): ?>
            <button id="profileBtn" class="popup-btn">Meus Packs</button>
            <div id="userOptions" class="user-options-popup">
                <a href="#">Perfil</a>
                <a href="#">Compras</a>
                <a href="#">Suporte</a>
                <a href="logout.php">Sair</a>
            </div>
        <?php else: ?>
            <button class="popup-btn" id="registerBtn">Cadastro</button>
            <button class="popup-btn" id="loginBtn">Entrar</button>
        <?php endif; ?>
    </div>
</div>

<div class="banner">
    <div class="banner-images">
        <?php foreach ($modelController->getBannerUrls() as $banner): ?>
            <a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['url']; ?>" alt="Banner"></a>
        <?php endforeach; ?>
    </div>
</div>

<div class="search-bar">
    <form action="index.php" method="post" class="d-flex justify-content-center align-items-center">
        <input type="text" name="search" placeholder="Buscar modelo" class="form-control">
        <button type="submit" class="btn">
            <i class="fa fa-search"></i>
        </button>
    </form>
    <button type="button" class="filter-btn" id="filterBtn">Filtros</button>
</div>

<div class="filter-tags" style="display: none;">
    <label for="filterTags">Filtrar por Tags:</label>
    <select id="filterTags" name="tag" class="form-control" form="filterForm">
        <option value="">Todas</option>
        <?php foreach ($tags as $tag): ?>
            <option value="<?php echo $tag; ?>" <?php echo ($tag == $tagFilter) ? 'selected' : ''; ?>><?php echo $tag; ?></option>
        <?php endforeach; ?>
    </select>
</div>

<form id="filterForm" method="post" style="display:none;">
    <input type="hidden" name="order" value="<?php echo $order; ?>">
</form>

<div class="order-options">
    <p>Ordem de Exibição:</p>
    <div class="order-btns">
        <button class="order-btn <?php echo ($order == 'alphabetical') ? 'active' : ''; ?>" data-order="alphabetical">Alfabética</button>
        <button class="order-btn <?php echo ($order == 'popularity') ? 'active' : ''; ?>" data-order="popularity">Popularidade</button>
        <button class="order-btn <?php echo ($order == 'recent') ? 'active' : ''; ?>" data-order="recent">Recentes</button>
    </div>
</div>

<div class="model-list">
    <?php foreach ($models as $model): ?>
        <div class="model-item" data-id="<?php echo $model['id']; ?>">
            <h2><?php echo $model['name']; ?></h2>
            <a href="#" class="preview-link">
                <img src="<?php echo $model['cover_url']; ?>" alt="<?php echo $model['name']; ?>">
                <p class="price-tag">R$<?php echo number_format($model['price'], 2, ',', '.'); ?></p>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="window.location.href='index.php?page=<?php echo $i; ?>'"><?php echo $i; ?></button>
    <?php endfor; ?>
</div>

<div id="previewPopup" class="popup">
    <div class="popup-content">
        <h2>Prévias</h2>
        <div class="preview-slider"></div>
        <button class="btn btn-primary">Comprar</button>
        <span class="close-popup">Fechar</span>
    </div>
</div>

<div id="registerPopup" class="popup">
    <div class="popup-content">
        <h2>Cadastro</h2>
        <form action="register.php" method="post">
            <input type="email" name="email" placeholder="Seu Email" required class="form-control mb-3">
            <input type="password" name="password" placeholder="Sua Senha" required class="form-control mb-3">
            <input type="text" name="whatsapp" placeholder="WhatsApp (Opcional)" class="form-control mb-3">
            <button type="submit" class="btn btn-primary">Criar Conta</button>
        </form>
        <span class="close-popup">Fechar</span>
    </div>
</div>

<div id="loginPopup" class="popup">
    <div class="popup-content">
        <h2>Entrar</h2>
        <form action="login.php" method="post">
            <input type="email" name="email" placeholder="Seu Email" required class="form-control mb-3">
            <input type="password" name="password" placeholder="Sua Senha" required class="form-control mb-3">
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
        <a href="#" class="d-block mt-2">Esqueci minha senha</a>
        <span class="close-popup">Fechar</span>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<script src="js/script.js"></script>
<script>
    $(document).ready(function() {
        $('#filterBtn').click(function() {
            $('.filter-tags').toggle();
        });

        $('.order-btn').click(function() {
            var order = $(this).data('order');
            $('<form>', {
                "html": '<input type="hidden" name="order" value="' + order + '">',
                "action": "index.php",
                "method": "post"
            }).appendTo(document.body).submit();
        });

        $('.preview-link').click(function(event) {
            event.preventDefault();
            var modelId = $(this).closest('.model-item').data('id');
            $.get('fetch_preview.php', { id: modelId }, function(data) {
                var response = JSON.parse(data);
                if (!response.error) {
                    var previewsHtml = response.previews.map(function(preview) {
                        return '<div><img src="' + preview.preview_url + '" alt="Prévia"></div>';
                    }).join('');
                    $('.preview-slider').html(previewsHtml).slick();
                    $('#previewPopup').fadeIn();
                }
            });
        });

        $('.close-popup').click(function() {
            $(this).closest('.popup').fadeOut();
        });
    });
</script>
</body>
</html>
