-- Nome database: BBFC
drop table if exists abbonamento;
drop table if exists utente;
drop table if exists accesso;
drop table if exists personal_trainer;
drop table if exists utente_personal_trainer;
drop table if exists allenamento;
drop table if exists utente_allenamento;
drop table if exists sala;
drop table if exists corso;
drop table if exists utente_corso;
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

create table utente(
    username varchar(50) primary key,
    password char(60) not null,
    nome varchar(20) not null,
    cognome varchar(20) not null,
    email varchar(50),
    data_nascita date not null,
    badge char(16) unique,
    entrate smallint unsigned default 0,
    numero_telefono varchar(20),
    nome_abbonamento varchar(50)
        references abbonamento(nome)
            on delete set null
            on update cascade,
    data_inizio date,
    data_fine date,
    is_admin boolean default false,
    constraint CHK_utente check (data_inizio <= data_fine)
);

create table accesso(
    username_utente varchar(50)
        references utente(username)
            on delete cascade
            on update cascade,
    dataora_entrata datetime default current_timestamp,
    dataora_uscita datetime,

    
    primary key(username_utente, dataora_entrata),
    constraint CHK_accesso check (dataora_entrata <= dataora_uscita)
);

create table personal_trainer(
    id int primary key auto_increment,
    nome varchar(20) not null,
    cognome varchar(20) not null,
    email varchar(50),
    numero_telefono varchar(20)
);

create table utente_personal_trainer(
    username_utente varchar(50)
        references utente(username)
            on delete cascade
            on update cascade,
    id_personal_trainer int
        references personal_trainer(id)
            on delete cascade
            on update cascade,

    primary key(username_utente, id_personal_trainer)
);

create table allenamento(
    id int primary key auto_increment, 
    nome varchar(100) not null,
    descrizione text,
    username_utente varchar(50)
        references utente(username)
            on delete set null
            on update cascade,
    data_creazione date default (current_date),
    id_personal_trainer int
        references personal_trainer(id)
            on delete set null
            on update cascade
);

create table utente_allenamento(
    username_utente varchar(50)
        references utente(username)
            on delete cascade
            on update cascade,
    id_allenamento int
        references allenamento(id)
            on delete cascade
            on update cascade,

    primary key(username_utente, id_allenamento)
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

create table utente_corso(
    username_utente varchar(50)
        references utente(username)
            on delete cascade
            on update cascade,
    nome_corso varchar(50)
        references corso(nome)
            on delete cascade
            on update cascade,

    primary key(username_utente, nome_corso)
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

insert into abbonamento(nome, prezzo)
values ('Mensile', 80),
	   ('Annuale', 760),
	   ('Resort Pass', 0);

insert into utente(username, password, nome, cognome, email, data_nascita, badge, entrate, numero_telefono, nome_abbonamento, data_inizio, data_fine, is_admin)
values ('admin', '$2y$10$C1t0K8PSME4joo94czYkUutjnjA3gyfwebXU6FT/aXviqpoUgidKy', 'Ad', 'Min', 'admin@gmail.com', '1970-01-01', 'BID61d8b7d0759a7', 2, '+39 377 541 2343', 'Annuale', '2021-01-01', '2021-12-31', true),
	   ('user', '$2y$10$3zE3K.4w70zcoQLEQPvrouAoSuWn/Gnq7UAfhQKlNcUf5x50TV86S', 'Mario', 'Rossi', 'mariorossi@gmail.com', '2000-01-29', 'BID61d8b7d0759ac', 8, '+689 36 28 12 88', 'Mensile', '2021-01-13', '2021-02-13', false);

insert into accesso(username_utente, dataora_entrata, dataora_uscita)
values ('user', '2021-12-23 17:00', '2021-12-23 19:30'),
	   ('user', '2021-01-29 16:00', null),
	   ('admin', '2021-11-23 17:00', '2021-11-23 19:30'),
	   ('admin', '2021-02-27 16:00', null);

insert into personal_trainer(nome, cognome, email, numero_telefono)
values ('Fabio', 'Villotti', 'fabio.villotti@bbfc.com', '+39 111 111 1111'),
	   ('Adriana', 'Lima', 'adriana.lima@bbfc.com', '+39 222 222 2222'),
	   ('Chris', 'Edwards', 'chris.edwards@bbfc.com', '+39 333 333 3333'),
	   ('Fen', 'Wang Li', 'fen.wangli@bbfc.com', '+39 444 444 4444'),
	   ('Costas', 'Garcia', 'costas.garcia@bbfc.com', '+39 555 555 5555'),
	   ('Anastasia', 'Smirnova', 'anastasia.smirnova@bbfc.com', '+39 666 666 6666'),
	   ('Gustavo', 'Perez', 'gustavo.perez@bbfc.com', '+39 777 777 7777');

insert into utente_personal_trainer(username_utente, id_personal_trainer)
values ('admin', '1'),
	   ('admin', '2'),
	   ('admin', '3'),
	   ('admin', '4'),
	   ('admin', '5'),
	   ('admin', '6'),
	   ('admin', '7'),
	   ('user', '1'),
	   ('user', '2'),
	   ('user', '3'),
	   ('user', '4'),
	   ('user', '5'),
	   ('user', '6'),
	   ('user', '7');

insert into allenamento(nome, descrizione, username_utente, data_creazione, id_personal_trainer)
values ('Brucia grassi', 'Principalmente esercizi cardio per bruciare grassi e dimagrire', 'admin', '2021-12-28', null),
	   ('Massa muscolare', 'Tutti gli esercizi per mettere su muscoli VELOCEMENTE!', 'admin', '2021-11-28', 1),
	   ('Allenamento per ritornare in forma', 'In un mese si può ritornare in forma seguendo gli esericizi con il giusto ritmo', 'user', '2021-12-27', null),
	   ('Gambe e spalle', 'Principalmente gambe, ma anche spalle nel finale di allenamento', 'user', '2021-11-27', 2);

insert into utente_allenamento(username_utente, id_allenamento)
values ('admin', 1),
	   ('admin', 2),
	   ('admin', 3),
	   ('admin', 4),
	   ('user', 1),
	   ('user', 2),
	   ('user', 3),
	   ('user', 4);

insert into sala(nome)
values ('Sala pesi'),
	   ('Sala cardio'),
	   ('Sala fitness'),
	   ('Piscina'),
	   ('Spazio outdoor'),
	   ('Giardino zen');

insert into corso(nome, nome_sala)
values ('Boxe', 'Sala pesi'),
	   ('Clisthenics', 'Sala fitness'),
	   ('Cross Active Induction', 'Sala cardio'),
	   ('HIIT', 'Sala fitness'),
	   ('Canoe polinesiane va\'a', 'Spazio outdoor'),
	   ('Cycle Burn', 'Sala cardio'),
	   ('Aero Dance', 'Sala fitness'),
	   ('Zumba', 'Sala fitness'),
	   ('Nuoto Sincronizzato', 'Piscina'),
	   ('Aero Pilates', 'Sala fitness'),
	   ('Dynamic Dancing', 'Sala fitness'),
	   ('Performance Long Run', 'Spazio outdoor'),
	   ('Camminata', 'Spazio outdoor'),
	   ('Performance Speed Run', 'Sala cardio'),
	   ('Abdominal', 'Sala pesi'),
	   ('Arms Muscle', 'Sala pesi'),
	   ('Back Muscle', 'Sala pesi'),
	   ('Low Body', 'Sala pesi'),
	   ('Full Body', 'Sala pesi'),
	   ('Active Jump', 'Sala fitness'),
	   ('Upper Body', 'Sala pesi'),
	   ('Pilates', 'Giardino zen'),
	   ('Power Yoga', 'Giardino zen'),
	   ('Aerogym', 'Giardino zen'),
	   ('Leg Flexability', 'Giardino zen'),
	   ('Water Endurance', 'Piscina'),
	   ('Water Hydrobike', 'Piscina'),
	   ('Water Tone', 'Piscina'),
	   ('Water Reaxraft', 'Piscina'),
	   ('Meditation', 'Giardino zen'),
	   ('Postural Training', 'Spazio outdoor'),
	   ('Flexability', 'Giardino zen'),
	   ('Gravity Pilates', 'Giardino zen'),
	   ('Mat Pilates', 'Giardino zen'),
	   ('Postural Recomposition', 'Sala fitness'),
	   ('Child Fun Activity', 'Spazio outdoor'),
	   ('Psyco Wellness', 'Spazio outdoor'),
	   ('Wellness Coaching', 'Spazio outdoor');

insert into utente_corso(username_utente, nome_corso)
values ('admin', 'Boxe'),
	   ('admin', 'Clisthenics'),
	   ('admin', 'Cross Active Induction'),
	   ('admin', 'HIIT'),
	   ('admin', 'Canoe polinesiane va\'a'),
	   ('admin', 'Cycle Burn'),
	   ('admin', 'Aero Dance'),
	   ('admin', 'Zumba'),
	   ('admin', 'Nuoto Sincronizzato'),
	   ('admin', 'Aero Pilates'),
	   ('admin', 'Dynamic Dancing'),
	   ('admin', 'Performance Long Run'),
	   ('admin', 'Camminata'),
	   ('admin', 'Performance Speed Run'),
	   ('admin', 'Abdominal'),
	   ('admin', 'Arms Muscle'),
	   ('admin', 'Back Muscle'),
	   ('admin', 'Low Body'),
	   ('admin', 'Full Body'),
	   ('admin', 'Active Jump'),
	   ('admin', 'Upper Body'),
	   ('admin', 'Pilates'),
	   ('admin', 'Power Yoga'),
	   ('admin', 'Aerogym'),
	   ('admin', 'Leg Flexability'),
	   ('admin', 'Water Endurance'),
	   ('admin', 'Water Hydrobike'),
	   ('admin', 'Water Tone'),
	   ('admin', 'Water Reaxraft'),
	   ('admin', 'Meditation'),
	   ('admin', 'Postural Training'),
	   ('admin', 'Flexability'),
	   ('admin', 'Gravity Pilates'),
	   ('admin', 'Mat Pilates'),
	   ('admin', 'Postural Recomposition'),
	   ('admin', 'Child Fun Activity'),
	   ('admin', 'Psyco Wellness'),
	   ('admin', 'Wellness Coaching'),
	   ('user', 'Boxe'),
	   ('user', 'Clisthenics'),
	   ('user', 'Cross Active Induction'),
	   ('user', 'HIIT'),
	   ('user', 'Canoe polinesiane va\'a'),
	   ('user', 'Cycle Burn'),
	   ('user', 'Aero Dance'),
	   ('user', 'Zumba'),
	   ('user', 'Nuoto Sincronizzato'),
	   ('user', 'Aero Pilates'),
	   ('user', 'Dynamic Dancing'),
	   ('user', 'Performance Long Run'),
	   ('user', 'Camminata'),
	   ('user', 'Performance Speed Run'),
	   ('user', 'Abdominal'),
	   ('user', 'Arms Muscle'),
	   ('user', 'Back Muscle'),
	   ('user', 'Low Body'),
	   ('user', 'Full Body'),
	   ('user', 'Active Jump'),
	   ('user', 'Upper Body'),
	   ('user', 'Pilates'),
	   ('user', 'Power Yoga'),
	   ('user', 'Aerogym'),
	   ('user', 'Leg Flexability'),
	   ('user', 'Water Endurance'),
	   ('user', 'Water Hydrobike'),
	   ('user', 'Water Tone'),
	   ('user', 'Water Reaxraft'),
	   ('user', 'Meditation'),
	   ('user', 'Postural Training'),
	   ('user', 'Flexability'),
	   ('user', 'Gravity Pilates'),
	   ('user', 'Mat Pilates'),
	   ('user', 'Postural Recomposition'),
	   ('user', 'Child Fun Activity'),
	   ('user', 'Psyco Wellness'),
	   ('user', 'Wellness Coaching');

insert into personal_trainer_corso(id_personal_trainer, nome_corso)
values (1, 'Boxe'),
	   (1, 'Clisthenics'),
	   (1, 'Cross Active Induction'),
	   (1, 'HIIT'),
	   (2, 'Canoe polinesiane va\'a'),
	   (2, 'Cycle Burn'),
	   (2, 'Aero Dance'),
	   (2, 'Zumba'),
	   (2, 'Nuoto Sincronizzato'),
	   (2, 'Aero Pilates'),
	   (2, 'Dynamic Dancing'),
	   (3, 'Performance Long Run'),
	   (3, 'Camminata'),
	   (3, 'Performance Speed Run'),
	   (3, 'Abdominal'),
	   (3, 'Arms Muscle'),
	   (3, 'Back Muscle'),
	   (4, 'Low Body'),
	   (4, 'Full Body'),
	   (4, 'Active Jump'),
	   (4, 'Upper Body'),
	   (5, 'Pilates'),
	   (5, 'Power Yoga'),
	   (5, 'Aerogym'),
	   (5, 'Leg Flexability'),
	   (6, 'Water Endurance'),
	   (6, 'Water Hydrobike'),
	   (6, 'Water Tone'),
	   (6, 'Water Reaxraft'),
	   (6, 'Meditation'),
	   (6, 'Postural Training'),
	   (7, 'Flexability'),
	   (7, 'Gravity Pilates'),
	   (7, 'Mat Pilates'),
	   (7, 'Postural Recomposition'),
	   (7, 'Child Fun Activity'),
	   (7, 'Psyco Wellness'),
	   (7, 'Wellness Coaching');

insert into persona_trainer_sala(id_personal_trainer, nome_sala)
values (1, 'Sala pesi'),
	   (1, 'Sala cardio'),
	   (1, 'Sala fitness'),
	   (1, 'Piscina'),
	   (1, 'Spazio outdoor'),
	   (1, 'Giardino zen'),
	   (2, 'Sala pesi'),
	   (2, 'Sala cardio'),
	   (2, 'Sala fitness'),
	   (2, 'Piscina'),
	   (2, 'Spazio outdoor'),
	   (2, 'Giardino zen'),
	   (3, 'Sala pesi'),
	   (3, 'Sala cardio'),
	   (3, 'Sala fitness'),
	   (3, 'Piscina'),
	   (3, 'Spazio outdoor'),
	   (3, 'Giardino zen'),
	   (4, 'Sala pesi'),
	   (4, 'Sala cardio'),
	   (4, 'Sala fitness'),
	   (4, 'Piscina'),
	   (4, 'Spazio outdoor'),
	   (4, 'Giardino zen'),
	   (5, 'Sala pesi'),
	   (5, 'Sala cardio'),
	   (5, 'Sala fitness'),
	   (5, 'Piscina'),
	   (5, 'Spazio outdoor'),
	   (5, 'Giardino zen'),
	   (6, 'Sala pesi'),
	   (6, 'Sala cardio'),
	   (6, 'Sala fitness'),
	   (6, 'Piscina'),
	   (6, 'Spazio outdoor'),
	   (6, 'Giardino zen'),
	   (7, 'Sala pesi'),
	   (7, 'Sala cardio'),
	   (7, 'Sala fitness'),
	   (7, 'Piscina'),
	   (7, 'Spazio outdoor'),
	   (7, 'Giardino zen');

insert into attrezzatura(nome)
values ('Lat Machine'),
	   ('Chest Press'),
	   ('Leg extension'),
	   ('Leg Press'),
	   ('Abductor Machine'),
	   ('Adductor Machine'),
	   ('Tapis Roulant'),
	   ('Cyclette'),
	   ('Manubri'),
	   ('Kettlebell'),
	   ('Palla medica'),
	   ('Panca per addominali'),
	   ('Panca piana'),
	   ('Sacco da Boxe');

insert into esercizio(nome, descrizione, nome_sala)
values ('Flessioni', '', 'Sala fitness'),
	   ('Capriole', 'Capriole all\'indietro', 'Sala fitness'),
	   ('Addominali', 'Usando la macchina', 'Sala pesi'),
	   ('Alzate laterali', 'Con manubri leggeri', 'Sala pesi'),
	   ('Alzate verticali', 'Con manubri leggeri', 'Sala pesi'),
	   ('Squat', '', 'Sala pesi'),
	   ('Calf Extension', '', ''),
	   ('Handstand', '', ''),
	   ('Burpees', '', ''),
	   ('Bent Over Rows', 'Con sbarra lunga', 'Sala pesi');

insert into esercizio_attrezzatura(nome_esercizio, nome_attrezzatura)
values ('Flessioni', 'Lat Machine'),
	   ('Capriole', 'Palla medica'),
	   ('Addominali', 'Panca per addominali'),
	   ('Alzate laterali', 'Panca piana'),
	   ('Alzate verticali', 'Manubri'),
	   ('Squat', 'Kettlebell'),
	   ('Calf Extension', 'Cyclette'),
	   ('Handstand', 'Leg Press'),
	   ('Burpees', 'Tapis Roulant'),
	   ('Bent Over Rows', 'Lat Machine');

insert into allenamento_esercizio(id_allenamento, nome_esercizio, peso, ripetizioni, serie)
values (1, 'Flessioni', 0, 20, 3),
	   (1, 'Capriole', 0, 10, 3),
	   (1, 'Addominali', 5, 20, 3),
	   (1, 'Alzate laterali', 5, 20, 3),
	   (1, 'Alzate verticali', 5, 20, 3),
	   (1, 'Squat', 50, 20, 3),
	   (1, 'Calf Extension', 50, 20, 3),
	   (1, 'Handstand', 0, 10, 2),
	   (1, 'Burpees', 0, 10, 5),
	   (1, 'Bent Over Rows', 100, 5, 5),
	   (2, 'Flessioni', 10, 10, 3),
	   (2, 'Capriole', 5, 5, 3),
	   (2, 'Addominali', 10, 30, 3),
	   (2, 'Alzate laterali', 5, 20, 3),
	   (2, 'Alzate verticali', 5, 20, 3),
	   (2, 'Squat', 100, 20, 3),
	   (2, 'Calf Extension', 10, 20, 3),
	   (2, 'Handstand', 10, 10, 3),
	   (2, 'Burpees', 0, 15, 5),
	   (2, 'Bent Over Rows', 50, 5, 5),
	   (3, 'Flessioni', 0, 30, 3),
	   (3, 'Capriole', 0, 15, 2),
	   (3, 'Addominali', 5, 10, 3),
	   (3, 'Alzate laterali', 5, 20, 3),
	   (3, 'Alzate verticali', 5, 20, 3),
	   (3, 'Squat', 50, 10, 2),
	   (3, 'Calf Extension', 20, 15, 3),
	   (3, 'Handstand', 0, 2, 3),
	   (3, 'Burpees', 5, 10, 10),
	   (3, 'Bent Over Rows', 20, 10, 3),
	   (4, 'Flessioni', 20, 20, 2),
	   (4, 'Capriole', 0, 10, 3),
	   (4, 'Addominali', 0, 20, 3),
	   (4, 'Alzate laterali', 5, 20, 3),
	   (4, 'Alzate verticali', 5, 20, 3),
	   (4, 'Squat', 50, 10, 5),
	   (4, 'Calf Extension', 40, 10, 3),
	   (4, 'Handstand', 0, 5, 5),
	   (4, 'Burpees', 0, 20, 5),
	   (4, 'Bent Over Rows', 50, 20, 3);

insert into corso_attrezzatura(nome_corso, nome_attrezzatura)
values ('Boxe', 'Lat Machine'),
	   ('Clisthenics', 'Chest Press'),
	   ('Cross Active Induction', 'Leg extension'),
	   ('HIIT', 'Leg Press'),
	   ('Canoe polinesiane va\'a', 'Abductor Machine'),
	   ('Cycle Burn', 'Adductor Machine'),
	   ('Aero Dance', 'Tapis Roulant'),
	   ('Zumba', 'Cyclette'),
	   ('Nuoto Sincronizzato', 'Manubri'),
	   ('Aero Pilates', 'Kettlebell'),
	   ('Dynamic Dancing', 'Palla medica'),
	   ('Performance Long Run', 'Panca per addominali'),
	   ('Camminata', 'Panca piana'),
	   ('Performance Speed Run', 'Sacco da Boxe'),
	   ('Abdominal', 'Lat Machine'),
	   ('Arms Muscle', 'Chest Press'),
	   ('Back Muscle', 'Leg extension'),
	   ('Low Body', 'Leg Press'),
	   ('Full Body', 'Abductor Machine'),
	   ('Active Jump', 'Adductor Machine'),
	   ('Upper Body', 'Tapis Roulant'),
	   ('Pilates', 'Cyclette'),
	   ('Power Yoga', 'Manubri'),
	   ('Aerogym', 'Kettlebell'),
	   ('Leg Flexability', 'Palla medica'),
	   ('Water Endurance', 'Panca per addominali'),
	   ('Water Hydrobike', 'Panca piana'),
	   ('Water Tone', 'Sacco da Boxe'),
	   ('Water Reaxraft', 'Lat Machine'),
	   ('Meditation', 'Chest Press'),
	   ('Postural Training', 'Leg extension'),
	   ('Flexability', 'Leg Press'),
	   ('Gravity Pilates', 'Abductor Machine'),
	   ('Mat Pilates', 'Adductor Machine'),
	   ('Postural Recomposition', 'Tapis Roulant'),
	   ('Child Fun Activity', 'Cyclette'),
	   ('Psyco Wellness', 'Manubri'),
	   ('Wellness Coaching', 'Kettlebell');

insert into sala_attrezzatura(nome_sala, nome_attrezzatura)
values ('Sala pesi', 'Lat Machine'),
	   ('Sala pesi', 'Chest Press'),
	   ('Sala pesi', 'Leg extension'),
	   ('Sala pesi', 'Leg Press'),
	   ('Sala pesi', 'Abductor Machine'),
	   ('Sala pesi', 'Adductor Machine'),
	   ('Sala pesi', 'Tapis Roulant'),
	   ('Sala pesi', 'Cyclette'),
	   ('Sala pesi', 'Manubri'),
	   ('Sala pesi', 'Kettlebell'),
	   ('Sala pesi', 'Palla medica'),
	   ('Sala pesi', 'Panca per addominali'),
	   ('Sala pesi', 'Panca piana'),
	   ('Sala pesi', 'Sacco da Boxe'),
	   ('Sala cardio', 'Lat Machine'),
	   ('Sala cardio', 'Chest Press'),
	   ('Sala cardio', 'Leg extension'),
	   ('Sala cardio', 'Leg Press'),
	   ('Sala cardio', 'Abductor Machine'),
	   ('Sala cardio', 'Adductor Machine'),
	   ('Sala cardio', 'Tapis Roulant'),
	   ('Sala cardio', 'Cyclette'),
	   ('Sala cardio', 'Manubri'),
	   ('Sala cardio', 'Kettlebell'),
	   ('Sala cardio', 'Palla medica'),
	   ('Sala cardio', 'Panca per addominali'),
	   ('Sala cardio', 'Panca piana'),
	   ('Sala cardio', 'Sacco da Boxe'),
	   ('Sala fitness', 'Lat Machine'),
	   ('Sala fitness', 'Chest Press'),
	   ('Sala fitness', 'Leg extension'),
	   ('Sala fitness', 'Leg Press'),
	   ('Sala fitness', 'Abductor Machine'),
	   ('Sala fitness', 'Adductor Machine'),
	   ('Sala fitness', 'Tapis Roulant'),
	   ('Sala fitness', 'Cyclette'),
	   ('Sala fitness', 'Manubri'),
	   ('Sala fitness', 'Kettlebell'),
	   ('Sala fitness', 'Palla medica'),
	   ('Sala fitness', 'Panca per addominali'),
	   ('Sala fitness', 'Panca piana'),
	   ('Sala fitness', 'Sacco da Boxe'),
	   ('Piscina', 'Lat Machine'),
	   ('Piscina', 'Chest Press'),
	   ('Piscina', 'Leg extension'),
	   ('Piscina', 'Leg Press'),
	   ('Piscina', 'Abductor Machine'),
	   ('Piscina', 'Adductor Machine'),
	   ('Piscina', 'Tapis Roulant'),
	   ('Piscina', 'Cyclette'),
	   ('Piscina', 'Manubri'),
	   ('Piscina', 'Kettlebell'),
	   ('Piscina', 'Palla medica'),
	   ('Piscina', 'Panca per addominali'),
	   ('Piscina', 'Panca piana'),
	   ('Piscina', 'Sacco da Boxe'),
	   ('Spazio outdoor', 'Lat Machine'),
	   ('Spazio outdoor', 'Chest Press'),
	   ('Spazio outdoor', 'Leg extension'),
	   ('Spazio outdoor', 'Leg Press'),
	   ('Spazio outdoor', 'Abductor Machine'),
	   ('Spazio outdoor', 'Adductor Machine'),
	   ('Spazio outdoor', 'Tapis Roulant'),
	   ('Spazio outdoor', 'Cyclette'),
	   ('Spazio outdoor', 'Manubri'),
	   ('Spazio outdoor', 'Kettlebell'),
	   ('Spazio outdoor', 'Palla medica'),
	   ('Spazio outdoor', 'Panca per addominali'),
	   ('Spazio outdoor', 'Panca piana'),
	   ('Spazio outdoor', 'Sacco da Boxe'),
	   ('Giardino zen', 'Lat Machine'),
	   ('Giardino zen', 'Chest Press'),
	   ('Giardino zen', 'Leg extension'),
	   ('Giardino zen', 'Leg Press'),
	   ('Giardino zen', 'Abductor Machine'),
	   ('Giardino zen', 'Adductor Machine'),
	   ('Giardino zen', 'Tapis Roulant'),
	   ('Giardino zen', 'Cyclette'),
	   ('Giardino zen', 'Manubri'),
	   ('Giardino zen', 'Kettlebell'),
	   ('Giardino zen', 'Palla medica'),
	   ('Giardino zen', 'Panca per addominali'),
	   ('Giardino zen', 'Panca piana'),
	   ('Giardino zen', 'Sacco da Boxe');