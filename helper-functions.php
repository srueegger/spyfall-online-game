<?php
/* Sicherstellen, dass die Datei nicht direkt aufgerufen wurde */
if (!defined('ABSPATH')) {
  header('HTTP/1.0 403 Forbidden');
	exit;
}

/* Function zum erstellen einer LobbyID */
