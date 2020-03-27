<?php
session_start();
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
        <div class="beitrag"> <!-- Rubrik hat eigene Farbe border-top -->
          <div class="beitrag-top"><span>19.02.2020</span><a href="">textUeberschrift</a></div>
          <div class="beitrag-text"><p>Test 123</p></div>
          <div class="beitrag-account"><img src="img/icon/account.svg"><h3>anzeigeVorname</h3></div>
        </div>

        <div class="beitrag"> <!-- Rubrik hat eigene Farbe border-top -->
          <div class="beitrag-top"><span>19.02.2020</span><a href="">textUeberschrift</a></div>
          <div class="beitrag-text"><p>Test 123</p></div>
          <div class="beitrag-account"><img src="img/icon/account.svg"><h3>anzeigeVorname</h3></div>
        </div>

        <div class="beitrag"> <!-- Rubrik hat eigene Farbe border-top -->
          <div class="beitrag-top"><span>19.02.2020</span><a href="">textUeberschrift</a></div>
          <div class="beitrag-text"><p>Test 123</p></div>
          <div class="beitrag-account"><img src="img/icon/account.svg"><h3>anzeigeVorname</h3></div>
        </div>

        <div class="beitrag"> <!-- Rubrik hat eigene Farbe border-top -->
          <div class="beitrag-top"><span>19.02.2020</span><a href="">textUeberschrift</a></div>
          <div class="beitrag-text"><p>Test 123</p></div>
          <div class="beitrag-account"><img src="img/icon/account.svg"><h3>anzeigeVorname</h3></div>
        </div>
      </div>
    </div>

    <script>

    </script>

  </body>
</html>
