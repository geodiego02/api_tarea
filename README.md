Tarea  creada en cliente javascript, sin diseño y con una api en php.
Adjunto script de base de datos


CREATE DATABASE tarea_pega;
use tarea_pega;
CREATE TABLE `appoinment` (
  `id` int(10) AUTO_INCREMENT ,
  `date` date ,
  `start_time` time ,
  `contact` varchar(20) ,
    PRIMARY KEY(`id`)
);
