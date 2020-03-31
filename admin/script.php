<?php
function getMysqlData() {
  return parse_ini_file("database.ini");
}

function echoDate() {
  return date("Y-n-d");
}

function connect() {
  $data = getMysqlData();
  $servername = $data['servername'];
  $username = $data['username'];
  $password = $data['password'];
  $db = $data['database'];

  // Create connection
  $conn = new mysqli($servername, $username, $password, $db);

  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
      return false;
  }
  return $conn;
}

function getRubriken() {
  $conn = connect();
  $statement = "SELECT * FROM rubriken";
  $result = $conn -> query($statement);
  $data = array();

  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
       $data[$row['rubrikenID']] = $row['Rubrik'];
    }
    return $data;
  }
  return false;
}

function createAnzeige($name,$vorname,$plz,$ort,$strasse,$tel,$rubrikID,$date) {
  $conn = connect();
  $statement = "INSERT INTO anzeige (anzeigeID, anzeigeName, anzeigeVorname, anzeigePLZ, anzeigeStrasse, anzeigeTNR, anzeigeDatum) VALUES (NULL, '$name', '$vorname', '$plz', '$strasse', '$tel', '$date')";
  $result = $conn -> query($statement);
  if($result === TRUE){
    $statement2 = "SELECT MAX(anzeigeID) as id FROM anzeige";
    $result2 = $conn -> query($statement2);
    $data = "";
    if($result2->num_rows == 1){
      while($row = $result2->fetch_assoc()){
         $data = $row['id'];
         break;
      }
        return $data;
    }
    return false;
  }
  return false;
}

function compareAnzeigeAndRubrik($anzeigeID, $rubrikID) {
  $conn = connect();
  $statement = "INSERT INTO anz_rubrik (anzID, rubID) VALUES ('$anzeigeID', '$rubrikID')";
  $result = $conn -> query($statement);

  if($result === TRUE){
    return true;
  }
  return false;
}

function compareTexte($ueberschrift,  $text, $anzID) {
  $conn = connect();
  $statement = "INSERT INTO texte (texteID, textUeberschrift, texteText, anzID) VALUES (NULL, '$ueberschrift', '$text', '$anzID')";
  $result = $conn -> query($statement);

  if($result === TRUE){
    return true;
  }
  return false;
}

function compareBilder($bild, $anzID) {
  $conn = connect();
  $statement = "INSERT INTO bilder (bilderID, bilderDatei, anzID) VALUES (NULL, '$bild', '$anzID')";
  $result = $conn -> query($statement);

  if($result === TRUE){
    return true;
  }
  return false;
}

function createOrt($plz, $ort) {
  $conn = connect();
  $statementAbfrage = "SELECT ortePLZ FROM Orte WHERE ortePLZ = '$plz'";
  $result = $conn -> query($statementAbfrage);

  if($result->num_rows == 0) {
    $statementInsert = "INSERT INTO Orte (ortePLZ, orteOrt) VALUES ('$plz', '$ort')";
    $result2 = $conn -> query($statementInsert);
    if($result2 === TRUE){
      return true;
    }
  }
  return false;
}

function createZahlung($zkn, $zkt, $zad, $anzID) {
  $conn = connect();
  $statement = "INSERT INTO zahlungsinfo (zID, zKarten_Nummer, zKartenTyp, zAblaufdatum, anzID) VALUES (NULL,'$zkn','$zkt','$zad','$anzID')";
  $result = $conn -> query($statement);

  if($result === TRUE){
    return true;
  }
  return false;
}

function trueDate($date) {
  if($date != "0000-00-00"){
    return true;
  }
  return false;
}

function checkKartenNummer($nummer) {
  if(strlen($nummer) >= 12 && strlen($nummer) <= 16 && is_int($nummer)){
    return true;
  }
  return false;
}

function checkText($text) {
  if(strlen($text) <= 250 && strlen($text) > 0){
    return true;
  }
  return false;
}

function getRubrikColor($id) {
  switch($id){
    case 1:
    return "blue";
    break;
    case 2:
    return "yellow";
    break;
    case 3:
    return "purple";
    break;
    case 4:
    return "orange";
    break;
  }
  return "black";
}

function getAnzeigen() {
  $conn = connect();
  $spalten = "anzeigeID, anzeigeName, anzeigeVorname, anzeigePLZ, anzeigeStrasse, anzeigeTNR, anzeigeDatum, textUeberschrift, texteText, Rubrik, rubrikFarbe";
  $statement = "SELECT $spalten FROM anzeige INNER JOIN bilder ON anzeige.anzeigeID = bilder.anzID INNER JOIN texte ON bilder.anzID = texte.anzID INNER JOIN anz_rubrik ON texte.anzID = anz_rubrik.anzID INNER JOIN rubriken ON anz_rubrik.rubID = rubriken.rubrikenID";
  $result = $conn -> query($statement);
  $daten = array();

  if($result->num_rows > 0){
    $r = 0;
    while($row = $result->fetch_assoc()){
      $daten[$r]['id'] = $r;
      $daten[$r]['anzeigeID'] = $row['anzeigeID'];
      $daten[$r]['anzeigeName'] = $row['anzeigeName'];
      $daten[$r]['anzeigeVorname'] = $row['anzeigeVorname'];
      $daten[$r]['anzeigePLZ'] = $row['anzeigePLZ'];
      $daten[$r]['anzeigeStrasse'] = $row['anzeigeStrasse'];
      $daten[$r]['anzeigeTNR'] = $row['anzeigeTNR'];
      $daten[$r]['anzeigeDatum'] = $row['anzeigeDatum'];
      $daten[$r]['textUeberschrift'] = $row['textUeberschrift'];
      $daten[$r]['texteText'] = $row['texteText'];
      $daten[$r]['Rubrik'] = $row['Rubrik'];
      $daten[$r]['rubrikFarbe'] = $row['rubrikFarbe'];
      $r++;
    }
      return $daten;
  }
  return false;
}

function getBilder($id) {
  $conn = connect();
  $statement = "SELECT bilderDatei, bilderID FROM bilder WHERE bilder.anzID = '$id'";
  $result = $conn -> query($statement);
  $daten = array();

  if($result->num_rows > 0) {
    $r = 0;
    while($row = $result->fetch_assoc()) {
      $daten[$r]['bilderDatei'] = $row['bilderDatei'];
      $daten[$r]['bilderID'] = $row['bilderID'];
      $r++;
    }
    return $daten;
  }
  return false;
}
?>
