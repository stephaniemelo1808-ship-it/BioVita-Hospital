DROP DATABASE trabalho;
CREATE DATABASE trabalho
DEFAULT CHARACTER SET utf8
DEFAULT collate utf8_general_ci;
use trabalho;
-- /----------------------------------------------------------------------------------------------------\

create table registro_usuario(
	id int auto_increment,  
	nome varchar(255) not null,
    cpf decimal(11) not null unique,
	telefone varchar(20),
    csn char(15) not null unique,
	endereco varchar(255) not null,
    dt_nasc date not null,
    primary key(id)
);

drop table registro_medico;
CREATE TABLE registro_medico (
    id_med INT AUTO_INCREMENT PRIMARY KEY,
    id_log INT NOT NULL UNIQUE,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    crm VARCHAR(20) NOT NULL UNIQUE,
    uf CHAR(2) NOT NULL,
    telefone VARCHAR(20),
    ubs VARCHAR(100),
    FOREIGN KEY (id_log) REFERENCES login(id_log)
);
/*----------------------------------------------------------------------------------------------------*/
drop table login;
create table login (
	id_log int auto_increment,
	usuario varchar(255) not null unique,
    senha_usu varchar(255) not null,
    tipo_usu enum ('Administrador', 'Médico','Recepção') NOT NULL,
    nome_usu varchar(255) not null ,
    primary key(id_log)
); 

/*----------------------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------------------*/
drop table consultas;
CREATE TABLE consultas (
    id_consulta INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,  
    id_log_medico INT NOT NULL, 
    data_hora_consul DATETIME NOT NULL,
    status_consulta ENUM('Agendada', 'Confirmada', 'Em Andamento', 'Concluída', 'Cancelada') DEFAULT 'Agendada',
	tipo_consulta VARCHAR(50) DEFAULT 'Rotina',
    observacoes TEXT,
    data_retorno DATE,
    FOREIGN KEY (id_paciente) REFERENCES registro_usuario(id),
    FOREIGN KEY (id_log_medico) REFERENCES login(id_log)
);

/*----------------------------------------------------------------------------------------------------*/
CREATE TABLE prescricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta INT NOT NULL,
    medicamento VARCHAR(255),
    dosagem VARCHAR(100),
    instrucoes TEXT,
    data_prescricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
/*----------------------------------------------------------------------------------------------------*/
DELIMITER $$
CREATE TRIGGER valida_cargo_medico
BEFORE INSERT ON registro_medico
FOR EACH ROW
BEGIN
    DECLARE cargo VARCHAR(50);
    
    
    SELECT tipo_usu INTO cargo FROM login WHERE id_log = NEW.id_log;
    
    
    IF cargo != 'Médico' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Bloqueado: Este usuário não tem o cargo de Médico na tabela login.';
    END IF;
END $$

DELIMITER ;