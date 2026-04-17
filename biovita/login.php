<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MedSystem</title>
    <link rel="stylesheet" href="./CSS/style.css">
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
        <form action="usuarios.php" method="POST">
            <div class="input-group">
                <label>Usuário</label>
                <input type="text" placeholder="Seu login" required>
            </div>
            <div class="input-group">
                <label>Senha</label>
                <input type="password" placeholder="Sua senha" required>
            </div>
            <button type="submit" class="btn-login">ENTRAR NO SISTEMA</button>
        </form>
    </div>

</body>

</html>