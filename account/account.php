<?php
require("../admin/script.php");
session_start();
// Session wird Benutzername gespeichert in COOKIE wird BenutzerID gespeichert

if(isset($_COOKIE['sb_user'])){ // Cookie ist aber gesetzt
  $userid = openssl_decrypt($_COOKIE['sb_user'], "AES-128-ECB", "key_sb_user"); // Dann erhalte die UserID von cookie
  if(strlen($userid) >= 1){ // Wenn die String länge von userID >= 1 ist
    $_SESSION['sb_user'] = getUsernameById($userid, "AES-128-ECB","key_sb_user"); // Dann erhalte Username von UserID
    header("Location: ../index.php");
  } else { // Wenn länge von String nicht >= 1 ist
    header("Location: logout.php");
  }
}


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
      <div class="icon-account-dropdown" onClick="displayAccount()">
        <!--<img src="../img/icon/account.svg" id="icon-account">
        <div class="icon-account-data" id="icon-account-data">
          <?php
          /*if(isset($_SESSION['sb_user']) && $_SESSION['sb_user'] != "Gast"){
            echo "<h5>" . $_SESSION['sb_user'] . "</h5>";
            echo "<a href='logout.php'>Abmelden</a>";
          } else {
            echo "<h5>Gast</h5>";
            echo "<a href=''>Anmelden</a>";
          }*/
          ?>
        </div>-->
      </div>
    </div>

    <?php

      function checkVariableMail($mail) { // E-Mail muss VALIDE sein
        if(preg_match('/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/', $mail)) {
          return true;
        }
        return false;
      }

      function checkVariableInt($var) { // INT's dürfen nicht länger als 20 Zeichen lang sein int's = Telefon, PLZ
        $int = (int) $var;
        if(strlen($int) <= 20) {
          return $int;
        }
        return false;
      }
      if(isset($_POST['button'])){
        switch($_POST['button']){
          case "Login":
            if(isset($_POST['benutzernameLogin']) && isset($_POST['pwLogin'])){
              $benutzername = trim(checkVariableString($_POST['benutzernameLogin'], 30));
              $pw = $_POST['pwLogin'];
              if($benutzername != "" && $pw != ""){
                $userID = loginUser($benutzername, $pw);
                if(strlen($userID) != 0){
                  setcookie("sb_user", openssl_encrypt($userID, "AES-128-ECB", "key_sb_user"), time()+3600*24*365 ,"/");
                  $_SESSION['sb_user'] = $benutzername;
                  header("Location: ../");
                  return;
                } else { echo "<div id='error'> $userID Fehler beim Übermitteln der Daten!<br>Die eingegebenen Daten sind ungültig</div>"; }
              }
            }
          break;
          case "Erstellen":
            if(isset($_POST['name']) && isset($_POST['vorname']) && isset($_POST['strasse']) && isset($_POST['benutzernameRegister']) && isset($_POST['pwRegister']) && isset($_POST['tnr'])){ // Wenn alle Felder editiert wurden!
              if($_POST['name'] != "" && $_POST['vorname'] != "" && $_POST['pwRegister'] != "" && $_POST['strasse'] != "" && $_POST['tnr'] != ""){
                if(checkVariableMail($_POST['email']) && checkVariableInt($_POST['tnr']) != false && checkVariableString($_POST['pwRegister'], 30) != false){
                    $benutzername = trim(checkVariableString($_POST['benutzernameRegister'], 30));
                    if(userExist($benutzername)){
                      echo "<div id='error'>Benutzername wird schon verwendet!</div>";
                    } else {
                      $name = trim(checkVariableString($_POST['name'], 30));
                      $vorname = trim(checkVariableString($_POST['vorname'], 30));
                      $email = trim(htmlspecialchars($_POST['email']));
                      $pw = password_hash($_POST['pwRegister'], PASSWORD_BCRYPT);
                      // Nochmal Überprüfung, ob die Variablen nicht gleich "null" sind
                      if($name != "" && $vorname != "" && $pw != "" && $benutzername != ""){
                        $userID = createUser($name, $vorname, $benutzername, $email, $pw);
                        setcookie("sb_user", openssl_encrypt($userID, "AES-128-ECB", "key_sb_user"), time()+3600*24*365 ,"/");
                        $_SESSION['sb_user'] = $benutzername;
                        header("Location: ../");
                        return;
                      }
                    }
                  } else { echo "<div id='error'>Fehler beim Übermitteln der Daten!<br>Die eingegebenen Daten sind ungültig</div>"; } // Überprüfung falsch
              } else { echo "<div id='error'>Fehler beim Übermitteln der Daten!<br>Die eingegebenen Daten sind ungültig</div>"; } // Eine Eingabe ist Falsch
            }
          break;
        }
      }
    ?>

    <div id="main-account">
      <?php
      if(!isset($_COOKIE['sb_user'])){ // Wenn keine Session und kein Cookie gesetzt ist
        session_destroy();
        echo '<form method="POST" action="account.php">
                <div class="form-account-container" id="register">
                  <div class="form-account-left">
                    <h2>User</h2>
                    <p>Vorname:<br><input type="text" name="vorname" placeholder="Vorname"></p><br>
                    <p>Benutzername:<br><input type="text" name="benutzernameRegister" placeholder="Benutzername"></p><br>
                    <p>E-Mail:<br><input type="email" name="email" placeholder="E-Mail"></p><br>
                    <p>Passwort:<br><input type="password" name="pwRegister" placeholder="Passwort"></p><br>
                  </div>
                  <div class="form-account-right">
                    <h2>Allgemein</h2>
                    <p>Name:<br><input type="text" name="name" placeholder="Name"></p><br>
                  </div>
                </div>
                <div class="form-account-login" id="login">
                  <p>Benutzername:<br><input type="text" name="benutzernameLogin" placeholder="Benutzername"></p>
                  <p>Passwort:<br><input type="password" name="pwLogin" placeholder="Passwort"></p>
                </div>
                <input type="submit" value="Login" name="button" id="button">
              </form>';
       echo '<div class="account-register-info">
               <h5 id="account-info">Du hast noch kein Konto?</h5><br>
               <button type="button" class="pointer" id="ac-button" onClick="accountSwitch()">Registrieren</button>
             </div>';
      } else if(isset($_COOKIE['sb_user'])){ // Wenn Cookie gesetzt ist
        if(strlen(openssl_decrypt($_COOKIE['sb_user'], "AES-128-ECB", "key_sb_user")) >= 1){ // Wenn Cookie richtigen Wert hat
          echo '<div id="account-angemeldet"><h2>Sie sind bereits angemeldet als ' . $_SESSION['sb_user'] . '!</h2>';
          echo '<a href="logout.php">Abmelden</a></div>';
        } else { // Andernfalls Weiterleitung zu logout
          header("Location: logout.php");
        }
      }
      ?>
    </div>

    <script>
      function accountSwitch() {
        if(document.getElementById("button").value == "Login"){

          if(document.getElementById("button").value == "Erstellen"){
            document.getElementById("ac-button").childNodes[0].nodeValue  = "Registrieren";
          } else {
            document.getElementById("ac-button").childNodes[0].nodeValue  = "Login";
          }

          document.getElementById("login").style.display = "none";
          document.getElementById("register").style.display = "block";
          document.getElementById("button").value = "Erstellen";
          document.getElementById("account-info").innerHTML = "Du hast schon ein Konto?";

        } else {
          document.getElementById("ac-button").childNodes[0].nodeValue  = "Registrieren";
          document.getElementById("login").style.display = "block";
          document.getElementById("register").style.display = "none";
          document.getElementById("button").value = "Login";
          document.getElementById("account-info").innerHTML = "Du hast noch kein Konto?";

        }

      }
    </script>

  </body>
</html>
