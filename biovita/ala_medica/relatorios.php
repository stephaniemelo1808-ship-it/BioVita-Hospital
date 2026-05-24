<?php
session_start();

if (!isset($_SESSION['tipo_usu']) || $_SESSION['tipo_usu'] !== 'Médico') {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

require_once '../conexao.php';
$id_login_medico = $_SESSION['id'];

if (isset($_GET['excluir_prescricao'])) {
    $id_excluir = (int)$_GET['excluir_prescricao'];
    
    $mysqli->query("DELETE FROM prescricoes WHERE id = $id_excluir");
    
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        header('Content-Type: application/json');
        echo json_encode(['sucesso' => true]);
        exit();
    }
    
    $_SESSION['alerta'] = "Medicamento removido com sucesso!";
    header("Location: relatorios.php?t=" . time());
    exit();
}

$sql_relatorios = "SELECT c.*, p.nome as paciente,
                   (SELECT GROUP_CONCAT(CONCAT(id, '::', medicamento, ' (', dosagem, ')') SEPARATOR '||') 
                    FROM prescricoes 
                    WHERE id_consulta = c.id_consulta) as medicamentos_prescritos
                   FROM consultas c
                   JOIN registro_usuario p ON c.id_paciente = p.id
                   WHERE c.id_log_medico = '$id_login_medico'
                   ORDER BY c.data_hora_consul DESC";

$resultado = $mysqli->query($sql_relatorios);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios Médicos - Bio Vita</title>
    <link rel="stylesheet" href="medico.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .relatorio-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }
        .relatorio-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-color: #cbd5e1;
        }
        
        .relatorio-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer; 
            user-select: none;
        }
        
        .paciente-nome {
            margin: 0;
            color: #2C82B5;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .toggle-icon {
            font-size: 1.3rem;
            color: #94a3b8;
            transition: transform 0.3s ease;
        }
        
        .badge-status {
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .relatorio-body {
            display: none; 
            margin-top: 20px;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
        
        .relatorio-body p {
            margin: 0 0 10px 0;
            color: #475569;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .relatorio-body p i {
            color: #64748b;
            font-size: 1.1rem;
        }
        .box-observacoes {
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            border-left: 4px solid #e2e8f0;
        }
        .box-observacoes.com-obs {
            border-left-color: #2C82B5;
        }
        .btn-voltar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border: 1px solid #cbd5e1;
            background: white;
            color: #475569;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.2s;
        }
        .btn-voltar:hover {
            background: #f1f5f9;
            color: #2C82B5;
            border-color: #2C82B5;
        }

        .med-tag {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s ease;
        }
        .btn-del-med {
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1.1rem;
            opacity: 0.7;
            transition: 0.2s;
            cursor: pointer;
            border: none;
            background: none;
        }
        .btn-del-med:hover {
            opacity: 1;
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar-medico.php'; ?>

    <?php if (isset($_SESSION['alerta'])): ?>
        <div id="phpToast" style="position:fixed; top:20px; right:20px; background-color:#2ecc71; color:white; padding:15px 25px; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.1); z-index:9999;">
            <i class='bx bx-check-circle'></i> <?= $_SESSION['alerta'] ?>
        </div>
        <script>setTimeout(() => document.getElementById('phpToast').style.display = 'none', 3500);</script>
        <?php unset($_SESSION['alerta']); ?>
    <?php endif; ?>

    <main class="main-content" id="mainContent">

        <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
            <div>
                <h1 style="color: #2C3E50; margin: 0 0 5px 0;">Relatórios</h1>
                <p style="color: #7f8c8d; margin: 0;">Histórico de atendimentos, procedimentos e evolução clínica.</p>
            </div>
            <div class="date-badge" style="background: #2C82B5; color: white; padding: 8px 15px; border-radius: 20px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                <i class='bx bx-calendar'></i>
                <span><?= date('d/m/Y') ?></span> 
            </div>
        </div>

        <div class="card" style="background: transparent; box-shadow: none; padding: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #334155; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class='bx bx-history' style="color: #2C82B5;"></i> Histórico de Atendimentos
                </h3>
                <a href="medico.php" class="btn-voltar">
                    <i class='bx bx-arrow-back'></i> Voltar ao Painel
                </a>
            </div>

            <?php if($resultado->num_rows > 0): ?>
                <?php while($row = $resultado->fetch_assoc()): 
                    $status = strtoupper($row['status_consulta']);
                    $corStatus = '#64748b';
                    if($status == 'AGENDADA') $corStatus = '#f39c12';
                    elseif($status == 'CONFIRMADA') $corStatus = '#f1c40f';
                    elseif($status == 'CONCLUÍDA') $corStatus = '#27ae60';
                    elseif($status == 'CANCELADA') $corStatus = '#e74c3c';
                    elseif($status == 'EM ANDAMENTO') $corStatus = '#3498db';
                ?>
                <div class="relatorio-card">
                    <div class="relatorio-header" onclick="toggleConsulta(this)">
                        <h3 class="paciente-nome">
                            <i class='bx bx-user'></i> 
                            <?= htmlspecialchars($row['paciente']) ?>
                            <i class='bx bx-chevron-down toggle-icon'></i>
                        </h3>
                        <span class="badge-status" style="color: <?= $corStatus ?>;">
                            <?= $status ?>
                        </span>
                    </div>

                    <div class="relatorio-body">
                        <p><i class='bx bx-calendar-event'></i> <strong>Data/Hora:</strong> <?= date('d/m/Y H:i', strtotime($row['data_hora_consul'])) ?></p>
                        <p><i class='bx bx-plus-medical'></i> <strong>Procedimentos:</strong> <?= htmlspecialchars($row['tipo_consulta']) ?></p>
                        
                        <?php if(!empty($row['medicamentos_prescritos'])): ?>
                            <div style="margin-top: 15px; margin-bottom: 15px;">
                                <p style="color: #475569; font-weight: 600; margin-bottom: 8px;">
                                    <i class='bx bx-capsule' style="color: #27ae60;"></i> Medicamentos Prescritos:
                                </p>
                                <div style="display: flex; flex-wrap: wrap; gap: 10px; padding-left: 28px;">
                                    <?php 
                                    $meds_list = explode('||', $row['medicamentos_prescritos']);
                                    foreach($meds_list as $med_item): 
                                        $partes = explode('::', $med_item);
                                        if(count($partes) < 2) continue; 
                                        
                                        $id_prescricao = $partes[0];
                                        $nome_prescricao = $partes[1];
                                    ?>
                                        <span class="med-tag">
                                            <?= htmlspecialchars($nome_prescricao) ?>
                                            <button type="button" 
                                               class="btn-del-med" 
                                               onclick="excluirMedicamento(event, <?= $id_prescricao ?>, this, '<?= htmlspecialchars($nome_prescricao, ENT_QUOTES) ?>')" 
                                               title="Excluir este medicamento">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="box-observacoes <?= !empty($row['observacoes']) ? 'com-obs' : '' ?>">
                            <p style="margin: 0 0 5px 0; font-weight: 600; color: #334155;">
                                <i class='bx bx-edit-alt'></i> Observações / Evolução:
                            </p>
                            <p style="margin: 0; color: <?= !empty($row['observacoes']) ? '#475569' : '#94a3b8' ?>; font-size: 0.9rem;">
                                <?= !empty($row['observacoes']) ? nl2br(htmlspecialchars($row['observacoes'])) : 'Nenhuma observação ou evolução registrada para este atendimento.' ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="relatorio-card" style="text-align: center; padding: 40px;">
                    <i class='bx bx-file-blank' style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <h3 style="color: #475569; margin: 0 0 10px 0;">Nenhum atendimento registado</h3>
                    <p style="color: #94a3b8; margin: 0;">O seu histórico de consultas e relatórios aparecerá aqui.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <script>
        function toggleConsulta(headerElement) {
            const cardBody = headerElement.nextElementSibling;
            const toggleIcon = headerElement.querySelector('.toggle-icon');
            
            if (cardBody.style.display === 'block') {
                cardBody.style.display = 'none';
                if(toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
            } else {
                cardBody.style.display = 'block';
                if(toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
            }
        }

        function excluirMedicamento(event, idPrescricao, elementoBotao, nomeMedicamento) {
            event.preventDefault(); 
            event.stopPropagation(); 
            
            if (confirm('Tem certeza que deseja remover [' + nomeMedicamento + '] desta receita?')) {
                fetch('relatorios.php?excluir_prescricao=' + idPrescricao + '&ajax=1')
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        const tagMedicamento = elementoBotao.closest('.med-tag');
                        if (tagMedicamento) {
                            tagMedicamento.style.opacity = '0';
                            tagMedicamento.style.transform = 'scale(0.8)';
                            setTimeout(() => tagMedicamento.remove(), 300);
                        }
                        
                        mostrarToastJS('Medicamento removido com sucesso!');
                    }
                })
                .catch(error => console.error('Erro ao excluir medicamento:', error));
            }
        }

        function mostrarToastJS(mensagem) {
            const toast = document.createElement('div');
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.backgroundColor = '#2ecc71';
            toast.style.color = 'white';
            toast.style.padding = '15px 25px';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
            toast.style.zIndex = '9999';
            toast.style.transition = 'opacity 0.3s ease';
            toast.innerHTML = "<i class='bx bx-check-circle'></i> " + mensagem;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }
    </script>
</body>
</html>