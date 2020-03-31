<?php
session_start();
require("admin/script.php");

if(!isset($_COOKIE['user'])){
  header('Location: account/account.php');
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
      <img src="img/icon/account.svg" onClick="registerUser()">
    </div>

    <div id="main-create">
      <div id="create-info">
        <h1>Willkommen bei Schwarzes Brett</h1>
        <p>hier kannst du Anzeigen aufgeben und einsehen. Pro Anzeige spendet der Mitarbeiter einen Betrag, der sich aus der Textlänge
          <b>(pro Zeichen 1 cent)</b> und der hochgeladenen Bilder <b>(pro Bild 20 cent)</b> errechnet. Es können maximal 3 Bilder hochgeladen werden.</p>
      </div>
      <div id="create-container">
        <div id="create-sidebar">
          <ul>
            <li>Bearbeiten</li>
            <li onClick="startVorschau()">Vorschau</li>
            <li onClick="resetVorschau()">Zurücksetzen</li>
          </ul>
        </div>
          <form method="POST" action="pay.php" enctype="multipart/form-data">
            <input type="text" name="ueberschrift" id="vorschau-ueberschrift-form" placeholder="Überschrift">
            <input type="hidden" name="user" id="vorschau-account-form" value="<?php $user = $_COOKIE['user']; $user = explode(";", $user); echo $user[1]. " " . $user[0]; ?>">
            <span>27.03.2020</span>
            <label>Rubrik: <select id="rubrik" name="rubrik">
              <?php
                $data = getRubriken();
                foreach($data as $key => $row){
                  echo "<option value='$key'>$row</option>";
                }
              ?>
            </select></label>
            <h3 id="count">Zeichen: 0</h3>
            <textarea name="text" rows="6" cols="100" id="vorschau-text-form" onkeyup="count()"></textarea>
            <input type="submit" id="Button" name="submit" value="Weiter" disabled>
          </form>

          <div class="beitrag" id="vorschau">
            <div class="beitrag-top"><span id="vorschau-datum"></span><a id="vorschau-ueberschrift"></a></div>
            <div class="beitrag-text"><p id="vorschau-text"></p></div>
            <div class="beitrag-account"><img src="img/icon/account.svg"><h3 id="vorschau-account"></h3></div>
          </div>


      </div>
    </div>

    <script>
    function previewImage(){
     var previewBox = document.getElementById("preview");
     previewBox.src = URL.createObjectURL(event.target.files[0]);
    }
      var Button = document.getElementById('Button');
      Button.classList.remove('HoverClassDisabled','HoverClassPointer');
      /* Set the desired hover class */
      Button.classList.add('HoverClassDisabled');

      function startVorschau() {
        var ueberschrift = document.getElementById("vorschau-ueberschrift-form").value;
        var text = document.getElementById("vorschau-text-form").value;

        if(text.length < 1 || ueberschrift.length < 1) {
          alert("Sie müssen eine Überschrift und einen Text wählen");
          return;
        }

        Button.classList.remove('HoverClassPointer','HoverClassDisabled');
        Button.classList.add('HoverClassPointer');

        setTimeout(function(){
          document.getElementById("vorschau").style.display = "block";
          setTimeout(function(){
            document.getElementById("vorschau-ueberschrift").innerHTML = ueberschrift;
            var datum = new Date();
            document.getElementById("vorschau-datum").innerHTML = datum.getDate() + "." + datum.getMonth() + "." + datum.getFullYear();
            setTimeout(function(){
              var account = document.getElementById("vorschau-account-form").value;
              document.getElementById("vorschau-account").innerHTML = account;
              setTimeout(function(){
                document.getElementById("vorschau-text").innerHTML = text;
                document.getElementById("Button").disabled = false;
              }, 300);
            }, 300);
          }, 300);
        }, 500);
      }

      function count() {
        var text = document.getElementById("vorschau-text-form").value;
        document.getElementById("count").innerHTML = "Zeichen: " + text.length;
      }

      function resetVorschau() {
        var Button = document.getElementById('Button');
        Button.classList.remove('HoverClassPointer','HoverClassDisabled');
        Button.classList.add('HoverClassDisabled');
        document.getElementById("vorschau-ueberschrift-form").value = "";
        document.getElementById("vorschau-ueberschrift").innerHTML = "";
        document.getElementById("vorschau-text-form").value = "";
        document.getElementById("vorschau-account").innerHTML = "";
        document.getElementById("vorschau-text").innerHTML = "";
        document.getElementById("vorschau-account").innerHTML = "";
        document.getElementById("vorschau").style.display = "none";
        document.getElementById("count").innerHTML = "Zeichen: 0";
        document.getElementById("Button").disabled = true;
      }
    </script>

  </body>
</html>
