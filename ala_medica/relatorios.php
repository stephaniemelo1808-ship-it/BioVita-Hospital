<?php
require_once 'includes/mock-medico.php';
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
                <span>14 de Maio, 2026</span>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap;">
                <h3 class="card-title"><i class='bx bx-history'></i> Histórico de Atendimentos</h3>
                <a href="medico.php" class="btn-outline" style="display: inline-flex; align-items: center; gap: 8px;"> <i class='bx bx-arrow-back'></i> Voltar ao Painel</a>
            </div>
            <div class="relatorios-list">
                <?php foreach($mockRelatorios as $rel): 
                    $paciente = $mockPacientes[$rel['id_paciente']];
                    $dataFormato = date('d/m/Y H:i', strtotime($rel['data_hora']));
                    $statusClass = $rel['status'] == 'Concluída' ? 'status-finalizado' : 'status-pendente';
                ?>
                <div class="relatorio-card">
                    <div class="relatorio-header">
                        <h4><i class='bx bx-user'></i> <?= $paciente['nome'] ?></h4>
                        <span class="status-badge <?= $statusClass ?>"><?= $rel['status'] ?></span>
                    </div>
                    <div class="relatorio-body">
                        <div class="relatorio-info">
                            <p><strong><i class='bx bx-calendar'></i> Data/Hora:</strong> <?= $dataFormato ?></p>
                            <p><strong><i class='bx bx-plus-medical'></i> Procedimentos:</strong> <?= $rel['procedimentos'] ?></p>
                        </div>
                        <div class="relatorio-obs">
                            <strong><i class='bx bx-edit-alt'></i> Observações / Evolução:</strong>
                            <p><?= $rel['observacoes'] ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script src="medico.js"></script>
</body>
</html>
