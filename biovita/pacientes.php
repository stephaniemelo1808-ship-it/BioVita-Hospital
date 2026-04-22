<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastro de Pacientes - Bio Vita</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div style="margin-bottom: 35px;">
            <h1 style="color: #2C3E50; font-size: 1.8rem; margin: 0; font-weight: 600;">Cadastro de Pacientes</h1>
            <p style="color: #7f8c8d; margin: 0; font-size: 1rem;">Registro completo de informações clínicas e pessoais.
            </p>
        </div>

        <form action="#" method="POST">
            <div class="card" style="margin-bottom: 25px;">
                <h3
                    style="color: #2C82B5; margin-bottom: 20px; border-bottom: 2px solid #f0f5f9; padding-bottom: 10px;">
                    <i class='bx bx-user'></i> Dados Pessoais
                </h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nome Completo *</label>
                        <input type="text" placeholder="Nome completo" required>
                    </div>
                    <div class="form-group">
                        <label>CPF *</label>
                        <input type="text" placeholder="000.000.000-00" required>
                    </div>
                    <div class="form-group">
                        <label>Telefone *</label>
                        <input type="text" placeholder="(00) 00000-0000" required>
                    </div>
                    <div class="form-group">
                        <label>Data de Nascimento *</label>
                        <input type="date" required>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label>Endereço Completo *</label>
                    <input type="text" placeholder="Rua, Número, Bairro, Cidade - UF" required>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 15px; margin-bottom: 50px;">
                <button type="reset" class="btn-secondary"
                    style="padding: 12px 30px; border-radius: 10px; border: 1px solid #ddd; cursor: pointer; background: white;">LIMPAR</button>
                <button type="submit" class="btn-primary">FINALIZAR CADASTRO</button>
            </div>
        </form>
    </main>

</body>

</html>