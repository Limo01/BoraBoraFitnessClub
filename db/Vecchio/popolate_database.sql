insert into utente (Username, Nome, Cognome, Password, Badge, Data_nascita, Numero_telefono, Abbonamento, Data_inizio_abbonamento, Data_fine_abbonamento, Entrate) values
('admin', 'Amministratore', 'di Sistema', 'admin', '0123456789ABCDEF', '1970-01-01', '+39 377 541 2343', null, null, null, 0),
('user', 'Utente', 'Generico', 'user', 'FEDCBA9876543210', '2000-01-29', '+689 36 28 12 88', 'Mensile', '2022-01-13', '2022-02-13', 8);

insert into Abbonamento (Nome, Prezzo) values
('Mensile', 80),
('Annuale', 760),
('Resort Pass', 0);

insert into Accesso (Username, Dataora_entrata, Dataora_uscita) values
('user', '2021-12-23 17:00', '2021-12-23 19:30'),
('user', '2022-01-29 16:00', null);