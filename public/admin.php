<?php
session_start();
require_once __DIR__ . '/../src/Controller/AdminController.php';
require_once __DIR__ . '/../src/Controller/ModelController.php';

use Controller\AdminController;
use Controller\ModelController;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=packshot', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $adminController = new AdminController($pdo);
    $modelController = new ModelController($pdo);
    $models = $modelController->getModels(); // Corrigindo para usar o método correto
    $banners = $adminController->getBanners();
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
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
    <title>Painel Administrativo</title>
    <style>
        .preview-url-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .remove-preview {
            cursor: pointer;
            margin-left: 10px;
            color: red;
            font-size: 18px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Painel Administrativo</h1>
        <a href="logout.php" class="btn btn-danger">Sair</a>
    </div>

    <ul class="nav nav-tabs" id="adminTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="true">Configurações do Site</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="banners-tab" data-toggle="tab" href="#banners" role="tab" aria-controls="banners" aria-selected="false">Gerenciar Banners</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="models-tab" data-toggle="tab" href="#models" role="tab" aria-controls="models" aria-selected="false">Modelos</a>
        </li>
    </ul>

    <div class="tab-content mt-4" id="adminTabContent">
        <div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="settings-tab">
            <form action="update_settings.php" method="post">
                <div class="form-group">
                    <label for="logoUrl">URL da Logo</label>
                    <input type="text" name="logoUrl" class="form-control" value="<?php echo $adminController->getLogoUrl(); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Atualizar Logo</button>
            </form>
        </div>

        <div class="tab-pane fade" id="banners" role="tabpanel" aria-labelledby="banners-tab">
            <form action="update_banners.php" method="post">
                <div class="form-group">
                    <label for="banners">URLs das Imagens de Banner (separadas por vírgula)</label>
                    <textarea name="banners" class="form-control"><?php echo implode(',', array_column($banners, 'url')); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Atualizar Banners</button>
            </form>
        </div>

        <div class="tab-pane fade" id="models" role="tabpanel" aria-labelledby="models-tab">
            <h2 class="mt-3">Adicionar Novo Modelo</h2>
            <form action="add_model.php" method="post" id="addModelForm">
                <div class="form-row">
                    <div class="col">
                        <input type="text" name="name" class="form-control" placeholder="Nome do Modelo" required>
                    </div>
                    <div class="col">
                        <input type="text" name="price" class="form-control" placeholder="Preço" required>
                    </div>
                    <div class="col">
                        <input type="text" name="cover_url" class="form-control" placeholder="URL da Imagem de Capa" required>
                    </div>
                    <div class="col">
                        <input type="text" name="tags" class="form-control" placeholder="Tags (separadas por vírgula)">
                    </div>
                    <div class="col">
                        <input type="text" name="pack_url" class="form-control" placeholder="URL do Pack (Link de Redirecionamento)">
                    </div>
                </div>
                <div class="form-row mt-3">
                    <div class="col">
                        <input type="number" name="photos_count" class="form-control" placeholder="Quantidade de Fotos" required>
                    </div>
                    <div class="col">
                        <input type="number" name="videos_count" class="form-control" placeholder="Quantidade de Vídeos" required>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label>Prévias</label>
                    <div id="previewUrls">
                        <div class="preview-url-group">
                            <input type="text" name="preview_urls[]" class="form-control" placeholder="URL da Imagem de Prévia">
                            <span class="remove-preview">&times;</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" id="addPreviewUrl">Adicionar Prévia</button>
                </div>
                <button type="submit" class="btn btn-success mt-3">Adicionar Modelo</button>
            </form>

            <h2 class="mt-4">Modelos Existentes</h2>
            <div class="row">
                <?php foreach ($models as $model): ?>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <img class="card-img-top" src="<?php echo $model['cover_url']; ?>" alt="<?php echo $model['name']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $model['name']; ?></h5>
                                <button class="btn btn-primary edit-btn" data-id="<?php echo $model['id']; ?>">Editar</button>
                                <button class="btn btn-danger delete-btn" data-id="<?php echo $model['id']; ?>">Excluir</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('.delete-btn').click(function() {
            const modelId = $(this).data('id');
            if (confirm('Tem certeza que deseja excluir este modelo?')) {
                $.ajax({
                    url: 'delete_model.php',
                    method: 'POST',
                    data: { id: modelId },
                    success: function(response) {
                        alert(response);
                        location.reload(); // Recarrega a página para refletir as mudanças
                    },
                    error: function() {
                        alert('Erro ao excluir o modelo. Tente novamente.');
                    }
                });
            }
        });

        $('.edit-btn').click(function() {
            const modelId = $(this).data('id');
            window.location.href = `edit_model.php?id=${modelId}`;
        });
    });
</script>
</body>
</html>
