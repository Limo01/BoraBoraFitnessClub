drop table if exists abbonamento;
drop table if exists utente;
drop table if exists accesso;
drop table if exists personal_trainer;
drop table if exists utente_personal_trainer;
drop table if exists allenamento;
drop table if exists utente_allenamento;
drop table if exists esercizio;

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

create table esercizio(
    id_allenamento int
        references allenamento(id)
            on delete cascade
            on update cascade,
    nome varchar(100),
    descrizione text,
    peso decimal(5,1) check (peso >= 0),
    ripetizioni tinyint unsigned default 1,
    serie tinyint unsigned default 1,
    durata time,
    
    primary key(id_allenamento, nome)
);

insert into abbonamento(nome, prezzo)
values ('Mensile', 80),
	   ('Annuale', 760),
	   ('Resort Pass', 0);

insert into utente(username, password, nome, cognome, email, data_nascita, badge, entrate, numero_telefono, nome_abbonamento, data_inizio, data_fine, is_admin) values
	('admin', '$2y$10$C1t0K8PSME4joo94czYkUutjnjA3gyfwebXU6FT/aXviqpoUgidKy', 'Fabio', 'Villotti', 'admin@gmail.com', '1970-01-01', 'BID61d8b7d0759a7', 2, '+39 377 541 2343', 'Annuale', '2021-01-01', '2021-12-31', true),
	('user', '$2y$10$3zE3K.4w70zcoQLEQPvrouAoSuWn/Gnq7UAfhQKlNcUf5x50TV86S', 'Mario', 'Rossi', 'mariorossi@gmail.com', '2000-01-29', 'BID61d8b7d0759ac', 8, '+689 36 28 12 88', 'Mensile', '2021-01-13', '2021-02-13', false),
	('Lion23', '$2y$10$hIEVdyrQhBsPtqEePnaR0ObE17j8hQymy0r5i7CarssVV6tsQ6yPu', 'Massimiliano', 'De Leoni', 'h@h.c', '2000-01-01', 'BID61f005bb70050', '0', '1234567890', NULL, NULL, NULL, '0'),
	('albertolazari', '$2y$10$bRFvXHh/gezgZLmszTRLzeVJ5AQXQ/GOB2LiJ0l.SrmAmG4FxL2VO', 'Alberto', 'Lazari', 'a@d.c', '2000-01-01', 'BID61f0059ac312f', '0', '2222222222', NULL, NULL, NULL, '0'),
	('squizzi', '$2y$10$5RWjwy1JfqZ/AFVCj/d.Cel/jrv98vnSa1ykgwh9T6ldE/nEB6MlW', 'Alessandro', 'Squizzato', 'jhv@s.sc', '2000-01-01', 'BID61f00b109f482', '0', '1234567890', NULL, NULL, NULL, '0'),
	('mrmuscolo12', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Gilberto', 'Filè', 'k2@s.s', '2000-01-01', 'BID61f006108cd54', '0', '1234567890', NULL, NULL, NULL, '0'),
	('runner93', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Tullio', 'Vardanega', 'k2@s.s', '2000-01-01', 'BID61f006108cd55', '0', '1234567890', NULL, NULL, NULL, '0'),
	('superchest1', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Luigi', 'De Giovanni', 'k2@s.s', '2000-01-01', 'BID61f006108cd56', '0', '1234567890', NULL, NULL, NULL, '0'),
	('gino1', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Riccardo', 'Cardin', 'k2@s.s', '2000-01-01', 'BID61f006108cd57', '0', '1234567890', NULL, NULL, NULL, '0'),
	('cicciogamer89', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'ciccio', 'gamer', 'k2@s.s', '1989-01-01', 'BID61f006108cd58', '0', '1234567890', NULL, NULL, NULL, '0'),
	('mrmuscolo123', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Claudio', 'Palazzi', 'k2@s.s', '2000-01-01', 'BID61f006108cd59', '0', '1234567890', NULL, NULL, NULL, '0'),
	('john1212', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Massimo', 'Marchiori', 'k2@s.s', '2000-01-01', 'BID61f006108cd50', '0', '1234567890', NULL, NULL, NULL, '0'),
	('villotti23', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Claudio', 'Villotti', 'k2@s.s', '2000-01-01', 'BID61f006108cd63', '0', '1234567890', NULL, NULL, NULL, '0'),
	('borabora21', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Alessandro', 'Sperduti', 'k2@s.s', '2000-01-01', 'BID61f006108cd73', '0', '1234567890', NULL, NULL, NULL, '0'),
	('goku12', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Silvia', 'Crafa', 'k2@s.s', '2000-01-01', 'BID61f006108cd83', '0', '1234567890', NULL, NULL, NULL, '0'),
	('milly', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Emilia', 'Maietti', 'k2@s.s', '2000-01-01', 'BID61f006108cd93', '0', '1234567890', NULL, NULL, NULL, '0'),
	('dopato21', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Paolo', 'Baldan', 'k2@s.s', '2000-01-01', 'BID61f006108cd03', '0', '1234567890', NULL, NULL, NULL, '0'),
	('biker121', '$2y$10$DaFZLrsgzeh5XQjsWgGbh.dZ/65MUhL5x6AOxtQlwdO7HofdxGw12', 'Francesco', 'Ranzato', 'k2@s.s', '2000-01-01', 'BID61f006108cd43', '0', '1234567890', NULL, NULL, NULL, '0');

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
	   ('Allenamento per ritornare in forma', 'In un mese si può ritornare in forma seguendo gli esercizi con il giusto ritmo', 'user', '2021-12-27', null),
	   ('Gambe e spalle', 'Principalmente gambe, ma anche spalle nel finale di allenamento', 'user', '2021-11-27', 2),
	   ('Ingrassamento','Questo allenamento è l''ideale se il tuo obiettivo è quello di perdere massa muscolare e mettere su massa grassa','user','2021-12-21',1);

insert into utente_allenamento(username_utente, id_allenamento)
values ('admin', 1),
	   ('admin', 2),
	   ('admin', 3),
	   ('admin', 5),
	   ('user', 1),
	   ('user', 2),
	   ('user', 3),
	   ('user', 5),
	   ('user2', 5),
	   ('user3', 5),
	   ('user4', 5),
	   ('user5', 5),
	   ('user6', 5);

insert into esercizio(id_allenamento, nome, peso, ripetizioni, serie, descrizione, durata)
values (1, 'Flessioni', null, 20, 3, 'Descrizione di prova', null),
	   (1, 'Capriole', null, 10, 3, 'Descrizione di prova', null),
	   (1, 'Addominali', 5, 20, 3, 'Descrizione di prova', null),
	   (1, 'Alzate laterali', 5, 20, 3, 'Descrizione di prova', null),
	   (1, 'Alzate verticali', 5, 20, 3, 'Descrizione di prova', null),
	   (1, 'Squat', 50, 20, 3, 'Descrizione di prova', null),
	   (1, 'Calf Extension', 50, 20, 3, 'Descrizione di prova', null),
	   (1, 'Handstand', null, 10, 2, 'Descrizione di prova', null),
	   (1, 'Burpees', null, 10, 5, 'Descrizione di prova', null),
	   (1, 'Bent Over Rows', 100, 5, 5, 'Descrizione di prova', null),
	   (2, 'Flessioni', 10, 10, 3, 'Descrizione di prova', null),
	   (2, 'Capriole', 5, 5, 3, 'Descrizione di prova', null),
	   (2, 'Addominali', 10, 30, 3, 'Descrizione di prova', null),
	   (2, 'Alzate laterali', 5, 20, 3, 'Descrizione di prova', null),
	   (2, 'Alzate verticali', 5, 20, 3, 'Descrizione di prova', null),
	   (2, 'Squat', 100, 20, 3, 'Descrizione di prova', null),
	   (2, 'Calf Extension', 10, 20, 3, 'Descrizione di prova', null),
	   (2, 'Handstand', 10, 10, 3, 'Descrizione di prova', null),
	   (2, 'Burpees', null, 15, 5, 'Descrizione di prova', null),
	   (2, 'Bent Over Rows', 50, 5, 5, 'Descrizione di prova', null),
	   (3, 'Flessioni', null, 30, 3, 'Descrizione di prova', null),
	   (3, 'Capriole', null, 15, 2, 'Descrizione di prova', null),
	   (3, 'Addominali', 5, 10, 3, 'Descrizione di prova', null),
	   (3, 'Alzate laterali', 5, 20, 3, 'Descrizione di prova', null),
	   (3, 'Alzate verticali', 5, 20, 3, 'Descrizione di prova', null),
	   (3, 'Squat', 50, 10, 2, 'Descrizione di prova', null),
	   (3, 'Calf Extension', 20, 15, 3, 'Descrizione di prova', null),
	   (3, 'Handstand', null, 2, 3, 'Descrizione di prova', null),
	   (3, 'Burpees', 5, 10, 10, 'Descrizione di prova', null),
	   (3, 'Bent Over Rows', 20, 10, 3, 'Descrizione di prova', null),
	   (4, 'Flessioni', 20, 20, 2, 'Descrizione di prova', null),
	   (4, 'Capriole', null, 10, 3, 'Descrizione di prova', null),
	   (4, 'Addominali', null, 20, 3, 'Descrizione di prova', null),
	   (4, 'Alzate laterali', 5, 20, 3, 'Descrizione di prova', null),
	   (4, 'Alzate verticali', 5, 20, 3, 'Descrizione di prova', null),
	   (4, 'Squat', 50, 10, 5, 'Descrizione di prova', null),
	   (4, 'Calf Extension', 40, 10, 3, 'Descrizione di prova', null),
	   (4, 'Handstand', null, 5, 5, 'Descrizione di prova', null),
	   (4, 'Burpees', null, 20, 5, 'Descrizione di prova', null),
	   (4, 'Bent Over Rows', 50, 20, 3, 'Descrizione di prova', null),
	   (5, 'Divano', null, 2, 5, 'Restare sul divano senza muovere alcun muscolo', '00:30:00'),
	   (5, 'Letto', null, 3, 1, 'Restare sul letto senza muovere alcun muscolo', '00:15:00'),
	   (5, 'Mangiare cioccolata', 10, 5, 4, 'Evita il cioccolato fondente', null),
	   (5, 'Mangiare hot-dog', 5, 3, 4, 'Senza maionese', null),
	   (5, 'Mangiare kebab', 3.5, 3, 3, 'No scibola', null),
	   (5, 'Mangiare polpette', 0.5, 4, 2, 'Le polpette devono essere fritte due volte, se non puoi friggere raddoppia il peso', null);