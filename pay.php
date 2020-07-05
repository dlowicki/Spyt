<?php
session_start();
require("admin/script.php");

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
    <title>Bezahlen | Schwarzes Brett</title>
    <link href="style.css" rel="stylesheet">
    <script src="admin/script.js"></script>
  </head>
  <body>
    <?php require("admin/cookie.php"); // Lade Scripts ?>
    <?php
    if(!isset($_POST['createsubmit'])){ // Wenn er zuvor nicht bei create.php Daten eingegeben hat
      header("Location: index.php"); // Weiterleitung auf index.php, da er keine Daten bei create.php eingegeben hat
      return; // PHP Script abbruch
    }


    if(isset($_POST['paysubmit'])){ // paysubmit wurde gesetzt = Button betätigt
        $check1 = false;
        if(isset($_COOKIE['sb_user']) && isset($_POST['rubrik']) && isset($_SESSION['sb_user'])){ // cookie rubrik und session sind gesetzt
          $rubrikID = $_POST['rubrik']; // erhalten ausgewählte RubrikID
          $date = echoDate(); // erhalte aktuelles Datum
          $ueberschrift = $_POST['ueberschrift'];
          $text = $_POST['text'];
          $check1 = true;
          if(checkVariableString($text, 250) == "" || checkVariableString($ueberschrift, 50) == ""){ // Wenn checkVariableString
            echo "<div id='error'><h4>Der von Ihnen eingegeben Text ist zu lang!</h4></div>";
            $check1 = false;
          }

        }

        if($check1 == true){ // Wenn alle Eintragungen fehlerfrei eingetragen werden können
          $textID = uniqid();
          $userID = openssl_decrypt($_COOKIE['sb_user'], "AES-128-ECB", "key_sb_user");
          $anzID = createAnzeige($userID,$textID,$rubrikID,$date);

          if(compareTexte($textID, $ueberschrift, $text, $anzID)){
            $bilderCount = 1;
            if(isset($_FILES['bild1']) || isset($_FILES['bild2']) || isset($_FILES['bild3'])){
              for($t=1;$t<4;$t++){
                if($_FILES["bild$t"]['size'] > 0){ // Wenn Bild gesetzt ist und size > 0
                  $dir = "img/";
                  $file = $dir . basename($_FILES["bild$t"]['name']);
                  $check = getimagesize($_FILES["bild$t"]["tmp_name"]);
                  if($check !== false){
                    if(file_exists($file)){
                      $cp = compareBilder($file, $anzID);
                      continue;
                    }
                    if(!move_uploaded_file($_FILES["bild$t"]["tmp_name"], $file)){
                      echo "<div id='error'><p>Die Bilder konnten nicht hochgeladen werden! [87]</p></div>";
                    } else {
                      compareBilder($file, $anzID);
                      $bilderCount++;
                      continue;
                    }
                  } else {
                    echo "<div id='error'><p>Die Bilder konnten nicht hochgeladen werden! [89]</p></div>";
                    continue;
                  }
                }
              }
            }
          }
          header("Location: index.php");
        } else {
          echo "<div id='error'><p>Fehler wurde erzeugt</p></div>";
        }

    }
    ?>
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

    <div id="main-pay">
      <div id="create-info">
        <h1>Willkommen bei Schwarzes Brett</h1>
        <p>hier kannst du Anzeigen aufgeben und einsehen. Pro Anzeige spendet der Mitarbeiter einen Betrag, der sich aus der Textlänge
          <b>(pro Zeichen 1 cent)</b> und der hochgeladenen Bilder <b>(pro Bild 20 cent)</b> errechnet. Es können maximal 3 Bilder hochgeladen werden.</p>
      </div>

      <div id="pay-container">
        <div class="pay-vorschau">
          <div class="beitrag">
            <div class="beitrag-top"><span><?php echo echoDate(); ?></span><a><?php echo $_POST['ueberschrift'] ?></a></div>
            <div class="beitrag-text"><p><?php echo $_POST['text'] ?></p>
            </div>
            <div id="beitrag-account-vorschau"><img src="img/icon/account.svg"><h3><?php echo $_POST['user']; ?></h3></div>
          </div>

          <div class="pay-zahlung">
            <div id="pay-uebersicht">
              <h3>Hallo <?php echo $_POST['user']; ?>!</h3>
              <p>Unser System hat folgende Daten erkannt</p>
              <table>
                <tr><td>Zeichen: </td><td><?php  $bilder = 0;  echo strlen($_POST['text']) ?></td></tr>
                <tr><td>Bilder: </td><td><?php echo $bilder; ?></td></tr>
              </table>
              <ul>
                <li><?php echo "- <span id='zeichen'>" . strlen($_POST['text']) . "</span> (" . strlen($_POST['text']) . " * 1)"; ?></li>
                <li id="rechnung-bild">- <span id="bilder">0</span> (0*20)</li>
                <li class="summe" id="rechnung-summe"><?php
                  $summe = (strlen($_POST['text']) + ($bilder*20));
                  if(strlen($summe) == 1){
                    echo "- 00.0" . $summe . "€";
                  } else if(strlen($summe) == 2) {
                    echo "- 00." . $summe . "€";
                  } else if(strlen($summe) == 3) {
                    $summe = substr_replace($summe, ".", 1, 0);
                    echo "- 0" . $summe . "€";
                  } else {
                    $summe = substr_replace($summe, ".", 2, 0);
                    echo "- " . $summe . "€";
                  }
                ?></li>
              </ul>
            </div>

            <div class="pay-form">
              <form action="pay.php" method="POST" id="pay-form-hide" enctype="multipart/form-data">
                <input type="hidden" name="ueberschrift" value="<?php echo $_POST['ueberschrift']; ?>">
                <input type="hidden" name="createsubmit" value=" ">
                <input type="hidden" name="text" value="<?php echo $_POST['text']; ?>">
                <input type="hidden" name="user" value="<?php echo $_POST['user']; ?>">
                <input type="hidden" name="rubrik" value="<?php echo $_POST['rubrik']; ?>">

                <div id="form-upload">
                  <h3>Bilder hinzufügen:</h3>
                  <input type="file" name="bild1" id="bild1" accept="image/*" onchange="addBild('bild1');">
                  <input type="file" name="bild2" id="bild2" accept="image/*" onchange="addBild('bild2');">
                  <input type="file" name="bild3" id="bild3" accept="image/*" onchange="addBild('bild3');">
                </div>
                <input type="submit" name="paysubmit" value="Erstellen">
              </form>
            </div>
          </div>
        </div>


      </div>




    </div>

    <script src="admin/script.js"></script>
    <script>
    function addBild(id) {
      // bilder = Wie viele Bilder schon gesetzt wurden
      var bilder = document.getElementById("bilder").innerHTML;
      // Wenn Bilder nicht gleich 0 | Es wurde schon ein Bild gesetzt
      if(bilder != "0"){
        bilder = parseInt(bilder) + -1;

      }
      var b1 = document.getElementById("bild1").value;
      var b2 = document.getElementById("bild2").value;
      var b3 = document.getElementById("bild3").value;
      var bild = [b1,b2,b3];
      var bilder = 0;
      bild.forEach((item, i) => {
        if(item != 0){
          bilder++;
        }
      });

      document.getElementById("rechnung-bild").innerHTML =  "- <span id='bilder'>" + bilder + "</span>(" + bilder + " * 20)";
      // Bilder und Zeichen als Variabel
      var bilderSumme = bilder*20;
      var textSumme = document.getElementById("zeichen").innerHTML;

      var summe = (parseInt(bilderSumme) + parseInt(textSumme));
      var summeLength = summe.toString().length;
      if(summeLength == 1){
        summe = "- 00.0" + summe + "€";
      } else if(summeLength == 2) {
        summe = "- 00." + summe + "€";
      } else if(summeLength == 3){
        summe = "- 0" + summe.toString().substring(0,1) + "." + summe.toString().substring(1,3) +"€";
      } else {
        summe = "- " + summe.substring(0,2) + "." + summe.substring(1,4) +"€";
      }
      document.getElementById("rechnung-summe").innerHTML = summe;
    }

    function insertString(string, insertion, place) {
      return string.replace(string[place] + string[place + 1], string[place] + insertion + string[place + 1])
    }
    </script>

  </body>
</html>
