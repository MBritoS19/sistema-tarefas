<?php
require 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($usuario = $result->fetch_assoc()) {
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header('Location: index.php');
            exit;
        }
    }
    $erro = "Credenciais inválidas!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Acesso ao Sistema</h2>
                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger"><?= $erro ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control"
                                    placeholder="Seu e-mail" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="senha" class="form-control"
                                    placeholder="Sua senha" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>