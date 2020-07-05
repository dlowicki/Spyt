<?php

session_start(); // starte Session
require("admin/script.php"); // lade admin/script.php

if(!isset($_SESSION['sb_user']) && isset($_COOKIE['sb_user'])){ // Wenn Session nicht gesetzt aber cookie gesetzt
  $userid = openssl_decrypt($_COOKIE['sb_user'], "AES-128-ECB", "key_sb_user"); // Dann erhalte die UserID von cookie
  if(strlen($userid) >= 1){ // Wenn die String länge von userID >= 1 ist
    $_SESSION['sb_user'] = getUsernameById($userid, "AES-128-ECB","key_sb_user"); // Dann erhalte Username von UserID
  } else { // Wenn länge von String nicht >= 1 ist
    $_SESSION['sb_user'] = "Gast"; // Setze Session sb_user = Gast
  }
}
?>
<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Startseite | Schwarzes Brett</title>
    <link href="style.css" rel="stylesheet"> <!-- setze stylesheet und lade script.js -->
    <script src="admin/script.js"></script>
  </head>
  <body>
    <?php require("admin/cookie.php"); // Lade Scripts ?>
    <div id="header">
	<!-- onClick="reload()" lässt die Seite neuladen -->
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

    <div id="main">

      <div id="main-sidenav">
        <h5>Filter</h5>
        <hr>
        <ul>
          <?php
            $colors = getFarben(); // Führe Funktion getFarben() aus, um alle Rubriken + Farben zu erhalten bsp. array("Auto" -> "rgb(0,0,0));
            foreach($colors as $key => $value){ // Für jede Rubrik als Key und Farbe als Value
              echo "<li><a href='index.php?q=" . $key . "' style='border-left: 2px solid $value;'>" . $key . "</a></li>";
            }
          ?>
        </ul>
        <hr>
        <?php
          if(isset($_GET['q'])){ // Wenn $_GET['q'] gesetzt ist = Bedeutet er hat einen Filter ausgewählt
            echo "<div class='filter-active'><span>" . $_GET['q'] . "</span><img src='img/icon/x.ico' onClick='redirectStartseite();'></div>";
          }
        ?>


      </div>
      <button onClick="createArticle()">Beitrag erstellen</button> <!-- Button mit Weiterleitung auf create.php -->
      <div id="main-beitrag">
        <?php
    			$q = "";
    			$anzeigen = "";

    			if(isset($_GET['q'])){ // Wenn ein Filter aktiv ist
    				  foreach($colors as $key => $value) { // Für jede Rubrik als Key und Farbe als Value
      					if($key == $_GET['q']){ // Wenn der Suchbegriff in "q" ein zulässiger Begriff für die Rubrik ist
      						$q = $_GET['q']; // Erst dann wird in Variable "q" $_GET['q'] gespeichert
      					}
    				  }
    			}

        if($q != ""){  // Wenn q gesetzt ist, wird die Abfrage mit Filter geladen ansonst werden alle geladen
          $anzeigen = getAnzeigenFilter($q);
        } else {
          $anzeigen = getAnzeigen();
        }

        if($anzeigen){ // Wenn anzeigen true und nicht false = Anzeigen wurden geladen
  			  foreach($anzeigen as $key) { // Für jede $anzeige den $key lade $key = array(); und ausgabe der Datenbank Daten
                //$userData = getUserData($key['userID']);
  				      echo '<div class="beitrag" style="border-top: 4px solid ' . $key['rubrikFarbe'] . '">'; // border-top
  				      echo '<div class="beitrag-top"><span>' . $key['anzeigeDatum'] . '</span><a>' . $key['texteUeberschrift'] . '</a></div>';
  				      echo '<div class="beitrag-text"><p>' . $key['texteText'] . '</p></div>';
  				      echo '<div class="beitrag-account"><img src="img/icon/account.svg"><a href=mailto:' . $key['userMail'] . '>' . $key['userBenutzername'] . '</a></div>';
  			        echo '<div class="beitrag-img">';
  				      $bilder = getBilder($key['anzeigeID']); // Falls Bild vorhanden, wird in $bilder der Pfad als Array() gespeichert, ansonsten return false
  				      if($bilder){ // Wenn Pfad existiert
  				            foreach($bilder as $key2){ // für jedes Bilder as $key2, $key2 = array();
  					          echo "<div class='container-img'>";
  					          echo "<img src='" . $key2['bilderDatei'] . "' id='myImg' onClick='openImg(";
  					         	echo '"' . $key2['bilderDatei'] . '"';
  					        	echo ")'>";
  					          echo "</div>";
                    }
  				    }
  				    echo '</div>';
  				    echo '</div>';
  		}
    } else { // Wenn keine Anzeige gefunden wurde
      echo "<h5 style='text-align: center;'>Es wurden keine passenden Anzeigen gefunden!</h5>";
    }


        ?>
        <div id="myModal" class="modal">
          <span class="close">&times;</span>
          <img class="modal-content" id="img01">
        </div>
      </div>
    </div>

    <script>
	// reload setzt href auf aktuelle Seite
    function reload() {
      window.location.href = "index.php";
    }

    // Modal = container für das zu ladene Bild
    var modal = document.getElementById('myModal');
	// myImg das zu ladene Bild
    var img = document.getElementById('myImg');
	// modalImg das zu ersetzende Bild (Platzhalter)
    var modalImg = document.getElementById("img01");
	// Funktion, um das angeklickte Bild in den Platzhalte zu setzen
     function openImg(sourc){
      modal.style.display = "block";
      modalImg.src = sourc;
    }

    // span Element, um den Container zu schließen // ICON
    var span = document.getElementsByClassName("close")[0];

    // Wenn span angeklickt => Container schließt sich
    span.onclick = function() {
      modal.style.display = "none";
    }

    </script>

  </body>
</html>
