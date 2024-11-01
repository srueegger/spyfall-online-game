<?php
/* Sicherstellen, dass die Datei nicht direkt aufgerufen wurde */
if (!defined('ABSPATH')) {
  header('HTTP/1.0 403 Forbidden');
	exit;
}

/* Ausgabe eines Basic-Bootstrap HTML Layout, im Bodybereich befinden sich ein <DIV> in dem per JavaScript der Spielinhalt dargestellt wird. Zusätzliche wird es noch diverse HTML->Templates geben die von JavaScript verwendet werden */
?>
<!doctype html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo htmlspecialchars( $_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8' ); ?>">
    <title>Spyfall | Online Game</title>
    <link href="./assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body class="py-4">
    <!-- Toast Container für Benachrichtigungen -->
    <div id="notifications" class="toast-container top-0 end-0 p-3"></div>
    <!-- App Container, hier wird JavaScript die Spielausgabe rendern -->
    <div id="app"></div>
    <!-- HTML Templates über den HTML - Template Standard -->
    <!-- Template für die Erstellung eines neuen Spiels -->
    <template id="createNewGame">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-lg-8">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Neues Spiel erstellen</h5>
              </div>
              <div class="card-body">
                <form id="createNewGameForm" method="post">
                  <div class="mb-3">
                    <label for="playerName" class="form-label">Dein Name</label>
                    <input type="text" class="form-control" id="playerName" name="playerName" required>
                  </div>
                  <button type="submit" class="btn btn-primary">Spiel erstellen</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
    </template>
    <!--
    Template für Lobby erstellen -> Folgende Elemente müssen hier dargestellt werden:
    - Spielerliste (man muss sehen ob eine Spieler bereit ist oder nicht)
    - Bereit-Button
    - Start-Button
    - Link zum Lobby teilen
    - ChatBox wo die Spieler miteinander kommunizieren können
    -->
    <template id="lobby">
      <div class="container">
        <div class="row justify-content-center">
          <!-- Spielerliste -->
          <div class="col-12 col-lg-6">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Spielerliste</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <ul id="playerList" class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div class="fw-bold">Spielername</div>
                      <span class="badge text-bg-warning">Nicht bereit</span>
                    </li>
                  </ul>
                </div>
                <button id="readyButton" class="btn btn-primary">Ich bin bereit</button>
                <button id="startButton" class="btn btn-success">Spiel starten</button>
              </div>
            </div>
          </div>
          <!-- Chat Ansicht -->
          <div class="col-12 col-lg-4">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Chat</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <ul id="chatBox" class="list-group">
                    <li class="list-group">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold">Spielername</div>
                          <p>Chatnachricht</p>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- Link teilen -->
            <div class="card mt-3">
              <div class="card-header">
                <h5 class="card-title">Spieler einladen</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <input type="text" class="form-control" id="lobbyLink" readonly>
                </div>
                <!-- Button zum Inhalt kopieren -->
                <button id="copyLobbyLinkButton" class="btn btn-primary">Link kopieren</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <!-- Bootstrap Script -->
    <script src="./assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Script mit Game Logic -->
    <script src="./assets/js/app.js"></script>
  </body>
</html>