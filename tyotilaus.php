<?php
  // Sessio-funktion kutsu
  session_start();
  // Ohjataan käyttäjä kirjautumissivulle, jos sivua yritetään käyttää kirjautumatta
  if (!isset($_SESSION['kirjautunut'])) {
    header("Location:asiakas_login.php");
    exit();
  }
  else {
    // Sivun perusjutut, kuten muuttujien alustukset
    $otsikko = "Jätä uusi työtilaus";
    $_SESSION["muokattavaTyotilausID"] = "";
    $uusitilaus = true;
    $tilauksenmuokkaus = true;
    $hylattyTilaus = false;
    // Alustetaan muuttujat. Näiden muuttujien sisältö näytetään lomakkeen kentissä ja painikkeissa.
    $tyotilausID = "";
    $toimitusosoiteID = "";
    $laskutusosoiteID = "";
    $tyonkuvaus = "";
    $tilausPvm = "";
    $aloitusPvm = "";
    $valmistumisPvm = "";
    $hyvaksyttyPvm = "";
    $hylattyPvm = "";
    $kommentti = "";
    $tyotunnit = "";
    $tarvikeselostus = "";
    $kustannusarvio = "";
    $formaction = "tyotilaus.php";
    $formname = "uusitilaus";
    $formvalue = "";
    $buttonNimi = "Jätä uusi tilaus";
    $buttonTyyppi = "btn-primary";
    $buttonPeruutaNimi = "Peruuta";
    $tallenusOnnistui = "";
    date_default_timezone_set("Europe/Helsinki");
  }
  if (isset($_POST["nayta"]) && $_POST["nayta"] != "") {
    $otsikko = "Työtilaus";
    $_SESSION["muokattavaTyotilausID"] = $_POST["nayta"];
    $tilauksenmuokkaus = false;
    $buttonPeruutaNimi = "Palaa takaisin";
  }
  else if (isset($_POST["poista"]) && $_POST["poista"] != "") {
    $otsikko = "Poista työtilaus";
    $_SESSION["muokattavaTyotilausID"] = $_POST["poista"];
    $tilauksenmuokkaus = false;
    $formaction = "asiakas.php";
    $formname = "poista";
    $formvalue = $_POST["poista"];
    $buttonNimi = "Vahvista tilauksen poistaminen";
    $buttonTyyppi = "btn-danger";
  }
  else if (isset($_POST["muokkaa"]) && $_POST["muokkaa"] != "") {
    $otsikko = "Muokkaa työtilausta";
    $_SESSION["muokattavaTyotilausID"] = $_POST["muokkaa"];
    $tilauksenmuokkaus = true;
    $formname = "muokkaa";
    $formvalue = $_POST["muokkaa"];
    $buttonNimi = "Tallenna muutokset";
}
  else if (isset($_POST["hyvaksy"]) && $_POST["hyvaksy"] != "") {
    $otsikko = "Hyväksy työtilaus";
    $_SESSION["muokattavaTyotilausID"] = $_POST["hyvaksy"];
    $tilauksenmuokkaus = false;
    $formaction = "asiakas.php";
    $formname = "hyvaksy";
    $formvalue = $_POST["hyvaksy"];
    $buttonNimi = "Hyväksy";
}
if (isset($_SESSION["muokattavaTyotilausID"]) && $_SESSION["muokattavaTyotilausID"] != "") {
  $tyotilaus = haeTyotilaus($_SESSION["muokattavaTyotilausID"]);
  $uusitilaus = false;
  $toimitusosoiteID = $tyotilaus["toimitusosoiteID"];
  $laskutusosoiteID = $tyotilaus["laskutusosoiteID"];
  $tyonkuvaus = $tyotilaus["tyonkuvaus"];
  if ($tyotilaus["tilausPvm"] != "") $tilausPvm = date("d.m.Y",strtotime($tyotilaus["tilausPvm"]));
  if ($tyotilaus["aloitusPvm"] != "") $aloitusPvm = date("d.m.Y",strtotime($tyotilaus["aloitusPvm"]));
  if ($tyotilaus["valmistumisPvm"] != "") $valmistumisPvm = date("d.m.Y",strtotime($tyotilaus["valmistumisPvm"]));
  if ($tyotilaus["hyvaksyttyPvm"] != "") $hyvaksyttyPvm = date("d.m.Y",strtotime($tyotilaus["hyvaksyttyPvm"]));
  if ($tyotilaus["hylattyPvm"] != "") $hylattyPvm = date("d.m.Y",strtotime($tyotilaus["hylattyPvm"]));
  $kommentti = $tyotilaus["kommentti"];
  $tyotunnit = $tyotilaus["tyotunnit"];
  $tarvikeselostus = $tyotilaus["tarvikeselostus"];
  $kustannusarvio = $tyotilaus["kustannusarvio"];
  if ($hylattyPvm != "") $hylattyTilaus = true;
}
  //if (isset())
?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Kotitalkkari - Asiakassovellus">
    <meta name="author" content="Ilkka Rytkönen">
    <title>Työtilaus - Kotitalkkari</title>
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
        <?php
          echo "<h2>$otsikko</h2>";

          // Katsotaan, onko asiakkaalla sekä laskutus-, että toimitusosoitteet ja näytetään sen mukaan sisältöä.
          require("onkoOsoitetta.inc");

          if (isset($_SESSION["onToimitusosoite"]) && $_SESSION["onToimitusosoite"] == true && isset($_SESSION["onLaskutusosoite"]) && $_SESSION["onLaskutusosoite"] == true) {
            // Sivun pääsisältö näytetään, jos asiakkaalla on molempia osoitetyyppejä.
            //Ladataan sekä toimitus-, että laskutusosoitteet, jotta ne voidaan esittää alasvetovalikoissa
            require("lataaOsoitteet.inc");

            // Ladataan lomakkelle syötetyt tiedot muuttujiin, jotta ne voidaan asettaa sinne uudelleen sivun päivityksen yhteydessä
            if (isset($_SESSION["muokattavaTyotilausID"]) && $_SESSION["muokattavaTyotilausID"] != "") $tyotilausID = $_SESSION["muokattavaTyotilausID"];
            if (isset($_POST["toimitusosoiteID"]) && $_POST["toimitusosoiteID"] != "") $toimitusosoiteID = $_POST["toimitusosoiteID"];
            if (isset($_POST["laskutusosoiteID"]) && $_POST["laskutusosoiteID"] != "") $laskutusosoiteID = $_POST["laskutusosoiteID"];
            if (isset($_POST["tyonkuvaus"]) && $_POST["tyonkuvaus"] != "") $tyonkuvaus = $_POST["tyonkuvaus"];

            // Lomakkeen tietojen tallennus tietokantaan
            if (isset($_POST["tallenna"]) && $_POST["tallenna"] == "ok") {
              $pituus = strlen($tyonkuvaus);
              if (strlen($tyonkuvaus) < 10) {
                tulostaVirhe("Työnkuvaus on liian lyhyt. Minimimerkkimäärä on 10. Syötit $pituus merkkiä.");
              }
              else if (strlen($tyonkuvaus) > 65536) {
                tulostaVirhe("Työnkuvaus on liian pitkä. Maksimimerkkimäärä on 65536. Syötit $pituus merkkiä.");
              }
              else {
                // Tarkistus ok, eli voidaan yrittää tallennusta tietokantaan.
                $tallenusOnnistui = tallennaTyotilaus($tyotilausID, $toimitusosoiteID, $laskutusosoiteID, $tyonkuvaus);
                if ($tallenusOnnistui) {
                  $buttonPeruutaNimi = "Palaa takaisin";
                  $tilauksenmuokkaus = false;
                }
              }
            }
            ?>
            <form>
              <div class="form-row">
                <div class="form-group col-md-6 mb-2">
                  <label for="toimitusosoiteselect">Valitse toimitusosoite</label>
                  <select class="form-control" id="toimitusosoiteselect" name="toimitusosoiteID" <?php echo ($tilauksenmuokkaus) ? '' : 'disabled' ?>>
                    <?php
                    foreach ($toimitusosoitteet as $toimitusID => $osoiterimpsu) {
                      $selected = "";
                      if ($toimitusosoiteID == $toimitusID) $selected = "selected";
                      echo "<option value=\"$toimitusID\" $selected>$osoiterimpsu</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group col-md-6 mb-2">
                  <label for="laskutusosoiteselect">Valitse laskutusosoite</label>
                  <select class="form-control" id="laskutusosoiteselect" name="laskutusosoiteID" <?php echo ($tilauksenmuokkaus) ? '' : 'disabled' ?>>
                    <?php
                    foreach ($laskutusosoitteet as $laskutusID => $osoiterimpsu) {
                      $selected = "";
                      if ($laskutusosoiteID == $laskutusID) $selected = "selected";
                      echo "<option value=\"$laskutusID\" $selected>$osoiterimpsu</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-1">
                  <label for="textareaTyonkuvaus">Työkuvaus</label>
                  <textarea class="form-control" id="textareaTyonkuvaus" rows="3" minlength="10" maxlength="65526" placeholder="Kerro, milainen työtehtävä on kyseessä" name="tyonkuvaus" <?php echo ($tilauksenmuokkaus) ? 'required' : 'disabled' ?>><?php echo $tyonkuvaus ?></textarea>
                </div>
              </div>
              <?php if (!$uusitilaus): ?>

              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationTilausPvm">Tilauspäivämäärä</label>
                  <input type="text" class="form-control" id="validationTilausPvm" placeholder="" value="<?php echo $tilausPvm ?>" readonly>
                </div>
                <?php if ($hylattyTilaus): ?>
                  <div class="col-md-6 mb-2">
                    <label for="validationHylkaysPvm">Hylkäyspäivämäärä</label>
                    <input type="text" class="form-control" id="validationHylkaysPvm" placeholder="" value="<?php echo $hylattyPvm ?>" readonly>
                  </div>
                </div>
                <?php endif;
                if (!$hylattyTilaus): ?>
                <div class="col-md-6 mb-2">
                  <label for="validationAloitusPvm">Aloituspäivämäärä</label>
                  <input type="text" class="form-control" id="validationAloitusPvm" placeholder="" value="<?php echo $aloitusPvm ?>" readonly>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationValmistumisPvm">Valmistumispäivämäärä</label>
                  <input type="text" class="form-control" id="validationValmistumisPvm" placeholder="" value="<?php echo $valmistumisPvm ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationHyvaksyttyPvm">Hyväksymispäivämäärä</label>
                  <input type="text" class="form-control" id="validationHyvaksyttyPvm" placeholder="" value="<?php echo $hyvaksyttyPvm ?>" readonly>
                </div>
              </div>
              <?php endif; ?>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationKommentti">Kommentti</label>
                  <textarea class="form-control" id="validationKommentti" rows="3" placeholder="" readonly><?php echo $kommentti ?></textarea>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationTarvikeselostus">Tarvikeselostus</label>
                  <textarea class="form-control" id="validationTarvikeselostus" rows="3" placeholder="" readonly><?php echo $tarvikeselostus ?></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationTyotunnit">Työtunnit</label>
                  <input type="text" class="form-control" id="validationTyotunnit" placeholder="" value="<?php echo $tyotunnit ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationKustannusarvio">Kustannusarvio</label>
                  <input type="text" class="form-control" id="validationKustannusarvio" placeholder="" value="<?php echo $kustannusarvio ?>" readonly>
                </div>
              </div>
            <?php endif; ?>
             <input type="hidden" name="tallenna" value="ok">
              <?php
              if (!isset($_POST["tallenna"]) && !$tallenusOnnistui && !isset($_POST["nayta"]) )
              echo "<button class=\"btn $buttonTyyppi\" type=\"submit\" formmethod=\"post\" formaction=\"$formaction\" name=\"$formname\" value=\"$formvalue\">$buttonNimi</button>";
              ?>
            </form><br />
            <form>
              <button class="btn btn-outline-primary" type="submit" formmethod="post" formaction="asiakas.php"><?php echo $buttonPeruutaNimi ?></button>
            </form>
            <?php
          }
          ?>
      </div>
    </main>
    <!-- Ladataan footer ulkopuolisesta tiedostosta -->
    <?php

    function haeTyotilaus($muokattavaTyotilausID) {
      // Otetaan tietokanta käyttöön
      require_once("db.inc");
      // suoritetaan tietokantakysely ja kokeillaan hakea työtilaus
      $tunnus = $_SESSION["kirjautunut"];
      $query = "Select * from tyotilaus WHERE tyotilausiD='$muokattavaTyotilausID'";
      $tulos = mysqli_query($conn, $query);
      // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
      if ( !$tulos )
      {
        echo "Kysely epäonnistui " . mysqli_error($conn);
      }
      else {
        // Alustetaan muuttujat.
        $toimitusosoiteID = "";
        $laskutusosoiteID = "";
        $tyonkuvaus = "";
        $tilausPvm = "";
        $aloitusPvm = "";
        $valmistumisPvm = "";
        $hyvaksyttyPvm = "";
        $hylattyPvm = "";
        $kommentti = "";
        $tyotunnit = "";
        $tarvikeselostus = "";
        $kustannusarvio = "";
        $tyotilaustaulukko = array();
        //käydään läpi löytyneet rivit
        while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
          // Haetaan tiedot ja tallennetaan ne taulukkoon
          $tyotilaustaulukko["toimitusosoiteID"] = $rivi["toimitusosoiteID"];
          $tyotilaustaulukko["laskutusosoiteID"] = $rivi["laskutusosoiteID"];
          $tyotilaustaulukko["tyonkuvaus"] = $rivi["tyonkuvaus"];
          $tyotilaustaulukko["tilausPvm"] = $rivi["tilausPvm"];
          $tyotilaustaulukko["aloitusPvm"] = $rivi["aloitusPvm"];
          $tyotilaustaulukko["valmistumisPvm"] = $rivi["valmistumisPvm"];
          $tyotilaustaulukko["hyvaksyttyPvm"] = $rivi["hyvaksyttyPvm"];
          $tyotilaustaulukko["hylattyPvm"] = $rivi["hylattyPvm"];
          $tyotilaustaulukko["kommentti"] = $rivi["kommentti"];
          $tyotilaustaulukko["tyotunnit"] = $rivi["tyotunnit"];
          $tyotilaustaulukko["tarvikeselostus"] = $rivi["tarvikeselostus"];
          $tyotilaustaulukko["kustannusarvio"] = $rivi["kustannusarvio"];
        }
      }
      return $tyotilaustaulukko;
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

    function tallennaTyotilaus($tyotilausID, $toimitusosoiteID, $laskutusosoiteID, $tyonkuvaus) {
      //require_once("db.inc");
      // Create connection
      $conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
      $conn->set_charset("utf8");
      // Check connection
      if (!$conn) {
          die("Yhteys epäonnistui: " . mysqli_connect_error());
      }
      // Tehdään kysely
      if ($tyotilausID == "") {
        // Kokonaan uusi työtilaus
        $query = "INSERT INTO tyotilaus (toimitusosoiteID, laskutusosoiteID, tyonkuvaus) VALUES
          ('$toimitusosoiteID', '$laskutusosoiteID', '$tyonkuvaus')";
          // suoritetaan tietokantakysely ja kokeillaan tallentaa uusi työtilaus
          if (mysqli_query($conn, $query)) {
            tulostaSuccess("Onnistui!", "Uusi työtilaus on nyt tallennettu.<br />Kun tilaus on otettu käsittelyyn, muuttuu sen status aloitetuksi. Sen jälkeen tilausta ei voi enää muokata.");
            mysqli_close($conn);
            return true;
          } else {
            tulostaVirhe("Työtilauksen tallennus ei onnistunut!<br>" . mysqli_error($conn));
            mysqli_close($conn);
            return false;
          }
      }
      else {
        // Vanhan tiedon päivitys
        $query = "UPDATE tyotilaus SET toimitusosoiteID = '$toimitusosoiteID', laskutusosoiteID = '$laskutusosoiteID', tyonkuvaus = '$tyonkuvaus' WHERE tyotilausID = $tyotilausID";
        // suoritetaan tietokantakysely ja kokeillaan tallentaa uusi työtilaus
        if (mysqli_query($conn, $query)) {
          tulostaSuccess("Onnistui!", "Uuden työtilauksen muutokset on nyt tallennettu.<br />Kun tilaus on otettu käsittelyyn, muuttuu sen status aloitetuksi. Sen jälkeen tilausta ei voi enää muokata");
          mysqli_close($conn);
          return true;
        } else {
          tulostaVirhe("Työtilauksen muutosten tallennus ei onnistunut!<br>" . mysqli_error($conn));
          mysqli_close($conn);
          return false;
        }
      }
    }

    require 'footer.php';
    ?>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>  </body>
</html>
