<?php
if(isset($_COOKIE['sb_accept'])){
  $value = openssl_decrypt($_COOKIE['sb_accept'], "AES-128-ECB", "key_sb_accept");
  if($value != "1"){
    $value = openssl_encrypt("1", "AES-128-ECB", "key_sb_accept");
    // Cookie noch nicht akzeptiert
    echo "<div id='cookie'><p>Wir verwenden Cookies um die Nutzung unserer Website zu optimieren.<br><br><button
    onClick='createCookieAccept(";
    echo '"' . $value . '"';
    echo ")' class='pointer'>Verstanden</button></p></div>";
  }
} else {
  $value = openssl_encrypt("1", "AES-128-ECB", "key_sb_accept");
  // Cookie noch nicht akzeptiert
  echo "<div id='cookie'><p>Wir verwenden Cookies um die Nutzung unserer Website zu optimieren.<br><br><button
  onClick='createCookieAccept(";
  echo '"' . $value . '"';
  echo ")' class='pointer'>Verstanden</button></p></div>";
}


?>
