<?php
require 'config.php';
verificarLogin();

// Recebe os dados do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome' => $_POST['nome'],
        'email' => $_POST['email'],
        'cargo' => $_POST['cargo'],
        'setor' => $_POST['setor'],
        'ativo' => isset($_POST['ativo']) ? 1 : 0
    ];

    // Se a senha não estiver vazia, faz o hash da senha
    if (!empty($_POST['senha'])) {
        $dados['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    }

    // Se o ID estiver definido, atualiza o usuário existente
    // Caso contrário, insere um novo usuário
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE usuarios SET 
                nome = ?, email = ?, cargo = ?, setor = ?, ativo = ?"
                . (!empty($_POST['senha']) ? ", senha = ?" : "") . 
                " WHERE id = ?";
        
        $tipos = "ssssi" . (!empty($_POST['senha']) ? "s" : "") . "i";
        $valores = [
            $dados['nome'],
            $dados['email'],
            $dados['cargo'],
            $dados['setor'],
            $dados['ativo'],
            $id
        ];
        
        if (!empty($_POST['senha'])) {
            array_splice($valores, 5, 0, $dados['senha']);
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($tipos, ...$valores);
        $stmt->execute();

        header("Location: gerenciar_usuarios.php");
        exit;

    } else {

        $stmt = $conn->prepare("INSERT INTO usuarios 
                (nome, email, senha, cargo, setor, ativo) 
                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi",
            $dados['nome'],
            $dados['email'],
            $dados['senha'],
            $dados['cargo'],
            $dados['setor'],
            $dados['ativo']
        );
        $stmt->execute();
    }

// Se o usuário foi inserido ou atualizado com sucesso, redireciona para a página de gerenciamento
    if ($stmt->affected_rows > 0) {
        header("Location: gerenciar_usuarios.php");
        exit;
    } else {
        echo "<script>alert('Erro ao cadastrar/atualizar usuário.');</script>";
    }
} elseif (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

} elseif (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $conn->prepare("UPDATE usuarios SET ativo = NOT ativo WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">
                    <i class="bi bi-people"></i> 
                    Gerenciamento de Usuários
                </h3>
            </div>

            <div class="card-body">

                <form method="POST" class="mb-5">
                    <?php if(isset($_GET['edit'])): 
                        $editUser = $conn->query("SELECT * FROM usuarios WHERE id = ".(int)$_GET['edit'])->fetch_assoc();
                    ?>
                        <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="nome" class="form-control" 
                                placeholder="Nome completo" value="<?= $editUser['nome'] ?? '' ?>" required>
                        </div>

                        <div class="col-md-4">
                            <input type="email" name="email" class="form-control" 
                                placeholder="E-mail" value="<?= $editUser['email'] ?? '' ?>" required>
                        </div>

                        <div class="col-md-4">
                            <input type="password" name="senha" class="form-control" 
                                placeholder="<?= isset($editUser) ? 'Deixe em branco para manter' : 'Senha' ?>" 
                                <?= !isset($editUser) ? 'required' : '' ?>>
                        </div>

                        <div class="col-md-3">
                            <select name="cargo" class="form-select" required>
                                <option value="">Cargo...</option>
                                <option value="Gerente" <?= ($editUser['cargo'] ?? '') === 'Gerente' ? 'selected' : '' ?>>Gerente</option>
                                <option value="Colaborador" <?= ($editUser['cargo'] ?? '') === 'Colaborador' ? 'selected' : '' ?>>Colaborador</option>
                                <option value="Administrador" <?= ($editUser['cargo'] ?? '') === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="setor" class="form-select" required>
                                <option value="">Setor...</option>
                                <option value="TI" <?= ($editUser['setor'] ?? '') === 'TI' ? 'selected' : '' ?>>TI</option>
                                <option value="RH" <?= ($editUser['setor'] ?? '') === 'RH' ? 'selected' : '' ?>>RH</option>
                                <option value="Financeiro" <?= ($editUser['setor'] ?? '') === 'Financeiro' ? 'selected' : '' ?>>Financeiro</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-check form-switch d-flex align-items-center mt-3 ">
                            <input type="checkbox" name="ativo" class="form-check-input col-md-2 me-2" role="switch" 
                                id="ativo" <?= ($editUser['ativo'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="ativo">Ativo</label>
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-save"></i> 
                                <?= isset($editUser) ? 'Atualizar' : 'Cadastrar' ?>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Cargo</th>
                                <th>Setor</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($usuario = $usuarios->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($usuario['nome']) ?>
                                    <div class="text-muted small"><?= $usuario['email'] ?></div>
                                </td>
                                <td><?= $usuario['cargo'] ?></td>
                                <td><?= $usuario['setor'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $usuario['ativo'] ? 'success' : 'danger' ?>">
                                        <?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="gerenciar_usuarios.php?edit=<?= $usuario['id'] ?>" 
                                            class="btn btn-sm btn-primary"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <a href="gerenciar_usuarios.php?toggle=<?= $usuario['id'] ?>" 
                                            class="btn btn-sm btn-<?= $usuario['ativo'] ? 'warning' : 'success' ?>"
                                            title="<?= $usuario['ativo'] ? 'Desativar' : 'Ativar' ?>">
                                            <i class="bi bi-power"></i>
                                        </a>

                                        <a href="gerenciar_usuarios.php?delete=<?= $usuario['id'] ?>" 
                                            class="btn btn-sm btn-danger"
                                            title="Excluir"
                                            onclick="return confirm('Tem certeza? Esta ação é irreversível!')">
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