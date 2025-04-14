<?php
require 'config.php';

// Redirecionar usuários já autenticados
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Limitar tentativas de login
$max_tentativas = 5;
$bloqueio_tempo = 300;

if (!isset($_SESSION['tentativas'])) {
    $_SESSION['tentativas'] = 0;
    $_SESSION['ultimo_login'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar bloqueio por tentativas
    if ($_SESSION['tentativas'] >= $max_tentativas && (time() - $_SESSION['ultimo_login']) < $bloqueio_tempo) {
        $erro = "Muitas tentativas falhas. Tente novamente em " . ceil(($bloqueio_tempo - (time() - $_SESSION['ultimo_login'])) / 60) . " minutos.";
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $senha = $_POST['senha'];

        // Verificar campos vazios
        if (empty($email) || empty($senha)) {
            $erro = "Preencha todos os campos!";
        } else {
            $stmt = $conn->prepare("SELECT id, nome, senha, ativo FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($usuario = $result->fetch_assoc()) {
                // Verificar conta ativa
                if (!$usuario['ativo']) {
                    $erro = "Conta desativada. Contate o administrador.";
                } elseif (password_verify($senha, $usuario['senha'])) {
                    // Login bem-sucedido - resetar tentativas
                    $_SESSION['tentativas'] = 0;
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    header('Location: index.php');
                    exit;
                } else {
                    $_SESSION['tentativas']++;
                    $_SESSION['ultimo_login'] = time();
                    $erro = "Credenciais inválidas!";
                }
            } else {
                $_SESSION['tentativas']++;
                $_SESSION['ultimo_login'] = time();
                $erro = "Credenciais inválidas!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso ao Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .login-card {
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="login-card card">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold mb-1">Bem-vindo</h2>
                            <p class="text-muted">Faça login para continuar</p>
                        </div>

                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $erro ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                            
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" name="email" id="email" 
                                        class="form-control form-control-lg"
                                        placeholder="seu.email@exemplo.com"
                                        required
                                        autocomplete="username"
                                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                    <div class="invalid-feedback">
                                        Por favor, insira um e-mail válido.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="senha" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" name="senha" id="senha"
                                        class="form-control form-control-lg"
                                        placeholder="••••••••"
                                        required
                                        autocomplete="current-password">
                                    <div class="invalid-feedback">
                                        Por favor, insira sua senha.
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Entrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação do formulário no cliente
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>