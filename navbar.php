
<nav class="navbar navbar-light bg-light mb-4 shadow-sm">
    <div class="container">
        <div class="d-flex justify-content-between w-100 align-items-center">
            <div class="d-flex gap-3 align-items-center">
                <?php if(basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i>
                        <span class="d-none d-md-inline">Voltar</span>
                    </a>
                <?php endif; ?>
                
                <span class="navbar-brand text-primary fw-bold">
                    <i class="bi bi-list-task me-2"></i>
                    <?= $_SESSION['usuario_nome'] ?>
                </span>
            </div>

            <div class="d-flex gap-3">
                <a href="gerenciar_usuarios.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-people"></i>
                    <span class="d-none d-md-inline">Usuários</span>
                </a>

                <a href="logout.php" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-md-inline">Sair</span>
                </a>
            </div>
        </div>
    </div>
</nav>