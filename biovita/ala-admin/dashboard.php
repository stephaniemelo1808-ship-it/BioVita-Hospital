<?php
session_start();

if (!isset($_SESSION['tipo_usu']) || ($_SESSION['tipo_usu'] !== 'Administrador' && $_SESSION['tipo_usu'] !== 'Admin')) {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

require_once '../conexao.php';

$total_pacientes = 0;
$resultado_pacientes = $mysqli->query("SELECT COUNT(*) as total FROM registro_usuario");
if ($resultado_pacientes) {
    $row_pacientes = $resultado_pacientes->fetch_assoc();
    $total_pacientes = $row_pacientes['total'];
}

$total_medicos = 0;
$resultado_medicos = $mysqli->query("SELECT COUNT(*) as total FROM login WHERE tipo_usu = 'Médico'");
if ($resultado_medicos) {
    $row_medicos = $resultado_medicos->fetch_assoc();
    $total_medicos = $row_medicos['total'];
}

$total_consultas = 0;
$resultado_consultas = $mysqli->query("SELECT COUNT(*) as total FROM consultas");
if ($resultado_consultas) {
    $row_consultas = $resultado_consultas->fetch_assoc();
    $total_consultas = $row_consultas['total'];
}

$total_novos = 0;
$resultado_novos = $mysqli->query("SELECT COUNT(*) as total FROM consultas WHERE DATE(data_hora_consul) >= CURDATE()");
if ($resultado_novos) {
    $row_novos = $resultado_novos->fetch_assoc();
    $total_novos = $row_novos['total'];
}

$usuarios_ativos = [];
$id_logado = $_SESSION['id'];
$resultado_ativos = $mysqli->query("SELECT nome_usu, tipo_usu FROM login WHERE id_log != '$id_logado' LIMIT 3");
if ($resultado_ativos) {
    while($row = $resultado_ativos->fetch_assoc()) {
        $usuarios_ativos[] = $row;
    }
}

$dados_semana = [0, 0, 0, 0, 0, 0, 0];

$sql_grafico = "SELECT WEEKDAY(data_hora_consul) as dia_semana, COUNT(*) as total 
                FROM consultas 
                WHERE data_hora_consul IS NOT NULL 
                GROUP BY WEEKDAY(data_hora_consul)";

$resultado_grafico = $mysqli->query($sql_grafico);

if ($resultado_grafico) {
    while ($row = $resultado_grafico->fetch_assoc()) {
        $dia_index = (int)$row['dia_semana'];
        if ($dia_index >= 0 && $dia_index <= 6) {
            $dados_semana[$dia_index] = $row['total'];
        }
    }
}

$dados_grafico_js = implode(', ', $dados_semana);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MedSystem Bio Vita</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-bottom: 4px solid #2C82B5;
        }

        .stat-card.purple { border-color: #9b59b6; }
        .stat-card.green { border-color: #27ae60; }
        .stat-card.yellow { border-color: #f1c40f; }

        .stat-info h3 { font-size: 0.8rem; color: #7f8c8d; text-transform: uppercase; margin: 0; }
        .stat-info p { font-size: 1.6rem; font-weight: 700; color: #2C3E50; margin: 5px 0 0 0; }
        .stat-icon { font-size: 2rem; color: #2C82B5; opacity: 0.2; }

        .layout-flex {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .chart-container { flex: 2; min-width: 400px; }
        .actions-container { flex: 1; min-width: 280px; }

        .export-btn {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 6px 15px;
            border-radius: 8px;
            cursor: pointer;
            color: #2C3E50;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .export-btn:hover {
            background: #27ae60;
            color: white;
            border-color: #27ae60;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.2);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .bg-online { background: #ecfaf2; color: #27ae60; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div style="margin-bottom: 30px;">
        <h1 style="color: #2C3E50; font-size: 1.8rem; margin: 0; font-weight: 600;">Painel de Controle</h1>
        <p style="color: #7f8c8d; margin: 0;">Resumo analítico e exportação de indicadores operacionais.</p>
    </div>

    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>Pacientes</h3>
                <p><?= number_format($total_pacientes, 0, ',', '.') ?></p>
            </div>
            <i class='bx bx-user stat-icon'></i>
        </div>
        
        <div class="stat-card purple">
            <div class="stat-info">
                <h3>Médicos</h3>
                <p><?= $total_medicos ?></p>
            </div>
            <i class='bx bx-plus-medical stat-icon'></i>
        </div>
        
        <div class="stat-card green">
            <div class="stat-info">
                <h3>Consultas</h3>
                <p><?= $total_consultas ?></p>
            </div>
            <i class='bx bx-calendar-check stat-icon'></i>
        </div>
        
        <div class="stat-card yellow">
            <div class="stat-info">
                <h3>Novos Agendamentos</h3>
                <p><?= str_pad($total_novos, 2, '0', STR_PAD_LEFT) ?></p>
            </div>
            <i class='bx bx-trending-up stat-icon'></i>
        </div>
    </div>

    <div class="layout-flex">
        <div class="card chart-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3 style="color: #2C3E50; font-size: 1.1rem; margin: 0;">Fluxo Semanal de Atendimento</h3>
                
                <button onclick="exportarParaExcel()" class="export-btn">
                    <i class='bx bx-spreadsheet'></i> EXPORTAR PARA EXCEL
                </button>
            </div>

            <div style= "height: 300px;">
                <canvas id="graficoFluxo"></canvas>
            </div>
        </div>

        <div class="actions-container">
            <div class="card" style="margin-bottom: 20px;">
                <h3 style="margin-bottom: 15px; color: #2C3E50; font-size: 1rem;">Módulo de Gestão</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="relatorios_gerais.php?tipo=consultas" class="btn-primary" style="text-decoration: none; text-align: center; background: #34495e;">
                        <i class='bx bx-file'></i> Configurar Relatórios
                    </a>
                    
                    <a href="usuarios.php" class="btn-primary" style="text-decoration: none; text-align: center;">
                        <i class='bx bx-user-plus'></i> Novo Funcionário
                    </a>
                </div>
            </div>

            <div class="card">
                <h3 style="margin-bottom: 15px; color: #2C3E50; font-size: 1rem;">Sessões Ativas</h3>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.85rem;">
                    
                    <li style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;">
                        <span>Você (<?= $_SESSION['usuario'] ?>)</span>
                        <span class="status-badge bg-online">Online</span>
                    </li>

                    <?php foreach($usuarios_ativos as $user): ?>
                    <li style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;">
                        <span><?= $user['nome_usu'] ?> (<?= $user['tipo_usu'] ?>)</span>
                        <span class="status-badge bg-online">Online</span>
                    </li>
                    <?php endforeach; ?>
                    
                </ul>
            </div>
        </div>
    </div>
</main>

<script>
    const labelsSemana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
    const dadosAtendimento = [<?= $dados_grafico_js ?>];

    const ctx = document.getElementById('graficoFluxo').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(44, 130, 181, 0.3)');
    gradient.addColorStop(1, 'rgba(44, 130, 181, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelsSemana,
            datasets: [{
                label: 'Atendimentos',
                data: dadosAtendimento,
                borderColor: '#2C82B5',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#2C82B5'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f5f5f5' } },
                x: { grid: { display: false } }
            }
        }
    });

    function exportarParaExcel() {
        const dadosDetalhes = [
            <?php
            $sql_detalhe = "SELECT c.data_hora_consul, p.nome as paciente, l.nome_usu as medico, c.tipo_consulta 
                            FROM consultas c 
                            JOIN registro_usuario p ON c.id_paciente = p.id 
                            JOIN login l ON c.id_log_medico = l.id_log 
                            ORDER BY c.data_hora_consul DESC LIMIT 100";
            $res = $mysqli->query($sql_detalhe);
            while($row = $res->fetch_assoc()) {
                $data = date('d/m/Y H:i', strtotime($row['data_hora_consul']));
                echo "{data:'$data', pac:'".addslashes($row['paciente'])."', med:'".addslashes($row['medico'])."', tipo:'".$row['tipo_consulta']."'},";
            }
            ?>
        ];

        let htmlTable = `
            <meta charset="utf-8">
            <table border="1">
                <tr style="background-color: #2C82B5; color: white; font-weight: bold;">
                    <th>Data/Hora</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Especialidade</th>
                </tr>
        `;

        dadosDetalhes.forEach((item) => {
            htmlTable += `
                <tr>
                    <td>${item.data}</td>
                    <td>${item.pac}</td>
                    <td>${item.med}</td>
                    <td>${item.tipo}</td>
                </tr>
            `;
        });

        htmlTable += "</table>";

        const blob = new Blob([htmlTable], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", "consultas_detalhadas_biovita.xls");
        document.body.appendChild(link);

        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
</script>

</body>
</html>