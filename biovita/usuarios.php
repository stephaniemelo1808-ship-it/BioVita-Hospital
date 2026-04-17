<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários - Bio Vita</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div style="margin-bottom: 35px;">
            <h1 style="color: #2C3E50; font-size: 1.8rem; margin: 0; font-weight: 600;">Gerenciar Usuários do Sistema
            </h1>
            <p style="color: #7f8c8d; margin: 0; font-size: 1rem;">Administre as contas de acesso e permissões de
                funcionários.</p>
        </div>

        <div class="card">
            <h3
                style="color: var(--azul-med); margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <i class='bx bx-user-plus'></i> Novo Usuário
            </h3>
            <form action="#" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nome Completo *</label>
                        <input type="text" placeholder="Digite o nome completo" required>
                    </div>
                    <div class="form-group">
                        <label>Usuário (Login) *</label>
                        <input type="text" placeholder="Ex: carlos.med" required>
                    </div>
                    <div class="form-group">
                        <label>Senha *</label>
                        <input type="password" placeholder="Digite a senha" required>
                    </div>
                    <div class="form-group">
                        <label>Perfil *</label>
                        <select required>
                            <option value="">Selecione o perfil...</option>
                            <option value="admin">Administrador</option>
                            <option value="medico">Médico</option>
                            <option value="recepcao">Recepção</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                    <button type="submit" class="btn-primary">CRIAR CONTA</button>
                </div>
            </form>
        </div>
    </main>

</body>

</html>