<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Login | Schwarzes Brett</title>
    <link href="../style.css" rel="stylesheet">
    <script src="../admin/script.js"></script>
  </head>
  <body>
    <div id="header">
      <h1><a href="../">Schwarzes Brett</a></h1>
      <img src="../img/icon/account.svg">
    </div>

    <?php
      if(isset($_POST['button'])){
        if(isset($_POST['name']) && isset($_POST['vorname']) && isset($_POST['plz']) && isset($_POST['ort']) && isset($_POST['strasse']) && isset($_POST['tnr'])){
          $name = str_replace(";", "", $_POST['name']);
          $vorname = str_replace(";", "", $_POST['vorname']);
          $plz = str_replace(";", "", $_POST['plz']);
          $ort = str_replace(";", "", $_POST['ort']);
          $st = str_replace(";", "", $_POST['strasse']);
          $tnr = str_replace(";", "", $_POST['tnr']);

          if($name != "" && $vorname != "" && $plz != "" && $ort != "" && $st != "" && $tnr != ""){
            setcookie("user", $name . ";" . $vorname . ";" . $plz . ";" . $ort . ";" . $st . ";" . $tnr, time()+3600*24*365 ,"/");
            header("Location: ../");
          }
        }
      }
    ?>

    <div id="main-account">
      <?php
      if(!isset($_COOKIE['user'])){
        echo '<form method="POST" action="account.php">
                <p>Name:<br><input type="text" name="name" placeholder="Name"></p>
                <p>Vorname:<br><input type="text" name="vorname" placeholder="Vorname"></p>
                <p>Postleitzahl:<br><input type="text" name="plz" placeholder="PLZ"></p>
                <p>Ort:<br><input type="text" name="ort" placeholder="Ort"></p>
                <p>Straße:<br><input type="text" name="strasse" placeholder="Straße"></p>
                <p>Telefon:<br><input type="text" name="tnr" placeholder="Telefon-Nr"></p>
                <input type="submit" value="Erstellen" name="button">
              </form>';
      } else {
        echo '<div id="account-angemeldet"><h2>Sie sind bereits angemeldet!</h2>';
        echo '<a href="logout.php">Abmelden</a></div>';
      }
      ?>

    </div>

    <script>

    </script>

  </body>
</html>
