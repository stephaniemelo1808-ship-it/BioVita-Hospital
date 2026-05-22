<?php
session_start();

if (!isset($_SESSION['tipo_usu'])) {
    die("Acesso negado. Por favor, faça login.");
}

require_once '../conexao.php';

$data_inicio = $mysqli->real_escape_string($_GET['data_inicio'] ?? date('Y-m-01'));
$data_fim = $mysqli->real_escape_string($_GET['data_fim'] ?? date('Y-m-t'));
$tipo = $mysqli->real_escape_string($_GET['tipo_relatorio'] ?? 'atendimentos');
$formato = $mysqli->real_escape_string($_GET['formato'] ?? 'pdf');

$titulo_relatorio = "";
$colunas = "";
$linhas = "";

$data_inicio_sql = $data_inicio . ' 00:00:00';
$data_fim_sql = $data_fim . ' 23:59:59';

if ($tipo == 'atendimentos') {
    $titulo_relatorio = "Resumo de Atendimentos";
    $colunas = "<th>Data/Hora</th><th>Paciente</th><th>Médico</th><th>Especialidade</th><th>Status</th>";
    
    $sql = "SELECT c.*, p.nome as paciente, l.nome_usu as medico 
            FROM consultas c 
            JOIN registro_usuario p ON c.id_paciente = p.id 
            JOIN login l ON c.id_log_medico = l.id_log 
            WHERE c.data_hora_consul BETWEEN '$data_inicio_sql' AND '$data_fim_sql' 
            ORDER BY c.data_hora_consul ASC";
            
    $result = $mysqli->query($sql);
    while($row = $result->fetch_assoc()) {
        $data_fmt = date('d/m/Y H:i', strtotime($row['data_hora_consul']));
        $linhas .= "<tr>
                        <td>{$data_fmt}</td>
                        <td>{$row['paciente']}</td>
                        <td>{$row['medico']}</td>
                        <td>{$row['tipo_consulta']}</td>
                        <td><strong>{$row['status_consulta']}</strong></td>
                    </tr>";
    }

} elseif ($tipo == 'pacientes') {
    $titulo_relatorio = "Lista de Pacientes Cadastrados";
    $colunas = "<th>ID</th><th>Nome do Paciente</th><th>CPF</th><th>Telefone</th><th>Convênio</th>";
    
    $sql = "SELECT * FROM registro_usuario ORDER BY nome ASC";
    $result = $mysqli->query($sql);
    while($row = $result->fetch_assoc()) {
        $linhas .= "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nome']}</td>
                        <td>{$row['cpf']}</td>
                        <td>{$row['telefone']}</td>
                        <td>{$row['convenio_usu']}</td>
                    </tr>";
    }

} elseif ($tipo == 'usuarios') {
    $titulo_relatorio = "Relatório de Colaboradores (Staff)";
    $colunas = "<th>ID</th><th>Nome</th><th>Login</th><th>Perfil</th><th>CRM</th>";
    
    $sql = "SELECT l.*, m.crm FROM login l LEFT JOIN registro_medico m ON l.id_log = m.id_log ORDER BY l.tipo_usu, l.nome_usu ASC";
    $result = $mysqli->query($sql);
    while($row = $result->fetch_assoc()) {
        $crm = $row['crm'] ? $row['crm'] : 'N/A';
        $linhas .= "<tr>
                        <td>{$row['id_log']}</td>
                        <td>{$row['nome_usu']}</td>
                        <td>{$row['usuario']}</td>
                        <td>{$row['tipo_usu']}</td>
                        <td>{$crm}</td>
                    </tr>";
    }
}

if (empty($linhas)) {
    $linhas = "<tr><td colspan='5' style='text-align:center; padding: 30px; color: #7f8c8d;'>Nenhum registo encontrado com estes filtros.</td></tr>";
}


if ($formato === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Relatorio_{$tipo}_" . date('Y-m-d') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    echo "<meta charset=\"UTF-8\">";
    echo "<table border='1'>";
    echo "<tr><th colspan='5' style='background:#2C82B5; color:white; font-size:16px; padding:10px;'>{$titulo_relatorio} - Bio Vita</th></tr>";
    echo "<tr><th colspan='5' style='background:#f2f2f2;'>Período: ".date('d/m/Y', strtotime($data_inicio))." a ".date('d/m/Y', strtotime($data_fim))."</th></tr>";
    echo "<tr>{$colunas}</tr>";
    echo $linhas;
    echo "</table>";
    exit();
}


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo_relatorio ?> - Bio Vita</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 40px; background: #2C3E50; }
        
        #conteudo-pdf { background: white; width: 1000px; margin: 0 auto; padding: 40px; box-shadow: 0 0 20px rgba(0,0,0,0.5); }
        
        .header { text-align: center; border-bottom: 2px solid #2C82B5; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0 0 5px 0; font-size: 24px; color: #2C82B5; }
        .header p { margin: 0; color: #7f8c8d; font-size: 14px; }
        .info-relatorio { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 13px; color: #666; background: #f8f9fa; padding: 12px; border-radius: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th { background-color: #2C82B5; color: white; text-align: left; padding: 12px; font-size: 14px; }
        table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 13px; }
        table tr:nth-child(even) { background-color: #f8f9fa; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
        
        /* Estilo da tela de carregamento */
        #loading-screen { text-align: center; color: white; margin-top: 10vh; }
        #loading-screen h2 { font-size: 24px; margin-bottom: 10px; }
    </style>
</head>
<body>
    
    <div id="loading-screen">
        <h2>A processar o seu Relatório...</h2>
        <p>O download do PDF iniciará automaticamente em instantes.</p>
    </div>

    <div style="display: flex; justify-content: center;">
        <div id="conteudo-pdf">
            <div class="header">
                <h1><?= $titulo_relatorio ?></h1>
                <p>Sistema de Gestão Hospitalar - Bio Vita</p>
            </div>

            <div class="info-relatorio">
                <span><strong>Período Analisado:</strong> <?= date('d/m/Y', strtotime($data_inicio)) ?> a <?= date('d/m/Y', strtotime($data_fim)) ?></span>
                <span><strong>Data da Emissão:</strong> <?= date('d/m/Y \à\s H:i') ?></span>
            </div>

            <table>
                <thead>
                    <tr><?= $colunas ?></tr>
                </thead>
                <tbody>
                    <?= $linhas ?>
                </tbody>
            </table>

            <div class="footer">
                MedSystem Bio Vita Hospital - Rua Fictícia, 123, São Paulo/SP<br>
                Documento gerado eletronicamente por <strong><?= $_SESSION['usuario'] ?></strong> - Uso Interno
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const elemento = document.getElementById('conteudo-pdf');
            
            const opt = {
                margin:       10,
                filename:     'Relatorio_BioVita_<?= date('Ymd_Hi') ?>.pdf',
                image:        { type: 'jpeg', quality: 1.0 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            html2pdf().set(opt).from(elemento).save().then(() => {
                document.getElementById('loading-screen').innerHTML = `
                    <h2 style="color: #2ecc71;">Download Concluído com Sucesso!</h2>
                    <p>Pode fechar este separador e voltar ao sistema.</p>
                `;
            });
        });
    </script>
</body>
</html>