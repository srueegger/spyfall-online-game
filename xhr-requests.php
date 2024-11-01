<?php
/* Session starten */
session_start();

/** ABSPATH als Verzeichnis für diese Datei definieren  */
if (!defined( 'ABSPATH' )) {
	define('ABSPATH', __DIR__ . '/');
}

/* Helper Functionen einbinden */
require_once(ABSPATH . 'consts.php');
require_once(ABSPATH . 'helper-functions.php');

/* Diese Datei verwaltet alle eingehenden XHR Requests */

/* CSRF Token validieren */
$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
if (!isset($csrfToken) || $csrfToken !== $_SESSION['csrf_token']) {
  header('HTTP/1.0 403 Forbidden');
  echo json_encode([
    'error' => 'Ungültiger CSRF-Token',
    'sessionToken' => $_SESSION['csrf_token'],
    'requestToken' => $csrfToken
  ]);
  exit;
}

if (isset($_POST['action'])) {
  $action = $_POST['action'];
  switch ($action) {
    case 'startNewGame':
      $playerName = $_POST['playerName'] ?? null;
      startNewGame( $playerName );
      break;
    case 'joinGame':
      echo json_encode(['status' => 'Einem Spiel beitreten...']);
      exit;
      break;
    default:
      echo json_encode(['error' => 'Unbekannte Aktion']);
      exit;
  }
} else {
  echo json_encode(['error' => 'Keine Aktion angegeben']);
  exit;
}

/* Spielfunktionen */

/*
Funktion zum erstellen eines neuen Spiels:
- Erstellt eine LobbyID uniqid()
- Erstellt eine SpielerID uniqid()
- Erstellt ein Game Objekt in der Session:
  - LobbyID
  - SpielerID
  - SpielerName
  - GameMaster (bool)
*/
function startNewGame( $playerName ) {
  $lobbyID = uniqid();
  $playerUUID = uniqid();
  /* Leeres Array mit allen Spielern erstelen */
  $players = [];
  $players = addPlayerToGame( $players, $playerName, $playerUUID );
  /* Spiel in der Datenbank erstellen */
  $db = new SQLite3(DB_NAME);
  $stmt = $db->prepare("INSERT INTO games (LobbyID, GMID, players) VALUES (:lobbyID, :playerUUID, :players)");
  $stmt->execute();
  $gameID = $db->lastInsertRowID();
  $db->close();


  /* Spiel->Object erstellen */
  $gameArray = [
    'gameID' => $gameID,
    'lobbyID' => $lobbyID,
    'playerUUID' => $playerUUID,
    'playerName' => htmlspecialchars($playerName, ENT_QUOTES, 'UTF-8'),
    'isGameMaster' => true,
    'players' => $players
  ];
  $_SESSION['game'] = $gameArray;
  $return = [
    'status' => 'Spiel erstellt',
    'gameObject' => $_SESSION['game']
  ];
  echo json_encode($return);
  exit;
}