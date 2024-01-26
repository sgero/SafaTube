drop table if exists visualizacion_video_usuario;
drop table if exists dislikes;
drop table if exists likes;
drop table if exists video_lista_reproduccion;
drop table if exists lista_reproduccion;
drop table if exists mensaje;
drop table if exists comentario;
drop table if exists video;
drop table if exists tipo_privacidad;
drop table if exists tipo_categoria;
drop table if exists suscripcion;
drop table if exists notificacion;
drop table if exists tipo_notificacion;
drop table if exists canal;
drop table if exists tipo_contenido;
drop table if exists token;
drop table if exists usuario;

CREATE TABLE usuario
(
    id       serial,
    username varchar(50) UNIQUE not null,
    password varchar(50)        not null,
    es_admin boolean            not null,
    activo   bool               not null default true,
    primary key (id)
);

alter table usuario add column verification_token varchar(255);
alter table usuario add column email varchar(255);
alter table usuario add column cuenta_validada boolean not null default false;


CREATE TABLE token
(
    id               serial,
    token            varchar(1000) NOT NULL,
    fecha_expiracion timestamp(6)  not null,
    id_usuario       int           NOT NULL,
    PRIMARY KEY (id),
    constraint fk_token_usuario foreign key (id_usuario) references usuario (id)
);
create table tipo_contenido
(
    id     serial primary key,
    nombre varchar(100) not null
);
create table canal
(
    id                 serial,
    descripcion        varchar(400)  not null,
    nombre             varchar(30)   not null,
    apellidos          varchar(30)   not null,
    email              varchar(100)  not null,
    fecha_nacimiento   date          not null,
    telefono           varchar(12)   not null,
    foto               varchar(2000) not null,
    activo             bool          not null default true,
    total_suscriptores int           not null default 0,
    id_usuario         int           not null,
    id_tipo_contenido  int           not null,
    primary key (id),
    constraint fk_canal_usuario foreign key (id_usuario) references usuario (id),
    constraint fk_canal_tipo_contenido foreign key (id_tipo_contenido) references tipo_contenido (id)
);

ALTER TABLE canal
    ADD CONSTRAINT email_unico UNIQUE (email);
alter table canal drop column email;


create table tipo_notificacion
(
    id     serial primary key,
    nombre varchar(100) not null
);

CREATE TABLE notificacion
(
    id                   serial,
    mensaje              varchar(200) NOT NULL,
    fecha                date         NOT NULL,
    atendida             boolean      not null default false,
    id_tipo_notificacion int          not null,
    id_canal             int          NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_notificacion_canal FOREIGN KEY (id_canal) REFERENCES canal (id),
    constraint fk_notificacion_tipo_notificacion FOREIGN KEY (id_tipo_notificacion) REFERENCES tipo_notificacion (id)
);


CREATE TABLE suscripcion
(
    id                    serial,
    id_usuario_suscriptor int NOT NULL,
    id_canal_suscrito     int NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_suscripcion_usuario_suscriptor FOREIGN KEY (id_usuario_suscriptor) REFERENCES usuario (id),
    CONSTRAINT fk_suscripcion_canal_suscrito FOREIGN KEY (id_canal_suscrito) REFERENCES canal (id)
);
create table tipo_categoria
(
    id     serial primary key,
    nombre varchar(50) not null
);


create table tipo_privacidad
(
    id     serial primary key,
    nombre varchar(50) not null
);
create table video
(
    id                 serial,
    titulo             varchar(100)   not null,
    descripcion        varchar(1000)  not null,
    enlace             varchar(10000) not null,
    duracion           int            not null,
    fecha              date           not null,
    total_visitas      int            not null default 0,
    contador_likes     INT            not null DEFAULT 0,
    contador_dislikes  INT            not null DEFAULT 0,
    activo             bool           not null default true,
    id_tipo_categoria  int            not null,
    id_tipo_privacidad int            not null,
    id_canal           int            not null,
    primary key (id),
    constraint fk_video_canal foreign key (id_canal) references canal (id),
    constraint fk_video_tipo_categoria foreign key (id_tipo_categoria) references tipo_categoria (id),
    constraint fk_video_tipo_privacidad foreign key (id_tipo_privacidad) references tipo_privacidad (id)
);


create table comentario
(
    id                  serial,
    texto               varchar(400) not null,
    fecha               timestamp(6) not null,
    activo              bool         not null default true,
    contador_likes      INT          not null DEFAULT 0,
    contador_dislikes   INT          not null DEFAULT 0,
    id_video            int          not null,
    id_usuario          int          not null,
    id_comentario_padre int,
    primary key (id),
    constraint fk_comentario_video foreign key (id_video) references video (id),
    constraint fk_comentario_usuario foreign key (id_usuario) references usuario (id),
    constraint fk_comentario_padre foreign key (id_comentario_padre) references comentario (id)
);



create table mensaje
(
    id                  serial,
    texto               varchar(500) not null,
    fecha               timestamp(6) not null,
    leido               boolean      not null,
    id_usuario_emisor   int          not null,
    id_usuario_receptor int          not null,
    primary key (id),
    constraint fk_mensaje_usuario_emisor foreign key (id_usuario_emisor) references usuario (id),
    constraint fk_mensaje_usuario_receptor foreign key (id_usuario_receptor) references usuario (id)
);



create table lista_reproduccion
(
    id       serial,
    nombre   varchar(100) not null,
    id_canal int          not null,
    primary key (id),
    constraint fk_lista_reproduccion_canal foreign key (id_canal) references canal (id)
);

create table video_lista_reproduccion
(
    id_video              int not null,
    constraint fk_video_lista_reproduccion_video foreign key (id_video) references video (id),
    id_lista_reproduccion int not null,
    constraint fk_video_lista_reproduccion_lista_reproduccion foreign key (id_lista_reproduccion) references lista_reproduccion (id)
);

create table likes
(
    id            serial,
    id_usuario    int4 not null,
    id_video      int4,
    id_comentario int4,
    primary key (id),
    constraint likes_usuario_fk foreign key (id_usuario) references usuario (id),
    constraint likes_video_fk foreign key (id_video) references video (id),
    constraint likes_comentario_fk foreign key (id_comentario) references comentario (id)
);

create table dislikes
(
    id            serial,
    id_usuario    int4 not null,
    id_video      int4,
    id_comentario int4,
    primary key (id),
    constraint dislike_usuario_fk foreign key (id_usuario) references usuario (id),
    constraint dislike_video_fk foreign key (id_video) references video (id),
    constraint dislike_comentario_fk foreign key (id_comentario) references comentario (id)
);

create table visualizacion_video_usuario
(
    id_usuario int4 not null,
    id_video   int4 not null,
    primary key (id_usuario, id_video),
    constraint visualizacion_usuario_fk foreign key (id_usuario) references usuario (id),
    constraint visualizacion_video_fk foreign key (id_video) references video (id)
);















