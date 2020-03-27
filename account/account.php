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
      <img src="../img/icon/account.svg" onClick="registerUser()">
    </div>

    <div id="main-account">
      <?php
      if(!isset($_SESSION['user'])){
        echo '<form method="POST" action="script.php">
                <p>Name:<br><input type="text" name="name" placeholder="Name"></p>
                <p>Vorname:<br><input type="text" name="vorname" placeholder="Vorname"></p>
                <p>Postleitzahl:<br><input type="text" name="plz" placeholder="PLZ"></p>
                <p>Ort:<br><input type="text" name="ort" placeholder="Ort"></p>
                <p>Straße:<br><input type="text" name="strasse" placeholder="Straße"></p>
                <p>Telefon:<br><input type="text" name="tnr" placeholder="Telefon-Nr"></p>
                <input type="submit" value="Erstellen" name="button">
              </form>';
      } else {
        echo 'Sie sind bereits angemeldet!';
      }
      ?>

    </div>

    <script>

    </script>

  </body>
</html>
