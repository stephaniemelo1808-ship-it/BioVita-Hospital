<?php
session_start();
require_once('conexao.php');

if(isset($_POST['btn-primary'])) {
    $novo_user = $mysqli->real_escape_string($_POST['reg_usuario']);
    $nova_senha = $mysqli->real_escape_string($_POST['reg_senha']);
    $novo_tipo = $mysqli->real_escape_string($_POST['reg_tipo']);
    $novo_nome = $mysqli->real_escape_string($_POST['reg_nome']);

    
    $check = $mysqli->query("SELECT * FROM login WHERE usuario = '$novo_user'");
    if($check->num_rows > 0) {
        $_SESSION['alerta'] = "Esse nome de usuário já existe! Escolha outro.";
        $_SESSION['tipo_alerta'] = "erro";
    } else {
        $mysqli->query("INSERT INTO login (usuario, senha_usu, tipo_usu, nome_usu) VALUES ('$novo_user', '$nova_senha', '$novo_tipo', '$novo_nome')");
        $_SESSION['alerta'] = "Cadastro realizado com sucesso! Agora você já pode fazer o login.";
        $_SESSION['tipo_alerta'] = "sucesso";
        header("Location: usuarios.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários - Bio Vita</title>
    <link rel="stylesheet" href="../css/style.css">
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

        <?php if(isset($_SESSION['alerta'])): ?>
            <div style="padding: 15px; margin-bottom: 20px; border-radius: 8px; color: white; font-weight: bold; background-color: <?php echo ($_SESSION['tipo_alerta'] == 'sucesso') ? '#2ecc71' : '#e74c3c'; ?>;">
                <?php 
                    echo $_SESSION['alerta']; 
                    unset($_SESSION['alerta']); 
                    unset($_SESSION['tipo_alerta']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3
                style="color: var(--azul-med); margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <i class='bx bx-user-plus'></i> Novo Usuário
            </h3>
            
            <form action="" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nome Completo *</label>
                        <input type="text" name="reg_nome" placeholder="Digite o nome completo" required>
                    </div>
                    <div class="form-group">
                        <label>Usuário (Login) *</label>
                        <input type="text" name="reg_usuario" placeholder="Ex: carlos.med" required>
                    </div>
                    <div class="form-group">
                        <label>Senha *</label>
                        <input type="password" name="reg_senha" placeholder="Digite a senha" required>
                    </div>
                    <div class="form-group">
                        <label>Perfil *</label>
                        <select name="reg_tipo" required>
                            <option value="">Selecione o perfil...</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Médico">Médico</option>
                            <option value="Recepção">Recepção</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                    <button type="submit" name="btn-primary" class="btn-primary">CRIAR CONTA</button>
                </div>
            </form>
        </div>
    </main>

</body>

</html>