<?php
session_start();
require_once('conexao.php');

if(isset($_POST['btn-primary'])) {
    
    try {
        $novo_nome = $mysqli->real_escape_string($_POST['reg_nome']);
        $novo_telefone = $mysqli->real_escape_string($_POST['reg_telefone']);
        $novo_endereco = $mysqli->real_escape_string($_POST['reg_endereco']);
        $nova_data = $mysqli->real_escape_string($_POST['reg_data']);
        
        $cpf_apenas_numeros = preg_replace('/[^0-9]/', '', $_POST['reg_cpf']);
        $nova_cpf = $mysqli->real_escape_string($cpf_apenas_numeros);

        $cns_apenas_numeros = preg_replace('/[^0-9]/', '', $_POST['reg_cns']);
        $novo_cns = $mysqli->real_escape_string($cns_apenas_numeros);

        $check = $mysqli->query("SELECT * FROM registro_usuario WHERE cpf = '$nova_cpf'");
        
        if($check->num_rows > 0) {
            $_SESSION['alerta'] = "Este CPF já está cadastrado no sistema!";
            $_SESSION['tipo_alerta'] = "erro";
        } else {
            $mysqli->query("INSERT INTO registro_usuario (nome, cpf, telefone, csn, endereco, dt_nasc) 
                            VALUES ('$novo_nome', '$nova_cpf', '$novo_telefone', '$novo_cns', '$novo_endereco', '$nova_data')");
            
            $_SESSION['alerta'] = "Cadastro de paciente realizado com sucesso!";
            $_SESSION['tipo_alerta'] = "sucesso";
            
            header("Location: pacientes.php");
            exit();
        }

    } catch (Exception $e) {
        die("<div style='background: #e74c3c; color: white; padding: 20px; text-align: center;'>
                <h3>Erro Crítico no Banco de Dados:</h3>
                <p>" . $e->getMessage() . "</p>
             </div>");
    }
}
?>

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
            <p style="color: #7f8c8d; margin: 0; font-size: 1rem;">Registro completo de informações clínicas e pessoais.</p>
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

        <form action="" method="POST">
            <div class="card" style="margin-bottom: 25px;">
                <h3 style="color: #2C82B5; margin-bottom: 20px; border-bottom: 2px solid #f0f5f9; padding-bottom: 10px;">
                    <i class='bx bx-user'></i> Dados Pessoais
                </h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nome Completo *</label>
                        <input type="text" name="reg_nome" placeholder="Nome completo" required>
                    </div>
                    <div class="form-group">
                        <label>CPF *</label>
                        <input type="text" name="reg_cpf" placeholder="000.000.000-00" required>
                    </div>
                    <div class="form-group">
                        <label>Telefone *</label>
                        <input type="text" name="reg_telefone" placeholder="(00) 00000-0000" required>
                    </div>
                    <div class="form-group">
                        <label>CNS *</label>
                        <input type="text" name="reg_cns" placeholder="000 0000 0000 0000" required>
                    </div>
                    <div class="form-group">
                        <label>Data de Nascimento *</label>
                        <input type="date" name="reg_data" required>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label>Endereço Completo *</label>
                    <input type="text" name="reg_endereco" placeholder="Rua, Número, Bairro, Cidade - UF" required>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 15px; margin-bottom: 50px;">
                <button type="reset" class="btn-secondary"
                    style="padding: 12px 30px; border-radius: 10px; border: 1px solid #ddd; cursor: pointer; background: white;">LIMPAR</button>
                <button type="submit" name="btn-primary" class="btn-primary">FINALIZAR CADASTRO</button>
            </div>
        </form>
    </main>

</body>

</html>