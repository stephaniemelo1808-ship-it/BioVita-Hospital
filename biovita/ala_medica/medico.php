<?php
session_start();

// 1. TRAVA DE SEGURANÇA
if (!isset($_SESSION['tipo_usu']) || $_SESSION['tipo_usu'] !== 'Médico') {
    header("Location: ../login.php?erro=acesso_negado");
    exit();
}

// 2. CONEXÃO COM O BANCO E VARIÁVEIS
require_once '../conexao.php';
$id_login_medico = $_SESSION['id'];

// =======================================================
// A) PROCESSAR ATUALIZAÇÃO DA CONSULTA
// =======================================================
if (isset($_POST['status_consulta']) && isset($_POST['id_consulta'])) {
    $id_cons = (int)$_POST['id_consulta'];
    $status = $mysqli->real_escape_string($_POST['status_consulta']);
    $obs = $mysqli->real_escape_string($_POST['observacoes']);
    $retorno = $mysqli->real_escape_string($_POST['data_retorno']);

    $sql_update = "UPDATE consultas SET status_consulta = '$status', observacoes = '$obs'";
    if (!empty($retorno)) {
        $sql_update .= ", data_retorno = '$retorno'";
    } else {
        $sql_update .= ", data_retorno = NULL";
    }
    $sql_update .= " WHERE id_consulta = '$id_cons' AND id_log_medico = '$id_login_medico'";

    if ($mysqli->query($sql_update)) {
        $_SESSION['alerta'] = "Prontuário atualizado com sucesso!";
        $_SESSION['tipo_alerta'] = "sucesso";
    }
    header("Location: medico.php?aba=tab-consultas");
    exit();
}

$aba_ativa = $_GET['aba'] ?? 'tab-painel';

// =======================================================
// B) BUSCAR DADOS DO PERFIL DO MÉDICO
// =======================================================
$sql_medico = "SELECT l.nome_usu, l.usuario, m.crm, m.uf, m.telefone, m.ubs 
               FROM login l 
               LEFT JOIN registro_medico m ON l.id_log = m.id_log 
               WHERE l.id_log = '$id_login_medico'";
$resultado_medico = $mysqli->query($sql_medico);
$dados_medico = $resultado_medico->fetch_assoc();

$nome_medico = $dados_medico['nome_usu'] ?? 'Dr(a). Não Informado';
$crm_medico  = $dados_medico['crm'] ?? 'CRM Pendente';
$tel_medico  = $dados_medico['telefone'] ?? '(00) 00000-0000';
$email_medico = $dados_medico['usuario'] ?? 'Não informado';
$ubs_medico = $dados_medico['ubs'] ?? 'Não vinculada';

// =======================================================
// C) BUSCAR TODOS OS PACIENTES E FUNÇÃO IDADE
// =======================================================
$sql_pacientes = "SELECT * FROM registro_usuario ORDER BY nome ASC";
$resultado_pacientes = $mysqli->query($sql_pacientes);

$lista_pacientes = [];
if ($resultado_pacientes) {
    while ($row = $resultado_pacientes->fetch_assoc()) {
        $row['convenio'] = $row['convenio_usu'] ?? 'SUS / Não inf.';
        $row['tipo_sanguineo'] = $row['tipo_sanguineo'] ?? 'Não inf.';
        $row['id_usu'] = $row['id'];
        $lista_pacientes[$row['id']] = $row;
    }
}

function calculaIdade(?string $dataNasc)
{
    if (!$dataNasc) return 'N/A';
    $data = new DateTime($dataNasc);
    $agora = new DateTime();
    return $agora->diff($data)->y;
}

// =======================================================
// D) BUSCAR CONSULTAS DO MÉDICO
// =======================================================
$hoje = date('Y-m-d');
$sql_consultas = "SELECT * FROM consultas WHERE id_log_medico = '$id_login_medico' ORDER BY data_hora_consul ASC";
$resultado_consultas = $mysqli->query($sql_consultas);

$todas_consultas = [];
$consultas_hoje = [];
$eventos_calendario = []; // Array novo para alimentar o Calendário JS
$total_hoje = 0;
$realizadas_hoje = 0;
$proximas_hoje = 0;
$qtd_concluida = 0;
$qtd_agendada = 0;
$qtd_andamento = 0;
$qtd_cancelada = 0;

if ($resultado_consultas) {
    while ($row = $resultado_consultas->fetch_assoc()) {
        $todas_consultas[] = $row;

        $data_iso = date('Y-m-d', strtotime($row['data_hora_consul']));
        $hora = date('H:i', strtotime($row['data_hora_consul']));
        $pac_nome = $lista_pacientes[$row['id_paciente']]['nome'] ?? 'Desconhecido';

        // Alimenta o array do calendário
        $eventos_calendario[] = [
            'data' => $data_iso,
            'hora' => $hora,
            'paciente' => $pac_nome,
            'status' => $row['status_consulta']
        ];

        if ($data_iso == $hoje) {
            $consultas_hoje[] = $row;

            $total_hoje++;
            if ($row['status_consulta'] == 'Concluída') {
                $realizadas_hoje++;
                $qtd_concluida++;
            } elseif ($row['status_consulta'] == 'Agendada' || $row['status_consulta'] == 'Confirmada') {
                $proximas_hoje++;
                $qtd_agendada++;
            } elseif ($row['status_consulta'] == 'Em Andamento') {
                $qtd_andamento++;
            } elseif ($row['status_consulta'] == 'Cancelada') {
                $qtd_cancelada++;
            }
        }
    }
}
$eventos_js = json_encode($eventos_calendario); // Converte para o JS ler

// =======================================================
// E) DADOS PARA O GRÁFICO
// =======================================================
$dados_semana = [0, 0, 0, 0, 0, 0, 0];
$sql_grafico = "SELECT WEEKDAY(data_hora_consul) as dia_semana, COUNT(*) as total 
                FROM consultas 
                WHERE data_hora_consul IS NOT NULL AND id_log_medico = '$id_login_medico'
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

// =======================================================
// F) LISTA DE MEDICAMENTOS
// =======================================================
$lista_medicamentos = [
    ['nome' => 'Paracetamol', 'tipo' => 'Analgesico', 'dosagem' => '750mg', 'instrucoes' => '1 comprimido a cada 8h'],
    ['nome' => 'Ibuprofeno', 'tipo' => 'Anti-inflamatorio', 'dosagem' => '600mg', 'instrucoes' => '1 comprimido a cada 12h'],
    ['nome' => 'Amoxicilina', 'tipo' => 'Antibiotico', 'dosagem' => '500mg', 'instrucoes' => '1 capsula a cada 8h por 7 dias'],
    ['nome' => 'Losartana', 'tipo' => 'Anti-hipertensivo', 'dosagem' => '50mg', 'instrucoes' => '1 comprimido ao dia']
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Médico - MedSystem Bio Vita</title>
    <link rel="stylesheet" href="medico.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* CHIPS DE FILTRO */
        .chip-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .chip-btn {
            background: var(--cinza-card);
            border: 1px solid var(--cinza-borda);
            color: var(--texto-secundario);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .chip-btn:hover {
            border-color: var(--azul-primario);
            color: var(--azul-primario);
        }

        .chip-btn.active {
            background: var(--azul-primario);
            color: white;
            border-color: var(--azul-primario);
            box-shadow: 0 2px 8px rgba(44, 130, 181, 0.3);
        }

        /* CALENDÁRIO FIXES (Caso falte no CSS principal) */
        .calendario-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e2e8f0;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .calendario-dia-header {
            background: #f8fafc;
            color: var(--texto-secundario);
            text-align: center;
            padding: 12px 5px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .calendario-dia {
            background: white;
            min-height: 110px;
            padding: 8px;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
        }

        .calendario-dia.outro-mes {
            background: #f8fafc;
            opacity: 0.6;
        }

        .calendario-dia.hoje {
            background: #f0f7ff;
        }

        .calendario-dia-numero {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 5px;
            color: var(--texto-principal);
        }

        .calendario-dia.hoje .calendario-dia-numero {
            background: var(--azul-primario);
            color: white;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .cal-evento {
            font-size: 0.7rem;
            padding: 3px 6px;
            border-radius: 4px;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>

    <?php include('includes/sidebar-medico.php'); ?>

    <?php if (isset($_SESSION['alerta'])): ?>
        <div id="phpToast" style="position:fixed; top:20px; right:20px; background-color:#2ecc71; color:white; padding:15px 25px; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.1); z-index:9999;">
            <i class='bx bx-check-circle'></i> <?= $_SESSION['alerta'] ?>
        </div>
        <script>
            setTimeout(() => document.getElementById('phpToast').style.display = 'none', 3500);
        </script>
        <?php unset($_SESSION['alerta']);
        unset($_SESSION['tipo_alerta']); ?>
    <?php endif; ?>

    <div id="toast" class="toast"></div>

    <main class="main-content" id="mainContent">

        <div class="page-header">
            <div>
                <h1>Área do Médico</h1>
            </div>
            <div class="date-badge">
                <i class='bx bx-calendar'></i>
                <span><?= date('d/m/Y') ?></span>
            </div>
        </div>

        <div class="tabs-nav">
            <button class="tab-btn <?= $aba_ativa == 'tab-painel' ? 'active' : '' ?>" onclick="openTab(event, 'tab-painel')"><i class='bx bx-grid-alt'></i> Painel</button>
            <button class="tab-btn <?= $aba_ativa == 'tab-consultas' ? 'active' : '' ?>" onclick="openTab(event, 'tab-consultas')"><i class='bx bx-calendar'></i> Consultas</button>
            <button class="tab-btn <?= $aba_ativa == 'tab-calendario' ? 'active' : '' ?>" onclick="openTab(event, 'tab-calendario')"><i class='bx bx-calendar-alt'></i> Calendário</button>
            <button class="tab-btn <?= $aba_ativa == 'tab-pacientes' ? 'active' : '' ?>" onclick="openTab(event, 'tab-pacientes')"><i class='bx bx-user'></i> Pacientes</button>
            <button class="tab-btn <?= $aba_ativa == 'tab-prescricoes' ? 'active' : '' ?>" onclick="openTab(event, 'tab-prescricoes')"><i class='bx bx-capsule'></i> Prescrições</button>
            <button class="tab-btn <?= $aba_ativa == 'tab-perfil' ? 'active' : '' ?>" onclick="openTab(event, 'tab-perfil')"><i class='bx bx-user-circle'></i> Perfil</button>
        </div>

        <div id="tab-painel" class="tab-content <?= $aba_ativa == 'tab-painel' ? 'active' : '' ?>">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Consultas Hoje</h3>
                        <p><?= $total_hoje ?></p>
                    </div>
                    <div class="stat-info">
                        <h3>Realizadas</h3>
                        <p><?= $realizadas_hoje ?></p>
                    </div>
                    <div class="stat-info">
                        <h3>Próximas</h3>
                        <p><?= $proximas_hoje ?></p>
                    </div>
                    <i class='bx bx-time stat-icon'></i>
                </div>
                <div class="stat-card purple">
                    <div class="stat-info">
                        <h3>Horas Trabalhadas</h3>
                        <p>4h 30min</p>
                    </div>
                    <i class='bx bx-timer stat-icon'></i>
                </div>
            </div>

            <div class="layout-flex">
                <div class="flex-2">
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3 class="card-title" style="margin: 0;"><i class='bx bx-line-chart'></i> Consultas da Semana</h3>
                            <button onclick="showToast('Dados exportados com sucesso!', 'success')" class="export-btn">
                                <i class='bx bx-export'></i> Exportar
                            </button>
                        </div>
                        <div style="height: 250px;">
                            <canvas id="graficoSemana"></canvas>
                        </div>
                    </div>

                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-list-ul'></i> Próximas Consultas (Hoje)</h3>
                        <?php foreach ($consultas_hoje as $consulta):
                            $paciente = isset($lista_pacientes[$consulta['id_paciente']]) ? $lista_pacientes[$consulta['id_paciente']] : ['nome' => 'Paciente Desconhecido'];
                            $hora = date('H:i', strtotime($consulta['data_hora_consul']));
                            $statusClass = $consulta['status_consulta'] == 'Agendada' ? 'status-aguardando' : 'status-confirmada';
                        ?>
                            <div class="consulta-item">
                                <div class="consulta-info">
                                    <h4><?= $paciente['nome'] ?></h4>
                                    <span><i class='bx bx-time'></i> <?= $hora ?> - Rotina</span>
                                </div>
                                <span class="status-badge <?= $statusClass ?>"><?= $consulta['status_consulta'] ?></span>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($consultas_hoje)): ?>
                            <p style="color: #7f8c8d; text-align: center; margin-top: 20px;">Nenhuma consulta agendada para a data de hoje.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex-1">
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-pie-chart'></i> Status do Dia</h3>
                        <div style="height: 200px; display: flex; justify-content: center;">
                            <canvas id="graficoStatus"></canvas>
                        </div>
                    </div>

                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-bolt'></i> Acesso Rápido</h3>
                        <div class="actions-list">
                            <button class="btn-primary" onclick="openTabById('tab-prescricoes')">
                                <i class='bx bx-plus-medical'></i> Nova Prescrição
                            </button>
                            <button class="btn-outline" onclick="openTabById('tab-perfil')">
                                <i class='bx bx-user'></i> Meu Perfil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="tab-consultas" class="tab-content <?= $aba_ativa == 'tab-consultas' ? 'active' : '' ?>">
            <div class="layout-flex">

                <div style="width: 380px; min-width: 320px;">
                    <div class="card">
                        <h3 class="card-title" style="margin-bottom: 10px;"><i class='bx bx-filter-alt'></i> Filtros Visuais</h3>

                        <div class="form-group" style="margin-bottom: 5px;">
                            <label>Status da Consulta:</label>
                        </div>
                        <div class="chip-group" id="chips-status">
                            <button class="chip-btn active" data-valor="todas" onclick="clicarChip(this, 'status')">Todas</button>
                            <button class="chip-btn" data-valor="agendada" onclick="clicarChip(this, 'status')">Agendadas</button>
                            <button class="chip-btn" data-valor="confirmada" onclick="clicarChip(this, 'status')">Confirmadas</button>
                            <button class="chip-btn" data-valor="em andamento" onclick="clicarChip(this, 'status')">Em Andamento</button>
                            <button class="chip-btn" data-valor="concluída" onclick="clicarChip(this, 'status')">Concluídas</button>
                        </div>

                        <div class="form-group" style="margin-bottom: 5px; margin-top: 15px;">
                            <label>Período (Data):</label>
                        </div>
                        <div class="chip-group" id="chips-periodo">
                            <button class="chip-btn active" data-valor="todas" onclick="clicarChip(this, 'periodo')">Todas as datas</button>
                            <button class="chip-btn" data-valor="hoje" onclick="clicarChip(this, 'periodo')">Somente Hoje</button>
                            <button class="chip-btn" data-valor="semana" onclick="clicarChip(this, 'periodo')">Próximos 7 dias</button>
                        </div>

                        <div class="form-group" style="margin-top: 15px;">
                            <label>Buscar Paciente:</label>
                            <input type="text" id="buscaPaciente" onkeyup="aplicarFiltros()" placeholder="Digite o nome aqui...">
                        </div>
                    </div>

                    <div style="max-height: 500px; overflow-y: auto;" id="listaPacientesConsulta">
                        <?php foreach ($todas_consultas as $consulta):
                            $pac = isset($lista_pacientes[$consulta['id_paciente']]) ? $lista_pacientes[$consulta['id_paciente']] : ['nome' => 'Desconhecido', 'telefone' => '-', 'convenio' => '-'];
                            $hora = date('H:i', strtotime($consulta['data_hora_consul']));
                            $data_iso = date('Y-m-d', strtotime($consulta['data_hora_consul']));
                            $data_br = date('d/m/Y', strtotime($consulta['data_hora_consul']));
                            $obsSegura = htmlspecialchars($consulta['observacoes'] ?? '');
                            $retorno = $consulta['data_retorno'] ?? '';
                        ?>
                            <div class="paciente-item item-consulta"
                                data-id-consulta="<?= $consulta['id_consulta'] ?>"
                                data-id-paciente="<?= $consulta['id_paciente'] ?>"
                                data-nome="<?= htmlspecialchars($pac['nome']) ?>"
                                data-convenio="<?= htmlspecialchars($pac['convenio']) ?>"
                                data-telefone="<?= htmlspecialchars($pac['telefone']) ?>"
                                data-data-iso="<?= $data_iso ?>"
                                data-hora-full="<?= $data_br . ' ' . $hora ?>"
                                data-obs="<?= $obsSegura ?>"
                                data-retorno="<?= $retorno ?>"
                                data-status="<?= $consulta['status_consulta'] ?>"
                                onclick="selecionarAtendimento(this)">

                                <strong><?= $pac['nome'] ?></strong>
                                <span style="font-size: 0.85rem;"><i class='bx bx-calendar'></i> <?= $data_br ?> às <?= $hora ?></span>
                                <span style="display:block; font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">Status: <?= $consulta['status_consulta'] ?></span>
                            </div>
                        <?php endforeach; ?>

                        <p id="msgNenhumEncontrado" style="color: #7f8c8d; text-align: center; padding: 20px; display: none;">Nenhuma consulta encontrada com estes filtros.</p>
                    </div>
                </div>

                <div class="flex-1">
                    <form method="POST" action="" class="card" id="consultaDetalhes">
                        <input type="hidden" name="id_consulta" id="form_id_consulta" value="">
                        <input type="hidden" name="id_paciente" id="form_id_paciente" value="">

                        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f0f5f9; padding-bottom: 15px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--azul-claro-bg); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: var(--azul-primario);">
                                    <i class='bx bx-user'></i>
                                </div>
                                <div>
                                    <h2 id="form_nome_paciente" style="margin: 0; color: var(--azul-primario); font-size: 1.3rem;">Nenhum paciente selecionado</h2>
                                    <p style="margin: 4px 0 0 0; color: var(--texto-secundario); font-size: 0.9rem;">Para ver os detalhes, clique na lista ao lado.</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label>Convênio</label>
                                <input type="text" id="form_convenio" value="-" readonly style="background: var(--cinza-card);">
                            </div>
                            <div class="form-group">
                                <label>Telefone</label>
                                <input type="text" id="form_telefone" value="-" readonly style="background: var(--cinza-card);">
                            </div>
                            <div class="form-group">
                                <label>Data/Hora Consulta</label>
                                <input type="text" id="form_data_hora" value="-" readonly style="background: var(--cinza-card);">
                            </div>
                            <div class="form-group">
                                <label>Agendar Retorno</label>
                                <input type="date" name="data_retorno" id="form_retorno" style="background: var(--cinza-card);">
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 15px;">
                            <label>Anotações Clínicas (Prontuário)</label>
                            <textarea name="observacoes" id="form_obs" rows="6" placeholder="Descreva os sintomas, diagnóstico, pressão arterial e conduta médica..."></textarea>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button type="submit" name="status_consulta" value="Concluída" class="btn-success">
                                <i class='bx bx-check-circle'></i> Salvar e Concluir
                            </button>
                            <button type="submit" name="status_consulta" value="Em Andamento" class="btn-primary">
                                <i class='bx bx-play-circle'></i> Em Andamento
                            </button>
                            <button type="submit" name="status_consulta" value="Cancelada" class="btn-danger">
                                <i class='bx bx-x-circle'></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="tab-calendario" class="tab-content <?= $aba_ativa == 'tab-calendario' ? 'active' : '' ?>">
            <div class="card">
                <div class="calendario-header">
                    <button class="cal-nav-btn" onclick="mudarMes(-1)"><i class='bx bx-chevron-left'></i></button>
                    <h3 id="mesAnoAtual" style="margin: 0; color: var(--texto-principal);">Mês e Ano</h3>
                    <button class="cal-nav-btn" onclick="mudarMes(1)"><i class='bx bx-chevron-right'></i></button>
                </div>
                <div class="calendario-grid" id="calendarioGrid"></div>
            </div>
        </div>

        <div id="tab-pacientes" class="tab-content <?= $aba_ativa == 'tab-pacientes' ? 'active' : '' ?>">
            <div class="pacientes-grid">
                <?php foreach ($lista_pacientes as $pac): ?>
                    <div class="paciente-card">
                        <div class="paciente-avatar"><i class='bx bx-user'></i></div>
                        <h4><?= $pac['nome'] ?></h4>
                        <p><?= calculaIdade($pac['dt_nasc']) ?> anos | Tipo <?= $pac['tipo_sanguineo'] ?></p>
                        <p><i class='bx bx-health'></i> <?= $pac['convenio'] ?></p>
                        <p style="font-size: 0.75rem; margin-bottom: 15px;">Telefone: <?= $pac['telefone'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="tab-prescricoes" class="tab-content <?= $aba_ativa == 'tab-prescricoes' ? 'active' : '' ?>">
            <div class="layout-flex">
                <div class="flex-1">
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-capsule'></i> 1. Selecione a Consulta</h3>
                        <select id="select_consulta" class="form-control" style="width:100%; padding: 10px; margin-bottom: 15px;">
                            <option value="">Escolha a consulta do paciente...</option>
                            <?php
                            // Puxa as consultas deste médico
                            $cons_med = $mysqli->query("SELECT c.id_consulta, p.nome, c.data_hora_consul 
                                                FROM consultas c 
                                                JOIN registro_usuario p ON c.id_paciente = p.id 
                                                WHERE c.id_log_medico = '$id_login_medico' 
                                                ORDER BY c.data_hora_consul DESC LIMIT 20");
                            while ($c = $cons_med->fetch_assoc()): ?>
                                <option value="<?= $c['id_consulta'] ?>">
                                    <?= date('d/m', strtotime($c['data_hora_consul'])) ?> - <?= $c['nome'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>

                        <h3 class="card-title"><i class='bx bx-plus-medical'></i> 2. Adicionar Medicamentos</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Medicamento</th>
                                    <th>Dosagem</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lista_medicamentos as $med): ?>
                                    <tr>
                                        <td><strong><?= $med['nome'] ?></strong></td>
                                        <td><?= $med['dosagem'] ?></td>
                                        <td><button class="btn-sm btn-primary" onclick="adicionarMedicamento('<?= $med['nome'] ?>', '<?= $med['dosagem'] ?>', '<?= $med['instrucoes'] ?>')"><i class='bx bx-plus'></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="width: 400px;">
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-receipt'></i> 3. Finalizar Receituário</h3>
                        <form id="formPrescricao" method="POST" action="salvar_prescricao.php">
                            <input type="hidden" name="id_consulta" id="input_id_consulta">
                            <input type="hidden" name="lista_meds" id="input_lista_meds">
                            <div id="receituarioLista"></div>
                            <button type="submit" class="btn-success" style="width:100%; margin-top: 15px;">Salvar Prescrição</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="tab-perfil" class="tab-content <?= $aba_ativa == 'tab-perfil' ? 'active' : '' ?>">
            <div class="layout-flex">
                <div style="width: 320px; min-width: 280px;">
                    <div class="card perfil-card">
                        <h3><?= $nome_medico ?></h3>
                        <p class="especialidade">Médico Especialista</p>
                        <p class="crm">CRM: <?= $crm_medico ?></p>
                        <span class="perfil-status">Online</span>
                    </div>
                </div>

                <div class="flex-1">
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-id-card'></i> Informacoes Profissionais</h3>
                        <div class="perfil-info-grid">
                            <div class="perfil-info-item"><label>Nome Completo</label><span><?= $nome_medico ?></span></div>
                            <div class="perfil-info-item"><label>CRM</label><span><?= $crm_medico ?></span></div>
                            <div class="perfil-info-item"><label>Especialidade</label><span>Clínica Médica</span></div>
                            <div class="perfil-info-item"><label>Email</label><span><?= $email_medico ?></span></div>
                            <div class="perfil-info-item"><label>Telefone</label><span><?= $tel_medico ?></span></div>
                            <div class="perfil-info-item"><label>UBS Vinculada</label><span><?= $ubs_medico ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="medico.js"></script>
    <script>
        // ----------------------------------------------------
        // LÓGICA 1: GRÁFICOS (Chart.js)
        // ----------------------------------------------------
        document.addEventListener('DOMContentLoaded', function() {
            const canvasSemana = document.getElementById('graficoSemana');
            if (canvasSemana) {
                const ctxSemana = canvasSemana.getContext('2d');
                const gradient = ctxSemana.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(44, 130, 181, 0.3)');
                gradient.addColorStop(1, 'rgba(44, 130, 181, 0)');

                new Chart(ctxSemana, {
                    type: 'line',
                    data: {
                        labels: ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'],
                        datasets: [{
                            label: 'Atendimentos',
                            data: [<?= $dados_grafico_js ?>],
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
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f5f5f5'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            const canvasStatus = document.getElementById('graficoStatus');
            if (canvasStatus) {
                new Chart(canvasStatus.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Concluídas', 'Agendadas', 'Em Espera', 'Canceladas'],
                        datasets: [{
                            data: [<?= $qtd_concluida ?>, <?= $qtd_agendada ?>, <?= $qtd_andamento ?>, <?= $qtd_cancelada ?>],
                            backgroundColor: ['#2ecc71', '#3498db', '#f1c40f', '#e74c3c'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });

        // ----------------------------------------------------
        // LÓGICA 2: CHIPS VISUAIS DE FILTRAGEM
        // ----------------------------------------------------
        let filtroAtualStatus = 'todas';
        let filtroAtualPeriodo = 'todas';

        function clicarChip(botao, tipo) {
            const parent = botao.parentElement;
            parent.querySelectorAll('.chip-btn').forEach(b => b.classList.remove('active'));
            botao.classList.add('active');

            const valor = botao.getAttribute('data-valor');
            if (tipo === 'status') {
                filtroAtualStatus = valor;
            }
            if (tipo === 'periodo') {
                filtroAtualPeriodo = valor;
            }

            aplicarFiltros();
        }

        const dataHojeServidor = '<?= $hoje ?>';

        function aplicarFiltros() {
            const termoBusca = document.getElementById('buscaPaciente').value.toLowerCase();
            const itens = document.querySelectorAll('.item-consulta');
            let qtdVisiveis = 0;

            let hojeData = new Date(dataHojeServidor + 'T00:00:00');
            let fimSemana = new Date(hojeData);
            fimSemana.setDate(hojeData.getDate() + 7);
            const fimSemanaIso = fimSemana.toISOString().split('T')[0];

            itens.forEach(item => {
                const nomeItem = item.getAttribute('data-nome').toLowerCase();
                let rawStatus = item.getAttribute('data-status');
                const statusItem = rawStatus ? rawStatus.toLowerCase() : '';
                const dataItem = item.getAttribute('data-data-iso');

                let mostraPorNome = nomeItem.includes(termoBusca);
                let mostraPorStatus = (filtroAtualStatus === 'todas') || (filtroAtualStatus === statusItem);

                let mostraPorData = false;
                if (filtroAtualPeriodo === 'todas') {
                    mostraPorData = true;
                } else if (filtroAtualPeriodo === 'hoje') {
                    mostraPorData = (dataItem === dataHojeServidor);
                } else if (filtroAtualPeriodo === 'semana') {
                    mostraPorData = (dataItem >= dataHojeServidor && dataItem <= fimSemanaIso);
                }

                if (mostraPorNome && mostraPorStatus && mostraPorData) {
                    item.style.display = 'block';
                    qtdVisiveis++;
                } else {
                    item.style.display = 'none';
                }
            });

            const msg = document.getElementById('msgNenhumEncontrado');
            if (msg) msg.style.display = (qtdVisiveis === 0) ? 'block' : 'none';
        }

        // ----------------------------------------------------
        // LÓGICA 3: CALENDÁRIO DINÂMICO
        // ----------------------------------------------------
        const eventosCal = <?= $eventos_js ?>; // Recebe as consultas do PHP
        let dataBaseCal = new Date(dataHojeServidor + 'T00:00:00');
        let mesAtual = dataBaseCal.getMonth();
        let anoAtual = dataBaseCal.getFullYear();

        function renderizarCalendario(mes, ano) {
            const meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            document.getElementById('mesAnoAtual').innerText = `${meses[mes]} ${ano}`;

            const grid = document.getElementById('calendarioGrid');
            grid.innerHTML = '';

            const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
            diasSemana.forEach(dia => {
                grid.innerHTML += `<div class="calendario-dia-header">${dia}</div>`;
            });

            const primeiroDia = new Date(ano, mes, 1).getDay();
            const diasNoMes = new Date(ano, mes + 1, 0).getDate();
            const diasMesAnterior = new Date(ano, mes, 0).getDate();

            // Preenche os espaços em branco com os dias finais do mês passado
            for (let i = primeiroDia - 1; i >= 0; i--) {
                grid.innerHTML += `<div class="calendario-dia outro-mes"><div class="calendario-dia-numero">${diasMesAnterior - i}</div></div>`;
            }

            // Renderiza os dias oficiais do mês atual
            for (let dia = 1; dia <= diasNoMes; dia++) {
                let diaFormatado = String(dia).padStart(2, '0');
                let mesFormatado = String(mes + 1).padStart(2, '0');
                let dataIso = `${ano}-${mesFormatado}-${diaFormatado}`;
                let classeHoje = (dataIso === dataHojeServidor) ? 'hoje' : '';

                // Filtra as consultas do PHP que caem exatamente neste dia
                let eventosDoDia = eventosCal.filter(e => e.data === dataIso);
                eventosDoDia.sort((a, b) => a.hora.localeCompare(b.hora)); // Ordena por horário

                let htmlEventos = '';
                eventosDoDia.forEach(ev => {
                    let corBg, corTexto;
                    // Cores baseadas no status
                    if (ev.status === 'Cancelada') {
                        corBg = '#fee2e2';
                        corTexto = '#991b1b';
                    } else if (ev.status === 'Concluída') {
                        corBg = '#dcfce7';
                        corTexto = '#166534';
                    } else if (ev.status === 'Em Andamento') {
                        corBg = '#fef9c3';
                        corTexto = '#854d0e';
                    } else {
                        corBg = '#dbeafe';
                        corTexto = '#1e40af';
                    }

                    htmlEventos += `<div class="cal-evento" style="background: ${corBg}; color: ${corTexto};" title="${ev.hora} - ${ev.paciente}">
                        ${ev.hora} - ${ev.paciente.split(' ')[0]}
                    </div>`;
                });

                grid.innerHTML += `
                    <div class="calendario-dia ${classeHoje}">
                        <div class="calendario-dia-numero">${dia}</div>
                        ${htmlEventos}
                    </div>
                `;
            }

            // Preenche os quadrados restantes com o início do próximo mês
            const totalCells = primeiroDia + diasNoMes;
            const remainingCells = (totalCells > 35) ? 42 - totalCells : 35 - totalCells;
            for (let dia = 1; dia <= remainingCells; dia++) {
                grid.innerHTML += `<div class="calendario-dia outro-mes"><div class="calendario-dia-numero">${dia}</div></div>`;
            }
        }

        function mudarMes(direcao) {
            mesAtual += direcao;
            if (mesAtual > 11) {
                mesAtual = 0;
                anoAtual++;
            }
            if (mesAtual < 0) {
                mesAtual = 11;
                anoAtual--;
            }
            renderizarCalendario(mesAtual, anoAtual);
        }

        // ----------------------------------------------------
        // INICIALIZAÇÃO GERAL AO CARREGAR A PÁGINA
        // ----------------------------------------------------
        document.addEventListener('DOMContentLoaded', function() {
            aplicarFiltros();
            renderizarCalendario(mesAtual, anoAtual);
        });

        // ----------------------------------------------------
        // LÓGICA 4: SELECIONAR PACIENTE PARA PRONTUÁRIO
        // ----------------------------------------------------
        function selecionarAtendimento(elemento) {
            document.querySelectorAll('.item-consulta').forEach(item => item.classList.remove('active'));
            elemento.classList.add('active');

            document.getElementById('form_id_consulta').value = elemento.getAttribute('data-id-consulta');
            document.getElementById('form_id_paciente').value = elemento.getAttribute('data-id-paciente');
            document.getElementById('form_nome_paciente').innerText = elemento.getAttribute('data-nome');
            document.getElementById('form_convenio').value = elemento.getAttribute('data-convenio');
            document.getElementById('form_telefone').value = elemento.getAttribute('data-telefone');
            document.getElementById('form_data_hora').value = elemento.getAttribute('data-hora-full');
            document.getElementById('form_obs').value = elemento.getAttribute('data-obs');
            document.getElementById('form_retorno').value = elemento.getAttribute('data-retorno');
        }

        // ----------------------------------------------------
        // LÓGICA 5: RECEITUÁRIO (PRESCRITOS)
        // ----------------------------------------------------
        let receituarioItens = [];

        function adicionarMedicamento(nome, dosagem, instrucoes) {
            const existe = receituarioItens.find(item => item.nome === nome);
            if (existe) {
                if (typeof showToast === "function") showToast('Medicamento já adicionado!', 'warning');
                return;
            }
            receituarioItens.push({
                nome,
                dosagem,
                instrucoes
            });
            atualizarPainelReceituario();
        }

        function removerMedicamento(index) {
            receituarioItens.splice(index, 1);
            atualizarPainelReceituario();
        }

        function atualizarPainelReceituario() {
            const container = document.getElementById('receituarioLista');
            if (receituarioItens.length === 0) {
                container.innerHTML = `<div class="receituario-item"><div><div class="med-nome">Selecione um medicamento</div></div></div>`;
                return;
            }
            container.innerHTML = '';
            receituarioItens.forEach((med, index) => {
                container.innerHTML += `
                    <div class="receituario-item" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0f5f9; padding: 10px 0;">
                        <div>
                            <div class="med-nome" style="font-weight: 600; color: #2C3E50;">${med.nome}</div>
                            <div class="med-dose" style="font-size: 0.85rem; color: #7f8c8d;">${med.dosagem} - ${med.instrucoes}</div>
                        </div>
                        <button type="button" onclick="removerMedicamento(${index})" style="background: transparent; border: none; color: #e74c3c; cursor: pointer; font-size: 1.2rem;">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>`;
            });
        }

        document.getElementById('formPrescricao').onsubmit = function() {
            const idCons = document.getElementById('select_consulta').value;
            if (!idCons) {
                alert('Selecione uma consulta antes!');
                return false;
            }
            document.getElementById('input_id_consulta').value = idCons;
            document.getElementById('input_lista_meds').value = JSON.stringify(receituarioItens);
        };
    </script>
</body>

</html>