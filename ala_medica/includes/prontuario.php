<?php
require_once __DIR__ . '/mock-medico.php';
$paciente = $mockPacientes[1];
$idade = calculaIdade($paciente['dt_nasc']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prontuário - MedSystem Bio Vita</title>
    <link rel="stylesheet" href="../medico.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<?php include __DIR__ . '/sidebar-medico.php'; ?>

    <main class="main-content" id="mainContent">
        <div class="page-header">
            <div>
                <h1>Prontuário</h1>
                <p>Registro médico com histórico, exames e plano terapêutico.</p>
            </div>
            <div>
                <a href="../medico.php" class="btn-outline" style="display: inline-flex; align-items: center; gap: 8px;"><i class='bx bx-arrow-back'></i> Voltar</a>
            </div>
        </div>

        <div class="card document-card">
            <div class="document-header">
                <div class="document-logo-box">
                    <img src="img/logo_biovita.png" alt="Bio Vita" class="document-logo">
                    <div>
                        <h2>Prontuário do Paciente</h2>
                        <p>Informações clínicas e evolução do tratamento.</p>
                    </div>
                </div>
                <div class="document-meta">
                    <span>Paciente: <?= $paciente['nome'] ?></span>
                    <span>Idade: <?= $idade ?> anos</span>
                </div>
            </div>

            <div class="document-section document-info-grid">
                <div>
                    <strong>Convênio</strong>
                    <p><?= $paciente['convenio'] ?></p>
                </div>
                <div>
                    <strong>Tipo Sanguíneo</strong>
                    <p><?= $paciente['tipo_sanguineo'] ?></p>
                </div>
                <div>
                    <strong>Contato</strong>
                    <p><?= $paciente['telefone'] ?></p>
                </div>
            </div>

            <div class="document-section">
                <h3>Histórico Clínico</h3>
                <p>Paciente com quadro de hipertensão arterial controlada. Manutenção de acompanhamento mensal e avaliação de sinais vitais a cada consulta.</p>
            </div>

            <div class="document-section">
                <h3>Exames Recentes</h3>
                <ul class="prescricao-list">
                    <li>Pressão arterial: 130/80 mmHg</li>
                    <li>Glicemia capilar: 98 mg/dL</li>
                    <li>Colesterol total: 185 mg/dL</li>
                </ul>
            </div>

            <div class="document-section">
                <h3>Prescrições Atuais</h3>
                <ul class="prescricao-list">
                    <li>Losartana 50mg – 1 comprimido ao dia</li>
                    <li>Omeprazol 20mg – 1 cápsula em jejum</li>
                </ul>
            </div>

            <div class="document-section">
                <h3>Plano Terapêutico</h3>
                <p>Manter dieta balanceada, atividades físicas regulares e retorno em 30 dias. Caso haja cefaleia intensa ou dor torácica, procurar atendimento imediatamente.</p>
            </div>

            <div class="document-footer">
                <div>
                    <span>Assinatura do Médico</span>
                    <p>Dr. Carlos Eduardo Mendes</p>
                </div>
                <div class="document-badge">Última atualização: 14/05/2026</div>
            </div>
        </div>
    </main>

    <script src="../medico.js"></script>
</body>
</html>
