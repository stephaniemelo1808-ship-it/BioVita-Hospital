<?php

/* =========================================================
DADOS FICTÍCIOS

Futuramente:
substituir por banco de dados MySQL.

Compatível com:
- login
- registro_medico
- consultas
- registro_usuario
========================================================= */

// Mock Data - Pacientes
$mockPacientes = [
    1 => ['id_usu' => 1, 'nome' => 'João Silva Oliveira', 'telefone' => '(11) 98765-4321', 'dt_nasc' => '1982-05-10', 'convenio' => 'Bradesco Saúde', 'tipo_sanguineo' => 'O+'],
    2 => ['id_usu' => 2, 'nome' => 'Maria Aparecida Santos', 'telefone' => '(11) 91234-5678', 'dt_nasc' => '1991-08-22', 'convenio' => 'Particular', 'tipo_sanguineo' => 'A+'],
    3 => ['id_usu' => 3, 'nome' => 'Pedro Henrique Lima', 'telefone' => '(11) 99988-7766', 'dt_nasc' => '1998-02-15', 'convenio' => 'SUS', 'tipo_sanguineo' => 'B+'],
    4 => ['id_usu' => 4, 'nome' => 'Ana Lucia Ferreira', 'telefone' => '(11) 97766-5544', 'dt_nasc' => '1968-11-05', 'convenio' => 'Amil', 'tipo_sanguineo' => 'AB+'],
    5 => ['id_usu' => 5, 'nome' => 'Roberto Carlos Dias', 'telefone' => '(11) 96655-4433', 'dt_nasc' => '1961-04-19', 'convenio' => 'Unimed', 'tipo_sanguineo' => 'O-']
];

function calculaIdade($dataNasc) {
    $data = new DateTime($dataNasc);
    $agora = new DateTime();
    return $agora->diff($data)->y;
}

// Mock Data - Consultas de Hoje (Painel e Consultas)
$mockConsultasHoje = [
    ['id_consulta' => 101, 'id_paciente' => 1, 'data_hora_consul' => '2026-05-14 14:05:00', 'status_consulta' => 'Em Andamento'],
    ['id_consulta' => 102, 'id_paciente' => 2, 'data_hora_consul' => '2026-05-14 14:30:00', 'status_consulta' => 'Agendada'],
    ['id_consulta' => 103, 'id_paciente' => 3, 'data_hora_consul' => '2026-05-14 15:00:00', 'status_consulta' => 'Agendada'],
    ['id_consulta' => 104, 'id_paciente' => 4, 'data_hora_consul' => '2026-05-14 15:30:00', 'status_consulta' => 'Agendada'],
    ['id_consulta' => 105, 'id_paciente' => 5, 'data_hora_consul' => '2026-05-14 16:00:00', 'status_consulta' => 'Agendada'],
];

// Mock Data - Relatorios (Historico)
$mockRelatorios = [
    ['id_relatorio' => 1, 'id_paciente' => 1, 'data_hora' => '2026-05-07 10:00:00', 'procedimentos' => 'Consulta de Rotina, Aferição de Pressão', 'status' => 'Concluída', 'observacoes' => 'Paciente com história de hipertensão arterial controlada. Em acompanhamento mensal. Prescrito Losartana 50mg.'],
    ['id_relatorio' => 2, 'id_paciente' => 2, 'data_hora' => '2026-05-10 14:00:00', 'procedimentos' => 'Retorno, Exames Laboratoriais', 'status' => 'Concluída', 'observacoes' => 'Exames normais. Manter acompanhamento.'],
    ['id_relatorio' => 3, 'id_paciente' => 4, 'data_hora' => '2026-05-05 09:30:00', 'procedimentos' => 'Consulta de Urgência', 'status' => 'Concluída', 'observacoes' => 'Quadro viral agudo. Receitado sintomáticos e repouso de 3 dias.'],
    ['id_relatorio' => 4, 'id_paciente' => 5, 'data_hora' => '2026-05-08 11:15:00', 'procedimentos' => 'Avaliação Cardiológica', 'status' => 'Concluída', 'observacoes' => 'ECG sem alterações isquêmicas. Retorno em 6 meses.']
];

// Mock Data - Perfil do Médico
$mockMedico = [
    'nome_usu' => 'Carlos Eduardo',
    'crm' => 'SP-123456',
    'especialidade' => 'Clínica Médica / Clínico Geral',
    'email' => 'dr.carlos@biovita.com.br',
    'telefone' => '(11) 98765-1234'
];
// Mock Data - Medicamentos
$mockMedicamentos = [
    ['nome' => 'Paracetamol', 'tipo' => 'Analgesico', 'dosagem' => '750mg', 'instrucoes' => '1 comprimido a cada 8h'],
    ['nome' => 'Ibuprofeno', 'tipo' => 'Anti-inflamatorio', 'dosagem' => '600mg', 'instrucoes' => '1 comprimido a cada 12h'],
    ['nome' => 'Amoxicilina', 'tipo' => 'Antibiotico', 'dosagem' => '500mg', 'instrucoes' => '1 capsula a cada 8h por 7 dias'],
    ['nome' => 'Dipirona', 'tipo' => 'Analgesico', 'dosagem' => '1g', 'instrucoes' => '1 comprimido a cada 6h se dor'],
    ['nome' => 'Omeprazol', 'tipo' => 'Protetor gastrico', 'dosagem' => '20mg', 'instrucoes' => '1 capsula em jejum'],
    ['nome' => 'Losartana', 'tipo' => 'Anti-hipertensivo', 'dosagem' => '50mg', 'instrucoes' => '1 comprimido ao dia'],
    ['nome' => 'Metformina', 'tipo' => 'Anti-diabetico', 'dosagem' => '850mg', 'instrucoes' => '1 comprimido 2x ao dia'],
    ['nome' => 'Azitromicina', 'tipo' => 'Antibiotico', 'dosagem' => '500mg', 'instrucoes' => '1 capsula ao dia por 5 dias']
];

?>