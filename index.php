<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/master.css">
    <link rel="stylesheet" href="css/place.css">
    <title>Spyt | Startseite</title>
  </head>
  <body>
    <div class="container">
      <div class="navigation">
        <ul>
          <li><a href="">Startseite</a></li>
          <li><a href="">Raum 1</a></li>
          <li><a href="">Raum 2</a></li>
        </ul>
      </div>
      <div class="main">
        <h1>Übericht der Räume</h1>
        <div class="display">
          <div class="display-main">
            <div class="edv-1 display-box" id="edv-1" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
            <div class="edv-2 display-box" id="edv-2" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
            <div class="edv-3 display-box" id="edv-3" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
          </div>

          <div class="display-list">
            <ul ondrop="drop(event)" ondragover="allowDrop(event)">
              <div class="computer" id="pccad153" draggable='true' ondragstart='drag(event)'><li>PCCAD153</li></div>
              <div class="computer" id="edv01" draggable='true' ondragstart='drag(event)'><li>EDV01</li></div>
            </ul>
          </div>

        </div>


      </div>
    </div>
    <script type="text/javascript">
      function allowDrop(ev) {
        ev.preventDefault();
      }

      function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
      }

      function drop(ev) {
        ev.preventDefault();
        // data = id
        var data = ev.dataTransfer.getData("text");
        ev.target.appendChild(document.getElementById(data));
        var inhalt = document.getElementById(data).innerHTML;
      }
    </script>
  </body>
</html>
