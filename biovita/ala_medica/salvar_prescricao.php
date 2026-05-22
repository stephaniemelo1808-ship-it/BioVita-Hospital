<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_consulta']) && !empty($_POST['lista_meds'])) {
    
    $id_consulta = (int)$_POST['id_consulta'];
    $meds_json = $_POST['lista_meds'];
    $meds = json_decode($meds_json, true);

    if (is_array($meds)) {
        foreach($meds as $m) {
            $nome = $mysqli->real_escape_string($m['nome']);
            $dose = $mysqli->real_escape_string($m['dosagem']);
            $instr = $mysqli->real_escape_string($m['instrucoes']);
            
            $mysqli->query("INSERT INTO prescricoes (id_consulta, medicamento, dosagem, instrucoes) 
                            VALUES ('$id_consulta', '$nome', '$dose', '$instr')");
        }
    }
    
    header("Location: medico.php?aba=tab-prescricoes&sucesso=1");
    exit();
} else {
    echo "Erro: Dados incompletos.";
}
?>