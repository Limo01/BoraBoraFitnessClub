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

insert into esercizio(id_allenamento, nome, peso, ripetizioni, serie, descrizione)
values (1, 'Flessioni', 0, 20, 3, 'Descrizione di prova'),
	   (1, 'Capriole', 0, 10, 3, 'Descrizione di prova'),
	   (1, 'Addominali', 5, 20, 3, 'Descrizione di prova'),
	   (1, 'Alzate laterali', 5, 20, 3, 'Descrizione di prova'),
	   (1, 'Alzate verticali', 5, 20, 3, 'Descrizione di prova'),
	   (1, 'Squat', 50, 20, 3, 'Descrizione di prova'),
	   (1, 'Calf Extension', 50, 20, 3, 'Descrizione di prova'),
	   (1, 'Handstand', 0, 10, 2, 'Descrizione di prova'),
	   (1, 'Burpees', 0, 10, 5, 'Descrizione di prova'),
	   (1, 'Bent Over Rows', 100, 5, 5, 'Descrizione di prova'),
	   (2, 'Flessioni', 10, 10, 3, 'Descrizione di prova'),
	   (2, 'Capriole', 5, 5, 3, 'Descrizione di prova'),
	   (2, 'Addominali', 10, 30, 3, 'Descrizione di prova'),
	   (2, 'Alzate laterali', 5, 20, 3, 'Descrizione di prova'),
	   (2, 'Alzate verticali', 5, 20, 3, 'Descrizione di prova'),
	   (2, 'Squat', 100, 20, 3, 'Descrizione di prova'),
	   (2, 'Calf Extension', 10, 20, 3, 'Descrizione di prova'),
	   (2, 'Handstand', 10, 10, 3, 'Descrizione di prova'),
	   (2, 'Burpees', 0, 15, 5, 'Descrizione di prova'),
	   (2, 'Bent Over Rows', 50, 5, 5, 'Descrizione di prova'),
	   (3, 'Flessioni', 0, 30, 3, 'Descrizione di prova'),
	   (3, 'Capriole', 0, 15, 2, 'Descrizione di prova'),
	   (3, 'Addominali', 5, 10, 3, 'Descrizione di prova'),
	   (3, 'Alzate laterali', 5, 20, 3, 'Descrizione di prova'),
	   (3, 'Alzate verticali', 5, 20, 3, 'Descrizione di prova'),
	   (3, 'Squat', 50, 10, 2, 'Descrizione di prova'),
	   (3, 'Calf Extension', 20, 15, 3, 'Descrizione di prova'),
	   (3, 'Handstand', 0, 2, 3, 'Descrizione di prova'),
	   (3, 'Burpees', 5, 10, 10, 'Descrizione di prova'),
	   (3, 'Bent Over Rows', 20, 10, 3, 'Descrizione di prova'),
	   (4, 'Flessioni', 20, 20, 2, 'Descrizione di prova'),
	   (4, 'Capriole', 0, 10, 3, 'Descrizione di prova'),
	   (4, 'Addominali', 0, 20, 3, 'Descrizione di prova'),
	   (4, 'Alzate laterali', 5, 20, 3, 'Descrizione di prova'),
	   (4, 'Alzate verticali', 5, 20, 3, 'Descrizione di prova'),
	   (4, 'Squat', 50, 10, 5, 'Descrizione di prova'),
	   (4, 'Calf Extension', 40, 10, 3, 'Descrizione di prova'),
	   (4, 'Handstand', 0, 5, 5, 'Descrizione di prova'),
	   (4, 'Burpees', 0, 20, 5, 'Descrizione di prova'),
	   (4, 'Bent Over Rows', 50, 20, 3, 'Descrizione di prova');