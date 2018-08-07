<?php
  // Sessio-funktion kutsu
  session_start();

  // Ladataan sessiosta kirjautunueen asiakkaan tiedot muuttujiin, jotta ne voidaan asettaa lomakkeelle.
  if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != "") {
    $tunnus = $_SESSION["kirjautunut"];
    $salasana = $_SESSION["salasana"];
    $etunimi = $_SESSION["etunimi"];
    $sukunimi = $_SESSION["sukunimi"];
    $puhelin = $_SESSION["puhelin"];
    $email = $_SESSION["email"];
    $muokkaustila = $_SESSION["muokkaustila"];
  }
  else {
    // Jos tullaan rekisteöitymään, niin muokkaustila ei ole päällä
    $muokkaustila = false;
    // Nollataan muuttujat
    $tunnus = "";
    $salasana = "";
    $etunimi = "";
    $sukunimi = "";
    $puhelin = "";
    $email = "";
  }
  if (isset($_POST["muokkaa"]) && $_POST["muokkaa"] == "rekisteroidy" || isset($_POST["muokkaa"]) && $_POST["muokkaa"] == "tallenna") {
    $tunnus = $_POST["tunnus"];
    $etunimi = $_POST["etunimi"];
    $sukunimi = $_POST["sukunimi"];
    $puhelin = $_POST["puhelin"];
    $email = $_POST["email"];
  }
  if (isset($_POST["osoitteet"])) {
    $osoitteet = true;
  }
  else {
    $osoitteet = false;
  }
?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Kotitalkkari - Asiakassovellus">
    <meta name="author" content="Ilkka Rytkönen">
    <title>Muokkaa profiilia - Kotitalkkari</title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
  </head>
  <body>
    <?php
    if (isset($_POST["muokkaa"]) && $_POST["muokkaa"] == "tallenna") {
        // Tarkistetaan ja päivitetään muuttuneet tiedot tietokantaan
        $tulos = tarkistamuutos($etunimi, $sukunimi, $puhelin, $email, $errorText);
        if ( $tulos == true )
        {
          $onnistuiko = tallennamuutokset($tunnus, $etunimi, $sukunimi, $puhelin, $email);
          // Muutoksessa ei tarvitsisi päivittää tunnusta ja salasanaa sessioon, mutta
          // samaa funktiota käytetään myös rekisteröinnin yhteydessä.
          // Siksi laitetaan myös nämä parametrit mukaan.
          //paivitasessio($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana);
          if ($onnistuiko) {
            paivitasessio($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana);
          }
        }
        else
        {
          tulostaVirhe($errorText);
        }
    }
      else if (isset($_POST["muokkaa"]) && $_POST["muokkaa"] == "rekisteroidy") {
        $salasana = $_POST["salasana"];
        $salasana2 = $_POST["salasana2"];
        $tulos = tarkistarekisterointi($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana, $salasana2, $errorText);
        if ( $tulos == true )
        {
          rekisteroiAsiakas($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana);
        }
        else
        {
          tulostaVirhe($errorText);
        }
    }
    if (isset($_POST["muokkaa"]) && $_POST["muokkaa"] == "salasana") {
      $uusisalasana = true;
      $vanhasalasana = $_POST["vanhasalasana"];
      $uusisalasana = $_POST["uusisalasana"];
      $salasanauudelleen = $_POST["salasanauudelleen"];
      $tulos = tarkistasalasananmuutos($salasana, $vanhasalasana, $uusisalasana, $salasanauudelleen, $errorText);
      if ($tulos) {
        vaihdasalasana($tunnus, $uusisalasana);
      }
      else {
        tulostaVirhe($errorText);
      }
    }
    else {
      $uusisalasana = false;
    }
    // Ladataan päävalikko ulkopuolisesta tiedostosta.
    // Menu on erilainen kirjautuneelle ja kirjautumattomalle käyttäjälle.
    if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != "") {
      require 'asiakasmenu.php';
    }
    else {
      // Kirjautumatton käyttäjän menu
      require 'perusmenu.php';
    }
    ?>
    <main role="main" class="container">
      <div class="starter-template">
        <h1>Kotitalkkarin asiakassovellus</h1>
        <?php if ($muokkaustila) { ?>
        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link <?php echo ($uusisalasana || $osoitteet) ? '' : 'active' ?>" id="nav-profiili-tab" data-toggle="tab" href="#nav-profiili" role="tab" aria-controls="nav-profiili" aria-selected="<?php echo ($uusisalasana) ? 'false' : 'true' ?>">Profiili</a>
            <a class="nav-item nav-link <?php echo ($osoitteet) ? 'active' : '' ?>" id="nav-osoitteet-tab" data-toggle="tab" href="#nav-osoitteet" role="tab" aria-controls="nav-osoitteet" aria-selected="false">Osoitteet</a>
            <a class="nav-item nav-link <?php echo ($uusisalasana) ? 'active' : '' ?>" id="nav-salasana-tab" data-toggle="tab" href="#nav-salasana" role="tab" aria-controls="nav-salasana" aria-selected="<?php echo ($uusisalasana) ? 'true' : 'false' ?>">Salasana</a>
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
        <?php } ?>
          <div class="tab-pane fade <?php echo ($uusisalasana || $osoitteet) ? '' : 'show active' ?>" id="nav-profiili" role="tabpanel" aria-labelledby="nav-profiili-tab"><h3><?php echo ($muokkaustila) ? 'Profiili</h3>' : 'Rekisteröidy</h3><br/>Kaikki kentät ovat pakollisia!<br/><br/>';?>
            <!-- Profiili-välilehti alkaa tästä -->
            <!-- Rekisteröinti tai tietojen muutos -->
          <form>
            <div class="form-row">
              <div class="col-md-4 mb-3">
                <label for="validationDefaultUsername">Käyttäjätunnus</label>
                <div class="input-group">
                <input type="text" maxlength="30" <?php if ($muokkaustila) echo "readonly"; ?> class="form-control" id="validationDefaultUsername" placeholder="Käyttäjätunnus" name="tunnus" value="<?php echo "$tunnus"; ?>" required>
                </div>
              </div>
              <div class="col-md-4 mb-3">
                <label for="validationDefault01">Etunimi</label>
                <input type="text" maxlength="40" class="form-control" id="validationDefault01" placeholder="Etunimi" name="etunimi" value="<?php echo "$etunimi"; ?>" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="validationDefault02">Sukunimi</label>
                <input type="text" maxlength="50" class="form-control" id="validationDefault02" placeholder="Sukunimi" name="sukunimi" value="<?php echo "$sukunimi"; ?>" required>
              </div>
            </div>
            <div class="form-row">
              <div class="col-md-6 mb-2">
                <label for="validationDefault03">Puhelin</label>
                <input type="text" maxlength="13" class="form-control" id="validationDefault03" placeholder="Puhelin" name="puhelin" value="<?php echo "$puhelin"; ?>" required>
              </div>
              <div class="col-md-6 mb-2">
                <label for="validationDefault04">Email</label>
                <input type="email" maxlength="50" class="form-control" id="validationDefault04" placeholder="Email" name="email" value="<?php echo "$email"; ?>" required>
              </div>
            </div>
            <?php if (!isset($_SESSION["kirjautunut"])) { ?>
            <div class="form-row">
              <div class="col-md-6 mb-2">
                <label for="validationDefault05">Salasana</label>
                <input type="password" maxlength="30" class="form-control" id="validationDefault05" placeholder="Salasana" name="salasana" required>
              </div>
              <div class="col-md-6 mb-2">
                <label for="validationDefault06">Salasana uudelleen</label>
                <input type="password" maxlength="30" class="form-control" id="validationDefault06" placeholder="Salasana uudelleen" name="salasana2" required>
              </div>
            </div>
            <button class="btn btn-primary" type="submit" formaction="kayttajatiedot.php" formmethod="post" name="muokkaa" value="rekisteroidy">Rekisteröidy</button>
            <?php }
            else { ?>
              <button class="btn btn-primary" type="submit" formaction="kayttajatiedot.php" formmethod="post" name="muokkaa" value="tallenna">Tallenna muutokset</button>
            <?php } ?>
          </form>
          <br />
        </div> <!-- Profiili-välilehti loppuu tähän -->
        <?php if ($muokkaustila): ?>
          <!-- Osoite- ja -salasana-välilehdet näytetään ainoastaan kirjautuneille käyttäjille. -->
          <div class="tab-pane fade <?php echo ($osoitteet) ? 'show active' : '' ?>" id="nav-osoitteet" role="tabpanel" aria-labelledby="nav-osoitteet-tab"><h3>Osoitteet</h3>
            <?php
            // Katsotaan, onko asiakkaalla yhtään osoitetta
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
              //tulostaSuccess("Onnistui!", "Tietokantakysely onnistui");
              if (mysqli_num_rows($tulos) == 0) {
                echo "<div class=\"alert alert-warning\" role=\"alert\">Ei löytynyt yhtään toimitusosoitetta.<br />Lisää vähintään yksi toimitusosoite.</div>";
                echo "<form><button type=\"submit\" class=\"btn btn-primary\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"toimitusosoite\" value=\"lisaa\">Uusi toimitusosoite</button></form><br />";
                $_SESSION["onToimitusosoite"] = false;
              }
              else {
                $_SESSION["onToimitusosoite"] = true;
                // Osoitteita löytyi, listataan ne tähän.
                echo "<strong>Toimitusosoitteet ovat:</strong><table class=\"table\"><thead><tr><th scope=\"col\">Lähiosoite</th><th scope=\"col\">Postinumero</th><th scope=\"col\">Postitoimipaikka</th><th scope=\"col\">Asunnon tyyppi</th><th scope=\"col\"></th></tr></thead><tbody>";
                while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
                  //haetaan tiedot muuttujiin
                  $osoiteID = $rivi["osoiteID"];
                  $lahiosoite = $rivi["lahiosoite"];
                  $postinumero = $rivi["postinumero"];
                  $postitoimipaikka = $rivi["postitoimipaikka"];
                  $asunnonTyyppi = $rivi["asunnonTyyppi"];
                  //tulostetaan taulukon rivi
                  echo "<tr><td>$lahiosoite</td><td>$postinumero</td><td>$postitoimipaikka</td><td>$asunnonTyyppi</td>
                  <td><form><button type=\"submit\" class=\"btn btn-success btn-sm\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"toimitusosoite\" value=\"$osoiteID\">Muokkaa</button></form></td>
                  <td><form><button type=\"submit\" class=\"btn btn-danger btn-sm\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"poista\" value=\"$osoiteID\">Poista</button></form></td></tr>";
                }
                echo "<tr><td colspan=\"6\"><form><button type=\"submit\" class=\"btn btn-primary btn-sm\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"toimitusosoite\" value=\"lisaa\">Uusi toimitusosoite</button></form></td></tr></tbody></table>";
                //echo '<pre>', print_r($tulos, true) ,'</pre>';
              }
            }

            require_once("db.inc");
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
              //tulostaSuccess("Onnistui!", "Tietokantakysely onnistui");
              if (mysqli_num_rows($tulos) == 0) {
                echo "<div class=\"alert alert-warning\" role=\"alert\">Ei löytynyt yhtään laskutusosoitetta.<br />Lisää vähintään yksi laskutusosoite.</div>";
                echo "<form><button type=\"submit\" class=\"btn btn-primary\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"laskutusosoite\" value=\"lisaa\">Uusi laskutusosoite</button></form><br />";
                $_SESSION["onLaskutusosoite"] = false;
              }
              else {
                $_SESSION["onLaskutusosoite"] = true;
                // Osoitteita löytyi, listataan ne tähän.
                echo "<strong>Laskutusosoitteet ovat:</strong><table class=\"table\"><thead><tr><th scope=\"col\">Nimi</th><th scope=\"col\">Lähiosoite</th><th scope=\"col\">Postinumero</th><th scope=\"col\">Postitoimipaikka</th><th scope=\"col\"></th><th scope=\"col\"></th></tr></thead><tbody>";
                while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
                  //haetaan tiedot muuttujiin
                  $osoiteID = $rivi["osoiteID"];
                  $laskutusnimi = $rivi["laskutusnimi"];
                  $lahiosoite = $rivi["lahiosoite"];
                  $postinumero = $rivi["postinumero"];
                  $postitoimipaikka = $rivi["postitoimipaikka"];
                  //tulostetaan taulukon rivi
                  echo "<tr><td>$laskutusnimi</td><td>$lahiosoite</td><td>$postinumero</td><td>$postitoimipaikka</td>
                  <td><form><button type=\"submit\" class=\"btn btn-success btn-sm\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"laskutusosoite\" value=\"$osoiteID\">Muokkaa</button></form></td>
                  <td><form><button type=\"submit\" class=\"btn btn-danger btn-sm\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"poista\" value=\"$osoiteID\">Poista</button></form></td></tr>";
                }
                echo "<tr><td colspan=\"6\"><form><button type=\"submit\" class=\"btn btn-primary btn-sm\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"laskutusosoite\" value=\"lisaa\">Uusi laskutusosoite</button></form></td></tr></tbody></table>";
                //echo '<pre>', print_r($tulos, true) ,'</pre>';
              }
            }

             ?>
          </div> <!-- Osoite-välilehti loppuu tähän. -->
          <div class="tab-pane fade <?php echo ($uusisalasana) ? 'show active' : '' ?>" id="nav-salasana" role="tabpanel" aria-labelledby="nav-salasana-tab"><h3>Salasana</h3>
            <form>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationDefaultUsername">Käyttäjätunnus</label>
                  <input type="text" readonly class="form-control" id="validationDefaultUsername" placeholder="Käyttäjätunnus" name="tunnus" value="<?php echo "$tunnus"; ?>" required>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationDefault01">Vanha salasana</label>
                  <input type="password" class="form-control" id="validationDefault01" placeholder="Vanha salasana" name="vanhasalasana" required>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationDefault02">Uusi salasana</label>
                  <input type="password" class="form-control" id="validationDefault02" placeholder="Uusi salasana" name="uusisalasana" required>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationDefault03">Uusi salasana uudelleen</label>
                  <input type="password" class="form-control" id="validationDefault03" placeholder="Uusi salasana uudelleen" name="salasanauudelleen" required>
                </div>
              </div>
              <button class="btn btn-primary" type="submit" formaction="kayttajatiedot.php" formmethod="post" name="muokkaa" value="salasana">Muuta salasana</button>
            </form>
            <br />
          </div> <!-- Salasana-välilehti loppuu tähän. -->
        </div>
      <?php endif; ?> <!-- Välilehdet loppuvat tähän -->
      <form>
      <button class="btn btn-outline-primary" type="submit" formaction="asiakas.php" formmethod="post">Peruuta</button>
      </form>
      </div>
      <?php
      echo "Post-sisältö: ";
      print_r($_POST);
      echo "<br>Session sisältö: ";
      print_r($_SESSION); ?>

    </main>
<?php
function tarkistamuutos($etunimi, $sukunimi, $puhelin, $email, &$errorText) {
  // Suoritetaan virhetarkistus lomakkeen tiedoille. Osa on tosin eliminoitu jo lomakkeen lähetyksessä, joten
  // tämä on osin vain varmistus.
  $retcode = true;
  $errorText = "";

  if ( $etunimi == "" )
  {
    $errorText .= " Etunimi puuttuu, se on pakollinen tieto.<br>";
    $retcode = false;
  }

  if ( $sukunimi == "" )
  {
    $errorText .= " Sukunimi puuttuu, se on pakollinen tieto.<br>";
    $retcode = false;
  }

  if ( $puhelin == "" )
  {
    $errorText .= " Puhelinnumero puuttuu, se on pakollinen tieto.<br>";
    $retcode = false;
  }

  if ( $email == "" )
  {
    $errorText .= " Sähköpostiosoite puuttuu, se on pakollinen tieto.<br>";
    $retcode = false;
  }
return $retcode;
}

function tarkistarekisterointi($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana, $salasana2, &$errorText) {
  // hyödynnetään tietojen tarkistuksessa olemassa olevaa funktiota
  $retcode = tarkistamuutos($etunimi, $sukunimi, $puhelin, $email, $errorText);
  if ( $tunnus == "" )
  {
    $errorText .= " Käyttäjätunnus puuttuu, se on pakollinen tieto.<br>";
    $retcode = false;
  }
  else {
    $loytyi = tarkistatunnus($tunnus);
    if ($loytyi) {
      // Sama käyttäjätunnus löytyi jo kannasta.
      $errorText .= "Hakemasi käyttäjätunnus on jo käytössä. Kokeile jotakin toista tunnusvaihtoehtoa.<br>";
      $retcode = false;
    }
  }
  $retcode = tarkistasalasana($salasana, $salasana2, $retcode, $errorText);
return $retcode;
}

function tarkistasalasana($salasana, $salasana2, $retcode, &$errorText) {
  if ( strlen($salasana) < 6 )
  {
    $errorText .= " Salasana on liian lyhyt, käytä vähintään kuutta merkkiä.<br>";
    $retcode = false;
  }

  if ( $salasana != $salasana2 )
  {
    $errorText .= " Salasanat eivät tästää, tarkista tiedot.<br>";
    $retcode = false;
  }
  return $retcode;
}

function tarkistasalasananmuutos($salasana, $vanhasalasana, $uusisalasana, $salasanauudelleen, &$errorText) {
  $retcode = true;
  $errorText = "";
  if ($vanhasalasana != $salasana) {
    $errorText = "Vanha salasana ei täsmää tietokannassa olevaan salasanaan.<br>";
    $retcode = false;
  }
  $retcode = tarkistasalasana($uusisalasana, $salasanauudelleen, $retcode, $errorText);
  return $retcode;
}

function tarkistatunnus($tarkistettavatunnus) {
  $samaloytyi = false;
  // Otetaan tietokanta käyttöön
  require_once("db.inc");
  // suoritetaan tietokantakysely ja kokeillaan hakea samaa tunnusta
  $query = "Select tunnus from asiakas WHERE tunnus='$tarkistettavatunnus'";
  $tulos = mysqli_query($conn, $query);
  // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
  if ( !$tulos )
  {
    tulostaVirhe("tietokantakysely epäonnistui!" . mysqli_error($conn));
  }
  else
  {
    if (mysqli_num_rows($tulos) != 0) {
      $samaloytyi = true;
    }
  }
  mysqli_close($conn);
  return $samaloytyi;
}

function tulostaVirhe($errorText) {
  ?>
  <div class="alert alert-danger" role="alert">
    <h4 class="alert-heading">Virhe!</h4>
    <p><?php echo "$errorText" ?></p>
  </div>
  <?php
}

function tulostaSuccess($successOtsikko, $successText) {
  ?>
  <div class="alert alert-success" role="alert">
    <h4 class="alert-heading"><?php echo $successOtsikko ?></h4>
    <p><?php echo "$successText" ?></p>
  </div>
  <?php
}

function tallennamuutokset($tunnus, $etunimi, $sukunimi, $puhelin, $email)  {
  require_once("db.inc");
  $conn2 = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
  $conn2->set_charset("utf8");
  if ( mysqli_connect_errno() )
  {
    // Lopettaa tämän skriptin suorituksen ja tulostaa parametrina tulleen tekstin
    die ("Tietokantapalvelinta ei löydy, syy: " . mysqli_connect_error());
  }
  // suoritetaan tietokantakysely ja kokeillaan hakea salasana
  $query = "UPDATE Asiakas SET etunimi='$etunimi', sukunimi='$sukunimi', puhelin='$puhelin', email='$email'  WHERE tunnus='$tunnus'";
  $tulos = mysqli_query($conn2, $query);
  // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
  if ( !$tulos )
  {
    tulostaVirhe("Tietojen päivitys epäonnistui " . mysqli_error($conn));
    return false;
  }
  else {
    tulostaSuccess("Onnistui!", "Muutokset on onnistuneesti tallennettu");
    return true;
  }
}

function vaihdasalasana($tunnus, $uusisalasana) {
  require_once("db.inc");
  // suoritetaan tietokantakysely ja kokeillaan päivittää salasana
  $query = "UPDATE Asiakas SET salasana='$uusisalasana' WHERE tunnus='$tunnus'";
  $tulos = mysqli_query($conn, $query);
  // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
  if ( !$tulos )
  {
    tulostaVirhe("Salasanan päivitys epäonnistui " . mysqli_error($conn));
    return false;
  }
  else {
    tulostaSuccess("Onnistui!", "Salasana on onnistuneesti vaihdettu.");
    $_SESSION["salasana"] = $uusisalasana;
    return true;
  }
}

function paivitasessio($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana) {
  $_SESSION["kirjautunut"] = $tunnus;
  $_SESSION["etunimi"] = $etunimi;
  $_SESSION["sukunimi"] = $sukunimi;
  $_SESSION["puhelin"] = $puhelin;
  $_SESSION["email"] = $email;
  $_SESSION["salasana"] = $salasana;
}

function rekisteroiAsiakas($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana) {
  //require_once("db.inc");

  // Create connection
  $conn2 = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
  $conn2->set_charset("utf8");
  // Check connection
  if (!$conn2) {
      die("Connection failed: " . mysqli_connect_error());
  }
  // suoritetaan tietokantakysely ja kokeillaan tallentaa uusi asiakas
  $query = "INSERT INTO asiakas (tunnus, etunimi, sukunimi, puhelin, email, salasana) VALUES
    ('$tunnus', '$etunimi', '$sukunimi', '$puhelin', '$email', '$salasana')";
  if (mysqli_query($conn2, $query)) {
    tulostaSuccess("Onnistui!", "Olet nyt onnistuneesti rekisteröitynyt.<br>Jotta voit tehdä työtilauksia tai tarjouspyyntojä, on sinun lisättävä osoitetiedot.");
    paivitasessio($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana);
    $_SESSION["muokkaustila"] = true;
    mysqli_close($conn2);
  } else {
    tulostaVirhe("Rekisteröinti ei onnistunut!<br>" . mysqli_error($conn2));
    mysqli_close($conn2);
  }
}

  require 'footer.php';
?>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
  </body>
</html>
