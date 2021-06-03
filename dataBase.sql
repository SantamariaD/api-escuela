/*CREAR BASE DE DATOS*/
CREATE DATABASE IF NOT  EXISTS escolar;
USE escolar;

/*INICIO TABLA alumno*/
CREATE TABLE alumno(
     id              INT(255) auto_increment not null,
     nombre          VARCHAR (50) NOT NULL,
     apellido          VARCHAR (50) NOT NULL,
     pais          VARCHAR (50) NOT NULL,
     sexo          VARCHAR (20) NOT NULL,
     nacionalidad          VARCHAR (50) NOT NULL,
     email          VARCHAR (50) NOT NULL,
     usuario          VARCHAR (20) NOT NULL,
     confirmar_cuenta          VARCHAR (20) NOT NULL,
     contrasena           VARCHAR (200) NOT NULL,
    created_at      datetime DEFAULT NULL,
    updated_at      datetime DEFAULT NULL,
    CONSTRAINT pk_usuario PRIMARY KEY(id)
)ENGINE=InnoDb;
/*FIN TABLA alumno*/

/*INICIO TABLA CUROS*/
CREATE TABLE cursos(
    id_curso              INT(255) auto_increment not null,
    nombre          TEXT (150) NOT NULL,
    imagen          VARCHAR (200) NOT NULL,
    area          VARCHAR (50) NOT NULL,
    profesor          VARCHAR (100) NOT NULL,
    calificacion          TINYINT (2) NOT NULL,
    grado          VARCHAR (50) NOT NULL,
    descripcion          TEXT (30000) NOT NULL,
    created_at      datetime DEFAULT NULL,
    updated_at      datetime DEFAULT NULL,
    CONSTRAINT pk_usuario PRIMARY KEY(id_curso)
)ENGINE=InnoDb;
/*FIN TABLA CUROS*/

/*INICIO TABLA VIDEOS*/
CREATE TABLE videos(
    id              INT(11) auto_increment not null,
    id_curso          int (11) NOT NULL,
    video1           VARCHAR (200) NOT NULL,
    video2           VARCHAR (200) NOT NULL,
    video3           VARCHAR (200) NOT NULL,
    video4           VARCHAR (200) NOT NULL,
    video5           VARCHAR (200) NOT NULL,
    video6           VARCHAR (200) NOT NULL,
    video7           VARCHAR (200) NOT NULL,
    video8           VARCHAR (200) NOT NULL,
    video9           VARCHAR (200) NOT NULL,
    video10          VARCHAR (200) NOT NULL,
    video11          VARCHAR (200) NOT NULL,
    video12          VARCHAR (200) NOT NULL,
    video13          VARCHAR (200) NOT NULL,
    video14          VARCHAR (200) NOT NULL,
    video15          VARCHAR (200) NOT NULL,
    video16          VARCHAR (200) NOT NULL,
    video17          VARCHAR (200) NOT NULL,
    video18          VARCHAR (200) NOT NULL,
    video19          VARCHAR (200) NOT NULL,
    video20          VARCHAR (200) NOT NULL,
    video21          VARCHAR (200) NOT NULL,
    video22          VARCHAR (200) NOT NULL,
    video23          VARCHAR (200) NOT NULL,
    video24          VARCHAR (200) NOT NULL,
    video25          VARCHAR (200) NOT NULL,
    video26          VARCHAR (200) NOT NULL,
    video27          VARCHAR (200) NOT NULL,
    video28          VARCHAR (200) NOT NULL,
    video29          VARCHAR (200) NOT NULL,
    video30          VARCHAR (200) NOT NULL,
    video31          VARCHAR (200) NOT NULL,
    video32          VARCHAR (200) NOT NULL,
    video33          VARCHAR (200) NOT NULL,
    video34          VARCHAR (200) NOT NULL,
    video35          VARCHAR (200) NOT NULL,
    video36          VARCHAR (200) NOT NULL,
    video37          VARCHAR (200) NOT NULL,
    video38          VARCHAR (200) NOT NULL,
    video39          VARCHAR (200) NOT NULL,
    video40          VARCHAR (200) NOT NULL,
    video41          VARCHAR (200) NOT NULL,
    video42          VARCHAR (200) NOT NULL,
    video43          VARCHAR (200) NOT NULL,
    video44          VARCHAR (200) NOT NULL,
    video45          VARCHAR (200) NOT NULL,
    video46          VARCHAR (200) NOT NULL,
    video47          VARCHAR (200) NOT NULL,
    video48          VARCHAR (200) NOT NULL,
    video49          VARCHAR (200) NOT NULL,
    video50          VARCHAR (200) NOT NULL,
    created_at      datetime DEFAULT NULL,
    updated_at      datetime DEFAULT NULL,
    CONSTRAINT pk_usuario PRIMARY KEY(id)
)ENGINE=InnoDb;
/*FIN TABLA VIDEOS*/

/*INICIO TABLA TITULO DE LOS VIDEOS*/
CREATE TABLE titulos_videos(
    id              INT(11) auto_increment not null,
    id_curso          int (11) NOT NULL,
    titulo1           VARCHAR (200) NOT NULL,
    titulo2           VARCHAR (200) NOT NULL,
    titulo3           VARCHAR (200) NOT NULL,
    titulo4           VARCHAR (200) NOT NULL,
    titulo5           VARCHAR (200) NOT NULL,
    titulo6           VARCHAR (200) NOT NULL,
    titulo7           VARCHAR (200) NOT NULL,
    titulo8           VARCHAR (200) NOT NULL,
    titulo9           VARCHAR (200) NOT NULL,
    titulo10          VARCHAR (200) NOT NULL,
    titulo11          VARCHAR (200) NOT NULL,
    titulo12          VARCHAR (200) NOT NULL,
    titulo13          VARCHAR (200) NOT NULL,
    titulo14          VARCHAR (200) NOT NULL,
    titulo15          VARCHAR (200) NOT NULL,
    titulo16          VARCHAR (200) NOT NULL,
    titulo17          VARCHAR (200) NOT NULL,
    titulo18          VARCHAR (200) NOT NULL,
    titulo19          VARCHAR (200) NOT NULL,
    titulo20          VARCHAR (200) NOT NULL,
    titulo21          VARCHAR (200) NOT NULL,
    titulo22          VARCHAR (200) NOT NULL,
    titulo23          VARCHAR (200) NOT NULL,
    titulo24          VARCHAR (200) NOT NULL,
    titulo25          VARCHAR (200) NOT NULL,
    titulo26          VARCHAR (200) NOT NULL,
    titulo27          VARCHAR (200) NOT NULL,
    titulo28          VARCHAR (200) NOT NULL,
    titulo29          VARCHAR (200) NOT NULL,
    titulo30          VARCHAR (200) NOT NULL,
    titulo31          VARCHAR (200) NOT NULL,
    titulo32          VARCHAR (200) NOT NULL,
    titulo33          VARCHAR (200) NOT NULL,
    titulo34          VARCHAR (200) NOT NULL,
    titulo35          VARCHAR (200) NOT NULL,
    titulo36          VARCHAR (200) NOT NULL,
    titulo37          VARCHAR (200) NOT NULL,
    titulo38          VARCHAR (200) NOT NULL,
    titulo39          VARCHAR (200) NOT NULL,
    titulo40          VARCHAR (200) NOT NULL,
    titulo41          VARCHAR (200) NOT NULL,
    titulo42          VARCHAR (200) NOT NULL,
    titulo43          VARCHAR (200) NOT NULL,
    titulo44          VARCHAR (200) NOT NULL,
    titulo45          VARCHAR (200) NOT NULL,
    titulo46          VARCHAR (200) NOT NULL,
    titulo47          VARCHAR (200) NOT NULL,
    titulo48          VARCHAR (200) NOT NULL,
    titulo49          VARCHAR (200) NOT NULL,
    titulo50          VARCHAR (200) NOT NULL,
    created_at      datetime DEFAULT NULL,
    updated_at      datetime DEFAULT NULL,
    CONSTRAINT pk_usuario PRIMARY KEY(id)
)ENGINE=InnoDb;
/*FIN TABLA TITULO DE LOS VIDEOS*/

/*INICIO TABLA CUROS*/
CREATE TABLE areas(
    id_area              INT(13) auto_increment not null,
    id_curso          int (11) NOT NULL,
    nombre          TEXT (50) NOT NULL,
    imagen          VARCHAR (200) NOT NULL,
    descripcion          TEXT (30000) NOT NULL,
    created_at      datetime DEFAULT NULL,
    updated_at      datetime DEFAULT NULL,
    CONSTRAINT pk_usuario PRIMARY KEY(id_area)
)ENGINE=InnoDb;
/*FIN TABLA CUROS*/

/*INICIO TABLA CUROS*/
CREATE TABLE carreras(
    id_carrera              INT(13) auto_increment not null,
    id_area          int (11) NOT NULL,
    nombre          TEXT (50) NOT NULL,
    imagen          VARCHAR (200) NOT NULL,
    descripcion          TEXT (30000) NOT NULL,
    created_at      datetime DEFAULT NULL,
    updated_at      datetime DEFAULT NULL,
    CONSTRAINT pk_usuario PRIMARY KEY(id_carrera)
)ENGINE=InnoDb;
/*FIN TABLA CUROS*/
