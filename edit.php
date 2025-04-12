<?php
require 'config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$tarefa = $conn->query("SELECT * FROM tarefas WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $concluida = isset($_POST['concluida']) ? 1 : 0;

    $sql = "UPDATE tarefas SET titulo='$titulo', descricao='$descricao', concluida=$concluida WHERE id=$id";
    $conn->query($sql);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarefa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media (max-width: 576px) {

            .table th,
            .table td {
                padding: 0.5rem;
                font-size: 0.9rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }

            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        .text-truncate {
            max-width: 40vw;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <h1 class="h4 mb-4">Editar Tarefa</h1>

                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="titulo" class="form-control form-control-lg"
                            value="<?= htmlspecialchars($tarefa['titulo']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="descricao" class="form-control" rows="3"
                            placeholder="Descrição detalhada"><?= htmlspecialchars($tarefa['descricao']) ?></textarea>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" name="concluida" class="form-check-input"
                            id="concluida" <?= $tarefa['concluida'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="concluida">Concluída</label>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" name="update" class="btn btn-primary btn-lg">Atualizar</button>
                        <a href="index.php" class="btn btn-secondary btn-lg">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>