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
    $otsikko = "Jätä uusi tarjouspyyntö";
    $_SESSION["muokattavaTarjouspyyntoID"] = "";
    $uusitarjous = true;
    $tarjouksenmuokkaus = true;
    $hylattyTarjous = false;
    // Alustetaan muuttujat. Näiden muuttujien sisältö näytetään lomakkeen kentissä ja painikkeissa.
    $tarjouspyyntoID = "";
    $toimitusosoiteID = "";
    $laskutusosoiteID = "";
    $tyonkuvaus = "";
    $jattoPvm = "";
    $vastattuPvm = "";
    $hyvaksyttyPvm = "";
    $hylattyPvm = "";
    $formaction = "tarjouspyynto.php";
    $formname = "uusitarjous";
    $formvalue = "";
    $buttonNimi = "Jätä tarjouspyyntö";
    $buttonTyyppi = "btn-primary";
    $buttonPeruutaNimi = "Peruuta";
    $tallenusOnnistui = "";
    date_default_timezone_set("Europe/Helsinki");
  }
  if (isset($_POST["nayta"]) && $_POST["nayta"] != "") {
    $otsikko = "Tarjouspyyntö";
    $_SESSION["muokattavaTarjouspyyntoID"] = $_POST["nayta"];
    $tarjouksenmuokkaus = false;
    $buttonPeruutaNimi = "Palaa takaisin";
  }
  else if (isset($_POST["poista"]) && $_POST["poista"] != "") {
    $otsikko = "Poista tarjouspyyntö";
    $_SESSION["muokattavaTarjouspyyntoID"] = $_POST["poista"];
    $tarjouksenmuokkaus = false;
    $formaction = "tarjouspyynnot.php";
    $formname = "poista";
    $formvalue = $_POST["poista"];
    $buttonNimi = "Vahvista tarjouspyynnön poistaminen";
    $buttonTyyppi = "btn-danger";
  }
  else if (isset($_POST["muokkaa"]) && $_POST["muokkaa"] != "") {
    $otsikko = "Muokkaa tarjouspyyntöä";
    $_SESSION["muokattavaTarjouspyyntoID"] = $_POST["muokkaa"];
    $tarjouksenmuokkaus = true;
    $formname = "muokkaa";
    $formvalue = $_POST["muokkaa"];
    $buttonNimi = "Tallenna muutokset";
}
  else if (isset($_POST["hyvaksy"]) && $_POST["hyvaksy"] != "") {
    $otsikko = "Hyväksy tai hylkää tarjouspyyntö";
    if (isset($_POST["hyvaksy"]) && $_POST["hyvaksy"] != "") $_SESSION["muokattavaTarjouspyyntoID"] = $_POST["hyvaksy"];
    $tarjouksenmuokkaus = false;
    $formaction = "tarjouspyynnot.php";
    $formname = "hyvaksy";
    $formvalue = $_SESSION["muokattavaTarjouspyyntoID"];
    $buttonNimi = "Hyväksy";
    $formname2 = "hylkaa";
    $buttonNimi2 = "Hylkää";
    $buttonTyyppi2 = "btn-danger";
}
if (isset($_SESSION["muokattavaTarjouspyyntoID"]) && $_SESSION["muokattavaTarjouspyyntoID"] != "") {
  $tarjouspyynto = haeTarjouspyynto($_SESSION["muokattavaTarjouspyyntoID"]);
  $uusitarjous = false;
  $toimitusosoiteID = $tarjouspyynto["toimitusosoiteID"];
  $laskutusosoiteID = $tarjouspyynto["laskutusosoiteID"];
  $tyonkuvaus = $tarjouspyynto["tyonkuvaus"];
  if ($tarjouspyynto["jattoPvm"] != "") $jattoPvm = date("d.m.Y",strtotime($tarjouspyynto["jattoPvm"]));
  if ($tarjouspyynto["vastattuPvm"] != "") $vastattuPvm = date("d.m.Y",strtotime($tarjouspyynto["vastattuPvm"]));
  if ($tarjouspyynto["hyvaksyttyPvm"] != "") $hyvaksyttyPvm = date("d.m.Y",strtotime($tarjouspyynto["hyvaksyttyPvm"]));
  if ($tarjouspyynto["hylattyPvm"] != "") $hylattyPvm = date("d.m.Y",strtotime($tarjouspyynto["hylattyPvm"]));
  $kustannusarvio = $tarjouspyynto["kustannusarvio"];
  if ($hylattyPvm != "") $hylattyTarjous = true;
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
    <title>Kotitalkkari - Kiinteistöhuoltofirman sovellus</title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
  </head>
  <body>
    <?php
    // Ladataan päävalikko ulkopuolisesta tiedostosta
    require 'firmamenu.inc';
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
            if (isset($_SESSION["muokattavaTarjouspyyntoID"]) && $_SESSION["muokattavaTarjouspyyntoID"] != "") $tarjouspyyntoID = $_SESSION["muokattavaTarjouspyyntoID"];
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
                $tallenusOnnistui = tallennaTarjouspyynto($tarjouspyyntoID, $toimitusosoiteID, $laskutusosoiteID, $tyonkuvaus);
                if ($tallenusOnnistui) {
                  $buttonPeruutaNimi = "Palaa takaisin";
                  $tarjouksenmuokkaus = false;
                }
              }
            }
            ?>
            <form>
              <div class="form-row">
                <div class="form-group col-md-6 mb-2">
                  <label for="toimitusosoiteselect">Valitse toimitusosoite</label>
                  <select class="form-control" id="toimitusosoiteselect" name="toimitusosoiteID" <?php echo ($tarjouksenmuokkaus) ? '' : 'disabled' ?>>
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
                  <select class="form-control" id="laskutusosoiteselect" name="laskutusosoiteID" <?php echo ($tarjouksenmuokkaus) ? '' : 'disabled' ?>>
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
                  <textarea class="form-control" id="textareaTyonkuvaus" rows="3" minlength="10" maxlength="65526" placeholder="Kerro, milainen työtehtävä on kyseessä" name="tyonkuvaus" <?php echo ($tarjouksenmuokkaus) ? 'required' : 'disabled' ?>><?php echo $tyonkuvaus ?></textarea>
                </div>
              </div>
              <?php if (!$uusitarjous): ?>

              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationJattoPvm">Jättöpäivämäärä</label>
                  <input type="text" class="form-control" id="validationJattoPvm" placeholder="" value="<?php echo $jattoPvm ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationVastausPvm">Vastauspäivämäärä</label>
                  <input type="text" class="form-control" id="validationVastausPvm" placeholder="" value="<?php echo $vastattuPvm ?>" readonly>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationHyvaksyttyPvm">Hyväksymispäivämäärä</label>
                  <input type="text" class="form-control" id="validationHyvaksyttyPvm" placeholder="" value="<?php echo $hyvaksyttyPvm ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationHylkaysPvm">Hylkäyspäivämäärä</label>
                  <input type="text" class="form-control" id="validationHylkaysPvm" placeholder="" value="<?php echo $hylattyPvm ?>" readonly>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationKustannusarvio">Kustannusarvio</label>
                  <input type="text" class="form-control" id="validationKustannusarvio" placeholder="" value="<?php echo $kustannusarvio ?>" readonly>
                </div>
              </div>
            <?php endif; ?>
             <input type="hidden" name="tallenna" value="ok">
              <?php
              if (!isset($_POST["tallenna"]) && !$tallenusOnnistui && !isset($_POST["nayta"]))
              echo "<button class=\"btn $buttonTyyppi\" type=\"submit\" formmethod=\"post\" formaction=\"$formaction\" name=\"$formname\" value=\"$formvalue\">$buttonNimi</button> ";
              if (isset($_POST["hyvaksy"]) && $_POST["hyvaksy"]) {
                echo "<input type=\"hidden\" name=\"laskutusosoiteID\" value=\"$laskutusosoiteID\">";
                echo "<input type=\"hidden\" name=\"toimitusosoiteID\" value=\"$toimitusosoiteID\">";
                echo "<input type=\"hidden\" name=\"tyonkuvaus\" value=\"$tyonkuvaus\">";
                echo "<input type=\"hidden\" name=\"kustannusarvio\" value=\"$kustannusarvio\">";
                echo "<button class=\"btn $buttonTyyppi2\" type=\"submit\" formmethod=\"post\" formaction=\"$formaction\" name=\"$formname2\" value=\"$formvalue\">$buttonNimi2</button>";
              }
              ?>
            </form>
            <form>
              <br />
              <button class="btn btn-outline-primary" type="submit" formmethod="post" formaction="tarjouspyynnot.php"><?php echo $buttonPeruutaNimi ?></button>
            </form>
            <?php
          }
          ?>
      </div>
    </main>
    <!-- Ladataan footer ulkopuolisesta tiedostosta -->
    <?php

    function haeTarjouspyynto($muokattavaTarjouspyyntoID) {
      // Otetaan tietokanta käyttöön
      require_once("db.inc");
      // suoritetaan tietokantakysely ja kokeillaan hakea työtilaus
      $tunnus = $_SESSION["kirjautunut"];
      $query = "Select * from tarjouspyynto WHERE tarjouspyyntoID='$muokattavaTarjouspyyntoID'";
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
        $jattoPvm = "";
        $vastattuPvm = "";
        $hyvaksyttyPvm = "";
        $hylattyPvm = "";
        $kustannusarvio = "";
        $tarjouspyyntotaulukko = array();
        //käydään läpi löytyneet rivit
        while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
          // Haetaan tiedot ja tallennetaan ne taulukkoon
          $tarjouspyyntotaulukko["toimitusosoiteID"] = $rivi["toimitusosoiteID"];
          $tarjouspyyntotaulukko["laskutusosoiteID"] = $rivi["laskutusosoiteID"];
          $tarjouspyyntotaulukko["tyonkuvaus"] = $rivi["tyonkuvaus"];
          $tarjouspyyntotaulukko["jattoPvm"] = $rivi["jattoPvm"];
          $tarjouspyyntotaulukko["vastattuPvm"] = $rivi["vastattuPvm"];
          $tarjouspyyntotaulukko["hyvaksyttyPvm"] = $rivi["hyvaksyttyPvm"];
          $tarjouspyyntotaulukko["hylattyPvm"] = $rivi["hylattyPvm"];
          $tarjouspyyntotaulukko["kustannusarvio"] = $rivi["kustannusarvio"];
        }
      }
      return $tarjouspyyntotaulukko;
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

    function tallennaTarjouspyynto($tarjouspyyntoID, $toimitusosoiteID, $laskutusosoiteID, $tyonkuvaus) {
      //require_once("db.inc");
      // Create connection
      $conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
      $conn->set_charset("utf8");
      // Check connection
      if (!$conn) {
          die("Yhteys epäonnistui: " . mysqli_connect_error());
      }
      // Tehdään kysely
      if ($tarjouspyyntoID == "") {
        // Kokonaan uusi työtilaus
        $query = "INSERT INTO tarjouspyynto (toimitusosoiteID, laskutusosoiteID, tyonkuvaus) VALUES
          ('$toimitusosoiteID', '$laskutusosoiteID', '$tyonkuvaus')";
          // suoritetaan tietokantakysely ja kokeillaan tallentaa uusi työtilaus
          if (mysqli_query($conn, $query)) {
            tulostaSuccess("Onnistui!", "Uusi tarjouspyyntö on nyt tallennettu.<br />Kun tarjouspyyntöön on vastattu, voit sen hyväksyä tai hylätä. On mahdollista, että myös kiinteistöhuoltofirma hylkää tarjouksen.");
            mysqli_close($conn);
            return true;
          } else {
            tulostaVirhe("Tarjouspyynnön tallennus ei onnistunut!<br>" . mysqli_error($conn));
            mysqli_close($conn);
            return false;
          }
      }
      else {
        // Vanhan tiedon päivitys
        $query = "UPDATE tarjouspyynto SET toimitusosoiteID = '$toimitusosoiteID', laskutusosoiteID = '$laskutusosoiteID', tyonkuvaus = '$tyonkuvaus' WHERE tarjouspyyntoID = $tarjouspyyntoID";
        // suoritetaan tietokantakysely ja kokeillaan tallentaa uusi työtilaus
        if (mysqli_query($conn, $query)) {
          tulostaSuccess("Onnistui!", "Tarjouspyynnön muutokset on nyt tallennettu.<br />Kun tarjouspyyntöön on vastattu, ei sitä sen jälkeen voi enää muokata");
          mysqli_close($conn);
          return true;
        } else {
          tulostaVirhe("Tarjouspyynnön muutosten tallennus ei onnistunut!<br>" . mysqli_error($conn));
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
