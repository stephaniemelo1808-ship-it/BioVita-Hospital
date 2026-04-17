<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bio Vita Hospital - Bem-vindo</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            height: 100vh;
            background: linear-gradient(rgba(44, 130, 181, 0.8), rgba(44, 130, 181, 0.8)),
                url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: white;
            text-align: center;
        }

        .portal-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 50px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 500px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .portal-card img {
            width: 220px;
            filter: brightness(0) invert(1);
            margin-bottom: 20px;
        }

        .portal-card h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .portal-card p {
            font-size: 1.1rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .btn-portal {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: white;
            color: #2C82B5;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-portal:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            background: #f0f5f9;
        }
    </style>
</head>

<body>

    <div class="portal-card">
        <img src="img/logo_biovita.png" alt="Bio Vita Logo">
        <h1>MedSystem</h1>
        <p>Plataforma Integrada de Gestão Hospitalar e Atendimento ao Paciente.</p>

        <a href="login.php" class="btn-portal">
            ACESSAR SISTEMA <i class='bx bx-right-arrow-alt'></i>
        </a>
    </div>

</body>

</html>