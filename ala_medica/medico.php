<?php
// require_once 'conexao.php'; // Descomente quando conectar ao banco real
require_once 'includes/mock-medico.php';
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Médico - MedSystem Bio Vita</title>
    <link rel="stylesheet" href="../ala_medica/medico.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include('includes/sidebar-medico.php'); ?>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <!-- MAIN CONTENT -->
    <main class="main-content" id="mainContent">

        <!-- Header -->
        <div class="page-header">
            <div>
                <h1>Área do Médico</h1>
            </div>
            <div class="date-badge">
                <i class='bx bx-calendar'></i>
                <span>14 de Maio, 2026</span>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-nav">
            <button class="tab-btn active" onclick="openTab(event, 'tab-painel')">
                <i class='bx bx-grid-alt'></i> Painel
            </button>
            <button class="tab-btn" onclick="openTab(event, 'tab-consultas')">
                <i class='bx bx-calendar'></i> Consultas
            </button>
            <button class="tab-btn" onclick="openTab(event, 'tab-calendario')">
                <i class='bx bx-calendar-alt'></i> Calendário
            </button>
            <button class="tab-btn" onclick="openTab(event, 'tab-pacientes')">
                <i class='bx bx-user'></i> Pacientes
            </button>
            <button class="tab-btn" onclick="openTab(event, 'tab-prescricoes')">
                <i class='bx bx-capsule'></i> Prescrições
            </button>
            <button class="tab-btn" onclick="openTab(event, 'tab-perfil')">
                <i class='bx bx-user-circle'></i> Perfil
            </button>
        </div>

        <!-- ==================== TAB: PAINEL ==================== -->
        <div id="tab-painel" class="tab-content active">
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Consultas Hoje</h3>
                        <p>8</p>
                    </div>
                    <i class='bx bx-calendar-check stat-icon'></i>
                </div>
                <div class="stat-card green">
                    <div class="stat-info">
                        <h3>Realizadas</h3>
                        <p>6</p>
                    </div>
                    <i class='bx bx-check-circle stat-icon'></i>
                </div>
                <div class="stat-card yellow">
                    <div class="stat-info">
                        <h3>Próximas</h3>
                        <p>2</p>
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
                <!-- Coluna Esquerda -->
                <div class="flex-2">
                    <!-- Grafico -->
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

                    <!-- Próximas Consultas -->
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-list-ul'></i> Próximas Consultas</h3>
                        <?php foreach($mockConsultasHoje as $consulta): 
                            $paciente = $mockPacientes[$consulta['id_paciente']];
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
                    </div>
                </div>

                <!-- Coluna Direita -->
                <div class="flex-1">
                    <!-- Status -->
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-pie-chart'></i> Status do Dia</h3>
                        <div style="height: 200px; display: flex; justify-content: center;">
                            <canvas id="graficoStatus"></canvas>
                        </div>
                    </div>

                    <!-- Acesso Rápido -->
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-bolt'></i> Acesso Rápido</h3>
                        <div class="actions-list">
                            <button class="btn-primary" onclick="openTabById('tab-prescricoes')">
                                <i class='bx bx-plus-medical'></i> Nova Prescrição
                            </button>
                            <button class="btn-outline" onclick="openTabById('tab-perfil')">
                                <i class='bx bx-user'></i> Meu Perfil
                            </button>
                            <button class="btn-outline" onclick="exportarDashboard()">
                                <i class='bx bx-export'></i> Exportar Dados
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB: CONSULTAS ==================== -->
        <div id="tab-consultas" class="tab-content">
            <div class="layout-flex">
                <!-- Filtros + Lista -->
                <div style="width: 380px; min-width: 320px;">
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-filter-alt'></i> Filtros</h3>
                        <div class="form-group">
                            <label>Status</label>
                            <select id="filtroStatus">
                                <option value="todas">Todas</option>
                                <option value="confirmada">Confirmadas</option>
                                <option value="aguardando">Pendentes</option>
                                <option value="concluida">Concluidas</option>
                                <option value="cancelada">Canceladas</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Periodo</label>
                            <select>
                                <option>Hoje</option>
                                <option>Esta semana</option>
                                <option>Este mes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Buscar Paciente</label>
                            <input type="text" placeholder="Nome do paciente...">
                        </div>
                        <button class="btn-primary" style="width: 100%; justify-content: center;" onclick="showToast('Filtros aplicados!', 'info')">
                            <i class='bx bx-search'></i> Filtrar
                        </button>
                    </div>

                    <div style="max-height: 500px; overflow-y: auto;">
                        <?php foreach($mockConsultasHoje as $index => $consulta): 
                            $pac = $mockPacientes[$consulta['id_paciente']];
                            $hora = date('H:i', strtotime($consulta['data_hora_consul']));
                            $activeClass = $index === 0 ? 'active' : '';
                        ?>
                        <div class="paciente-item <?= $activeClass ?>" onclick="selectConsulta(this, '<?= $pac['id_usu'] ?>')">
                            <strong><?= $pac['nome'] ?></strong>
                            <span><i class='bx bx-time'></i> <?= $hora ?> - Convênio: <?= $pac['convenio'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Detalhes -->
                <div class="flex-1">
                    <form method="POST" action="backend_atendimento.php" class="card" id="consultaDetalhes" onsubmit="event.preventDefault(); showToast('Ação registrada com sucesso!', 'success');">
                        <input type="hidden" name="id_consulta" value="101">
                        <input type="hidden" name="id_paciente" value="1">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f0f5f9; padding-bottom: 15px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--azul-claro-bg); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: var(--azul-primario);">
                                    <i class='bx bx-user'></i>
                                </div>
                                <div>
                                    <h2 style="margin: 0; color: var(--azul-primario); font-size: 1.3rem;">João Silva Oliveira</h2>
                                    <p style="margin: 4px 0 0 0; color: var(--texto-secundario); font-size: 0.9rem;">Masculino, 42 anos | Tipo Sanguíneo: O+</p>
                                </div>
                            </div>
                            <span class="status-badge status-confirmada">Confirmada</span>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label>Convênio</label>
                                <input type="text" value="Bradesco Saúde" readonly style="background: var(--cinza-card);">
                            </div>
                            <div class="form-group">
                                <label>Telefone</label>
                                <input type="text" value="(11) 98765-4321" readonly style="background: var(--cinza-card);">
                            </div>
                            <div class="form-group">
                                <label>Data/Hora Consulta</label>
                                <input type="datetime-local" name="data_hora_consul" value="2026-05-14T14:05" readonly style="background: var(--cinza-card);">
                            </div>
                            <div class="form-group">
                                <label>Próximo Retorno</label>
                                <input type="date" name="data_retorno" value="2026-06-14" style="background: var(--cinza-card);">
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 15px;">
                            <label>Diagnóstico Anterior</label>
                            <textarea readonly style="background: var(--cinza-card); min-height: 80px;">Paciente com história de hipertensão arterial controlada. Em acompanhamento mensal. Última prescrição: Losartana 50mg 1x ao dia.</textarea>
                        </div>

                        <div class="form-group">
                            <label>Anotações da Consulta Atual (Observações)</label>
                            <textarea name="observacoes" placeholder="Descreva os sintomas, diagnóstico e conduta médica..."></textarea>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button type="submit" name="status_consulta" value="Concluída" class="btn-success">
                                <i class='bx bx-check-circle'></i> Iniciar/Concluir Atendimento
                            </button>
                            <button type="submit" name="status_consulta" value="Cancelada" class="btn-danger">
                                <i class='bx bx-x-circle'></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ==================== TAB: CALENDARIO ==================== -->
        <div id="tab-calendario" class="tab-content">
            <div class="card">
                <div class="calendario-header">
                    <button class="cal-nav-btn" onclick="alert('Mês anterior')"><i class='bx bx-chevron-left'></i></button>
                    <h3>Maio 2026</h3>
                    <button class="cal-nav-btn" onclick="alert('Próximo mês')"><i class='bx bx-chevron-right'></i></button>
                </div>

                <div class="calendario-grid">
                    <div class="calendario-dia-header">Dom</div>
                    <div class="calendario-dia-header">Seg</div>
                    <div class="calendario-dia-header">Ter</div>
                    <div class="calendario-dia-header">Qua</div>
                    <div class="calendario-dia-header">Qui</div>
                    <div class="calendario-dia-header">Sex</div>
                    <div class="calendario-dia-header">Sab</div>

                    <!-- Semana 1 -->
                    <div class="calendario-dia outro-mes"><div class="calendario-dia-numero">27</div></div>
                    <div class="calendario-dia outro-mes"><div class="calendario-dia-numero">28</div></div>
                    <div class="calendario-dia outro-mes"><div class="calendario-dia-numero">29</div></div>
                    <div class="calendario-dia outro-mes"><div class="calendario-dia-numero">30</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">1</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">2</div></div>
                    <div class="calendario-dia"><div class="calendario-dia-numero">3</div></div>

                    <!-- Semana 2 -->
                    <div class="calendario-dia"><div class="calendario-dia-numero">4</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">5</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">6</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">7</div></div>
                    <div class="calendario-dia calendario-dia-multi"><div class="calendario-dia-numero">8</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">9</div></div>
                    <div class="calendario-dia"><div class="calendario-dia-numero">10</div></div>

                    <!-- Semana 3 -->
                    <div class="calendario-dia"><div class="calendario-dia-numero">11</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">12</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">13</div></div>
                    <div class="calendario-dia hoje calendario-dia-multi"><div class="calendario-dia-numero">14</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">15</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">16</div></div>
                    <div class="calendario-dia"><div class="calendario-dia-numero">17</div></div>

                    <!-- Semana 4 -->
                    <div class="calendario-dia"><div class="calendario-dia-numero">18</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">19</div></div>
                    <div class="calendario-dia calendario-dia-multi"><div class="calendario-dia-numero">20</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">21</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">22</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">23</div></div>
                    <div class="calendario-dia"><div class="calendario-dia-numero">24</div></div>

                    <!-- Semana 5 -->
                    <div class="calendario-dia"><div class="calendario-dia-numero">25</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">26</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">27</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">28</div></div>
                    <div class="calendario-dia calendario-dia-multi"><div class="calendario-dia-numero">29</div></div>
                    <div class="calendario-dia calendario-dia-evento"><div class="calendario-dia-numero">30</div></div>
                    <div class="calendario-dia"><div class="calendario-dia-numero">31</div></div>
                </div>

                <div style="margin-top: 20px; display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.85rem; color: var(--texto-secundario);">
                        <span style="width: 10px; height: 10px; background: var(--azul-primario); border-radius: 50%; display: inline-block;"></span>
                        1 consulta
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.85rem; color: var(--texto-secundario);">
                        <span style="width: 20px; height: 4px; background: linear-gradient(90deg, var(--azul-primario), var(--azul-secundario)); border-radius: 2px; display: inline-block;"></span>
                        Multiplas consultas
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.85rem; color: var(--texto-secundario);">
                        <span style="width: 24px; height: 24px; background: var(--azul-primario); border-radius: 50%; display: inline-block;"></span>
                        Hoje
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB: PACIENTES ==================== -->
        <div id="tab-pacientes" class="tab-content">
            <div style="margin-bottom: 20px;">
                <input type="text" placeholder="Buscar paciente por nome..." style="max-width: 400px;">
            </div>

            <div class="pacientes-grid">
                <?php foreach($mockPacientes as $pac): ?>
                <div class="paciente-card">
                    <div class="paciente-avatar"><i class='bx bx-user'></i></div>
                    <h4><?= $pac['nome'] ?></h4>
                    <p><?= calculaIdade($pac['dt_nasc']) ?> anos | Tipo <?= $pac['tipo_sanguineo'] ?></p>
                    <p><i class='bx bx-health'></i> <?= $pac['convenio'] ?></p>
                    <p style="font-size: 0.75rem; margin-bottom: 15px;">Telefone: <?= $pac['telefone'] ?></p>
                    <div style="display: flex; gap: 5px; justify-content: center; margin-top: 10px;">
                        <a class="btn-sm btn-primary" href="includes/prontuario.php" style="text-decoration: none;">
                            <i class='bx bx-file'></i> Ver
                        </a>
                        <button class="btn-sm btn-outline" onclick="window.print()">
                            <i class='bx bx-printer'></i> Imprimir
                        </button>
                        <button class="btn-sm btn-outline" onclick="showToast('Download iniciado!', 'success')">
                            <i class='bx bx-download'></i> Baixar
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ==================== TAB: PRESCRICOES ==================== -->
        <div id="tab-prescricoes" class="tab-content">
            <div class="layout-flex">
                <!-- Tabela Medicamentos -->
                <div class="flex-1">
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-capsule'></i> Medicamentos para Prescrição</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Medicamento</th>
                                    <th>Tipo</th>
                                    <th>Dosagem Padrão</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($mockMedicamentos as $med): ?>
                                <tr>
                                    <td><strong><?= $med['nome'] ?></strong></td>
                                    <td><?= $med['tipo'] ?></td>
                                    <td><?= $med['dosagem'] ?></td>
                                    <td><button class="btn-sm btn-primary" onclick="adicionarMedicamento('<?= $med['nome'] ?>', '<?= $med['dosagem'] ?>', '<?= $med['instrucoes'] ?>')"><i class='bx bx-plus'></i></button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Receituario -->
                <div style="width: 400px; min-width: 350px;">
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-receipt'></i> Receituário Atual</h3>
                        <div id="receituarioLista">
                            <div class="receituario-item">
                                <div>
                                    <div class="med-nome">Losartana</div>
                                    <div class="med-dose">50mg - 1 comprimido ao dia</div>
                                </div>
                                <button onclick="removerMedicamento(this)" title="Remover"><i class='bx bx-trash'></i></button>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label>Observações Gerais</label>
                            <textarea id="observacoes_receituario" name="observacoes_receituario" placeholder="Instruções adicionais para o paciente..."></textarea>
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 15px;">          
                           <a href="includes/receituario.php" class="btn-success" style="flex: 1; justify-content: center; text-decoration: none; display: flex; align-items: center;">
                           <i class='bx bx-check-circle'></i> Finalizar Prescrição </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB: PERFIL ==================== -->
        <div id="tab-perfil" class="tab-content">
            <div class="layout-flex">
                <!-- Coluna Perfil -->
                <div style="width: 320px; min-width: 280px;">
                    <div class="card perfil-card">
                       <!--  <img src="img/avatar_medico.jpg" alt="Dr. Carlos Eduardo" class="perfil-foto"> -->
                        <h3>Dr. Carlos Eduardo Mendes</h3>
                        <p class="especialidade">Clinico Geral</p>
                        <p class="crm">CRM-SP 123456</p>
                        <span class="perfil-status">Online</span>
                    </div>
                </div>

                <!-- Coluna Info -->
                <div class="flex-1">
                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-id-card'></i> Informacoes Profissionais</h3>
                        <div class="perfil-info-grid">
                            <div class="perfil-info-item">
                                <label>Nome Completo</label>
                                <span>Carlos Eduardo Mendes Silva</span>
                            </div>
                            <div class="perfil-info-item">
                                <label>CRM</label>
                                <span>SP-123456</span>
                            </div>
                            <div class="perfil-info-item">
                                <label>Especialidade</label>
                                <span>Clinica Medica / Clinico Geral</span>
                            </div>
                            <div class="perfil-info-item">
                                <label>Email</label>
                                <span>dr.carlos@biovita.com.br</span>
                            </div>
                            <div class="perfil-info-item">
                                <label>Telefone</label>
                                <span>(11) 98765-1234</span>
                            </div>
                            <div class="perfil-info-item">
                                <label>UBS Vinculada</label>
                                <span>UBS Vila Nova - Sao Paulo/SP</span>
                            </div>
                            <div class="perfil-info-item">
                                <label>Horario de Atendimento</label>
                                <span>Seg a Sex: 08h as 17h</span>
                            </div>
                            <div class="perfil-info-item">
                                <label>Dias de Plantao</label>
                                <span>Segundas, Quartas e Sextas</span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h3 class="card-title"><i class='bx bx-cog'></i> Configuracoes Rapidas</h3>
                        <div class="toggle-row">
                            <span>Notificacoes de consulta</span>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="toggle-row">
                            <span>Lembretes de prescrição</span>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="toggle-row">
                            <span>Disponivel para agendamento</span>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="receituarioOverlay" class="overlay-receituario">
        <div class="overlay-content">
            <button class="overlay-close" onclick="fecharOverlayReceituario()">&times;</button>
            <div class="overlay-header">
                <div class="document-logo-box">
                    <img src="includes/img/logo_biovita.png" alt="Bio Vita" class="document-logo">
                    <div>
                        <h2>Receituário Médico</h2>
                        <p>Documento pronto para impressão e salvamento.</p>
                    </div>
                </div>
                <div class="overlay-actions">
                    <button class="btn-primary" onclick="imprimirReceituario()"><i class='bx bx-printer'></i> Imprimir</button>
                    <button class="btn-outline" onclick="baixarReceituarioPDF()"><i class='bx bx-download'></i> Baixar PDF</button>
                </div>
            </div>

            <div class="preview-box">
                <div class="preview-meta">
                    <span>Data: 14/05/2026</span>
                    <span>Médico: Dr. Carlos Eduardo Mendes</span>
                </div>
                <div class="document-section">
                    <h3>Prescrições</h3>
                    <ul id="previewReceituarioList" class="prescricao-list"></ul>
                </div>
                <div class="document-section">
                    <h3>Observações</h3>
                    <p id="previewReceituarioNotes">Nenhuma observação adicional.</p>
                </div>
            </div>
        </div>
    </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        <script src="medico.js"></script>
</body>
</html>
