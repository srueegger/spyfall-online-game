<?php
/* Sicherstellen, dass die Datei nicht direkt aufgerufen wurde */
if (!defined('ABSPATH')) {
  header('HTTP/1.0 403 Forbidden');
	exit;
}

/* Function zum erstellen einer LobbyID */
function createDB() {
  $db = new SQLite3(DB_NAME);
  $db->exec("CREATE TABLE IF NOT EXISTS games (id INTEGER PRIMARY KEY, LobbyID TEXT, GMID INTEGER, players TEXT, started INTEGER DEFAULT 0, gameEndTime INTEGER DEFAULT 0)");
  $db->close();
}

function addPlayerToGame( $players, $playerName, $playerUUID ) {
  $players[$playerUUID] = [
    'playerName' => $playerName,
    'playerUUID' => $playerUUID,
    'ready' => false
  ];
  return $players;
}