<?php
session_start();
require_once '../conexao.php';

// Verificação de segurança padronizada para o painel Admin
if (!isset($_SESSION['tipo_usu']) || ($_SESSION['tipo_usu'] !== 'Administrador' && $_SESSION['tipo_usu'] !== 'Admin')) {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

$tipo = $_GET['tipo'] ?? 'consultas';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Central de Relatórios - MedSystem</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .page-header { margin-bottom: 30px; }
        .page-header h1 { color: #2C3E50; font-size: 1.8rem; margin: 0; font-weight: 600; }
        .page-header p { color: #7f8c8d; margin: 0; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .data-table th { background-color: #2C82B5; color: white; padding: 12px; text-align: left; border-radius: 4px 4px 0 0; }
        .data-table td { padding: 12px; border-bottom: 1px solid #eee; color: #475569; }
        .data-table tr:nth-child(even) { background-color: #f8fafc; }
        .data-table tr:hover { background-color: #f1f5f9; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1>Configurar Relatórios</h1>
        <p>Selecione a categoria de dados que deseja consultar e exportar.</p>
    </div>

    <div class="card" style="margin-bottom: 20px; display: flex; gap: 10px;">
        <a href="relatorios_gerais.php?tipo=consultas" class="btn-primary" style="<?= $tipo == 'consultas' ? 'background-color: #1e5c80;' : '' ?>">
            <i class='bx bx-calendar'></i> Relatório de Consultas
        </a>
        <a href="relatorios_gerais.php?tipo=pacientes" class="btn-primary" style="<?= $tipo == 'pacientes' ? 'background-color: #1e5c80;' : '' ?>">
            <i class='bx bx-user'></i> Relatório de Pacientes
        </a>
        <a href="relatorios_gerais.php?tipo=prescricoes" class="btn-primary" style="<?= $tipo == 'prescricoes' ? 'background-color: #1e5c80;' : '' ?>">
            <i class='bx bx-capsule'></i> Relatório de Prescrições
        </a>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f0f5f9; padding-bottom: 10px;">
            <h3 style="color: #2C3E50; margin: 0;">
                <i class='bx bx-list-ul'></i> Exibindo: <?= ucfirst($tipo) ?>
            </h3>
            
            <button onclick="exportarTabelaExcel()" class="btn-primary" style="background-color: #27ae60; padding: 8px 15px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                <i class='bx bx-spreadsheet'></i> Exportar para Excel
            </button>
        </div>
        
        <table class="data-table" id="tabelaRelatorio">
            <thead>
                <tr>
                    <?php if($tipo == 'consultas'): ?>
                        <th>Paciente</th><th>Data e Hora</th><th>Status</th>
                    <?php elseif($tipo == 'pacientes'): ?>
                        <th>Nome</th><th>Convênio</th><th>Telefone</th>
                    <?php else: ?>
                        <th>Paciente</th><th>Medicamento</th><th>Dosagem</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Preenchimento automático com base no tipo selecionado
                if ($tipo == 'consultas') {
                    $sql = "SELECT p.nome, c.data_hora_consul, c.status_consulta 
                            FROM consultas c 
                            JOIN registro_usuario p ON c.id_paciente = p.id 
                            ORDER BY c.data_hora_consul DESC LIMIT 100";
                    $res = $mysqli->query($sql);
                    
                    if ($res && $res->num_rows > 0) {
                        while($row = $res->fetch_assoc()) {
                            $dataFmt = date('d/m/Y H:i', strtotime($row['data_hora_consul']));
                            echo "<tr>
                                    <td><strong>{$row['nome']}</strong></td>
                                    <td>{$dataFmt}</td>
                                    <td>{$row['status_consulta']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; padding: 30px;'>Nenhuma consulta encontrada.</td></tr>";
                    }
                } 
                elseif ($tipo == 'pacientes') {
                    $sql = "SELECT nome, convenio_usu, telefone FROM registro_usuario ORDER BY nome ASC LIMIT 100";
                    $res = $mysqli->query($sql);
                    
                    if ($res && $res->num_rows > 0) {
                        while($row = $res->fetch_assoc()) {
                            $convenio = $row['convenio_usu'] ? $row['convenio_usu'] : 'Particular / Não Informado';
                            echo "<tr>
                                    <td><strong>{$row['nome']}</strong></td>
                                    <td>{$convenio}</td>
                                    <td>{$row['telefone']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; padding: 30px;'>Nenhum paciente cadastrado.</td></tr>";
                    }
                }
                elseif ($tipo == 'prescricoes') {
                    $sql = "SELECT p.medicamento, p.dosagem, u.nome as paciente 
                            FROM prescricoes p 
                            JOIN consultas c ON p.id_consulta = c.id_consulta 
                            JOIN registro_usuario u ON c.id_paciente = u.id 
                            ORDER BY p.data_prescricao DESC LIMIT 100";
                    $res = $mysqli->query($sql);
                    
                    if ($res && $res->num_rows > 0) {
                        while($row = $res->fetch_assoc()) {
                            echo "<tr>
                                    <td><strong>{$row['paciente']}</strong></td>
                                    <td>{$row['medicamento']}</td>
                                    <td>{$row['dosagem']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; padding: 30px;'>Nenhuma prescrição encontrada no sistema.</td></tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    function exportarTabelaExcel() {
        // Seleciona a tabela pelo ID
        let tabela = document.getElementById("tabelaRelatorio");
        let htmlTabela = tabela.outerHTML;

        // Monta a estrutura HTML suportada pelo Excel (com meta tag para acentuação UTF-8)
        let htmlExcel = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="utf-8">
                <style>
                    table { border-collapse: collapse; width: 100%; }
                    th { background-color: #2C82B5; color: white; font-weight: bold; border: 1px solid #ddd; padding: 8px; }
                    td { border: 1px solid #ddd; padding: 8px; }
                </style>
            </head>
            <body>
                ${htmlTabela}
            </body>
            </html>
        `;

        // Cria um Blob com o conteúdo e força o download
        let blob = new Blob([htmlExcel], { type: 'application/vnd.ms-excel' });
        let url = URL.createObjectURL(blob);
        
        let link = document.createElement("a");
        link.href = url;
        link.download = "Relatorio_<?= ucfirst($tipo) ?>_BioVita.xls";
        
        document.body.appendChild(link);
        link.click();
        
        // Limpeza
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
</script>

</body>
</html>