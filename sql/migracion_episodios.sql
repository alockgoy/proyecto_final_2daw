-- ============================================================
-- MIGRACIÓN: Añadir tabla Episodios y reestructurar Series
-- Ejecutar sobre una BD existente con datos
-- ============================================================

-- 1. Añadir columnas nuevas a Series (si no existen)
ALTER TABLE Series ADD COLUMN IF NOT EXISTS synopsis VARCHAR(600);
ALTER TABLE Series ADD COLUMN IF NOT EXISTS tmdb_id INT;

-- 2. Crear tabla Episodios
CREATE TABLE IF NOT EXISTS Episodios (
    id_episodio INT AUTO_INCREMENT PRIMARY KEY,
    id_serie    INT NOT NULL,
    temporada   INT NOT NULL,
    episodio    VARCHAR(20) NOT NULL,
    id_quality  INT NOT NULL,
    size        FLOAT NOT NULL,
    backup      VARCHAR(200),
    server      VARCHAR(2) NOT NULL CHECK (server IN ('si', 'no')),
    FOREIGN KEY (id_serie)    REFERENCES Series(id_serie)       ON DELETE CASCADE,
    FOREIGN KEY (id_quality)  REFERENCES Qualities(id_quality)
);

-- 3. OPCIONAL: una vez que hayas importado los episodios de cada serie
--    desde la interfaz, puedes eliminar los campos que ya no se usan en Series.
--    NO ejecutes esto hasta que hayas migrado los datos manualmente.
--
-- ALTER TABLE Series
--     DROP FOREIGN KEY <nombre_clave_foranea_id_quality>,
--     DROP COLUMN id_quality,
--     DROP COLUMN size,
--     DROP COLUMN backup,
--     DROP COLUMN server;
--
-- Para saber el nombre de la clave foránea:
-- SHOW CREATE TABLE Series;

-- ============================================================

ALTER TABLE Series
    DROP FOREIGN KEY Series_ibfk_1,
    DROP COLUMN id_quality,
    DROP COLUMN size,
    DROP COLUMN backup,
    DROP COLUMN server;
