<?php
session_start();

if (!isset($_SESSION['tipo_usu']) || ($_SESSION['tipo_usu'] !== 'Administrador' && $_SESSION['tipo_usu'] !== 'Admin')) {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

require_once('../conexao.php');
$aba_ativa = isset($_GET['aba']) ? $_GET['aba'] : 'tab-usuarios';


if (isset($_GET['excluir_usu'])) {
    $id_excluir = (int)$_GET['excluir_usu'];
    if ($id_excluir != $_SESSION['id']) {
        $mysqli->query("DELETE FROM registro_medico WHERE id_log = '$id_excluir'");
        $mysqli->query("DELETE FROM login WHERE id_log = '$id_excluir'");
        $_SESSION['alerta'] = "Usuário excluído com sucesso."; 
        $_SESSION['tipo_alerta'] = "sucesso";
    } else {
        $_SESSION['alerta'] = "Ação bloqueada: Não pode excluir a sua própria conta."; 
        $_SESSION['tipo_alerta'] = "erro";
    }
    header("Location: usuarios.php?aba=tab-usuarios"); exit();
}

if (isset($_GET['excluir_cons'])) {
    $id_excluir = (int)$_GET['excluir_cons'];
    $mysqli->query("DELETE FROM consultas WHERE id_consulta = '$id_excluir'");
    $_SESSION['alerta'] = "Consulta excluída com sucesso."; 
    $_SESSION['tipo_alerta'] = "sucesso";
    header("Location: usuarios.php?aba=tab-consultas"); exit();
}

if(isset($_POST['btn-add-user'])) {
    $novo_user = $mysqli->real_escape_string($_POST['reg_usuario']);
    $nova_senha = $mysqli->real_escape_string($_POST['reg_senha']);
    $novo_tipo = $mysqli->real_escape_string($_POST['reg_tipo']);
    $novo_nome = $mysqli->real_escape_string($_POST['reg_nome']);
    
    $check = $mysqli->query("SELECT * FROM login WHERE usuario = '$novo_user'");
    if($check->num_rows == 0) {
        $mysqli->query("INSERT INTO login (usuario, senha_usu, tipo_usu, nome_usu) VALUES ('$novo_user', '$nova_senha', '$novo_tipo', '$novo_nome')");
        if ($novo_tipo === 'Médico') {
            $id_log = $mysqli->insert_id;
            $crm = $mysqli->real_escape_string($_POST['reg_crm']);
            $uf = $mysqli->real_escape_string($_POST['reg_uf']);
            $telefone = $mysqli->real_escape_string($_POST['reg_telefone']);
            $ubs = $mysqli->real_escape_string($_POST['reg_ubs']);
            $cpf = preg_replace('/[^0-9]/', '', $_POST['reg_cpf']);
            $mysqli->query("INSERT INTO registro_medico (id_log, cpf, crm, uf, telefone, ubs) VALUES ('$id_log', '$cpf', '$crm', '$uf', '$telefone', '$ubs')");
        }
        $_SESSION['alerta'] = "Funcionário cadastrado com sucesso."; 
        $_SESSION['tipo_alerta'] = "sucesso";
    } else {
        $_SESSION['alerta'] = "Erro: Este Login já existe."; 
        $_SESSION['tipo_alerta'] = "erro";
    }
    header("Location: usuarios.php?aba=tab-usuarios"); exit();
}

if(isset($_POST['btn-add-cons'])) {
    $id_pac = (int)$_POST['cons_paciente'];
    $id_med = (int)$_POST['cons_medico'];
    $data_hora = $mysqli->real_escape_string($_POST['cons_data_hora']);
    $tipo = $mysqli->real_escape_string($_POST['cons_tipo']);
    $status = $mysqli->real_escape_string($_POST['cons_status']);

    if($mysqli->query("INSERT INTO consultas (id_paciente, id_log_medico, data_hora_consul, tipo_consulta, status_consulta) VALUES ('$id_pac', '$id_med', '$data_hora', '$tipo', '$status')")){
        $_SESSION['alerta'] = "Consulta agendada com sucesso!"; 
        $_SESSION['tipo_alerta'] = "sucesso";
    } else {
        $_SESSION['alerta'] = "Erro ao agendar consulta."; 
        $_SESSION['tipo_alerta'] = "erro";
    }
    header("Location: usuarios.php?aba=tab-consultas"); exit();
}


if(isset($_POST['btn-edit-user'])) {
    $id = (int)$_POST['edit_id'];
    $nome = $mysqli->real_escape_string($_POST['edit_nome']);
    $mysqli->query("UPDATE login SET nome_usu = '$nome' WHERE id_log = '$id'");
    $_SESSION['alerta'] = "Colaborador atualizado com sucesso."; 
    $_SESSION['tipo_alerta'] = "sucesso";
    header("Location: usuarios.php?aba=tab-usuarios"); exit();
}

if(isset($_POST['btn-edit-cons'])) {
    $id = (int)$_POST['edit_cons_id'];
    $data_hora = $mysqli->real_escape_string($_POST['edit_cons_data']);
    $tipo = $mysqli->real_escape_string($_POST['edit_cons_tipo']);
    $status = $mysqli->real_escape_string($_POST['edit_cons_status']);
    
    $mysqli->query("UPDATE consultas SET data_hora_consul = '$data_hora', tipo_consulta = '$tipo', status_consulta = '$status' WHERE id_consulta = '$id'");
    $_SESSION['alerta'] = "Consulta atualizada com sucesso."; 
    $_SESSION['tipo_alerta'] = "sucesso";
    header("Location: usuarios.php?aba=tab-consultas"); exit();
}


$res_usuarios = $mysqli->query("SELECT l.*, m.crm FROM login l LEFT JOIN registro_medico m ON l.id_log = m.id_log ORDER BY l.id_log DESC");

$res_consultas = $mysqli->query("SELECT c.*, p.nome as paciente, l.nome_usu as medico 
                                 FROM consultas c 
                                 JOIN registro_usuario p ON c.id_paciente = p.id 
                                 JOIN login l ON c.id_log_medico = l.id_log 
                                 ORDER BY c.data_hora_consul DESC");

$lista_pac = $mysqli->query("SELECT id, nome FROM registro_usuario ORDER BY nome ASC");
$lista_med = $mysqli->query("SELECT id_log, nome_usu FROM login WHERE tipo_usu = 'Médico' ORDER BY nome_usu ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Central de Gestão - Bio Vita</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .tabs-nav { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .tab-btn { background: none; border: none; padding: 10px 20px; font-size: 1rem; color: #64748b; cursor: pointer; border-radius: 8px; font-weight: 500; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .tab-btn:hover { background: #f1f5f9; color: #0f172a; }
        .tab-btn.active { background: #2C82B5; color: white; box-shadow: 0 4px 6px rgba(44,130,181,0.2); }
        .tab-content { display: none; animation: fadeIn 0.4s ease; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        
        .admin-table { width: 100%; border-collapse: collapse; text-align: left; background: white; margin-top: 15px;}
        .admin-table th { padding: 15px; color: #2C3E50; background-color: #f8f9fa; border-bottom: 2px solid #e2e8f0; }
        .admin-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; color: #475569; }
        .btn-action { background: none; border: none; font-size: 1.3rem; cursor: pointer; transition: 0.2s; }
        .btn-edit { color: #f39c12; }
        .btn-del { color: #e74c3c; text-decoration: none; display: inline-flex;}
        
        /* Estilos do Modal Flutuante */
        .modal { display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal-content { background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative;}
        .close-btn { position: absolute; top: 15px; right: 20px; font-size: 1.5rem; cursor: pointer; color: #7f8c8d; }
        .close-btn:hover { color: #e74c3c; }
        .overflow-x { overflow-x: auto; }
    </style>
</head>
<body>

    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div style="margin-bottom: 25px;">
            <h1 style="color: #2C3E50; font-size: 1.8rem; font-weight: 600;">Central de Gestão</h1>
            <p style="color: #7f8c8d;">Administre o Staff Hospitalar e os Agendamentos de Consultas.</p>
        </div>

        <?php if(isset($_SESSION['alerta'])): ?>
            <div style="padding: 15px; margin-bottom: 20px; border-radius: 8px; color: white; font-weight: bold; background-color: <?= ($_SESSION['tipo_alerta'] == 'sucesso') ? '#2ecc71' : '#e74c3c'; ?>;">
                <?= $_SESSION['alerta']; unset($_SESSION['alerta']); unset($_SESSION['tipo_alerta']); ?>
            </div>
        <?php endif; ?>

        <div class="tabs-nav">
            <button class="tab-btn <?= $aba_ativa == 'tab-usuarios' ? 'active' : '' ?>" onclick="abrirAba(event, 'tab-usuarios')"><i class='bx bx-user-pin'></i> Staff (Equipe)</button>
            <button class="tab-btn <?= $aba_ativa == 'tab-consultas' ? 'active' : '' ?>" onclick="abrirAba(event, 'tab-consultas')"><i class='bx bx-calendar'></i> Agendamentos</button>
        </div>

        <div id="tab-usuarios" class="tab-content <?= $aba_ativa == 'tab-usuarios' ? 'active' : '' ?>">
            <div class="card" style="margin-bottom: 20px;">
                <h3 style="color: #2C82B5;"><i class='bx bx-user-plus'></i> Cadastrar Novo Colaborador</h3>
                <form action="" method="POST" class="form-grid" style="margin-top: 15px;">
                    <div class="form-group"><input type="text" name="reg_nome" placeholder="Nome Completo *" required></div>
                    <div class="form-group"><input type="text" name="reg_usuario" placeholder="Login de Acesso *" required></div>
                    <div class="form-group"><input type="password" name="reg_senha" placeholder="Senha *" required></div>
                    <div class="form-group">
                        <select name="reg_tipo" id="reg_tipo" onchange="mostrarCamposMedico()" required onmousedown="event.stopPropagation()" style="pointer-events: auto !important; user-select: auto !important; position: relative; z-index: 9999;">
                            <option value="">Selecione o Perfil *</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Médico">Médico</option>
                            <option value="Recepção">Recepção</option>
                        </select>
                    </div>
                    
                    <div id="campos_medico" style="display: none; grid-column: span 2; background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 10px; border: 1px solid #e0e0e0;">
                        <h4 style="margin: 0 0 15px 0; color: #2C82B5;"><i class='bx bx-plus-medical'></i> Dados Médicos</h4>
                        <div class="form-grid">
                            <div class="form-group"><input type="text" name="reg_crm" id="reg_crm" placeholder="CRM *"></div>
                            <div class="form-group">
                                <select name="reg_uf" id="reg_uf" onmousedown="event.stopPropagation()" style="pointer-events: auto !important; user-select: auto !important; position: relative; z-index: 9999;">
                                    <option value="SP">SP</option><option value="RJ">RJ</option><option value="MG">MG</option><option value="SC">SC</option><option value="RS">RS</option>
                                </select>
                            </div>
                            <div class="form-group"><input type="text" name="reg_telefone" id="reg_telefone" placeholder="Telefone *"></div>
                            <div class="form-group"><input type="text" name="reg_cpf" id="reg_cpf" placeholder="CPF *"></div>
                            <div class="form-group" style="grid-column: span 2;">
                                <input type="text" name="reg_ubs" id="reg_ubs" placeholder="UBS Vinculada *" value="Hospital Bio Vita Central">
                            </div>
                        </div>
                    </div>

                    <div style="grid-column: span 2; display: flex; justify-content: flex-end; margin-top: 10px;">
                        <button type="submit" name="btn-add-user" class="btn-primary">Adicionar Colaborador</button>
                    </div>
                </form>
            </div>

            <div class="card overflow-x">
                <h3 style="color: #2C82B5; margin-bottom: 15px;"><i class='bx bx-list-ul'></i> Equipe Registrada</h3>
                <table class="admin-table">
                    <thead><tr><th>ID</th><th>Nome</th><th>Perfil</th><th>CRM</th><th style="text-align: center;">Ações</th></tr></thead>
                    <tbody>
                        <?php while($row = $res_usuarios->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id_log'] ?></td>
                                <td><strong><?= $row['nome_usu'] ?></strong></td>
                                <td><?= $row['tipo_usu'] ?></td>
                                <td><?= $row['crm'] ?: '-' ?></td>
                                <td style="text-align: center; display: flex; justify-content: center; gap: 15px; border-bottom: none;">
                                    <button class="btn-action btn-edit" onclick="abrirModalUsu(<?= $row['id_log'] ?>, '<?= addslashes($row['nome_usu']) ?>')" title="Editar Nome"><i class='bx bx-edit-alt'></i></button>
                                    <a href="?excluir_usu=<?= $row['id_log'] ?>" class="btn-action btn-del" onclick="return confirm('Excluir este usuário?')" title="Excluir"><i class='bx bx-trash'></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-consultas" class="tab-content <?= $aba_ativa == 'tab-consultas' ? 'active' : '' ?>">
            <div class="card" style="margin-bottom: 20px;">
                <h3 style="color: #2C82B5;"><i class='bx bx-calendar-plus'></i> Agendar Nova Consulta</h3>
                <form action="" method="POST" class="form-grid" style="margin-top: 15px;">
                    <div class="form-group">
                        <label>Paciente *</label>
                        <select name="cons_paciente" required onmousedown="event.stopPropagation()" style="pointer-events: auto !important; user-select: auto !important; position: relative; z-index: 9999;">
                            <option value="">Selecione...</option>
                            <?php while($p = $lista_pac->fetch_assoc()): ?><option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option><?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Médico Especialista *</label>
                        <select name="cons_medico" required onmousedown="event.stopPropagation()" style="pointer-events: auto !important; user-select: auto !important; position: relative; z-index: 9999;">
                            <option value="">Selecione...</option>
                            <?php while($m = $lista_med->fetch_assoc()): ?><option value="<?= $m['id_log'] ?>"><?= $m['nome_usu'] ?></option><?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Data e Hora *</label>
                        <input type="datetime-local" name="cons_data_hora" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Atendimento</label>
                        <select name="cons_tipo" onmousedown="event.stopPropagation()" style="pointer-events: auto !important; user-select: auto !important; position: relative; z-index: 9999;">
                            <option value="Rotina">Rotina</option>
                            <option value="Retorno">Retorno</option>
                            <option value="Exames">Exames</option>
                            <option value="Urgência">Urgência</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Inicial</label>
                        <select name="cons_status" onmousedown="event.stopPropagation()" style="pointer-events: auto !important; user-select: auto !important; position: relative; z-index: 9999;">
                            <option value="Agendada">Agendada</option>
                            <option value="Confirmada">Confirmada</option>
                        </select>
                    </div>
                    <div style="display: flex; align-items: flex-end; justify-content: flex-end;">
                        <button type="submit" name="btn-add-cons" class="btn-primary">Confirmar Agendamento</button>
                    </div>
                </form>
            </div>

            <div class="card overflow-x">
                <h3 style="color: #2C82B5; margin-bottom: 15px;"><i class='bx bx-history'></i> Histórico de Consultas</h3>
                <table class="admin-table">
                    <thead><tr><th>Data/Hora</th><th>Paciente</th><th>Médico</th><th>Status</th><th style="text-align: center;">Ações</th></tr></thead>
                    <tbody>
                        <?php while($row = $res_consultas->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($row['data_hora_consul'])) ?></td>
                                <td><strong><?= $row['paciente'] ?></strong></td>
                                <td><?= $row['medico'] ?></td>
                                <td>
                                    <?php
                                        $corBg = "#f1f5f9"; $corTexto = "#475569";
                                        if($row['status_consulta'] == 'Concluída') { $corBg = "#dcfce7"; $corTexto = "#166534"; }
                                        elseif($row['status_consulta'] == 'Cancelada') { $corBg = "#fee2e2"; $corTexto = "#991b1b"; }
                                        elseif($row['status_consulta'] == 'Confirmada') { $corBg = "#dbeafe"; $corTexto = "#1e40af"; }
                                        elseif($row['status_consulta'] == 'Em Andamento') { $corBg = "#fef9c3"; $corTexto = "#854d0e"; }
                                    ?>
                                    <span style="background: <?= $corBg ?>; color: <?= $corTexto ?>; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                                        <?= $row['status_consulta'] ?>
                                    </span>
                                </td>
                                <td style="text-align: center; display: flex; justify-content: center; gap: 15px; border-bottom: none;">
                                    <button class="btn-action btn-edit" onclick="abrirModalCons(<?= $row['id_consulta'] ?>, '<?= date('Y-m-d\TH:i', strtotime($row['data_hora_consul'])) ?>', '<?= $row['tipo_consulta'] ?>', '<?= $row['status_consulta'] ?>')" title="Editar Consulta"><i class='bx bx-edit-alt'></i></button>
                                    <a href="?excluir_cons=<?= $row['id_consulta'] ?>" class="btn-action btn-del" onclick="return confirm('Cancelar e excluir permanentemente esta consulta?')" title="Apagar"><i class='bx bx-trash'></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modalEditUsu" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="fecharModal('modalEditUsu')">&times;</span>
            <h3 style="color: #2C3E50; margin-bottom: 20px;"><i class='bx bx-edit'></i> Editar Colaborador</h3>
            <form action="" method="POST">
                <input type="hidden" name="edit_id" id="mod_usu_id">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Nome Completo</label>
                    <input type="text" name="edit_nome" id="mod_usu_nome" required>
                </div>
                <button type="submit" name="btn-edit-user" class="btn-primary" style="width: 100%; justify-content: center;">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <div id="modalEditCons" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="fecharModal('modalEditCons')">&times;</span>
            <h3 style="color: #2C3E50; margin-bottom: 20px;"><i class='bx bx-edit'></i> Editar Consulta</h3>
            <form action="" method="POST">
                <input type="hidden" name="edit_cons_id" id="mod_cons_id">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Data e Hora</label>
                    <input type="datetime-local" name="edit_cons_data" id="mod_cons_data" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Tipo</label>
                    <select name="edit_cons_tipo" id="mod_cons_tipo" onmousedown="event.stopPropagation()" style="pointer-events: auto !important; user-select: auto !important; position: relative; z-index: 9999;">
                        <option value="Rotina">Rotina</option>
                        <option value="Retorno">Retorno</option>
                        <option value="Exames">Exames</option>
                        <option value="Urgência">Urgência</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Status</label>
                    <select name="edit_cons_status" id="mod_cons_status" onmousedown="event.stopPropagation()" style="pointer-events: auto !important; user-select: auto !important; position: relative; z-index: 9999;">
                        <option value="Agendada">Agendada</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="Em Andamento">Em Andamento</option>
                        <option value="Concluída">Concluída</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>
                <button type="submit" name="btn-edit-cons" class="btn-primary" style="width: 100%; justify-content: center;">Atualizar Consulta</button>
            </form>
        </div>
    </div>

    <script>
        function abrirAba(evt, abaId) {
            document.querySelectorAll('.tab-content').forEach(t => { t.style.display = 'none'; t.classList.remove('active'); });
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(abaId).style.display = 'block';
            document.getElementById(abaId).classList.add('active');
            evt.currentTarget.classList.add('active');
        }

        function mostrarCamposMedico() {
            var tipo = document.getElementById('reg_tipo').value;
            var divCampos = document.getElementById('campos_medico');
            var campos = ['reg_crm', 'reg_uf', 'reg_telefone', 'reg_cpf', 'reg_ubs'];

            if (tipo === 'Médico') {
                divCampos.style.display = 'block';
                campos.forEach(id => document.getElementById(id).required = true);
            } else {
                divCampos.style.display = 'none';
                campos.forEach(id => {
                    let el = document.getElementById(id);
                    el.required = false;
                    el.value = '';
                });
            }
        }

        function fecharModal(id) { document.getElementById(id).style.display = 'none'; }
        
        function abrirModalUsu(id, nome) {
            document.getElementById('mod_usu_id').value = id;
            document.getElementById('mod_usu_nome').value = nome;
            document.getElementById('modalEditUsu').style.display = 'flex';
        }

        function abrirModalCons(id, dataHora, tipo, status) {
            document.getElementById('mod_cons_id').value = id;
            document.getElementById('mod_cons_data').value = dataHora;
            document.getElementById('mod_cons_tipo').value = tipo;
            document.getElementById('mod_cons_status').value = status;
            document.getElementById('modalEditCons').style.display = 'flex';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) { event.target.style.display = "none"; }
        }
    </script>
</body>
</html>