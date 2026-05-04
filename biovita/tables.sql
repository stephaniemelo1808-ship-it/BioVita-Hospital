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
/*
create table registro_medico(
	id_med int auto_increment,
    nome varchar(255) not null,
    senha varchar(255) not null,
    email varchar(255) not null unique,
    cpf decimal(11) not null unique,
    crn char(15) not null unique,
	uf char(2) not null,
    telefone varchar(20),
    primary key(id_med)
);
*/

drop table registro_medico;
CREATE TABLE registro_medico (
    id_med INT AUTO_INCREMENT PRIMARY KEY,
    id_log INT NOT NULL UNIQUE,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    crm VARCHAR(20) NOT NULL UNIQUE,
    uf CHAR(2) NOT NULL,
    telefone VARCHAR(20),
    FOREIGN KEY (id_log) REFERENCES login(id_log)
);
/*----------------------------------------------------------------------------------------------------*/
create table login (
	id_log int auto_increment,
	usuario varchar(255) not null unique,
    senha_usu varchar(255) not null,
    tipo_usu enum ('Administrador', 'Médico','Recepção') NOT NULL,
    nome_usu varchar(255) not null ,
    primary key(id_log)
); 

/*----------------------------------------------------------------------------------------------------*/
/*
create table consultas (
	id_consul int auto_increment,
    id_usu int not null,
    dt_consul date not null,
    hora_consul time not null,
    status_consul enum ('Agendada', 'Em Andamento', 'Concluída', 'Remarcada'),
    primary key (id_consul)
);

*/
/*----------------------------------------------------------------------------------------------------*/

CREATE TABLE consultas (
    id_consulta INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,  
    id_log_medico INT NOT NULL, 
    data_hora_consul DATETIME NOT NULL,
    status_consulta VARCHAR(50) DEFAULT 'Agendada',
    FOREIGN KEY (id_paciente) REFERENCES registro_usuario(id),
    FOREIGN KEY (id_log_medico) REFERENCES login(id_log)
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

/*----------------------------------------------------------------------------------------------------*/
-- ALTERAÇÕES, INSERTS:

alter table registro_usuario
change column csn cns char(15) not null unique;

alter table registro_usuario
change column id id_usu int auto_increment;
desc registro_usuario;

insert into login (id_log, nome_usu, senha_usu) values
(1, 'teste', '123');

select * from login;

alter table login
add column tipo_usu enum ('Administrador', 'Médico','Recepção') NOT NULL;

desc login;

alter table login
change column nome_usu usuario varchar(255) not null unique;

alter table login
add column nome_usu varchar(255) not null;

select * from registro_usuario;
desc registro_usuario;

INSERT INTO registro_usuario (nome, cpf, telefone, csn, endereco, dt_nasc) VALUES
('Tomás João Caleb Figueiredo', '38899416052', '(47) 99420-4379', '700000000000001', 'Rua Bernardino Ramos, 315, Barra do Rio, Itajaí - SC', '1993-03-16'),
('Pietro Felipe Fábio Bernardes', '93102418295', '(48) 98926-2591', '700000000000002', 'Servidão da União, 498, Tapera, Florianópolis - SC', '1982-04-04'),
('Sara Maria Joana Lopes', '23205300106', '(86) 98505-4159', '700000000000003', 'Quadra 03, 516, Santo Antônio, Teresina - PI', '1968-03-19'),
('Luiza Andrea Barros', '61938922735', '(98) 98663-8976', '700000000000004', 'Avenida Gapara, 430, Cidade Nova, São Luís - MA', '1963-04-28'),
('Carlos Eduardo Carlos Eduardo Cavalcanti', '85947785200', '(79) 98415-5450', '700000000000005', 'Rua C, 104, Zona de Expansão (Robalo), Aracaju - SE', '1956-01-27'),
('Iago Diogo Ian Castro', '72921943972', '(88) 98259-9152', '700000000000006', 'Rua Stênio Medeiros, 573, Paraná, Iguatu - CE', '1973-03-15'),
('Roberto Yuri Souza', '37092051070', '(96) 99256-9604', '700000000000007', 'Travessa Quinta do Matadouro, 497, Fazendinha, Macapá - AP', '1980-04-13'),
('Heloisa Beatriz Monteiro', '33681043221', '(51) 98153-4911', '700000000000008', 'Largo das Paineiras, 925, Vila João Pessoa, Porto Alegre - RS', '1968-03-23'),
('Gabrielly Esther Sabrina da Costa', '59314174000', '(51) 99152-2819', '700000000000009', 'Rua Santa Marta, 848, Santa Fé, Gravataí - RS', '2002-01-09'),
('Oliver Fábio Kaique Alves', '94631245948', '(92) 98753-1669', '700000000000010', 'Rua Lírio-do-Japão, 207, Novo Aleixo, Manaus - AM', '1949-02-27'),
('Vera Beatriz Lima', '51378069609', '(92) 98386-8725', '700000000000011', 'Avenida Cravina dos Poetas, 543, Planalto, Manaus - AM', '1950-01-24'),
('Cláudio Erick Alexandre Corte Real', '26371446703', '(71) 99719-5664', '700000000000012', 'Rua Santa Brígida, 641, Boa Vista de São Caetano, Salvador - BA', '1971-01-10'),
('Antonio Enzo Teixeira', '46848061468', '(66) 99743-4804', '700000000000013', 'Rua A-109, 385, Parque Sagrada Família, Rondonópolis - MT', '1993-03-01'),
('Patrícia Stefany Moura', '55355179681', '(96) 98594-6892', '700000000000014', 'Rua Jovino Dinoá, 457, Central, Macapá - AP', '2008-02-16'),
('Priscila Luzia Ribeiro', '46356816376', '(95) 98777-5767', '700000000000015', 'Rua dos Trabalhadores, 462, Operário, Boa Vista - RR', '1969-01-21'),
('Mariah Camila Camila Martins', '95621029712', '(67) 98331-7363', '700000000000016', 'Rua General Andrade Neves, 212, Vila Boa Vista, Ponta Porã - MS', '1974-01-07'),
('Cristiane Bruna Novaes', '07917155404', '(96) 99579-2202', '700000000000017', 'Avenida Antônio Carlos Reis, 237, Novo Horizonte, Macapá - AP', '1999-02-23'),
('Vicente Elias Santos', '63663543404', '(31) 98442-5141', '700000000000018', 'Rua Maçon Ribeiro, 643, Parque São Pedro (Venda Nova), Belo Horizonte - MG', '1994-01-13'),
('Ryan Danilo dos Santos', '22531432000', '(11) 99309-0291', '700000000000019', 'Avenida Armando de Andrade, 377, Parque Santos Dumont, Taboão da Serra - SP', '1968-01-22'),
('Hugo Iago Guilherme Viana', '68348067337', '(62) 99417-0333', '700000000000020', 'Rua Barão do Rio Branco, 938, Vila Santana, Anápolis - GO', '1985-01-05'),
('Sara Luana Farias', '28232975296', '(41) 99269-6627', '700000000000021', 'Rua Maria Clara de Jesus, 176, Ganchinho, Curitiba - PR', '1957-02-02'),
('Danilo Severino Caldeira', '61838947531', '(21) 99384-4922', '700000000000022', 'Rua Santa Mônica, 217, Parque São Vicente, Belford Roxo - RJ', '1988-02-27'),
('Andreia Sophie Olivia da Cunha', '04660050750', '(11) 99623-2001', '700000000000023', 'Rua Axicará, 564, Vila Isolina Mazzei, São Paulo - SP', '2007-01-21'),
('Caroline Silvana Tânia Mendes', '52382635037', '(68) 98645-8490', '700000000000024', 'Rua N5, 145, Conjunto Tucumã, Rio Branco - AC', '2006-03-11'),
('Luiz Erick Peixoto', '91021565555', '(68) 98381-3555', '700000000000025', 'Rua Primavera, 980, Bahia Nova, Rio Branco - AC', '1961-03-17'),
('Enzo Joaquim Diego da Mata', '46183685275', '(86) 99493-0867', '700000000000026', 'Quadra I1, 872, Esplanada, Teresina - PI', '1954-01-03'),
('Victor Fábio Davi Melo', '54613639620', '(92) 99639-7787', '700000000000027', 'Rua Jordânia, 161, Parque 10 de Novembro, Manaus - AM', '1959-01-11'),
('Pedro Otávio Edson Araújo', '79040974608', '(81) 98762-8311', '700000000000028', 'Rua Solânia, 922, COHAB, Recife - PE', '1998-01-16'),
('Henrique Paulo Ferreira', '45735454501', '(81) 99424-6780', '700000000000029', '7ª Travessa Álvares Teixeira de Mesquita, 165, Tabatinga, Camaragibe - PE', '1991-03-14'),
('Vicente Gabriel Gomes', '29484852580', '(67) 98921-3833', '700000000000030', 'Rua Artagnan dos Santos Machado, 171, Bosque das Araras, Campo Grande - MS', '1949-01-11');

INSERT INTO login (nome_usu, senha_usu, tipo_usu) 
VALUES ('Carlos Eduardo', 'senha123', 'Médico');

INSERT INTO login (nome_usu, senha_usu, tipo_usu) 
VALUES ('Ana Beatriz', 'senha123', 'Médico');