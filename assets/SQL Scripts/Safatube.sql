drop table if exists dislikes;
drop table if exists likes;
drop table if exists video_lista_reproduccion;
drop table if exists lista_reproduccion;
drop table if exists mensaje;
drop table if exists comentario;
drop table if exists video;
drop table if exists suscripcion;
drop table if exists notificacion;
drop table if exists canal;
drop table if exists token_acceso;
drop table if exists usuario;

CREATE TABLE usuario(
id serial,
username varchar(50) UNIQUE not null,
password varchar(50) not null,
es_admin boolean not null,
primary key (id)
);

CREATE TABLE token_acceso(
     id serial,
     token varchar(1000) NOT NULL,
     fecha_expiracion timestamp(6) not null,
     id_usuario int NOT NULL,
     PRIMARY KEY (id),
     constraint fk_token_usuario foreign key (id_usuario) references usuario(id)
);


create table canal(
id serial,
descripcion varchar(400) not null,
tipo_contenido int not null,
nombre varchar(30) not null,
apellidos varchar(30) not null,
email varchar(100) not null,
fecha_nacimiento date not null,
telefono varchar(12) not null,
foto varchar(2000) not null,
id_usuario int not null,
primary key (id),
constraint fk_canal_usuario foreign key (id_usuario) references usuario(id)
);

CREATE TABLE notificacion(
     id serial,
     tipo int NOT NULL,
     mensaje varchar(200) NOT NULL,
     fecha date NOT NULL,
     id_canal int NOT NULL,
     PRIMARY KEY (id),
     CONSTRAINT fk_notificacion_canal FOREIGN KEY (id_canal) REFERENCES canal(id)
);

CREATE TABLE suscripcion(
    id serial,
    id_usuario_suscriptor int NOT NULL,
    id_canal_suscrito int NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_suscripcion_usuario_suscriptor FOREIGN KEY (id_usuario_suscriptor) REFERENCES usuario(id),
    CONSTRAINT fk_suscripcion_canal_suscrito FOREIGN KEY (id_canal_suscrito) REFERENCES canal(id)
);

create table video(
id serial,
enlace varchar(10000) not null,
titulo varchar(100) not null,
descripcion varchar(1000) not null,
duracion int not null,
fecha date not null,
privacidad int not null,
activo bool not null default true,
id_canal int not null,
primary key (id),
constraint fk_video_canal foreign key (id_canal) references canal(id)
);

create table comentario(
   id serial,
   texto varchar(400) not null,
   fecha date not null,
   activo bool not null default true,
   id_video int not null,
   primary key (id),
   constraint fk_comentario_video foreign key (id_video) references video(id)
);

create table mensaje(
id serial,
texto varchar(500) not null,
fecha date not null,
id_usuario_emisor int not null,
id_usuario_receptor int not null,
primary key (id),
constraint fk_mensaje_usuario_emisor foreign key (id_usuario_emisor) references usuario(id),
constraint fk_mensaje_usuario_receptor foreign key (id_usuario_receptor) references usuario(id)
);


create table lista_reproduccion(
           id serial,
           nombre varchar(100) not null,
           id_canal int not null,
           primary key (id),
           constraint fk_lista_reproduccion_canal foreign key (id_canal) references canal(id)
);

create table video_lista_reproduccion(
                 id_video int not null,
                 constraint fk_video_lista_reproduccion_video foreign key (id_video) references video(id),
                 id_lista_reproduccion int not null,
                 constraint fk_video_lista_reproduccion_lista_reproduccion foreign key (id_lista_reproduccion) references lista_reproduccion(id)
);

create table likes(
id serial,
id_usuario int4 not null,
id_video int4,
id_comentario int4,
primary key (id),
constraint likes_usuario_fk foreign key (id_usuario) references usuario (id),
constraint likes_video_fk foreign key (id_video) references video (id),
constraint likes_comentario_fk foreign key (id_comentario) references comentario (id)
);

create table dislikes(
 id serial,
 id_usuario int4 not null,
 id_video int4,
 id_comentario int4,
 primary key (id),
 constraint dislike_usuario_fk foreign key (id_usuario) references usuario (id),
 constraint dislike_video_fk foreign key (id_video) references video (id),
 constraint dislike_comentario_fk foreign key (id_comentario) references comentario (id)
);

