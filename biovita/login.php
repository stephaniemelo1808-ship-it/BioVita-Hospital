<?php
session_start();
include('conexao.php');

if(isset($_POST['btn_login'])) {
    $user_digitado = $mysqli->real_escape_string($_POST['reg_usuario']);
    $senha_digitada = $mysqli->real_escape_string($_POST['reg_senha']);
    
    $sql_query = $mysqli->query("SELECT * FROM login WHERE usuario = '$user_digitado' AND senha_usu = '$senha_digitada'");
    
    if($sql_query->num_rows == 1) {
        $user = $sql_query->fetch_assoc();
        
        $_SESSION['id'] = $user['id_log'];
        $_SESSION['usuario'] = $user['nome_usu'];
        $_SESSION['tipo_usu'] = $user['tipo_usu'];

        if($user['tipo_usu'] === 'Médico') {
            header("Location: ala_medica/medico.php");
            exit(); 
            
        } elseif ($user['tipo_usu'] === 'Administrador' || $user['tipo_usu'] === 'Admin') {
            header("Location: ala-admin/dashboard.php");
            exit();
            
        } else {
            header("Location: ala-admin/dashboard.php");
            exit();
        }
        
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MedSystem</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f5f9;
            margin: 0;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .btn-login {
            width: 100%;
            background: #2C82B5;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .input-group {
            text-align: left;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <a href="index.php"
            style="text-decoration: none; color: #7f8c8d; font-size: 0.8rem; display: flex; align-items: center; gap: 5px; margin-bottom: 20px;">
            <i class='bx bx-left-arrow-alt'></i> Voltar ao início
        </a>
        <img src="img/logo_biovita.png" alt="Bio Vita" style="width: 150px; margin-bottom: 20px;">
        <h2>Login</h2>
        
        <?php if(isset($erro)) echo "<p style='color: #e74c3c; font-weight: bold; font-size: 14px;'>$erro</p>"; ?>

        <form action="" method="POST">
            <div class="input-group">
                <label>Usuário</label>
                <input type="text" name="reg_usuario" required>
            </div>
            <div class="input-group">
                <label>Senha</label>
                <input type="password" name="reg_senha" required>
            </div>
            
            <button type="submit" name="btn_login" class="btn-login">ENTRAR NO SISTEMA</button>
        </form>
    </div>

</body>

</html>