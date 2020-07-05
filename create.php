<?php
// wiede session starten und admin/script.php laden
session_start();
require("admin/script.php");
require("admin/cookie.php");

if(isset($_COOKIE['sb_user'])){ // Cookie ist aber gesetzt
  $userid = openssl_decrypt($_COOKIE['sb_user'], "AES-128-ECB", "key_sb_user"); // Dann erhalte die UserID von cookie
  if(strlen($userid) >= 1){ // Wenn die String länge von userID >= 1 ist
    $_SESSION['sb_user'] = getUsernameById($userid, "AES-128-ECB","key_sb_user"); // Dann erhalte Username von UserID
  } else { // Wenn länge von String nicht >= 1 ist
    header("Location: account/logout.php");
  }
} else {
    header("Location: account/account.php");
}

?>
<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Erstellen | Schwarzes Brett</title>
    <link href="style.css" rel="stylesheet">
    <script src="admin/script.js"></script>
  </head>
  <body>
    <div id="header">
      <h1><a href="index.php">Schwarzes Brett</a></h1>
      <div class="icon-account-dropdown" onClick="displayAccount()">
  			<img src="img/icon/account.svg" id="icon-account">
        <div class="icon-account-data" id="icon-account-data">
  				<?php
  				if(isset($_SESSION['sb_user'])){ // Wenn Session sb_user gesetzt ist
  					echo "<h5>" . $_SESSION['sb_user'] . "</h5>"; // Namen im Dropdown setzen von Session sb_user
  					echo "<a href='account/logout.php'>Abmelden</a>";
  				} else { // Wenn Session sb_user nicht gesetzt ist
  					echo "<h5>Gast</h5>";
            echo "<a href='account/account.php'>Anmelden</a>";
  				}
  				?>
  			</div>
  		</div>
    </div>

    <div id="main-create">
      <div id="create-info">
        <h1>Willkommen bei Schwarzes Brett</h1>
        <p>hier kannst du Anzeigen aufgeben und einsehen. Pro Anzeige spendet der Mitarbeiter einen Betrag, der sich aus der Textlänge
          <b>(pro Zeichen 1 cent)</b> und der hochgeladenen Bilder <b>(pro Bild 20 cent)</b> errechnet. Es können maximal 3 Bilder hochgeladen werden.
          </p>
      </div>

      <div id="responsive-create-sidebar">
        <ul>
          <li onclick="viewEdit()" class="pointer">Bearbeiten</li>
          <li onClick="startVorschau()" class="pointer">Vorschau</li>
          <li onClick="resetVorschau()" class="pointer">Zurücksetzen</li>
        </ul>
      </div>


      <div id="create-container">
        <div id="create-sidebar">
          <ul>
            <li onclick="viewEdit()" class="pointer">Bearbeiten</li>
            <li onClick="startVorschau()" class="pointer">Vorschau</li>
            <li onClick="resetVorschau()" class="pointer">Zurücksetzen</li>
          </ul>
        </div>


          <form id="form-create" method="POST" action="pay.php" enctype="multipart/form-data">
            <input type="text" name="ueberschrift" id="vorschau-ueberschrift-form" placeholder="Überschrift"> <!-- User Daten laden und in hidden input value schreiben -->
            <input type="hidden" name="user" id="vorschau-account-form" value="<?php echo $_SESSION['sb_user']; ?>">
            <span id="datum"><?php echo echoDate(); ?></span> <!-- Funktion echoDate() um das aktuelle Datum auszugeben -->
            <textarea name="text" rows="6" cols="100" id="vorschau-text-form" resizable="false" onkeyup="count()"></textarea>
            <h3 id="count">Zeichen: 0 / 250</h3>
            <select id="rubrik" name="rubrik">
              <?php
                $data = getRubriken(); // Funktion getRubriken(), um alle aktuellen Rubriken und die ID dazu auszugeben
                foreach($data as $key => $row){
                  echo "<option value='" . $key . "'>" . $row . "</option>";
                }
              ?>
            </select>
            <input type="submit" id="Button" name="createsubmit" value="Weiter" onClick="sendData(event)">
          </form>

          <div id="container-vorschau">
            <div class="beitrag" id="vorschau">
              <div class="beitrag-top"><span id="vorschau-datum"></span><a id="vorschau-ueberschrift"></a></div>
              <div class="beitrag-text"><p id="vorschau-text"></p></div>
              <div class="beitrag-account"><img src="img/icon/account.svg"><a id="vorschau-account"></a></div>
            </div>
          </div>
      </div>
    </div>

    <script>
      function sendData(event) {
        var ueberschrift = document.getElementById("vorschau-ueberschrift-form").value;
        var text = document.getElementById("vorschau-text-form").value;
        if(text.length < 1 || ueberschrift.length < 1) {
          alert("Sie müssen eine Überschrift und einen Text wählen");
          event.preventDefault();
          return;
        }
      }
		  // Erstelle eine Vorschai mit den eingegebenen Daten
      function startVorschau() {
        var ueberschrift = document.getElementById("vorschau-ueberschrift-form").value;
        var text = document.getElementById("vorschau-text-form").value;

        if(text.length < 1 || ueberschrift.length < 1) {
          alert("Sie müssen eine Überschrift und einen Text wählen");
          return;
        }

        document.getElementById("form-create").style.display = "none";
        document.getElementById("create-container").style.backgroundColor = "white";

		      // Erstelle die Vorschau Zeitverzögert
        setTimeout(function(){
          document.getElementById("container-vorschau").style.display = "block";
          setTimeout(function(){
            document.getElementById("vorschau-ueberschrift").innerHTML = ueberschrift;
            var datum = document.getElementById("datum").innerHTML;
            document.getElementById("vorschau-datum").innerHTML = datum;
            setTimeout(function(){
              var account = document.getElementById("vorschau-account-form").value;
              document.getElementById("vorschau-account").innerHTML = account;
              setTimeout(function(){
                document.getElementById("vorschau-text").innerHTML = text;
              }, 300);
            }, 300);
          }, 300);
        }, 500);
      }


     function count() {
        var text = document.getElementById("vorschau-text-form").value;
	      //const regex = /[\s\v\t]/g;
	      //var space = ((text || '').match(regex) || []).length;
	      //document.getElementById("count").innerHTML = "Zeichen: " + (text.length-space) + " / 250";
        document.getElementById("count").innerHTML = "Zeichen: " + text.length + " / 250";
    }

      function resetVorschau() {
        document.getElementById("vorschau-ueberschrift-form").value = "";
        document.getElementById("vorschau-ueberschrift").innerHTML = "";
        document.getElementById("vorschau-text-form").value = "";
        document.getElementById("vorschau-account").innerHTML = "";
        document.getElementById("vorschau-text").innerHTML = "";
        document.getElementById("vorschau-account").innerHTML = "";
        document.getElementById("container-vorschau").style.display = "none";
        document.getElementById("count").innerHTML = "Zeichen: 0 / 250";
        document.getElementById("form-create").style.display = "block";
        document.getElementById("container-vorschau").style.display = "none";
        document.getElementById("create-container").style.backgroundColor = "rgba(37, 48, 61, 1)";
      }

      function viewEdit() {
        document.getElementById("form-create").style.display = "block";
        document.getElementById("container-vorschau").style.display = "none";
        document.getElementById("create-container").style.backgroundColor = "rgba(37, 48, 61, 1)";
      }
    </script>

  </body>
</html>
