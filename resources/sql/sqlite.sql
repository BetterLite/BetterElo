CREATE TABLE IF NOT EXISTS elo (
    player_name TEXT PRIMARY KEY,
    elo INTEGER DEFAULT 0
);

-- name: create_table
CREATE TABLE IF NOT EXISTS elo (
    player_name TEXT PRIMARY KEY,
    elo INTEGER DEFAULT 0
);

-- name: get_elo
SELECT elo FROM elo WHERE player_name = :player_name;

-- name: insert_elo
INSERT INTO elo (player_name, elo) VALUES (:player_name, :elo);

-- name: update_elo
UPDATE elo SET elo = :elo WHERE player_name = :player_name;

-- name: get_top
SELECT player_name, elo FROM elo ORDER BY elo DESC LIMIT 10;