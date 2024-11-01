// wait until DOM is ready
document.addEventListener("DOMContentLoaded", function(e){
 
  //wait until images, links, fonts, stylesheets, and js is loaded
  window.addEventListener("load", function(e) {
    console.log( 'APP.JS LOADED' );
    /* Meta-Tag mit dem CSRF Token auslesen */
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('CSRF Token:', csrfToken);
    /* Fetch URL festlegen */
    const fetchUri = '/xhr-requests.php';
    /* Globales leeres Spiel-Daten Objekt erstellen */
    let gameData = {};

    /* JavaScript Funktionen initialisieren */
    init();
    function init() {
      console.log('init()');
      /*
      Anhand des URL Parameters "lobby" sich in eine Lobby einklinken oder ein neues Spiel erstellen
      */
      const urlParams = new URLSearchParams(window.location.search);
      const lobbyID = urlParams.get('lobby');
      /* Prüfen ob hier ein XSS Angriff versucht wird */
      if (lobbyID && !/^[a-zA-Z0-9_-]+$/.test(lobbyID)) {
        console.error('Ungültige Lobby-ID');
        return;
      }
      /* Je nach dem ob es eine LobbyID gibt oder nicht wird das Template für ein neues Spiel oder das Template für einen Spiel beitritt geladen */
      if( !lobbyID ) {
        console.log('Neues Spiel erstellen');
        loadTemplate('#createNewGame');
      } else {
        console.log('In Lobby einklinken');
        //joinGame(lobbyID);
      }
    };

    /* Function "loadTemplate", diese Funktion ladet ein Template und lädt es in den #app Bereich. Als Parameter wird die Template ID (als CSS Selektor) mitgegeben */
    function loadTemplate( templateID, runAdditionalFunction = null ) {
      console.log('loadTemplate()', templateID);
      /* Das bestehende Template im App Bereich entfernen */
      const app = document.querySelector('#app');
      app.innerHTML = '';
      /* Das neue Template laden */
      const template = document.querySelector(templateID);
      /* Das neue Template in den App Bereich laden */
      app.appendChild(template.content.cloneNode(true));
      /* Zusätzliche Funktionen ausführen */
      if( runAdditionalFunction ) {
        runAdditionalFunction();
      }
    }

    /* EventListener erstellen, der beim Submit des Formulars mit der ID "createNewGameForm" ausgefhrt wird */
    document.querySelector('#createNewGameForm').addEventListener('submit', function(e) {
      /* Verhindern, dass der Browser das Absenden des Formulars startet */
      e.preventDefault();
      console.log('createNewGameForm submit');
      /* Formular-Daten auslesen */
      const formData = new FormData(this);
      console.log(formData.get('playerName'));
      /* Daten per JavaScript Fetch Methode versenden */
      fetch(fetchUri, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-CSRF-Token': csrfToken
        },
        body: new URLSearchParams({
          action: 'startNewGame',
          playerName: formData.get('playerName')
        })
      })
      .then(response => response.json())
      .then(data => {
        console.log(data);
        /* Das GameObject in die globale Variable speichern */
        gameData = data.gameObject;
        console.log('gameData:', gameData);
        /* Aktuelle URL anpassen, und den Paramete "lobby" mit der LobbyID anhängen */
        history.pushState(null, null, '?lobby=' + gameData.lobbyID);
        /* Lobby Template laden */
        loadTemplate('#lobby', joinLobby);
      });
    });

    /*
    Funktion "joinLobby":
    Diese Funktion setzt den Link zum Spiel, um weitere Spieler einzuladen.
    Zusätzlich werden die Funktionen gestartet die die Spielerliste aktualisieren. Und eine Funktion die die Chatnachrichten aktualisiert.
    */
    function joinLobby() {
      console.log('joinLobby()');
      /* Link zum Spiel setzen */
      document.querySelector('#lobbyLink').value = window.location.href;
      /* Spielerliste aktualisieren */
      updatePlayerList();
      /* Chatnachrichten aktualisieren */
      updateChatMessages();
      /* Wenn der Spieler ein GameMaster ist, die GameMaster funktionen einblenden */
      if (gameData.isGameMaster) {
        document.querySelectorAll('.js_gm_visible').forEach(element => {
          element.classList.remove('d-none');
        });
      }
    }

    /* Fucntion, die die Spielerliste in der Lobby aktualisiert. Die Liste soll über einen Fetch call alle 2 Sekunden ausgeführt werden */
    function updatePlayerList() {
      console.log('updatePlayerList()');
      /* Funktion alle 2 Sekunden ausführen */
      setInterval(function() {
        /* Daten per JavaScript Fetch Methode versenden */
        /* fetch(fetchUri, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': csrfToken
          },
          body: new URLSearchParams({
            action: 'getPlayerList',
            lobbyID: gameData.lobbyID
          })
        })
        .then(response => response.json())
        .then(data => {
          console.log(data);
          document.querySelector('#playerList').innerHTML = '';
          data.players.forEach(player => {
            const li = document.createElement('li');
            li.innerText = player;
            document.querySelector('#playerList').appendChild(li);
          });
        }); */
      }, 2000);
    }

    /* Function die die Chat-Nachrichten jede Sekunde aktualisiert */
    function updateChatMessages() {
      console.log('updateChatMessages()');
      /* Funktion alle 2 Sekunden ausführen */
      setInterval(function() {
        /* Daten per JavaScript Fetch Methode versenden */
        /* fetch(fetchUri, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': csrfToken
          },
          body: new URLSearchParams({
            action: 'getChatMessages',
            lobbyID: gameData.lobbyID
          })
        })
        .then(response => response.json())
        .then(data => {
          console.log(data);
          document.querySelector('#chat').innerHTML = '';
          data.chatMessages.forEach(chatMessage => {
            const li = document.createElement('li');
            li.innerText = chatMessage;
            document.querySelector('#chat').appendChild(li);
          });
        }); */
      }, 1000);
    }

    /* Klick Event Listener registrieren */
    document.addEventListener('click', function(event) {
      /* Function die den beim Klick auf den Link kopieren Button, den Link in die Zwischenablage kopiert */
      if (event.target && event.target.id === 'copyLobbyLinkButton') {
        console.log('copyLink');
        const link = document.querySelector('#lobbyLink');
        const linkText = link.value;
        navigator.clipboard.writeText(linkText).then(() => {
          createNotification('Link wurde in die Zwischenablage kopiert.', 'Link kopiert', 'success');
        }).catch(err => {
          createNotification('Link konnte nicht in die Zwischenablage kopiert werden.', 'Fehler', 'danger');
        });
      }
    });

    /* Funtion um eine Benachrichtigung zu generieren */
    function createNotification(message, title = 'Info', type = 'primary') {
      // Toast HTML-Struktur
      const toastHTML = `
      <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-${type} text-white">
          <strong class="me-auto">${title}</strong>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          ${message}
        </div>
      </div>`;
  
      // Füge Toast in den Container hinzu
      const toastContainer = document.getElementById('notifications');
      toastContainer.insertAdjacentHTML('beforeend', toastHTML);
  
      // Initialisiere und zeige den Toast
      const toastElement = toastContainer.lastElementChild;
      const toast = new bootstrap.Toast(toastElement, { delay: 5000 }); // 5000 ms delay (5 seconds)
      toast.show();
  
      // Entferne den Toast aus dem DOM, nachdem er geschlossen wurde
      toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
      });
    }

   }, false);
 
 });