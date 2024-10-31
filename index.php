<?php
/* 
===INFORMATION:===
Bei diesem Projekt handelt es sich um eine Wettberwerbs-Teilnahme für der GNU/Linux Herbstwettbewerb 2024.
Mehr Informationen zum Wettbewerb findet man hier: https://gnulinux.ch/herbstwettbewerb-spyfall

==Dieses Projekt wurde von folgenden Personen erstellt:==
- Name: 	Samuel Rüegger
- E-Mail:	samuel@rueegger.me
- GitHub:	https://github.com/srueegger

==Lizenz:==
Dieses Projekt steht unter der GPL-2.0 Lizenz. Mehr Informationen dazu findet ihr im beigelegten Lizenzdokument (LICENSE).

==Projektbeschreibung:==
Für die Gestaltung des Nutzerinterface verwende ich, das Bootstrap Framework, das unter der MIT Lizenz veröfentlicht wurde. Das Bootstrap Framework und das grundlegende Nuzerinterface sind nicht teil der Wettbewerbsbewertung. Bei dem Wettbewerb geht es effektiv nur um die Spiellogik und die Funktionalität des Spiels.

Konkret sind die Inhalte in /assets/bootstrap/* nicht von mir entwickelt und kein Teil der Wettbewerbseinreichung. Diese Dateien haben nicht mit der Spiellogik oder dem Spielablauf zu tun, sondern machen nur das Userinterface etwas schöner.

Die Logik des Spiels wurde von mir selbst entwickelt und ist nicht von anderen Projekten kopiert. Das Spiel hat eine Server-seitige PHP Komponente, die die Kommunikation zwischen den Spielern verwaltet.
Die Spiellogik und der Ablauf des Spiels befindet sich in der Client-seitigen JavaScript Komponente.
*/

/* Applikation auf UTF-8 stellen */
header('Content-Type: text/html; charset=utf-8');

/* PHP Session starten */
session_start();

/* CSRF Token erstrellen, und in die $_SESSION speichern */
if(empty($_SESSION['csrf_token'])){
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/** ABSPATH als Verzeichnis für diese Datei definieren  */
if (!defined( 'ABSPATH' )) {
	define('ABSPATH', __DIR__ . '/');
}

/* APP laden */
require_once(ABSPATH . 'app.php');