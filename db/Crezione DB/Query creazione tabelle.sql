-- Nome database: BBFC
drop table if exists abbonamento;
drop table if exists cliente;
drop table if exists accesso;
drop table if exists personal_trainer;
drop table if exists cliente_personal_trainer;
drop table if exists allenamento;
drop table if exists cliente_allenamento;
drop table if exists sala;
drop table if exists corso;
drop table if exists cliente_corso;
drop table if exists personal_trainer_corso;
drop table if exists persona_trainer_sala;
drop table if exists attrezzatura;
drop table if exists esercizio;
drop table if exists esercizio_attrezzatura;
drop table if exists allenamento_esercizio;
drop table if exists corso_attrezzatura;
drop table if exists sala_attrezzatura;

create table abbonamento(
    nome varchar(50) primary key,
    prezzo decimal(5,2) not null check (prezzo >= 0)
);

create table cliente(
    username varchar(50) primary key,
    password char(60) not null,
    nome varchar(20) not null,
    cognome varchar(20) not null,
    email varchar(50),
    data_nascita date not null,
    badge char(16) not null,
    entrate smallint unsigned default 0,
    numero_telefono varchar(20),
    nome_abbonamento varchar(50)
        references abbonamento(nome)
            on delete set null
            on update cascade,
    data_inizio date,
    data_fine date,

    constraint CHK_cliente check (data_inizio <= data_fine)
);

create table accesso(
    username_cliente varchar(50)
        references cliente(username)
            on delete cascade
            on update cascade,
    dataora_entrata datetime default current_timestamp,
    dataora_uscita datetime,

    
    primary key(username_cliente, dataora_entrata),
    constraint CHK_accesso check (dataora_entrata <= dataora_uscita)
);

create table personal_trainer(
    id int primary key auto_increment,
    nome varchar(20) not null,
    cognome varchar(20) not null,
    email varchar(50),
    numero_telefono varchar(20)
);

create table cliente_personal_trainer(
    username_cliente varchar(50)
        references cliente(username)
            on delete cascade
            on update cascade,
    id_personal_trainer int
        references personal_trainer(id)
            on delete cascade
            on update cascade,

    primary key(username_cliente, id_personal_trainer)
);

create table allenamento(
    id int primary key auto_increment, 
    nome varchar(100) not null,
    descrizione text,
    username_cliente varchar(50)
        references cliente(username)
            on delete set null
            on update cascade,
    data_creazione date default (current_date),
    id_personal_trainer int
        references personal_trainer(id)
            on delete set null
            on update cascade
);

create table cliente_allenamento(
    username_cliente varchar(50)
        references cliente(username)
            on delete cascade
            on update cascade,
    id_allenamento int
        references allenamento(id)
            on delete cascade
            on update cascade,

    primary key(username_cliente, id_allenamento)
);

create table sala(
    nome varchar(50) primary key
);

create table corso(
    nome varchar(50) primary key,
    nome_sala varchar(50)
        references sala(nome)
            on delete set null
            on update cascade
);

create table cliente_corso(
    username_cliente varchar(50)
        references cliente(username)
            on delete cascade
            on update cascade,
    nome_corso varchar(50)
        references corso(nome)
            on delete cascade
            on update cascade,

    primary key(username_cliente, nome_corso)
);

create table personal_trainer_corso(
    id_personal_trainer int
        references personal_trainer(id)
            on delete cascade
            on update cascade,
    nome_corso varchar(50)
        references corso(nome)
            on delete cascade
            on update cascade,

    primary key(id_personal_trainer, nome_corso)
);

create table persona_trainer_sala(
    id_personal_trainer int
        references personal_trainer(id)
            on delete cascade
            on update cascade,
    nome_sala varchar(50)
        references sala(nome)
            on delete cascade
            on update cascade,

    primary key(id_personal_trainer, nome_sala)
);

create table attrezzatura(
    nome varchar(50) primary key
);

create table esercizio(
    nome varchar(100) primary key,
    descrizione text,
    nome_sala varchar(50)
        references sala(nome)
            on delete set null
            on update cascade
);

create table esercizio_attrezzatura(
    nome_esercizio varchar(100)
        references esercizio(nome)
            on delete cascade
            on update cascade,
    nome_attrezzatura varchar(50)
        references attrezzatura(nome)
            on delete cascade
            on update cascade,

    primary key(nome_esercizio, nome_attrezzatura)
);

create table allenamento_esercizio(
    id_allenamento int
        references allenamento(id)
            on delete cascade
            on update cascade,
    nome_esercizio varchar(100)
        references esercizio(nome)
            on delete cascade
            on update cascade,
    peso decimal(5,1) default 0 check (peso >= 0),
    ripetizioni tinyint unsigned default 1,
    serie tinyint unsigned default 1,
    durata time,
    
    primary key(id_allenamento, nome_esercizio)
);

create table corso_attrezzatura(
    nome_corso varchar(50)
        references corso(nome)
            on delete cascade
            on update cascade,
    nome_attrezzatura varchar(50)
        references attrezzatura(nome)
            on delete cascade
            on update cascade,

    primary key(nome_corso, nome_attrezzatura)
);

create table sala_attrezzatura(
    nome_sala varchar(50)
        references sala(nome)
            on delete cascade
            on update cascade,
    nome_attrezzatura varchar(50)
        references attrezzatura(nome)
            on delete cascade
            on update cascade,

    primary key(nome_sala, nome_attrezzatura)
);