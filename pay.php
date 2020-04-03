<?php
session_start();
require("admin/script.php");

if(!isset($_COOKIE['user'])){
  header('Location: account/');
  return;
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
    <?php
    // Wenn er zuvor nicht bei create.php Daten eingegeben hat
    /*if(!isset($_POST['submit'])){
      header("Location: index.php");
      return;
    }*/

    if(isset($_POST['ksubmit']) && isset($_POST['kablaufdatum']) && isset($_POST['ktyp']) && isset($_POST['knummer'])){
      if($_POST['ksubmit'] != ""){
        $anzID = "";
        $anzeige = false;
        $anz_rubrik = false;
        $orte = false;
        $text = false;
        $zahlungsinfo = false;

        if(isset($_COOKIE['user']) && isset($_POST['rubrik'])){
          $cookie = explode(";", $_COOKIE['user']);
          $name = $cookie[0];
          $vorname = $cookie[1];
          $plz = $cookie[2];
          $ort = $cookie[3];
          $strasse = $cookie[4];
          $tel = $cookie[5];
          $rubrik = $_POST['rubrik'];
          $date = echoDate();
          // Daten überprüft Anzeige, anz_rubrik und Orte können in Datenbank eingetragen werden
          $anzeige = true;
          $anz_rubrik = true;
          $orte = true;
        }

        if(isset($_POST['rubrik'])){
          $ueberschrift = $_POST['ueberschrift'];
          $text = $_POST['text'];
          if(checkText($text)){
            // Text kann in Datenbank eingetragen werden
            $texte = true;
          } else {
            echo "<div id='error'><h4>Der von Ihnen eingegeben Text ist zu lang!</h4></div>";
          }
        }

        if(isset($_POST['kablaufdatum']) && isset($_POST['ktyp']) && isset($_POST['knummer'])){
            $knummer = $_POST['knummer'];
            $kablaufdatum = $_POST['kablaufdatum'];
          if(trueDate($kablaufdatum) && checkKartenNummer((int) $knummer)){
            $typ = $_POST['ktyp'];
            // zahlungsinfo kann in Datenbank eingetragen werden
            $zahlungsinfo = true;
          } else {
            echo "<div id='error'><h4>Die Daten der Kreditkarte sind nicht korrekt!</h4></div>";
          }
        }

        // Wenn alle Eintragungen fehlerfrei eingetragen werden können
        if($anzeige == true && $anz_rubrik == true && $orte == true  && $texte == true && $zahlungsinfo == true){
          $anzID = createAnzeige($name,$vorname,$plz,$ort,$strasse,$tel,$rubrik,$date);
          createZahlung($knummer,$typ,$kablaufdatum,$anzID);
          compareTexte($ueberschrift, $text, $anzID);
          compareAnzeigeAndRubrik($anzID, $rubrik);
          createOrt($plz, $ort);


          if(isset($_FILES['bild1']) || isset($_FILES['bild2']) || isset($_FILES['bild3'])){
            for($t=1;$t<4;$t++){
              // Wenn Bild gesetzt ist und size > 0
              if($_FILES["bild$t"]['size'] > 0){
                $dir = "img/";
                $file = $dir . basename($_FILES["bild$t"]['name']);
                $check = getimagesize($_FILES["bild$t"]["tmp_name"]);
                if($check !== false){
                  if(file_exists($file)){
                    compareBilder($file, $anzID);
                    continue;
                  }
                  if(!move_uploaded_file($_FILES["bild$t"]["tmp_name"], $file)){
                    echo "<div id='error'><p>Die Bilder konnten nicht hochgeladen werden! [87]</p></div>";
                  } else {
                    compareBilder($file, $anzID);
                  }
                  //$imageData = file_get_contents($_FILES["bild$t"]["tmp_name"]);
                  //echo sprintf('<img src="data:image/png;base64,%s" class="pay-vorschau-img">', base64_encode($imageData));
                } else {
                  echo "<div id='error'><p>Die Bilder konnten nicht hochgeladen werden! [89]</p></div>";
                  continue;
                }
              }
            }
          }
          header("Location: index.php");
        } else {
          echo "<div id='error'><p>Fehler wurde erzeugt</p></div>";
        }

      }
    }
    ?>
    <div id="header">
      <h1><a href="index.php">Schwarzes Brett</a></h1>
      <img src="img/icon/account.svg" onClick="registerUser()">
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

            <div id="pay-bezahlung">
              <div class="pay-bezahlung-radio">
                <div class="bezahlung">
                  <img src="img/icon/paypal.jpg">
                  <p><input type="radio" id="paypal" name="bezahlung" value="paypal" onchange="radioChange('PayPal')"> PayPal</p>
                </div>
                <div class="bezahlung">
                  <img src="img/icon/paysafecard.png">
                  <p><input type="radio" id="paysafe" name="bezahlung" value="paysafe" onchange="radioChange('PaySafeCard')"> PaySafeCard</p>
                </div>
                <div class="bezahlung">
                  <img src="img/icon/ueberweisung.png">
                  <p><input type="radio" id="ueberweisung" name="bezahlung" value="ueberweisung" onchange="radioChange('Überweisung')"> Überweisung</p>
                </div>
                <div class="bezahlung">
                  <img src="img/icon/Kreditkarte.png">
                  <p><input type="radio" id="kreditkarte" name="bezahlung" value="kreditkarte" onchange="radioChange('Kreditkarte')" checked> Kreditkarte</p>
                </div>
              </div>
            </div>
            <div class="pay-form">
              <h3 id="art">Kreditkarte</h3>
              <p id="pay-error">Das ausgewählte Bezahlsystem ist im moment nicht verfügbar</p>
              <form action="pay.php" method="POST" id="pay-form-hide" enctype="multipart/form-data">
                <input type="hidden" name="ueberschrift" value="<?php echo $_POST['ueberschrift']; ?>">
                <input type="hidden" name="text" value="<?php echo $_POST['text']; ?>">
                <input type="hidden" name="user" value="<?php echo $_POST['user']; ?>">
                <input type="hidden" name="rubrik" value="<?php echo $_POST['rubrik']; ?>">

                <div id="form-upload">
                  <h3>Bilder hinzufügen:</h3>
                  <input type="file" name="bild1" id="bild1" accept="image/*" onchange="addBild('bild1');">
                  <input type="file" name="bild2" id="bild2" accept="image/*" onchange="addBild('bild2');">
                  <input type="file" name="bild3" id="bild3" accept="image/*" onchange="addBild('bild3');">
                </div>

                <div id="form-kredit">
                  <input type="text" name="kuser" placeholder="Vorname Name" value="<?php echo $_POST['user']; ?>" readonly><br>
                  <input type="text" name="knummer" placeholder="Kartennummer"><br>
                  <select name="ktyp">
                    <option value="Charge">Charge-Kreditkarte</option>
                    <option value="Revolving">Revolving-Kreditkarte</option>
                    <option value="Prepaid">Prepaid-Kreditkarte</option>
                    <option value="Debit">Debit-Karte</option>
                  </select><br>
                  <input type="date" name="kablaufdatum" placeholder=""><br>
              </div>
              <input type="submit" name="ksubmit" value="Erstellen">
              </form>
            </div>
          </div>
        </div>


      </div>




    </div>

    <script src="admin/script.js"></script>
    <script>
    function radioChange(value) {
      document.getElementById("art").innerHTML = value;
      if(value != "Kreditkarte"){
        document.getElementById("pay-form-hide").style.display = "none";
        document.getElementById("pay-error").style.display = "block";
      } else {
        document.getElementById("pay-form-hide").style.display = "block";
        document.getElementById("pay-error").style.display = "none";
      }
    }

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
