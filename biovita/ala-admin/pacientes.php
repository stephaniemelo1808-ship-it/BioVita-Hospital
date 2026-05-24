<?php
session_start();

if (!isset($_SESSION['tipo_usu']) || ($_SESSION['tipo_usu'] !== 'Administrador' && $_SESSION['tipo_usu'] !== 'Admin')) {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

require_once('../conexao.php');


if (isset($_GET['excluir_pac'])) {
    $id_excluir = (int)$_GET['excluir_pac'];
    try {
        if ($mysqli->query("DELETE FROM registro_usuario WHERE id = '$id_excluir'")) {
            $_SESSION['alerta'] = "Paciente excluído com sucesso.";
            $_SESSION['tipo_alerta'] = "sucesso";
        }
    } catch (Exception $e) {
        $_SESSION['alerta'] = "Erro: Este paciente possui consultas. Cancele as consultas primeiro.";
        $_SESSION['tipo_alerta'] = "erro";
    }
    header("Location: pacientes.php"); 
    exit();
}


if(isset($_POST['btn-add-pac'])) {
    $nome = $mysqli->real_escape_string($_POST['pac_nome']);
    $cpf = preg_replace('/[^0-9]/', '', $_POST['pac_cpf']);
    $tel = $mysqli->real_escape_string($_POST['pac_telefone']);
    $cns = preg_replace('/[^0-9]/', '', $_POST['pac_cns']);
    $conv = $mysqli->real_escape_string($_POST['pac_convenio']);
    $nasc = $mysqli->real_escape_string($_POST['pac_dtnasc']);
    $end = $mysqli->real_escape_string($_POST['pac_endereco']);
    
    // Tenta detetar se a coluna real no banco se chama 'cns' ou 'csn'
    $coluna_cns = "cns"; // Padrão
    $check_col = $mysqli->query("SHOW COLUMNS FROM registro_usuario LIKE 'csn'");
    if($check_col->num_rows > 0) { $coluna_cns = "csn"; }
    
    $sql = "INSERT INTO registro_usuario (nome, cpf, telefone, $coluna_cns, dt_nasc, convenio_usu, endereco) 
            VALUES ('$nome', '$cpf', '$tel', '$cns', '$nasc', '$conv', '$end')";
            
    try {
        $mysqli->query($sql);
        $_SESSION['alerta'] = "Paciente cadastrado com sucesso.";
        $_SESSION['tipo_alerta'] = "sucesso";
    } catch (Exception $e) {
        $erro = $e->getMessage();
        if (strpos($erro, 'cpf') !== false) $erro = "Este CPF já se encontra registado.";
        elseif (strpos($erro, 'cns') !== false || strpos($erro, 'csn') !== false) $erro = "Este CNS já se encontra registado.";
        
        $_SESSION['alerta'] = "Erro ao cadastrar o paciente: " . $erro;
        $_SESSION['tipo_alerta'] = "erro";
    }
    header("Location: pacientes.php"); 
    exit();
}


if(isset($_POST['btn-edit-pac'])) {
    $id = (int)$_POST['edit_pac_id'];
    $nome = $mysqli->real_escape_string($_POST['edit_pac_nome']);
    $cpf = preg_replace('/[^0-9]/', '', $_POST['edit_pac_cpf']);
    $tel = $mysqli->real_escape_string($_POST['edit_pac_tel']);
    $cns = preg_replace('/[^0-9]/', '', $_POST['edit_pac_cns']);
    $conv = $mysqli->real_escape_string($_POST['edit_pac_conv']);
    $nasc = $mysqli->real_escape_string($_POST['edit_pac_dtnasc']);
    $end = $mysqli->real_escape_string($_POST['edit_pac_endereco']);
    
    $coluna_cns = "cns";
    $check_col = $mysqli->query("SHOW COLUMNS FROM registro_usuario LIKE 'csn'");
    if($check_col->num_rows > 0) { $coluna_cns = "csn"; }
    
    try {
        $mysqli->query("UPDATE registro_usuario SET 
                        nome = '$nome', 
                        cpf = '$cpf',
                        telefone = '$tel', 
                        $coluna_cns = '$cns',
                        dt_nasc = '$nasc',
                        convenio_usu = '$conv', 
                        endereco = '$end' 
                        WHERE id = '$id'");
                        
        $_SESSION['alerta'] = "Dados do paciente atualizados com sucesso."; 
        $_SESSION['tipo_alerta'] = "sucesso";
    } catch (Exception $e) {
        $erro = $e->getMessage();
        if (strpos($erro, 'cpf') !== false) $erro = "O CPF inserido pertence a outro paciente.";
        elseif (strpos($erro, 'cns') !== false || strpos($erro, 'csn') !== false) $erro = "O CNS inserido pertence a outro paciente.";
        
        $_SESSION['alerta'] = "Erro ao atualizar dados: " . $erro;
        $_SESSION['tipo_alerta'] = "erro";
    }
    header("Location: pacientes.php"); 
    exit();
}


$res_pacientes = $mysqli->query("SELECT * FROM registro_usuario ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pacientes - Bio Vita</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .admin-table { width: 100%; border-collapse: collapse; text-align: left; background: white; margin-top: 15px;}
        .admin-table th { padding: 15px; color: #2C3E50; background-color: #f8f9fa; border-bottom: 2px solid #e2e8f0; }
        .admin-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; color: #475569; }
        .btn-action { background: none; border: none; font-size: 1.3rem; cursor: pointer; transition: 0.2s; }
        .btn-edit { color: #f39c12; }
        .btn-del { color: #e74c3c; text-decoration: none; display: inline-flex;}
        
        /* Estilos do Modal Flutuante */
        .modal { display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; padding: 20px; box-sizing: border-box;}
        .modal-content { background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 700px; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative;}
        .modal-content input, .modal-content select { width: 100%; box-sizing: border-box; }
        .close-btn { position: absolute; top: 15px; right: 20px; font-size: 1.5rem; cursor: pointer; color: #7f8c8d; }
        .close-btn:hover { color: #e74c3c; }
        .overflow-x { overflow-x: auto; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .full-width { grid-column: span 2; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .full-width { grid-column: span 1; } }
    </style>
</head>
<body>

    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div style="margin-bottom: 25px;">
            <h1 style="color: #2C3E50; font-size: 1.8rem; font-weight: 600;">Cadastro de Pacientes</h1>
            <p style="color: #7f8c8d;">Registro completo de informações clínicas e pessoais.</p>
        </div>

        <?php if(isset($_SESSION['alerta'])): ?>
            <div style="padding: 15px; margin-bottom: 20px; border-radius: 8px; color: white; font-weight: bold; background-color: <?= ($_SESSION['tipo_alerta'] == 'sucesso') ? '#2ecc71' : '#e74c3c'; ?>;">
                <?= $_SESSION['alerta']; unset($_SESSION['alerta']); unset($_SESSION['tipo_alerta']); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-bottom: 30px;">
            <h3 style="color: #2C82B5; margin-bottom: 20px;"><i class='bx bx-user'></i> Dados Pessoais</h3>
            <form action="" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nome Completo *</label>
                        <input type="text" name="pac_nome" placeholder="Nome completo" required>
                    </div>
                    <div class="form-group">
                        <label>CPF *</label>
                        <input type="text" name="pac_cpf" placeholder="000.000.000-00" maxlength="14" oninput="mascaraCPF(this)" required>
                    </div>
                    <div class="form-group">
                        <label>Telefone *</label>
                        <input type="text" name="pac_telefone" placeholder="(00) 00000-0000" maxlength="15" oninput="mascaraTelefone(this)" required>
                    </div>
                    <div class="form-group">
                        <label>CNS *</label>
                        <input type="text" name="pac_cns" placeholder="000 0000 0000 0000" maxlength="18" oninput="mascaraCNS(this)" required>
                    </div>
                    <div class="form-group">
                        <label>Data de Nascimento *</label>
                        <input type="date" name="pac_dtnasc" required>
                    </div>
                    <div class="form-group">
                        <label>Convênio *</label>
                        <input type="text" name="pac_convenio" placeholder="Nome do Convênio" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Endereço Completo *</label>
                        <input type="text" name="pac_endereco" placeholder="Rua, Número, Bairro, Cidade - UF" required>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                    <button type="submit" name="btn-add-pac" class="btn-primary">CADASTRAR PACIENTE</button>
                </div>
            </form>
        </div>

        <div class="card overflow-x">
            <h3 style="color: #2C82B5; margin-bottom: 15px;"><i class='bx bx-list-ul'></i> Pacientes Registrados</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>Convênio</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $res_pacientes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><strong><?= $row['nome'] ?></strong></td>
                            <td>
                                <script>document.write(formataCPFJS('<?= $row['cpf'] ?>'));</script>
                            </td>
                            <td><?= $row['telefone'] ?></td>
                            <td><?= $row['convenio_usu'] ?></td>
                            <td style="text-align: center; display: flex; justify-content: center; gap: 15px; border-bottom: none;">
                                <button type="button" class="btn-action btn-edit" 
                                    onclick="abrirModalPac(this)"
                                    data-id="<?= $row['id'] ?>"
                                    data-nome="<?= htmlspecialchars($row['nome']) ?>"
                                    data-cpf="<?= htmlspecialchars($row['cpf'] ?? '') ?>"
                                    data-tel="<?= htmlspecialchars($row['telefone'] ?? '') ?>"
                                    data-cns="<?= htmlspecialchars($row['cns'] ?? $row['csn'] ?? '') ?>"
                                    data-dtnasc="<?= htmlspecialchars($row['dt_nasc'] ?? '') ?>"
                                    data-conv="<?= htmlspecialchars($row['convenio_usu'] ?? '') ?>"
                                    data-end="<?= htmlspecialchars($row['endereco'] ?? '') ?>"
                                    title="Editar">
                                    <i class='bx bx-edit-alt'></i>
                                </button>
                                <a href="?excluir_pac=<?= $row['id'] ?>" class="btn-action btn-del" onclick="return confirm('Deseja excluir este paciente?')" title="Excluir">
                                    <i class='bx bx-trash'></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalEditPac" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="fecharModal('modalEditPac')">&times;</span>
            <h3 style="color: #2C3E50; margin-bottom: 20px;"><i class='bx bx-edit'></i> Editar Paciente</h3>
            <form action="" method="POST">
                <input type="hidden" name="edit_pac_id" id="mod_pac_id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nome Completo *</label>
                        <input type="text" name="edit_pac_nome" id="mod_pac_nome" required>
                    </div>
                    <div class="form-group">
                        <label>CPF *</label>
                        <input type="text" name="edit_pac_cpf" id="mod_pac_cpf" maxlength="14" oninput="mascaraCPF(this)" required>
                    </div>
                    <div class="form-group">
                        <label>Telefone *</label>
                        <input type="text" name="edit_pac_tel" id="mod_pac_tel" maxlength="15" oninput="mascaraTelefone(this)" required>
                    </div>
                    <div class="form-group">
                        <label>CNS *</label>
                        <input type="text" name="edit_pac_cns" id="mod_pac_cns" maxlength="18" oninput="mascaraCNS(this)" required>
                    </div>
                    <div class="form-group">
                        <label>Data de Nascimento *</label>
                        <input type="date" name="edit_pac_dtnasc" id="mod_pac_dtnasc" required>
                    </div>
                    <div class="form-group">
                        <label>Convênio *</label>
                        <input type="text" name="edit_pac_conv" id="mod_pac_conv" required>
                    </div>
                    <div class="form-group full-width" style="margin-bottom: 20px;">
                        <label>Endereço Completo *</label>
                        <input type="text" name="edit_pac_endereco" id="mod_pac_end" required>
                    </div>
                </div>
                
                <button type="submit" name="btn-edit-pac" class="btn-primary" style="width: 100%; justify-content: center;">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script>
        // Funções de Máscara de Formatação
        function mascaraCPF(i) {
            let v = i.value;
            v = v.replace(/\D/g,""); // Remove o que não é dígito
            v = v.replace(/(\d{3})(\d)/,"$1.$2"); // Adiciona ponto após 3 dígitos
            v = v.replace(/(\d{3})(\d)/,"$1.$2"); // Adiciona ponto após 6 dígitos
            v = v.replace(/(\d{3})(\d{1,2})$/,"$1-$2"); // Adiciona traço antes dos 2 últimos
            i.value = v;
        }
        
        function mascaraTelefone(i) {
            let v = i.value;
            v = v.replace(/\D/g,"");
            v = v.replace(/^(\d{2})(\d)/g,"($1) $2"); // Coloca os parênteses
            v = v.replace(/(\d)(\d{4})$/,"$1-$2"); // Coloca o traço
            i.value = v;
        }

        function mascaraCNS(i) {
            let v = i.value;
            v = v.replace(/\D/g,""); // Apenas números
            v = v.replace(/(\d{3})(\d)/,"$1 $2"); // Espaço após 3 dígitos
            v = v.replace(/(\d{4})(\d)/,"$1 $2"); // Espaço após 4 dígitos (total 7)
            v = v.replace(/(\d{4})(\d{1,4})$/,"$1 $2"); // Espaço após mais 4 dígitos (total 11)
            i.value = v;
        }

        // Função rápida para formatar CPFs vindos do banco na tabela HTML
        function formataCPFJS(cpf) {
            cpf = cpf.replace(/\D/g, "");
            if(cpf.length === 11) {
                return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
            }
            return cpf;
        }

        function fecharModal(id) { 
            document.getElementById(id).style.display = 'none'; 
        }
        
        function abrirModalPac(elemento) {
            document.getElementById('mod_pac_id').value = elemento.getAttribute('data-id');
            document.getElementById('mod_pac_nome').value = elemento.getAttribute('data-nome');
            
            let cpfField = document.getElementById('mod_pac_cpf');
            cpfField.value = elemento.getAttribute('data-cpf');
            mascaraCPF(cpfField);
            
            let telField = document.getElementById('mod_pac_tel');
            telField.value = elemento.getAttribute('data-tel');
            mascaraTelefone(telField);
            
            let cnsField = document.getElementById('mod_pac_cns');
            cnsField.value = elemento.getAttribute('data-cns');
            mascaraCNS(cnsField);

            document.getElementById('mod_pac_dtnasc').value = elemento.getAttribute('data-dtnasc');
            document.getElementById('mod_pac_conv').value = elemento.getAttribute('data-conv');
            document.getElementById('mod_pac_end').value = elemento.getAttribute('data-end');
            
            document.getElementById('modalEditPac').style.display = 'flex';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) { 
                event.target.style.display = "none"; 
            }
        }
    </script>
</body>
</html>