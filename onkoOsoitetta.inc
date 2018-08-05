<?php
// Katsotaan, onko asiakkaalla yhtään osoitetta
require_once("db.inc");
$connosoite = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
$connosoite->set_charset("utf8");

if ( mysqli_connect_errno() )
{
  // Lopettaa tämän skriptin suorituksen ja tulostaa parametrina tulleen tekstin
  die ("Tietokantapalvelinta ei löydy, syy: " . mysqli_connect_error());
}

// suoritetaan tietokantakysely ja kokeillaan hakea osoitteita
$query = "SELECT * FROM toimitusosoite WHERE tunnus='$tunnus'";
$tulos = mysqli_query($connosoite, $query);
// Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
if ( !$tulos )
{
  tulostaVirhe("Osoitetietojen haku epäonnistui!" . mysqli_error($connosoite));
}
else {
  //tulostaSuccess("Onnistui!", "Tietokantakysely onnistui");
  if (mysqli_num_rows($tulos) == 0) {
    echo "<div class=\"alert alert-warning\" role=\"alert\">Ei löytynyt yhtään toimitusosoitetta.<br />Lisää vähintään yksi toimitusosoite, jotta voit tehdä työtilauksen tai tarjouksen.</div>";
    echo "<form><button type=\"submit\" class=\"btn btn-primary\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"toimitusosoite\" value=\"lisaa\">Uusi toimitusosoite</button></form><br />";
    $_SESSION["onToimitusosoite"] = false;
  }
  else {
    $_SESSION["onToimitusosoite"] = true;
  }
}

require_once("db.inc");
$connosoite = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
$connosoite->set_charset("utf8");

if ( mysqli_connect_errno() )
{
  // Lopettaa tämän skriptin suorituksen ja tulostaa parametrina tulleen tekstin
  die ("Tietokantapalvelinta ei löydy, syy: " . mysqli_connect_error());
}

// suoritetaan tietokantakysely ja kokeillaan hakea osoitteita
$query = "SELECT * FROM laskutusosoite WHERE tunnus='$tunnus'";
$tulos = mysqli_query($connosoite, $query);
// Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
if ( !$tulos )
{
  tulostaVirhe("Osoitetietojen haku epäonnistui!" . mysqli_error($connosoite));
}
else {
  //tulostaSuccess("Onnistui!", "Tietokantakysely onnistui");
  if (mysqli_num_rows($tulos) == 0) {
    echo "<div class=\"alert alert-warning\" role=\"alert\">Ei löytynyt yhtään laskutusosoitetta.<br />Lisää vähintään yksi laskutusosoite, jotta voit tehdä työtilauksen tai tarjouksen.</div>";
    echo "<form><button type=\"submit\" class=\"btn btn-primary\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"laskutusosoite\" value=\"lisaa\">Uusi laskutusosoite</button></form><br />";
    $_SESSION["onLaskutusosoite"] = false;
  }
  else {
    $_SESSION["onLaskutusosoite"] = true;
  }
}
?>
