function registerUser() {
  window.location.href = "account/account.php";
}

function createArticle() {
  window.location.href = "create.php";
}

function redirectStartseite() {
  window.location.href = "index.php";
}

function displayAccount() {
  if(document.getElementById("icon-account-data").style.display == "block"){
    document.getElementById("icon-account-data").style.display = "none";
  } else {
    document.getElementById("icon-account-data").style.display = "block";
  }
}

function createCookieAccept(id) {
  document.cookie = 'sb_accept='+id+'; expires=Thu, 18 Dec 2021 12:00:00 UTC; path=/';
  document.getElementById("cookie").style.display = "none";
}
