<?php
  // Sessio-funktion kutsu
  session_start();
  // Ohjataan käyttäjä kirjautumissivulle, jos sivua yritetään käyttää kirjautumatta
  if (!isset($_SESSION['kirjautunut'])) {
    header("Location:asiakas_login.php");
    exit();
  }
  // Ladataan sessiosta kirjautunueen asiakkaan tiedot muuttujiin, jotta ne voidaan asettaa lomakkeelle.
  if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != "") {
    $tunnus = $_SESSION["kirjautunut"];
    $salasana = $_SESSION["salasana"];
    $etunimi = $_SESSION["etunimi"];
    $sukunimi = $_SESSION["sukunimi"];
    $kokonimi = $etunimi . " " . $sukunimi;
    $puhelin = $_SESSION["puhelin"];
    $email = $_SESSION["email"];
    $muokkaustila = $_SESSION["muokkaustila"];
    // Alustetaan osoitemuuttujat
    $osoiteID = "";
    $laskutusnimi = "";
    $lahiosoite = "";
    $postinumero ="";
    $postitoimipaikka = "";
    $asunnonTyyppi = "";
    $asunnonAla = "";
    $tontinAla = "";
    $toimitusosoite = false;
    $laskutusosoite = false;
    $uusi = false;
    $poistetaan = false;
  }
  if (isset($_POST["toimitusosoite"]) && $_POST["toimitusosoite"] != "" || isset($_POST["laskutusosoite"]) && $_POST["laskutusosoite"] != "") {
    // Jos tullaan muokkaamaan kumpaa tahansa osoitetta, haetaan lomakkeen tiedot valmiiksi
    echo "Muokataan kumpaa vain. Ladataan tietokannasta tiedot.<br>";
    if (isset($_POST["toimitusosoite"])) $osoiteID = $_POST["toimitusosoite"];
    if (isset($_POST["laskutusosoite"])) $osoiteID = $_POST["laskutusosoite"];
    require_once("db.inc");
    // suoritetaan tietokantakysely ja kokeillaan hakea osoitetiedot
    $query = "Select * from Osoite WHERE osoiteID='$osoiteID'";
    $tulos = mysqli_query($conn, $query);
    // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
    if ( !$tulos )
    {
      echo "Kysely epäonnistui " . mysqli_error($conn);
    }
    else {
      //käydään läpi löytyneet rivit ja tallennetaan tiedot muuttujiin
      while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
        // Haetaan
        $laskutusnimi = $rivi["laskutusnimi"];
        $lahiosoite = $rivi["lahiosoite"];
        $postinumero = $rivi["postinumero"];
        $postitoimipaikka = $rivi["postitoimipaikka"];
        $asunnonTyyppiID = $rivi["asunnonTyyppiID"];
        $asunnonAla = $rivi["asunnonAla"];
        $tontinAla = $rivi["tontinAla"];
        echo "AsunnontyyppiID alussa: $asunnonTyyppiID<br>";
      }
    }
  }
  if (isset($_POST["testi"]) && $_POST["testi"] == "ok") {
    echo "Jee, nyt se pelaa!";
  }
  if (isset($_POST["poista"]) && $_POST["poista"] != "") {
    // Valitun osoitteen poistaminen
    echo "Poistetaan valittu osoite. Voi olla toimitus- tai laskutusosoite.<br>";
    $otsikko = "Poistetaan valittu osoite";
    $poistetaan = true;
  }
  else if (isset($_POST["toimitusosoite"]) && $_POST["toimitusosoite"] == "lisaa") {
    // Uuden toimitusosoitteen lisäys
    echo "Lisätään uusi toimitusosoite<br>";
    $toimitusosoite = true;
    $uusi = true;
    //$kokonimi = $_SESSION["etunimi"] . " " . $_SESSION["sukunimi"];
    echo "$kokonimi";
    $otsikko = "Lisätään uusi toimitusosoite";
    $postname = "muokkaatoimitusosoite";
    $postvalue = "lisaa";
    $buttonTallenna = "Lisää uusi toimitusosoite";
    //if (isset($_POST["laskutusnimi"])) $laskutusnimi = $_POST["laskutusnimi"];
    if (isset($_POST["lahiosoite"])) $lahiosoite = $_POST["lahiosoite"];
    if (isset($_POST["postinumero"])) $postinumero = $_POST["postinumero"];
    if (isset($_POST["postitoimipaikka"])) $postitoimipaikka = $_POST["postitoimipaikka"];
    if (isset($_POST["asunnonTyyppiID"])) $asunnonTyyppiID = $_POST["asunnonTyyppiID"];
    if (isset($_POST["asunnonAla"])) $asunnonAla = $_POST["asunnonAla"];
    if (isset($_POST["tontinAla"])) $tontinAla = $_POST["tontinAla"];
  }
  else if (isset($_POST["toimitusosoite"]) && $_POST["toimitusosoite"] != "") {
    // Toimitusosoitteen muokkaus, kun tälle sivulle tullaan ensimmäisen kerrran.
    echo "Muokataan toimitusosoitetta.<br>";
    $toimitusosoite = true;
    echo "OsoiteID = $osoiteID";
    $otsikko = "Muokataan toimitusosoitetta";
    $postname = "muokkaatoimitusosoite";
    $postvalue = $osoiteID;
    $buttonTallenna = "Tallenna muutokset";
  }
  else if (isset($_POST["muokkaatoimitusosoite"]) && $_POST["muokkaatoimitusosoite"] != "") {
    // Tämä suoritetaan aina tallenna-napin painamisen jälkeen.
    $testi = $_POST["muokkaatoimitusosoite"];
    if ($testi == "lisaa") {
      $otsikko = "Lisätään uusi toimitusosoite";
      $postname = "muokkaatoimitusosoite";
      $postvalue = "lisaa";
      $buttonTallenna = "Tallenna muutokset";
    }
    else {
      $osoiteID = $_POST["muokkaatoimitusosoite"];
      $postvalue = $osoiteID;
      $otsikko = "Muokataan toimitusosoitetta";
      $postname = "muokkaatoimitusosoite";
      $buttonTallenna = "Tallenna muutokset";
      echo "OsoiteID = $osoiteID";
    }
    echo "Painettiin tallenna-nappia!<br>";
    if (isset($_POST["laskutusnimi"])) $laskutusnimi = $_POST["laskutusnimi"];
    if (isset($_POST["lahiosoite"])) $lahiosoite = $_POST["lahiosoite"];
    if (isset($_POST["postinumero"])) $postinumero = $_POST["postinumero"];
    if (isset($_POST["postitoimipaikka"])) $postitoimipaikka = $_POST["postitoimipaikka"];
    if (isset($_POST["asunnonTyyppiID"])) $asunnonTyyppiID = $_POST["asunnonTyyppiID"];
    if (isset($_POST["asunnonAla"])) $asunnonAla = $_POST["asunnonAla"];
    if (isset($_POST["tontinAla"])) $tontinAla = $_POST["tontinAla"];
    $toimitusosoite = true;
    // Tietojen tarkistus ennen tallennusta
  }
  elseif (isset($_POST["laskutusosoite"]) && $_POST["laskutusosoite"] == "lisaa") {
    // Lisätään uutta laskutusosoitetta.
    echo "Lisätään laskutusosoitetta.<br>";
    $uusi = true;
    $laskutusosoite = true;
    $otsikko = "Lisätään uusi laskutusosoite";
    $kokonimi = "";
    $postname = "muokkaalaskutusosoite";
    $postvalue = "lisaa";
    $buttonTallenna = "Lisää uusi laskutusosoite";
    if (isset($_POST["laskutusnimi"])) $kokonimi = $_POST["laskutusnimi"];
    if (isset($_POST["lahiosoite"])) $lahiosoite = $_POST["lahiosoite"];
    if (isset($_POST["postinumero"])) $postinumero = $_POST["postinumero"];
    if (isset($_POST["postitoimipaikka"])) $postitoimipaikka = $_POST["postitoimipaikka"];
  }
  elseif (isset($_POST["laskutusosoite"]) && $_POST["laskutusosoite"] != "") {
    // Muokataan laskutusosoitetta
    echo "Muokataan laskutusosoitetta<br>";
    $laskutusosoite = true;
    $kokonimi = $laskutusnimi;
    echo "OsoiteID = $osoiteID";
    $otsikko = "Muokataan laskutusosoitetta";
    $postname = "muokkaalaskutusosoite";
    $postvalue = $osoiteID;
    $buttonTallenna = "Tallenna muutokset";
  }
  else if (isset($_POST["muokkaalaskutusosoite"]) && $_POST["muokkaalaskutusosoite"] != "") {
  // Ollaan painettu tallenna-nappia
  $testi = $_POST["muokkaalaskutusosoite"];
  if ($testi == "lisaa") {
    $otsikko = "Lisätään uusi laskutusosoite";
    $postvalue = "lisaa";
    $buttonTallenna = "Tallenna muutokset";
  }
  else {
    $otsikko = "Muokataan laskutusosoitetta";
    $osoiteID = $_POST["muokkaalaskutusosoite"];
    echo "OsoiteID = $osoiteID";
    $postvalue = $osoiteID;
    $buttonTallenna = "Tallenna muutokset";
  }
  if (isset($_POST["laskutusnimi"])) $kokonimi = $_POST["laskutusnimi"];
  if (isset($_POST["lahiosoite"])) $lahiosoite = $_POST["lahiosoite"];
  if (isset($_POST["postinumero"])) $postinumero = $_POST["postinumero"];
  if (isset($_POST["postitoimipaikka"])) $postitoimipaikka = $_POST["postitoimipaikka"];
  $laskutusosoite = true;
  $postname = "muokkaalaskutusosoite";
  }
  else if (isset($_POST["tallenna"]) && $_POST["tallenna"] != "") {

  }
  else {
    // Jos tälle sivulle tullaan suoraan, ohjataan käyttäjä profiilisivulle
    header("Location:kayttajatiedot.php");
    exit();
  }
?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Osoitteet - Kotitalkkari - Asiakassovellus">
    <meta name="author" content="Ilkka Rytkönen">
    <title>Osoitteet - Kotitalkkari</title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
  </head>
  <body>
    <?php
    // Ladataan päävalikko ulkopuolisesta tiedostosta
    require 'asiakasmenu.php';
    ?>
    <main role="main" class="container">
      <div class="starter-template">
        <h1>Kotitalkkarin asiakassovellus</h1>
          <h2><?php echo $otsikko ?></h2>

          <?php

          // Lomakkeen tietojentarkistus post-datasta
          if (isset($_POST["muokkaatoimitusosoite"]) && $_POST["muokkaatoimitusosoite"] != "") {
            // Tarkistetaan ja päivitetään muuttuneet tiedot tietokantaan
            // Laskutusnumeä, eli kokoniemä ei tarvitsisi tässä vaiheessa tarkistaa, mutta samaa funktiota käytetään laskutusosoitteen tarkistukseen.
            $tulos = tarkistaToimitusosoite($kokonimi, $lahiosoite, $postinumero, $postitoimipaikka, $asunnonAla, "$tontinAla", $errorText);
            if ( $tulos == true ) {
              $onnistuiko = tallennaToimitusosoite($tunnus, $osoiteID, $lahiosoite, $postinumero, $postitoimipaikka, $asunnonTyyppiID, $asunnonAla, $tontinAla);
              if ($onnistuiko) {
                //((paivitasessio($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana);
              }
            }
            else
            {
              tulostaVirhe($errorText);
            }
          }

          if (isset($_POST["muokkaalaskutusosoite"]) && $_POST["muokkaalaskutusosoite"] != "") {
            // Tarkistetaan ja päivitetään muuttuneet tiedot tietokantaan
            // Laskutusnumeä ei tarvitsisi tässä vaiheessa tarkistaa, mutta samaa funktiota käytetään laskutusosoitteen tarkistukseen.
            $tulos = tarkistaLaskutusosoite($kokonimi, $lahiosoite, $postinumero, $postitoimipaikka, $errorText);
            if ( $tulos == true ) {
              $onnistuiko = tallennaLaskutusosoite($tunnus, $osoiteID, $kokonimi, $lahiosoite, $postinumero, $postitoimipaikka);
              if ($onnistuiko) {
                //((paivitasessio($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana);
              }
            }
            else
            {
              tulostaVirhe($errorText);
            }
          }

          if (!$poistetaan) {
            // Sivun sisältö lisäystä ja muokkausta varten
            // Otetaan tietokanta käyttöön
            // Haetaan asuntotyypit lomakkeen vaihtoehtoja varten
            //require_once("asetukset.inc");
            // suoritetaan tietokantakysely ja kokeillaan hakea asuntotyypit
            // Otetaan yhteys tietokantapalvelimelle
            $conn2 = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
            $conn2->set_charset("utf8");

            if ( mysqli_connect_errno() )
            {
              // Lopettaa tämän skriptin suorituksen ja tulostaa parametrina tulleen tekstin
              die ("Tietokantapalvelinta ei löydy, syy: " . mysqli_connect_error());
            }

            $query = "Select * from AsunnonTyyppi";
            $tulos = mysqli_query($conn2, $query);
            // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
            if ( !$tulos )
            {
              echo "Kysely epäonnistui " . mysqli_error($conn2);
            }
            else {
              //käydään läpi löytyneet rivit ja tallennetaan tiedot taulukkoon
              $asuntotyypit = array();
              while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
                // Haetaan
                $asunnonTyyppiIDTemp = $rivi["asunnonTyyppiID"];
                $asunnonTyyppiTemp = $rivi["asunnonTyyppi"];
                $asuntotyypit[$asunnonTyyppiIDTemp] = $asunnonTyyppiTemp;
              }
            }
            if ($uusi) echo "<br>Uusi<br>";
            ?>
            <form>
              <div class="form-row">
                <div class="col-md-3 mb-3">
                  <label for="validationDefault01"><?php echo ($laskutusosoite) ? 'Laskutusnimi' : 'Asiakas'  ?></label>
                  <input type="text" maxlength="50" class="form-control" id="validationDefault01" placeholder="Etunimi Sukunimi" name="laskutusnimi" value="<?php echo $kokonimi; ?>" <?php echo ($toimitusosoite) ? 'readonly' : 'required' ?> >
                </div>
                <div class="col-md-3 mb-3">
                  <label for="validationDefault02">Lähiosoite</label>
                  <input type="text" maxlength="50" class="form-control" id="validationDefault02" placeholder="Lähiosoite" name="lahiosoite" value="<?php echo "$lahiosoite"; ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="validationDefault03">Postinumero</label>
                  <input type="number" size="5" min="0" max="99999" maxlength="5" class="form-control" id="validationDefault03" placeholder="Postinumero" name="postinumero" value="<?php echo "$postinumero"; ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="validationDefault07">Postitoimipaikka</label>
                  <input type="text" maxlength="40" class="form-control" id="validationDefault07" placeholder="Postitoimipaikka" name="postitoimipaikka" value="<?php echo "$postitoimipaikka"; ?>" required>
                </div>
              </div>
              <?php if ($toimitusosoite): ?>
              <div class="form-row">
                <div class="col-md-5 mb-3">
                  <label for="validationDefault04">Asunnon tyyppi</label>
                  <select class="form-control" id="validationDefault04" name="asunnonTyyppiID">
                    <?php
                    foreach ($asuntotyypit as $tyyppiID => $tyyppi) {
                      $selected = "";
                      if ($tyyppiID == $asunnonTyyppiID) $selected = "selected";
                      echo "<option value=\"$tyyppiID\" $selected>$tyyppi</option>";
                    } ?>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="validationDefault05">Asunnon pinta-ala</label>
                  <input type="number" min="0" max="9999" class="form-control" id="validationDefault05" placeholder="Asunnon pinta-ala" name="asunnonAla" value="<?php echo "$asunnonAla"; ?>">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="validationDefault06">Tontin pinta-ala</label>
                  <input type="number" min="0" max="99999" class="form-control" id="validationDefault06" placeholder="Tontin pinta-ala" name="tontinAla" value="<?php echo "$tontinAla"; ?>">
                </div>
              </div>
              <?php endif; ?>
              <input type="hidden" name="testi" value="ok">
              <button class="btn btn-primary" type="submit" formaction="osoitteet.php" formmethod="post" name="<?php echo $postname ?>" value="<?php echo $postvalue ?>"><?php echo $buttonTallenna ?></button>
            </form>
            <?php
          }
          else {
            // Tähän osoitteen poistaminen
            echo "Osoitteenpoistokoodia ajetaan!<br>";
            if (isset($_POST["poista"]) && $_POST["poista"] != "") $poistettavaID = $_POST["poista"];
            $lahios = "";
            $postinro = "";
            $postit = "";

            // Otetaan tietokanta käyttöön
            require_once("db.inc");
            // suoritetaan tietokantakysely ja kokeillaan hakea poistettavaa osoitetta
            $query = "Select * from Osoite WHERE osoiteID='$poistettavaID'";
            echo "$query";
            $tulos = mysqli_query($conn, $query);
            // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
            if ( !$tulos ) {
              echo "Kysely epäonnistui " . mysqli_error($conn);
            }
            else {
              //käydään läpi löytyneet rivit
              while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
                $lahios = $rivi["lahiosoite"];
                $postinro = $rivi["postinumero"];
                $postit = $rivi["postitoimipaikka"];
              }
            }
            $poistettava = $lahios . ", " . $postinro . " " . $postit;
            if (isset($_POST["poistetaan"]) && $_POST["poistetaan"] == "ok") {
              echo "Nysse häviää!<br>";
              require_once("db.inc");
              // suoritetaan tietokantakysely ja kokeillaan poistaa osoitetta
              $query = "DELETE FROM Osoite WHERE osoiteID='$poistettavaID'";
              echo "$query";
              $tulos = mysqli_query($conn, $query);
              // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
              if ( !$tulos ) {
                echo "Kysely epäonnistui " . mysqli_error($conn);
              }
              else {
                tulostaSuccess("Poisto onnistui!", "Osoite <strong>$poistettava</strong> on onnistuneesti poistettu.");
                echo "<form><button class=\"btn btn-primary\" type=\"submit\" formaction=\"kayttajatiedot.php\" formmethod=\"post\" name=\"osoitteet\">Palaa takaisin osoitteisiin</button></form>";
              }

            }
            else {
              ?>
              <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Poistetaanko osoite?</h4>
                <p>Olet poistamassa osoitetta:<strong> <?php echo "$poistettava" ?></strong></p>
                <hr>
                <p class="mb-0">Tätä toimenpidettä ei voi perua.</p>
              </div>
              <form>
                <input type="hidden" name="poistetaan" value="ok">
                <input type="hidden" name="poistettava" value="<?php echo $poistettava ?>">
              <button class="btn btn-primary" type="submit" formaction="osoitteet.php" formmethod="post" name="poista" value="<?php echo $poistettavaID ?>">Poista osoite</button><br />
              </form>
            <?php
            }
          }
          if (!isset($_POST["poistetaan"])) {
            ?>
            <form>
            <button class="btn btn-outline-primary" type="submit" formaction="kayttajatiedot.php" formmethod="post" name="osoitteet">Peruuta</button>
            </form>
            <?php
          }
      echo "Post-sisältö: ";
      print_r($_POST);
      echo "<br>Session sisältö: ";
      print_r($_SESSION); ?>
    </div>

    </main>

    <?php
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

    function tarkistaToimitusosoite($laskutusnimi, $lahiosoite, $postinumero, $postitoimipaikka, $asunnonAla, $tontinAla, &$errorText) {
      // Hyödynnetään olemassa olevaa funktiota osaan tarkistuksista
      $retcode = tarkistaLaskutusosoite($laskutusnimi, $lahiosoite, $postinumero, $postitoimipaikka, $errorText);
      if ( !is_numeric($asunnonAla) && $asunnonAla != "")
      {
        $errorText .= " Asunnon ala on ilmoitettu väärin. Pelkät numerot kelpaavat.<br>";
        $retcode = false;
      }

      if ( !is_numeric($tontinAla) && $tontinAla != "")
      {
        $errorText .= " Tontin ala on ilmoitettu väärin. Pelkät numerot kelpaavat.<br>";
        $retcode = false;
      }
    return $retcode;
    }

    function tarkistaLaskutusosoite($laskutusnimi, $lahiosoite, $postinumero, $postitoimipaikka, &$errorText) {
      // Suoritetaan virhetarkistus lomakkeen tiedoille. Osa on tosin eliminoitu jo lomakkeen lähetyksessä, joten
      // tämä on osin vain varmistus.
      $retcode = true;
      $errorText = "";

      if ( $laskutusnimi == "" )
      {
        $errorText .= " Laskutusnimi puuttuu, se on pakollinen tieto.<br>";
        $retcode = false;
      }

      if ( $lahiosoite == "" )
      {
        $errorText .= " Lähiosoite puuttuu, se on pakollinen tieto.<br>";
        $retcode = false;
      }

      if ( $postinumero == "" )
      {
        $errorText .= " Postinumero puuttuu, se on pakollinen tieto.<br>";
        $retcode = false;
      }

      if ( $postitoimipaikka == "" )
      {
        $errorText .= " Postitoimipaikka puuttuu, se on pakollinen tieto.<br>";
        $retcode = false;
      }
    return $retcode;
    }

    function tallennaToimitusosoite($tunnus, $osoiteID, $lahiosoite, $postinumero, $postitoimipaikka, $asunnonTyyppiID, $asunnonAla, $tontinAla) {
      echo "Tässä on toimitusosoitteen tallennus tietokantaan.<br>";
      echo "OsoiteID = $osoiteID<br>";
      echo "Asunnon ala: $asunnonAla";
      // Jos asunnon tai tontin pinta-alaa ei ole syötetty, asetetaan nämä arvot nolliksi.
      // Oikeasti pitäisi tallentaa NULL arvo tietokantaan, mutta oiotaan tässä mutkaa vähän.
      if ($asunnonAla == "") $asunnonAla = "0";
      if ($tontinAla == "") $tontinAla = "0";
      if ($osoiteID == "") {
        echo "Lisätään kokonaan uusi tieto.<br>";
        require_once("db.inc");
        // suoritetaan tietokantakysely ja kokeillaan tallentaa uutta osoitetta
        $query = "INSERT INTO Osoite (tunnus, lahiosoite, postinumero, postitoimipaikka, asunnonTyyppiID, asunnonAla, tontinAla) VALUES
          ('$tunnus', '$lahiosoite', '$postinumero', '$postitoimipaikka', '$asunnonTyyppiID', '$asunnonAla', '$tontinAla')";
        echo "Kysely on: $query";
        $tulos = mysqli_query($conn, $query);
        // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
        if ( !$tulos )
        {
          tulostaVirhe("Uuden toimitusosoitteen tallennus epäonnistui!" . mysqli_error($conn));
        }
        else {
          tulostaSuccess("Onnistui!", "Uusi toimitusosoite lisätty onnistui");
        }
      }
      else {
        echo "Vanhan tiedon päivitys.<br>";
        require_once("db.inc");
        // suoritetaan tietokantakysely ja kokeillaan päivittää toimitusosoitetta
        $query = "UPDATE Osoite SET lahiosoite = '$lahiosoite', postinumero = '$postinumero', postitoimipaikka = '$postitoimipaikka', asunnonTyyppiID = '$asunnonTyyppiID', asunnonAla = '$asunnonAla', tontinAla = '$tontinAla' WHERE OsoiteID = $osoiteID";
        echo "Kysely on: $query";
        $tulos = mysqli_query($conn, $query);
        // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
        if ( !$tulos )
        {
          tulostaVirhe("Toimitusosoiteen päivitys epäonnistui!" . mysqli_error($conn));
        }
        else {
          tulostaSuccess("Onnistui!", "Toimitusosoite on päivitetty onnistuneesti.");
        }
      }
    }

    function tallennaLaskutusosoite($tunnus, $osoiteID, $laskutusnimi, $lahiosoite, $postinumero, $postitoimipaikka) {
      echo "Tässä on laskutusosoitteen tallennus tietokantaan.<br>";
      echo "OsoiteID = $osoiteID<br>";
      if ($osoiteID == "") {
        echo "Lisätään kokonaan uusi tieto.<br>";
        require_once("db.inc");
        // suoritetaan tietokantakysely ja kokeillaan tallentaa uutta laskutusosoitetta
        $query = "INSERT INTO Osoite (tunnus, laskutusnimi, lahiosoite, postinumero, postitoimipaikka) VALUES
          ('$tunnus', '$laskutusnimi', '$lahiosoite', '$postinumero', '$postitoimipaikka')";
        echo "Kysely on: $query";
        $tulos = mysqli_query($conn, $query);
        // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
        if ( !$tulos )
        {
          tulostaVirhe("Uuden laskutusoitteen tallennus epäonnistui!" . mysqli_error($conn));
        }
        else {
          tulostaSuccess("Onnistui!", "Uusi laskutusosoite lisätty onnistui");
        }
      }
      else {
        echo "Vanhan tiedon päivitys.<br>";
        require_once("db.inc");
        // suoritetaan tietokantakysely ja kokeillaan päivittää laskutusosoitetta
        $query = "UPDATE Osoite SET laskutusnimi = '$laskutusnimi', lahiosoite = '$lahiosoite', postinumero = '$postinumero', postitoimipaikka = '$postitoimipaikka' WHERE OsoiteID = $osoiteID";
        echo "Kysely on: $query";
        $tulos = mysqli_query($conn, $query);
        // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
        if ( !$tulos )
        {
          tulostaVirhe("Toimitusosoiteen päivitys epäonnistui!" . mysqli_error($conn));
        }
        else {
          tulostaSuccess("Onnistui!", "Toimitusosoite on päivitetty onnistuneesti.");
        }
      }
    }

     ?>

    <!-- Ladataan footer ulkopuolisesta tiedostosta -->
    <?php require 'footer.php'; ?>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
  </body>
</html>
