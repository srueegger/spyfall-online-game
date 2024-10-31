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
      });
    });

   }, false);
 
 });