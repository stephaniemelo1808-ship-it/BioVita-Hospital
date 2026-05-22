<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório Hospitalar - Bio Vita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2C82B5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header img {
            width: 120px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #2C82B5;
        }

        .info-relatorio {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th {
            background-color: #f2f2f2;
            color: #2C82B5;
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="img/logo_biovita.png" alt="Bio Vita Hospital">
        <h1>Relatório Mensal de Atendimentos</h1>
    </div>

    <div class="info-relatorio">
        <span><strong>Período:</strong> 01/04/2026 a 30/04/2026</span>
        <span><strong>Gerado em:</strong> 14/04/2026 às 14:00</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Paciente</th>
                <th>Médico Responsável</th>
                <th>Especialidade</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>12/04/2026</td>
                <td>João Silva Oliveira</td>
                <td>Dr. Carlos Eduardo</td>
                <td>Clínico Geral</td>
                <td>Concluído</td>
            </tr>
            <tr>
                <td>12/04/2026</td>
                <td>Maria Aparecida Santos</td>
                <td>Dra. Helena Rios</td>
                <td>Pediatria</td>
                <td>Concluído</td>
            </tr>
            </tbody>
    </table>

    <div class="footer">
        Bio Vita Hospital - Rua Exemplo, 123, São Paulo/SP - CNPJ: 00.000.000/0001-00<br>
        Documento gerado pelo MedSystem - Página 1 de 1
    </div>

</body>
</html>