<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Atendimento Médico - Bio Vita</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .medico-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 20px;
            height: calc(100vh - 100px);
        }

        /* Lista de Pacientes */
        .lista-espera {
            background: white;
            border-radius: 15px;
            padding: 20px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .paciente-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: 0.3s;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .paciente-item:hover { background: #f0f7ff; }
        .paciente-item.active { border-left: 5px solid #2C82B5; background: #f0f7ff; }

        /* Prontuário */
        .prontuario {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }

        textarea {
            width: 100%;
            flex-grow: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-family: inherit;
            resize: none;
            margin-top: 15px;
            font-size: 1rem;
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <header style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="color: #2C3E50; margin: 0;">Área de Atendimento</h1>
            <p style="color: #7f8c8d; margin: 0;">Dr. Carlos Eduardo | Clínico Geral</p>
        </div>
        <div class="date-badge" style="background: #2C82B5; color: white; padding: 8px 15px; border-radius: 20px; font-size: 0.9rem;">
            14 de Abril, 2026
        </div>
    </header>

    <div class="medico-grid">
        <aside class="lista-espera">
            <h3 style="font-size: 1rem; color: #34495e; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class='bx bx-list-ul'></i> Fila de Espera (4)
            </h3>
            
            <div class="paciente-item active">
                <strong style="display: block; color: #2C3E50;">João Silva Oliveira</strong>
                <span style="font-size: 0.8rem; color: #7f8c8d;">Chegada: 14:05 | Convênio: Bradesco</span>
            </div>

            <div class="paciente-item">
                <strong style="display: block; color: #2C3E50;">Maria Aparecida</strong>
                <span style="font-size: 0.8rem; color: #7f8c8d;">Chegada: 14:20 | Convênio: Particular</span>
            </div>
        </aside>

        <section class="prontuario">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f4f7f6; padding-bottom: 15px;">
                <div>
                    <h2 style="margin: 0; color: #2C82B5;">João Silva Oliveira</h2>
                    <p style="margin: 5px 0 0 0; color: #7f8c8d;">Masculino, 42 anos | Tipo Sanguíneo: O+</p>
                </div>
                <button class="btn-primary" style="background: #27ae60;">
                    <i class='bx bx-check-circle'></i> FINALIZAR CONSULTA
                </button>
            </div>

            <div style="margin-top: 20px;">
                <label style="font-weight: 600; color: #34495e;">Anamnese e Evolução Clínica:</label>
                <textarea placeholder="Descreva os sintomas, diagnóstico e conduta médica..."></textarea>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button class="export-btn"><i class='bx bx-plus'></i> Prescrever Medicamento</button>
                <button class="export-btn"><i class='bx bx-file'></i> Solicitar Exame</button>
            </div>
        </section>
    </div>
</main>

</body>
</html>