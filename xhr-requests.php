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
    case 'updatePlayerList':
      $gameObject = json_decode($_POST['gameObject'], true);
      updatePlayerList($gameObject);
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
function startNewGame($playerName) {
  $lobbyID = uniqid();
  $playerUUID = uniqid();
  /* Leeres Array mit allen Spielern erstelen */
  $players = [];
  $players = addPlayerToGame($players, $playerName, $playerUUID);
  /* Spiel in der Datenbank erstellen */
  $db = new SQLite3(DB_NAME);
  $stmt = $db->prepare("INSERT INTO games (LobbyID, GMID, players) VALUES (:lobbyID, :playerUUID, :players)");
  $stmt->bindValue(':lobbyID', $lobbyID, SQLITE3_TEXT);
  $stmt->bindValue(':playerUUID', $playerUUID, SQLITE3_TEXT);
  $stmt->bindValue(':players', json_encode($players), SQLITE3_TEXT);
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

/* Funktion zum aktualisieren der Spieler Liste. Zurzeit wird nur überprüft ob es neue Spieler im Spiel gibt, falls ja wird das GameObject entsprechend aktualisert und an den Client zurückgesendet */
function updatePlayerList($gameObject) {
  /* Aus der Datenbank die aktuellen Spielerliste auslesen */
  $db = new SQLite3(DB_NAME);
  $stmt = $db->prepare("SELECT players FROM games WHERE id = :gameID");
  $stmt->bindValue(':gameID', $gameObject['gameID'], SQLITE3_INTEGER);
  $result = $stmt->execute();
  $row = $result->fetchArray();
  $players = json_decode($row['players'], true);
  $db->close();
  /* Spielerliste aktualisieren */
  $gameObject['players'] = $players;
  /* Aktuaulisierte Spielerdaten in der $_SESSION speichern und an den Client zurückgeben */
  $_SESSION['game'] = $gameObject;
  echo json_encode(['status' => 'Lobby aktualisiert', 'gameObject' => $_SESSION['game']]);
  exit;
}