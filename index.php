<?php
session_start();
require("admin/script.php");
?>
<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Startseite | Schwarzes Brett</title>
    <link href="style.css" rel="stylesheet">
    <script src="admin/script.js"></script>
  </head>
  <body>
    <div id="header">
      <h1><a href="">Schwarzes Brett</a></h1>
      <img src="img/icon/account.svg" onClick="registerUser()">
    </div>

    <div id="main">
      <div id="main-beitrag">
        <button onClick="createArticle()">Beitrag erstellen</button>
        <?php
        $anzeigen = getAnzeigen();
        foreach($anzeigen as $key) {
          echo '<div class="beitrag" style="border-top: 4px solid ' . $key['rubrikFarbe'] . '">'; // border-top
          echo '<div class="beitrag-top"><span>' . $key['anzeigeDatum'] . '</span><a href="anzeige.php?id=' . $key['anzeigeID'] . '">' . $key['textUeberschrift'] . '</a></div>';
          echo '<div class="beitrag-text"><p>' . $key['texteText'] . '</p></div>';
          echo '<div class="beitrag-account"><img src="img/icon/account.svg"><h3>' . $key['anzeigeVorname'] . ' ' . $key['anzeigeName'] . '</h3></div>';
          echo '<div class="beitrag-img">';
          $bilder = getBilder($key['anzeigeID']);
          foreach($bilder as $key2){
            //echo "<img src='" . $key2['bilderDatei'] . "' class='pay-vorschau-img' onClick='viewBild(" . $key2['bilderID'] . ")'>";

            echo "<div class='container-img'>";
            echo "<img src='" . $key2['bilderDatei'] . "' id='myImg' onClick='openImg(";
                echo '"' . $key2['bilderDatei'] . '"';
                echo ")'>";
            echo "</div>";
          }
          echo '</div>';
          echo '</div>';
        }

        ?>
        <div id="myModal" class="modal">
          <span class="close">&times;</span>
          <img class="modal-content" id="img01">
        </div>
      </div>
    </div>

    <script>
    function viewBild(id) {
      window.location.href = "anzeige.php?img=" + id;
    }

    // Get the modal
    var modal = document.getElementById('myModal');

    var img = document.getElementById('myImg');
    var modalImg = document.getElementById("img01");

     function openImg(sourc){
      modal.style.display = "block";
      modalImg.src = sourc;
    }

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
      modal.style.display = "none";
    }
    </script>

  </body>
</html>
