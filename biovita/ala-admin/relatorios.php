<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios - MedSystem</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div style="margin-bottom: 35px;">
        <h1 style="color: #2C3E50; font-size: 1.8rem; margin: 0; font-weight: 600;">Gerar Relatórios</h1>
        <p style="color: #7f8c8d; margin: 0;">Selecione os filtros abaixo para exportar os dados em PDF.</p>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="gerar_pdf_fake.php" method="GET">
            <h3 style="color: #2C82B5; margin-bottom: 20px; border-bottom: 2px solid #f0f5f9; padding-bottom: 10px;">
                <i class='bx bx-filter-alt'></i> Parâmetros do Relatório
            </h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Data Inicial *</label>
                    <input type="date" name="data_inicio" required>
                </div>
                <div class="form-group">
                    <label>Data Final *</label>
                    <input type="date" name="data_fim" required>
                </div>

                <div class="form-group">
                    <label>Tipo de Relatório</label>
                    <select name="tipo_relatorio" onmousedown="event.stopPropagation()" style="pointer-events: auto !important; user-select: auto !important; position: relative; z-index: 9999;">
                        <option value="pacientes">Lista de Pacientes Cadastrados</option>
                        <option value="atendimentos">Resumo de Atendimentos</option>
                        <option value="usuarios">Log de Atividades de Usuários</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Formato de Saída</label>
                    <select name="formato" onmousedown="event.stopPropagation()" style="pointer-events: auto !important; user-select: auto !important; position: relative; z-index: 9999;">
                        <option value="pdf">Documento PDF (.pdf)</option>
                        <option value="excel">Planilha Excel (.xlsx)</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" class="btn-primary" style="display: flex; align-items: center; gap: 10px;">
                    <i class='bx bxs-file-pdf'></i> GERAR E BAIXAR AGORA
                </button>
            </div>
        </form>
    </div>
</main>

</body>
</html>