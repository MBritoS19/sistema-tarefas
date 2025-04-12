<?php
require 'config.php';
verificarLogin();

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
        $stmt->bind_param(
            "ssii",
            $_POST['titulo'],
            $_POST['descricao'],
            $_POST['concluida'],
            $id
        );
        $stmt->execute();
    }
} else {
    if (isset($_GET['delete'])) {
        $id = (int)$_GET['delete'];
        $check = $conn->query("SELECT id FROM tarefas WHERE id = $id AND idUsuario = {$_SESSION['usuario_id']}");
        if ($check->num_rows > 0) {
            $conn->query("DELETE FROM tarefas WHERE id = $id");
        }
        header("Location: index.php");
        exit;
    } elseif (isset($_GET['complete'])) {
        $id = (int)$_GET['complete'];
    
        $stmt = $conn->prepare("SELECT id, concluida FROM tarefas WHERE id = ? AND idUsuario = ?");
        $stmt->bind_param("ii", $id, $_SESSION['usuario_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $tarefa = $result->fetch_assoc();
            $novo_status = $tarefa['concluida'] ? 0 : 1;
            
            // Atualizar status
            $stmt_update = $conn->prepare("UPDATE tarefas SET concluida = ? WHERE id = ?");
            $stmt_update->bind_param("ii", $novo_status, $id);
            $stmt_update->execute();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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
        <!-- Código da Navbar -->
        <nav class="navbar navbar-light bg-light mb-4 shadow-sm">
            <div class="container">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <span class="navbar-brand text-primary fw-bold">
                        <i class="bi bi-list-task me-2"></i>
                        <?= $_SESSION['usuario_nome'] ?>
                    </span>

                    <div class="d-flex gap-3">
                        <a href="gerenciar_usuarios.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-person-plus me-1"></i>
                            <span class="d-none d-md-inline">Gerenciar Usuários</span>
                        </a>

                        <a href="logout.php" class="btn btn-danger btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            <span class="d-none d-md-inline">Sair</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <h2 class="mb-4 text-primary">
            <i class="bi bi-journal-plus"></i>
            Gerenciador de Tarefas
        </h2>

        <!-- Formulário de Adição -->
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                Adicionar Nova Tarefa
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <input type="text" name="titulo" class="form-control"
                                placeholder="Digite o título da tarefa" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="add" class="btn btn-success w-100">
                                <i class="bi bi-save"></i> Salvar
                            </button>
                        </div>
                        <div class="col-12">
                            <textarea name="descricao" class="form-control"
                                placeholder="Descrição detalhada..." rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Tarefas -->
        <div class="card shadow">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-task"></i>
                    Tarefas Registradas
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Título</th>
                                <th class="d-none d-md-table-cell">Descrição</th>
                                <th>Criado Por</th>
                                <th>Status</th>
                                <th class="d-none d-sm-table-cell">Criação</th>
                                <th class="pe-4 text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($tarefa = $tarefas->fetch_assoc()): ?>
                                <tr class="<?= $tarefa['concluida'] ? 'table-success' : '' ?>">
                                    <td class="ps-4 text-truncate" title="<?= htmlspecialchars($tarefa['titulo']) ?>">
                                        <?= htmlspecialchars($tarefa['titulo']) ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="text-muted small">
                                            <?= htmlspecialchars($tarefa['descricao']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= htmlspecialchars($tarefa['criador']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" role="switch"
                                                <?= $tarefa['concluida'] ? 'checked' : '' ?>
                                                onclick="window.location.href='?complete=<?= $tarefa['id'] ?>'">
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($tarefa['data_criacao'])) ?>
                                        </small>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="edit.php?id=<?= $tarefa['id'] ?>"
                                                class="btn btn-sm btn-light border"
                                                title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?= $tarefa['id'] ?>"
                                                class="btn btn-sm btn-danger"
                                                title="Excluir"
                                                onclick="return confirm('Tem certeza que deseja excluir permanentemente?')">
                                                <i class="bi bi-trash"></i>
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