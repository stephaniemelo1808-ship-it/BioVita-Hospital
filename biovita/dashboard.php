<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MedSystem Bio Vita</title>
    <link rel="stylesheet" href="../css/style.cs">
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

        /* Estilo do Botao de Exportaçao d Dados */
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
            <div class="stat-info"><h3>Pacientes</h3><p>1,284</p></div>
            <i class='bx bx-user stat-icon'></i>
        </div>
        <div class="stat-card purple">
            <div class="stat-info"><h3>Médicos</h3><p>14</p></div>
            <i class='bx bx-plus-medical stat-icon'></i>
        </div>
        <div class="stat-card green">
            <div class="stat-info"><h3>Consultas</h3><p>42</p></div>
            <i class='bx bx-calendar-check stat-icon'></i>
        </div>
        <div class="stat-card yellow">
            <div class="stat-info"><h3>Novos</h3><p>08</p></div>
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

            <div style="height: 300px;">
                <canvas id="graficoFluxo"></canvas>
            </div>
        </div>

        <div class="actions-container">
            <div class="card" style="margin-bottom: 20px;">
                <h3 style="margin-bottom: 15px; color: #2C3E50; font-size: 1rem;">Módulo de Gestão</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="relatorios.php" class="btn-primary" style="text-decoration: none; text-align: center; background: #34495e;">
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
                        <span>Dr. Carlos Eduardo</span>
                        <span class="status-badge bg-online">Online</span>
                    </li>
                    <li style="display: flex; justify-content: space-between; padding: 8px 0;">
                        <span>Ana Souza (Recepção)</span>
                        <span class="status-badge bg-online">Online</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script>
    // 1. DADOS DO GRÁFICO
    const labelsSemana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
    const dadosAtendimento = [45, 58, 42, 65, 55, 22, 15];

    // 2. VISUAL DO GRÁFICO
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

    // 3. FUNÇÃO DE EXTRAÇÃO DE DADOS (CSV/EXCEL)
    function exportarParaExcel() {
        // o conteúdo do CSV
        let csvContent = "data:text/csv;charset=utf-8,\uFEFF"; // \uFEFF ajuda o Excel com acentos
        csvContent += "Dia da Semana;Total de Atendimentos\n";

        labelsSemana.forEach((dia, index) => {
            csvContent += `${dia};${dadosAtendimento[index]}\n`;
        });

        // Link de download temporário
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "fluxo_atendimento_biovita.csv");
        document.body.appendChild(link);

        link.click(); // Dispara o download
        document.body.removeChild(link); // Limpa a memória 
    }
</script>

</body>
</html>