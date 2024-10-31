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
    <!-- App Container, hier wird JavaScript die Spielausgabe rendern -->
    <div id="app"></div>
    <!-- HTML Templates über den HTML - Template Standard -->
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
    <!-- Bootstrap Script -->
    <script src="./assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Script mit Game Logic -->
    <script src="./assets/js/app.js"></script>
  </body>
</html>