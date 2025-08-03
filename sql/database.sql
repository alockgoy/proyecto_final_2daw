/* Creación de la base de datos */
CREATE DATABASE IF NOT EXISTS BibliotecaMultimedia;
USE BibliotecaMultimedia;

/* Tabla usuarios */
CREATE TABLE Users(
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    salt VARCHAR(255) NOT NULL,
    password VARCHAR(300) NOT NULL,
    profile VARCHAR(255),
    two_factor BOOLEAN DEFAULT FALSE,
    rol VARCHAR(10) DEFAULT 'normal' CHECK (rol IN ('root', 'normal', 'solicita'))
);

/* Creación de la tabla películas */
CREATE TABLE Movies(
    id_movie INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(300) NOT NULL,
    synopsis VARCHAR(600),
    poster VARCHAR(255) NOT NULL,
    director VARCHAR(100) NOT NULL,
    gender VARCHAR(100) NOT NULL,
    languages VARCHAR(100) NOT NULL,
    size FLOAT NOT NULL,
    year INT NOT NULL,
    id_quality INT NOT NULL,
    FOREIGN KEY (id_quality) REFERENCES Qualities(id_quality),
    backup VARCHAR(200),
    server VARCHAR(2) NOT NULL CHECK (server IN ('si', 'no')),
    rating FLOAT
);


/* Creación de la tabla series */
CREATE TABLE Series(
    id_serie INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(300) NOT NULL,
    poster VARCHAR(255) NOT NULL,
    gender VARCHAR(100) NOT NULL,
    languages VARCHAR(100) NOT NULL,
    seasons INT NOT NULL,
    complete VARCHAR(2) NOT NULL CHECK (complete IN ('si', 'no')),
    year INT NOT NULL,
    id_quality INT NOT NULL,
    FOREIGN KEY (id_quality) REFERENCES Qualities(id_quality),
    backup VARCHAR(200),
    size FLOAT NOT NULL,
    server VARCHAR(2) NOT NULL CHECK (server IN ('si', 'no')),
    rating FLOAT
);

/* Nueva tabla, calidades */
CREATE TABLE Qualities(
    id_quality INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(15) NOT NULL
);

    /* Insertar valores por defecto en la tabla de calidades */
    INSERT INTO Qualities(name) VALUES('4K');
    INSERT INTO Qualities(name) VALUES('1440p');
    INSERT INTO Qualities(name) VALUES('1080p');
    INSERT INTO Qualities(name) VALUES('720p');
    INSERT INTO Qualities(name) VALUES('480p');
    INSERT INTO Qualities(name) VALUES('otro');

/* Tabla que sale de la unión de usuarios y películas */
CREATE TABLE Users_Movies(
    id_user INT,
    id_movie INT,
    PRIMARY KEY (id_user, id_movie),
    FOREIGN KEY (id_user) REFERENCES Users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_movie) REFERENCES Movies(id_movie) ON DELETE CASCADE
);

/* Tabla que sale de la unión de usuarios y series */
CREATE TABLE Users_Series(
    id_user INT,
    id_serie INT,
    PRIMARY KEY (id_user, id_serie),
    FOREIGN KEY (id_user) REFERENCES Users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_serie) REFERENCES Series(id_serie) ON DELETE CASCADE
);

/* Tabla que guarda un registro de los movimientos en la aplicación */
CREATE TABLE Movements(
    id_movement INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    he_did VARCHAR(500) NOT NULL,
    moment DATETIME NOT NULL,
    with_result VARCHAR(200) NOT NULL
);

/* Crear un usuario específico para esta base de datos y asignarle todos los permisos */
CREATE USER 'usuario_multimedia'@'localhost' IDENTIFIED BY 'Esta contraseña será cambiada';
GRANT ALL PRIVILEGES ON BibliotecaMultimedia.* TO 'usuario_multimedia'@'localhost';
FLUSH PRIVILEGES;

---------------------------------------------------------------------------------------------

/* Eliminar al usuario (por el motivo que sea) */
DROP USER 'usuario_multimedia'@localhost;

/* Consulta para obtener todos los usuarios */
SELECT * FROM Users;

/* Consulta para obtener las películas (sin filtrar por usuario) */
SELECT * FROM Movies;

/* Consulta para obtener las series (sin filtrar por usuario) */
SELECT * FROM Series;

/* Consulta para obtener los valores de la tabla de las películas vinculadas a un usuario */
SELECT * FROM Users_Movies;

/* Consulta para obtener los valores de la tabla series vinculadas a un usuario */
SELECT * FROM Users_Series;

/* Añadir el nuevo rol para usuarios */
ALTER TABLE Users MODIFY rol VARCHAR(10) DEFAULT 'normal' CHECK (rol IN ('root', 'normal', 'solicita'));
ALTER TABLE Users MODIFY rol ENUM('root', 'normal', 'solicita') DEFAULT 'normal';

/* Modificar el campo "rating" en las tablas ya existentes */
ALTER TABLE Movies MODIFY rating FLOAT;
ALTER TABLE Series MODIFY rating FLOAT;

/* Modificar el campo 'quality' en las tablas ya existentes */
ALTER TABLE Movies ADD COLUMN id_quality INT;
ALTER TABLE Series ADD COLUMN id_quality INT;

    -- Películas
    UPDATE Movies SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = '4K'
    ) WHERE quality = '4K';

    UPDATE Movies SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = '1440p'
    ) WHERE quality = '1440p';

    UPDATE Movies SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = '1080p'
    ) WHERE quality = '1080p';

    UPDATE Movies SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = '720p'
    ) WHERE quality = '720p';

    UPDATE Movies SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = '420p'
    ) WHERE quality = '420p';

    UPDATE Movies SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = 'otro'
    ) WHERE quality = 'otro';

    -- Series
    UPDATE Series SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = '4K'
    ) WHERE quality = '4K';

    UPDATE Series SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = '1440p'
    ) WHERE quality = '1440p';

    UPDATE Series SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = '1080p'
    ) WHERE quality = '1080p';

    UPDATE Series SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = '720p'
    ) WHERE quality = '720p';

    UPDATE Series SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = '420p'
    ) WHERE quality = '420p';

    UPDATE Series SET id_quality = (
        SELECT q.id_quality FROM Qualities q WHERE q.name = 'otro'
    ) WHERE quality = 'otro';

-- Hacer las columnas NOT NULL
ALTER TABLE Movies MODIFY id_quality INT NOT NULL;
ALTER TABLE Series MODIFY id_quality INT NOT NULL;

-- Agregar las claves foráneas
ALTER TABLE Movies ADD FOREIGN KEY (id_quality) REFERENCES Qualities(id_quality);
ALTER TABLE Series ADD FOREIGN KEY (id_quality) REFERENCES Qualities(id_quality);

-- Eliminar las columnas 'quality' originales
ALTER TABLE Movies DROP COLUMN quality;
ALTER TABLE Series DROP COLUMN quality;

-- Ver qué restricciones CHECK existen
SELECT CONSTRAINT_NAME, CHECK_CLAUSE 
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = 'BibliotecaMultimedia';

-- Eliminar las restricciones CHECK de géneros
ALTER TABLE Movies DROP CHECK Movies_chk_1;
ALTER TABLE Series DROP CHECK Series_chk_1;
---------------------------------------------------------------------------------------------
