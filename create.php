<?php
session_start();
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
      <img src="img/icon/account.svg" onClick="registerUser()">
    </div>

    <div id="main-create">
      <div id="create-container">
        <div id="create-sidebar">
          <ul>
            <li>Bearbeiten</li>
            <li>Vorschau</li>
          </ul>
        </div>
          <form method="POST" acton="create.php">
            <input type="text" name="ueberschrift" placeholder="Überschrift">
            <span>27.03.2020</span>
            <textarea name="text" rows="6" cols="100"></textarea>
          </form>
      </div>
      <div id="vorschau">
        
      </div>
    </div>

    <script>

    </script>

  </body>
</html>
