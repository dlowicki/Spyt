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

  $conn = new mysqli($servername, $username, $password, $db); // Create connection

  if ($conn->connect_error) {   // Check connection
      die("Connection failed: " . $conn->connect_error);
      return false;
  }
  return $conn;
}

function createUser($userName, $userVorname, $benutzername,$userMail, $userPassword) {
  $con = connect();
  $userIP = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
  $statement = "INSERT INTO sb_user (userID, userName, userVorname, userBenutzername, userMail, userPassword, userIP) VALUES (NULL, '$userName', '$userVorname', '$benutzername', '$userMail', '$userPassword', '$userIP')";
  $result = $con -> query($statement);

  if($result === TRUE){
    $requestID = $con -> query("SELECT userID FROM sb_user WHERE userMail = '$userMail'");
    if($requestID->num_rows > 0){
      foreach ($requestID as $key) {
        return $key['userID'];
      }
    }
  }
  return false;
}

function userExist($benutzername) {
  $con = connect();
  $parameter = "SELECT userBenutzername FROM sb_user WHERE sb_user.userBenutzername = '$benutzername'";
  $result = $con -> query($parameter);

  if($result->num_rows > 0){
    foreach ($result as $key) {
      if($key['userBenutzername'] == $benutzername) {
        return 1;
      }
    }
  }
  return false;
}

function loginUser($name, $pw) {
  $con = connect();
  $statement = "SELECT userPassword, userID FROM sb_user WHERE userBenutzername = '$name' LIMIT 1";
  $result = $con -> query($statement);

  foreach ($result as $key) {
    if(password_verify($pw, $key['userPassword'])){
      return $key['userID'];
    }
  }
  return false;
}

function createAnzeige($userID, $texteID, $rubrikenID, $date) {
  $conn = connect();
  $statement = "INSERT INTO sb_anzeige (anzeigeID, userID, texteID, rubrikenID, anzeigeDatum) VALUES (NULL, '$userID', '$texteID', '$rubrikenID', '$date')";
  $result = $conn -> query($statement);
  if($result === TRUE){
    $statement2 = "SELECT MAX(anzeigeID) as id FROM sb_anzeige";
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

function compareTexte($textID, $ueberschrift,  $text, $anzID) {
  $conn = connect();
  $statement = "INSERT INTO sb_texte (texteID, texteUeberschrift, texteText, anzeigeID) VALUES ('$textID', '$ueberschrift', '$text', '$anzID')";
  $result = $conn -> query($statement);

  if($result === TRUE){
    return true;
  }
  return false;
}

function compareBilder($bild, $anzID) {
  $conn = connect();
  $statement = "INSERT INTO sb_bilder (bilderID, bilderDatei, anzeigeID) VALUES (NULL, '$bild', '$anzID')";
  $result = $conn -> query($statement);

  if($result === TRUE){
    return true;
  }
  return false;
}

function trueDate($date) {
  if($date != "0000-00-00" && preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $date)){
    $exp = explode("-",$date);
    if($exp[1] <= "12" && $exp[1] != "00" && $exp[2] <= "31" && $exp[2] != "00"){
    return true;
    }
  }
  return false;
}

function checkText($text) {
  if(strlen($text) <= 250 && strlen($text) > 0){
    return true;
  }
  return false;
}

function checkVariableString($var, $length) { // Eingaben dürfen nicht länger als $length Zeichen lang sein und nur Zahlen + Buchstaben enthalten
  $html = htmlspecialchars($var); // htmlspecialchars werden unleserlich gemacht
  ini_set("display_errors","off");
  if(strlen($html) <= $length && !preg_match('/[^A-Za-z0-9_äÄöÖüÜß!?-.,#+§\ ]+/', $var)) { // Wenn länge von $var kleiner gleich $length und kein Fehler gefunden
    return $html; // String wird zurückgegeben
  }
  ini_set("display_errors","on");
  return "";
}

/* GET FUNKTIONEN */

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

function getUsernameById($id) {
  $con = connect();
  $parameter = "SELECT DISTINCT userBenutzername FROM sb_user WHERE sb_user.userID = '$id'";
  $result = $con -> query($parameter);

  if($result->num_rows > 0){
    foreach ($result as $key) {
      return $key['userBenutzername'];
    }
  }
  return false;
}

function getUserData($id) {
  $con = connect();
  $parameter = "SELECT DISTINCT * FROM sb_user WHERE sb_user.userID = '$id'";
  $result = $con -> query($parameter);

  $daten = array();
  if($result->num_rows > 0){
    foreach ($result as $row) {
      $daten['userName'] = $row['userName'];
      $daten['userVorname'] = $row['userVorname'];
      $daten['userBenutzername'] = $row['userBenutzername'];
      $daten['userMail'] = $row['userMail'];
      $daten['userIP'] = $row['userIP'];
      $daten['userStrasse'] = $row['userStrasse'];
      $daten['userTelefon'] = $row['userTelefon'];
      $daten['userCreated'] = $row['userCreated'];
    }
    return $daten;
  }
  return false;
}

function getAnzeigen() { // Funktion getAnzeige
  $conn = connect(); // erhalte Verbindung mit Datenbank
  $spalten = "sb_anzeige.anzeigeID, anzeigeDatum, texteUeberschrift, texteText, Rubrik, rubrikFarbe, userBenutzername, userMail"; // gesuchten SELECT Werte für parameter
  $statement = "SELECT DISTINCT $spalten FROM sb_anzeige INNER JOIN sb_texte ON sb_anzeige.anzeigeID = sb_texte.anzeigeID INNER JOIN rubriken ON sb_anzeige.rubrikenID = rubriken.rubrikenID INNER JOIN sb_user WHERE sb_anzeige.userID = sb_user.userID ORDER BY anzeigeID DESC";
  $result = $conn -> query($statement); // query mit passendem parameter

  $daten = array(); // array zum speichern der erhaltenen Daten
  $r = 0; // counter zum hochzählen
  foreach($result as $row){ // Für jedes result als $row
    $daten[$r]['id'] = $r;
    $daten[$r]['anzeigeID'] = $row['anzeigeID'];
    $daten[$r]['anzeigeDatum'] = $row['anzeigeDatum'];
    $daten[$r]['texteUeberschrift'] = $row['texteUeberschrift'];
    $daten[$r]['texteText'] = $row['texteText'];
    $daten[$r]['Rubrik'] = $row['Rubrik'];
    $daten[$r]['rubrikFarbe'] = $row['rubrikFarbe'];
    $daten[$r]['userBenutzername'] = $row['userBenutzername'];
    $daten[$r]['userMail'] = $row['userMail'];
    $r++;
  }
  return $daten;
}

function getAnzeigenFilter($rubrik) {
  $conn = connect();
  $spalten = "sb_anzeige.anzeigeID, anzeigeDatum, texteUeberschrift, texteText, Rubrik, rubrikFarbe, userBenutzername, userMail";
  $statement = "SELECT DISTINCT $spalten FROM sb_anzeige INNER JOIN sb_texte ON sb_anzeige.anzeigeID = sb_texte.anzeigeID INNER JOIN rubriken ON sb_anzeige.rubrikenID = rubriken.rubrikenID INNER JOIN sb_user ON sb_anzeige.userID = sb_user.userID WHERE rubriken.Rubrik = '$rubrik' ORDER BY anzeigeID DESC";
  $result = $conn -> query($statement);
  $daten = array();

  $r = 0;
  foreach($result as $row){ // Für jedes result als $row
    $daten[$r]['id'] = $r;
    $daten[$r]['anzeigeID'] = $row['anzeigeID'];
    $daten[$r]['anzeigeDatum'] = $row['anzeigeDatum'];
    $daten[$r]['texteUeberschrift'] = $row['texteUeberschrift'];
    $daten[$r]['texteText'] = $row['texteText'];
    $daten[$r]['Rubrik'] = $row['Rubrik'];
    $daten[$r]['rubrikFarbe'] = $row['rubrikFarbe'];
    $daten[$r]['userBenutzername'] = $row['userBenutzername'];
    $daten[$r]['userMail'] = $row['userMail'];
    $r++;
  }
  return $daten;
}

function getBilder($id) {
  $conn = connect();
  $statement = "SELECT sb_bilder.bilderDatei FROM sb_bilder WHERE sb_bilder.anzeigeID = '$id'";
  $result = $conn -> query($statement);

  $daten = array();
  $r = 0;
  foreach ($result as $row) {
    $daten[$r]['bilderDatei'] = $row['bilderDatei'];
    $r++;
  }
  return $daten;
}

function getFarben() {
  $con = connect();
  $statement = "SELECT Rubrik, rubrikFarbe FROM rubriken";
  $result = $con -> query($statement);
  $colors = array();

  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      $colors[$row['Rubrik']] = $row['rubrikFarbe'];
    }
    return $colors;
  }
  return false;
}
?>
