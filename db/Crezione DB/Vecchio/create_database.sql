-- Nome database: BBFC
drop table if exists Cliente;
drop table if exists Accesso;
drop table if exists Abbonamento;
drop table if exists Allenamento;
drop table if exists Cliente_Allenamento;
drop table if exists Esercizio;
drop table if exists Allenamento_Esercizio;

create table Cliente (
    Username        varchar (20)    primary key,
    Nome            varchar (50)    not null,
    Cognome        varchar (50)    not null,
    Password        varchar (30)    not null,
    Badge            char (16)        not null,
    Data_nascita    date            not null,
    Numero_telefono    varchar (20)    ,
    Abbonamento     varchar (50)
        references  Abbonamento (Nome)
                    on delete cascade
                    on update cascade,
    Data_inizio_abbonamento date    ,
    Data_fine_abbonamento   date    ,
    Entrate        int            default 0
    check        (Entrate >= 0),
    check       (Data_inizio_abbonamento <= Data_fine_abbonamento)
);

create table Accesso (
    Username            varchar (30)    not null
references     Cliente(Username)
on delete cascade
on update cascade,
    Dataora_entrata        datetime     not null,
    Dataora_uscita        datetime
    check             (Dataora_uscita >= Dataora_entrata),
    primary key (Username, Dataora_entrata)
);

create table Abbonamento (
    Nome        varchar (50)    primary key,
    Prezzo    int            not null
    check     (Prezzo >= 0)
);

create table Allenamento (
    ID            int            primary key,
    Nome            varchar (50)    not null,
    Descrizione    varchar (500)    ,
    Autore        varchar (20)    not null
        references    Cliente (Username)
                on delete cascade
                on update cascade
);

create table Cliente_Allenamento (
    Username        varchar (20)
references     Cliente(Username)
on delete cascade
on update cascade,
    ID            int
references     Allenamento(ID)
on delete cascade
on update cascade,
    primary key (Username, ID)
);

create table Esercizio (
    Nome            varchar (50)    primary key,
    Descrizione    varchar (300)
);

create table Allenamento_Esercizio(
    ID            int
references     Allenamento(ID)
on delete cascade
on update cascade,
    Nome            varchar (50)
references     Esercizio(Nome)
on delete cascade
on update cascade,
    Peso            int        default 0,
    Ripetizioni    int        default 0,
    Serie            int        default 0,
    Durata        time        default '00:00:00'
    check            (Peso >= 0 and Ripetizioni >= 0 and Serie >= 0),
    primary key (ID, Nome)
);