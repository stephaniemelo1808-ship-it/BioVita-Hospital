<?php
require_once __DIR__ . '/mock-medico.php';
$paciente = $mockPacientes[1];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receituário - MedSystem Bio Vita</title>
    <link rel="stylesheet" href="../medico.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<?php include __DIR__ . '/sidebar-medico.php'; ?>

    <main class="main-content" id="mainContent">
        <div class="page-header">
            <div>
                <h1>Receituário</h1>
                <p>Documento médico com prescrições e orientações de uso.</p>
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
                        <h2>MedSystem Bio Vita</h2>
                        <p>Receituário Médico</p>
                    </div>
                </div>
                <div class="document-meta">
                    <span>Data: 14/05/2026</span>
                    <span>Médico: Dr. Carlos Eduardo Mendes</span>
                </div>
            </div>

            <div class="document-section document-info-grid">
                <div>
                    <strong>Paciente</strong>
                    <p><?= $paciente['nome'] ?></p>
                </div>
                <div>
                    <strong>Convênio</strong>
                    <p><?= $paciente['convenio'] ?></p>
                </div>
                <div>
                    <strong>Telefone</strong>
                    <p><?= $paciente['telefone'] ?></p>
                </div>
            </div>

            <div class="document-section">
                <h3>Prescrições</h3>
                <ul class="prescricao-list">
                    <li><strong>Losartana 50mg</strong> – 1 comprimido ao dia</li>
                    <li><strong>Paracetamol 750mg</strong> – 1 comprimido a cada 8h, se necessário</li>
                    <li><strong>Omeprazol 20mg</strong> – 1 cápsula em jejum</li>
                </ul>
            </div>

            <div class="document-section">
                <h3>Orientações</h3>
                <p>Tomar os medicamentos conforme prescrição. Manter hidratação adequada, repouso e retorno em 30 dias ou antes, caso haja piora.</p>
            </div>

            <div class="document-footer">
                <div>
                    <span>Assinatura do Médico</span>
                    <p>Dr. Carlos Eduardo Mendes</p>
                </div>
                <div class="document-badge">Receituário válido por 30 dias</div>
            </div>
        </div>
             <button class="btn-sm btn-outline" onclick="window.print()">
                            <i class='bx bx-printer'></i> Imprimir
                        </button>
                <button class="btn-sm btn-outline" onclick="showToast('Download iniciado!', 'success')">
                            <i class='bx bx-download'></i> Baixar
                        </button>
    </main>
    <script src="../medico.js"></script>
</body>
</html>
