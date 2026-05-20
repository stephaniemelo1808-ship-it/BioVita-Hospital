<?php
session_start();

// 1. TRAVA DE SEGURANÇA (Apenas Médicos)
if (!isset($_SESSION['tipo_usu']) || $_SESSION['tipo_usu'] !== 'Médico') {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

// 2. CONEXÃO COM O BANCO DE DADOS
require_once '../conexao.php';

$id_login_medico = $_SESSION['id']; // Pega o ID do médico logado

// 3. BUSCA O HISTÓRICO DE CONSULTAS (Relatórios)
// Faz um JOIN para buscar os dados da consulta E o nome do paciente na mesma query
$sql_relatorios = "SELECT c.data_hora_consul, c.status_consulta, c.tipo_consulta, c.observacoes, p.nome as nome_paciente 
                   FROM consultas c 
                   JOIN registro_usuario p ON c.id_paciente = p.id 
                   WHERE c.id_log_medico = '$id_login_medico' 
                   ORDER BY c.data_hora_consul DESC"; // Ordena das mais recentes para as mais antigas

$resultado_relatorios = $mysqli->query($sql_relatorios);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - MedSystem Bio Vita</title>
    <link rel="stylesheet" href="medico.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<?php include('includes/sidebar-medico.php'); ?>

    <main class="main-content" id="mainContent">
        <div class="page-header">
            <div>
                <h1>Relatórios</h1>
                <p>Histórico de atendimentos, procedimentos e evolução clínica.</p>
            </div>
            <div class="date-badge">
                <i class='bx bx-calendar'></i>
                <span><?= date('d/m/Y') ?></span> </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap;">
                <h3 class="card-title"><i class='bx bx-history'></i> Histórico de Atendimentos</h3>
                <a href="medico.php" class="btn-outline" style="display: inline-flex; align-items: center; gap: 8px;"> <i class='bx bx-arrow-back'></i> Voltar ao Painel</a>
            </div>
            <div class="relatorios-list">
                
                <?php 
                if ($resultado_relatorios && $resultado_relatorios->num_rows > 0):
                    while($rel = $resultado_relatorios->fetch_assoc()): 
                        // Formata a data para o padrão brasileiro
                        $dataFormato = date('d/m/Y H:i', strtotime($rel['data_hora_consul']));
                        
                        // Define a cor da badge baseado no status real da consulta
                        $statusClass = ($rel['status_consulta'] == 'Concluída') ? 'status-confirmada' : 'status-aguardando';
                        
                        // Tratamento caso a observação ou tipo estejam vazios no banco
                        $observacoes = !empty($rel['observacoes']) ? $rel['observacoes'] : "Nenhuma observação ou evolução registrada para este atendimento.";
                        $procedimentos = !empty($rel['tipo_consulta']) ? $rel['tipo_consulta'] : "Consulta de Rotina";
                ?>
                <div class="relatorio-card">
                    <div class="relatorio-header">
                        <h4><i class='bx bx-user'></i> <?= $rel['nome_paciente'] ?></h4>
                        <span class="status-badge <?= $statusClass ?>"><?= $rel['status_consulta'] ?></span>
                    </div>
                    <div class="relatorio-body">
                        <div class="relatorio-info">
                            <p><strong><i class='bx bx-calendar'></i> Data/Hora:</strong> <?= $dataFormato ?></p>
                            <p><strong><i class='bx bx-plus-medical'></i> Procedimentos:</strong> <?= $procedimentos ?></p>
                        </div>
                        <div class="relatorio-obs">
                            <strong><i class='bx bx-edit-alt'></i> Observações / Evolução:</strong>
                            <p><?= nl2br($observacoes) ?></p> </div>
                    </div>
                </div>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <div style="text-align: center; padding: 40px; color: #7f8c8d;">
                        <i class='bx bx-file-blank' style="font-size: 3rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                        <p>Nenhum relatório ou histórico de atendimento encontrado.</p>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </main>

    <script src="medico.js"></script>
</body>
</html>