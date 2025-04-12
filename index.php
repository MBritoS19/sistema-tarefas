<?php
require 'config.php';

// Operações CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO tarefas (titulo, descricao, idUsuario) 
                               VALUES (?, ?, ?)");
        $stmt->bind_param(
            "ssi",
            $_POST['titulo'],
            $_POST['descricao'],
            $usuario_id,
        );
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE tarefas SET titulo=?, descricao=?, concluida=? WHERE id=?");
        $stmt->bind_param("ssii",
            $_POST['titulo'],
            $_POST['descricao'],
            $_POST['concluida'],
            $id
        );
        $stmt->execute();
    }
} else {
    // Operações GET (delete e complete)
    if (isset($_GET['delete'])) {
        $id = (int)$_GET['delete'];
        // Verificar se a tarefa pertence ao usuário
        $check = $conn->query("SELECT id FROM tarefas WHERE id = $id AND idUsuario = {$_SESSION['usuario_id']}");
        if ($check->num_rows > 0) {
            $conn->query("DELETE FROM tarefas WHERE id = $id");
        }
        header("Location: index.php");
        exit;
    }
    elseif (isset($_GET['complete'])) {
        $id = (int)$_GET['complete'];
        // Verificar se a tarefa pertence ao usuário
        $check = $conn->query("SELECT id FROM tarefas WHERE id = $id AND idUsuario = {$_SESSION['usuario_id']}");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE tarefas SET concluida = 1 WHERE id = $id");
        }
        header("Location: index.php");
        exit;
    }
}

// Buscar todas as tarefas
$tarefas = $conn->query("
    SELECT t.*, u.nome as criador 
    FROM tarefas t
    INNER JOIN usuarios u ON t.idUsuario = u.id
    WHERE t.idUsuario = {$_SESSION['usuario_id']}
    ORDER BY t.data_criacao DESC
");

if (!$tarefas) {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Tarefas</title>
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
    <div class="container mt-5">
        <nav class="navbar navbar-light bg-light mb-4">
            <div class="container">
                <span class="navbar-brand">Olá, <?= $_SESSION['usuario_nome'] ?></span>
                <a href="logout.php" class="btn btn-danger">Sair</a>
            </div>
        </nav>

        <h1 class="mb-4">Gerenciador de Tarefas</h1>

        <!-- Formulário de Adição -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="titulo" class="form-control" placeholder="Título" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="descricao" class="form-control" placeholder="Descrição" rows="2"></textarea>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">Adicionar Tarefa</button>
                </form>
            </div>
        </div>

        <!-- Lista de Tarefas -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Lista de Tarefas</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="min-width: 120px;">Título</th>
                                <th class="d-none d-md-table-cell">Descrição</th>
                                <th style="min-width: 110px;">Criado Por</th>
                                <th style="min-width: 110px;">Status</th>
                                <th class="d-none d-sm-table-cell">Data de criação</th>
                                <th style="min-width: 140px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($tarefa = $tarefas->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-truncate" style="max-width: 150px;"><?= htmlspecialchars($tarefa['titulo']) ?></td>
                                    <td class="d-none d-md-table-cell"><?= htmlspecialchars($tarefa['descricao']) ?></td>
                                    <td><?= htmlspecialchars($tarefa['criador']) ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                <?= $tarefa['concluida'] ? 'checked' : '' ?> disabled>
                                            <span class="ms-2 d-inline d-sm-none"><?= $tarefa['concluida'] ? '✓' : '✗' ?></span>
                                            <span class="ms-2 d-none d-sm-inline"><?= $tarefa['concluida'] ? 'Concluída' : 'Pendente' ?></span>
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell"><?= date('d/m/Y H:i', strtotime($tarefa['data_criacao'])) ?></td>
                                    <td>
                                        <div class="d-flex flex-column flex-sm-row gap-1">
                                            <?php if (!$tarefa['concluida']): ?>
                                                <a href="?complete=<?= $tarefa['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Marcar como concluída?')">
                                                    <span class="d-none d-sm-inline">Finalizar</span>
                                                    <span class="d-inline d-sm-none">✓</span>
                                                </a>
                                            <?php endif; ?>
                                            <a href="edit.php?id=<?= $tarefa['id'] ?>" class="btn btn-sm btn-warning">
                                                <span class="d-none d-sm-inline">Editar</span>
                                                <span class="d-inline d-sm-none">✎</span>
                                            </a>
                                            <a href="?delete=<?= $tarefa['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">
                                                <span class="d-none d-sm-inline">Excluir</span>
                                                <span class="d-inline d-sm-none">🗑</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>