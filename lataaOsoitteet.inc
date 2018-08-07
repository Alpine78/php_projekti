<?php
// Katsotaan, onko asiakkaalla yhtään toimitusosoitetta ja tallennetaan ne taulukkoon
require_once("db.inc");
$conn2 = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
$conn2->set_charset("utf8");

if ( mysqli_connect_errno() )
{
  // Lopettaa tämän skriptin suorituksen ja tulostaa parametrina tulleen tekstin
  die ("Tietokantapalvelinta ei löydy, syy: " . mysqli_connect_error());
}

// suoritetaan tietokantakysely ja kokeillaan hakea osoitteita
$query = "SELECT * FROM toimitusosoite WHERE tunnus='$tunnus'";
$tulos = mysqli_query($conn2, $query);
// Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
if ( !$tulos )
{
  tulostaVirhe("Osoitetietojen haku epäonnistui!" . mysqli_error($conn2));
}
else {
  $toimitusosoitteet = array();
  while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
    //haetaan tiedot muuttujiin ja lopuksi taulukkoon
    $valittuToimitusosoiteID = $rivi["osoiteID"];
    $lahiosoite = $rivi["lahiosoite"];
    $postinumero = $rivi["postinumero"];
    $postitoimipaikka = $rivi["postitoimipaikka"];
    $asunnonTyyppi= $rivi["asunnonTyyppi"];
    $osoiterimpsu = $lahiosoite . ", " . $postinumero . " " . $postitoimipaikka . " (" . $asunnonTyyppi . ")";
    $toimitusosoitteet[$valittuToimitusosoiteID] = $osoiterimpsu;
  }
}

// Laskutusosoitteiden haku taulukkoon
$conn2 = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
$conn2->set_charset("utf8");

if ( mysqli_connect_errno() )
{
  // Lopettaa tämän skriptin suorituksen ja tulostaa parametrina tulleen tekstin
  die ("Tietokantapalvelinta ei löydy, syy: " . mysqli_connect_error());
}

// suoritetaan tietokantakysely ja kokeillaan hakea osoitteita
$query = "SELECT * FROM laskutusosoite WHERE tunnus='$tunnus'";
$tulos = mysqli_query($conn2, $query);
// Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
if ( !$tulos )
{
  tulostaVirhe("Osoitetietojen haku epäonnistui!" . mysqli_error($conn2));
}
else {
  $laskutusosoitteet = array();
  while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
    //haetaan tiedot muuttujiin
    $valittuLaskutusosoiteID = $rivi["osoiteID"];
    $laskutusnimi = $rivi["laskutusnimi"];
    $lahiosoite = $rivi["lahiosoite"];
    $postinumero= $rivi["postinumero"];
    $postitoimipaikka= $rivi["postitoimipaikka"];
    $osoiterimpsu = $laskutusnimi . ", " . $lahiosoite . ", " . $postinumero . " " . $postitoimipaikka;
    $laskutusosoitteet[$valittuLaskutusosoiteID] = $osoiterimpsu;
  }
}

?>
