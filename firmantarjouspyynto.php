<?php
  // Sessio-funktion kutsu
  session_start();
  if (!isset($_POST["nayta"]) && !isset($_POST["hylkaa"]) && !isset($_POST["hyvaksy"])) {
    header("Location:firmantarjouspyynnot.php");
    exit();
  }
    // Sivun perusjutut, kuten muuttujien alustukset
    $otsikko = "Tarjouspyyntö";
    //$_SESSION["muokattavaTarjouspyyntoID"] = "";
    $uusitarjous = true;
    $tarjouksenmuokkaus = true;
    $hylattyTarjous = false;
    // Alustetaan muuttujat. Näiden muuttujien sisältö näytetään lomakkeen kentissä ja painikkeissa.
    $tarjouspyyntoID = "";
    $toimitusosoiteID = "";
    $laskutusosoiteID = "";
    $tyonkuvaus = "";
    $asunnonAla = "";
    $kustannusarvio = "";
    $tontinAla = "";
    $jattoPvm = "";
    $vastattuPvm = "";
    $hyvaksyttyPvm = "";
    $hylattyPvm = "";
    $formaction = "firmantarjouspyynto.php";
    $formname = "uusitarjous";
    $formvalue = "";
    $buttonNimi = "Jätä tarjouspyyntö";
    $buttonTyyppi = "btn-primary";
    $buttonPeruutaNimi = "Peruuta";
    $tallenusOnnistui = "";
    $status = "";
    $muutaStatus = "";
    date_default_timezone_set("Europe/Helsinki");

    if (isset($_POST["asunnonAla"]) && $_POST["asunnonAla"] != "") {
      $asunnonAla = $_POST["asunnonAla"];
    }
    if (isset($_POST["tontinAla"]) && $_POST["tontinAla"] != "") {
      $tontinAla = $_POST["tontinAla"];
    }
    if (isset($_POST["tunnus"]) && $_POST["tunnus"] != "") {
      $tunnus = $_POST["tunnus"];
    }


  if (isset($_POST["nayta"]) && $_POST["nayta"] != "") {
    $otsikko = "Tarjouspyyntö";
    $_SESSION["muokattavaTarjouspyyntoID"] = $_POST["nayta"];
    $buttonPeruutaNimi = "Palaa takaisin";
    $tarjouksenmuokkaus = true;
    $formname = "nayta";
    $formvalue = $_POST["nayta"];
    $tunnus = $_POST["tunnus"];
    $buttonNimi = "Tallenna muutokset";
}


if (isset($_SESSION["muokattavaTarjouspyyntoID"]) && $_SESSION["muokattavaTarjouspyyntoID"] != "") {
  $tarjouspyynto = haeTarjouspyynto($_SESSION["muokattavaTarjouspyyntoID"]);
  $uusitarjous = false;
  $toimitusosoiteID = $tarjouspyynto["toimitusosoiteID"];
  $laskutusosoiteID = $tarjouspyynto["laskutusosoiteID"];
  $tyonkuvaus = $tarjouspyynto["tyonkuvaus"];
  $kustannusarvio = $tarjouspyynto["kustannusarvio"];
  if ($tarjouspyynto["jattoPvm"] != "") $jattoPvm = date("d.m.Y",strtotime($tarjouspyynto["jattoPvm"]));
  if ($tarjouspyynto["vastattuPvm"] != "") $vastattuPvm = date("d.m.Y",strtotime($tarjouspyynto["vastattuPvm"]));
  if ($tarjouspyynto["hyvaksyttyPvm"] != "") $hyvaksyttyPvm = date("d.m.Y",strtotime($tarjouspyynto["hyvaksyttyPvm"]));
  if ($tarjouspyynto["hylattyPvm"] != "") $hylattyPvm = date("d.m.Y",strtotime($tarjouspyynto["hylattyPvm"]));
  if ($hylattyPvm != "") $hylattyTarjous = true;
  $status = haeStatus($jattoPvm, $vastattuPvm, $hyvaksyttyPvm, $hylattyPvm);
  $otsikko = ucfirst($status) . " tarjouspyyntö";
  if ($status == "jätetty") {
    $tarjouksenmuokkaus = true;
    $buttonPeruutaNimi = "Peruuta";
  }
  else if ($status == "vastattu") {
    $tarjouksenmuokkaus = false;
  }
  else if ($status == "hyväksytty") {
    $tarjouksenmuokkaus = false;
  }
  else if ($status == "hylätty") {
    $tarjouksenmuokkaus = false;
  }
}
?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Kotitalkkari - Kiinteistöhuoltofirman sovellus">
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
        <h1>Kotitalkkari - Kiinteistöhuoltofirman sovellus</h1>
        <?php

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
            if (isset($_POST["kustannusarvio"]) && $_POST["kustannusarvio"] != "") $kustannusarvio = $_POST["kustannusarvio"];
            if (isset($_POST["asunnonAla"]) && $_POST["asunnonAla"] != "") $asunnonAla = $_POST["asunnonAla"];
            if (isset($_POST["tontinAla"]) && $_POST["tontinAla"] != "") $tontinAla = $_POST["tontinAla"];

            // Lomakkeen tietojen tallennus tietokantaan
            if (isset($_POST["tallenna"]) && $_POST["tallenna"] == "ok") {
              $tarkistus = true;
              $pituus = strlen($tyonkuvaus);
              if (strlen($tyonkuvaus) < 10) {
                tulostaVirhe("Työnkuvaus on liian lyhyt. Minimimerkkimäärä on 10. Syötit $pituus merkkiä.");
                $tarkistus = false;
              }
              else if (strlen($tyonkuvaus) > 65536) {
                tulostaVirhe("Työnkuvaus on liian pitkä. Maksimimerkkimäärä on 65536. Syötit $pituus merkkiä.");
                $tarkistus = false;
              }
              if ($kustannusarvio == "" && isset($_POST["hyvaksy"]) && $_POST["hyvaksy"] != "") {
                tulostaVirhe("Kustannusarvio on pakollinen kenttä.");
                $tarkistus = false;
              }
              if ($tarkistus) {
                if (isset($_POST["hyvaksy"]) && $_POST["hyvaksy"] != "") {
                  $muutaStatus = "hyvaksy";
                  $tarjouspyyntoID = $_POST["hyvaksy"];
                }
                if (isset($_POST["hylkaa"]) && $_POST["hylkaa"] != "") {
                  $muutaStatus = "hylkaa";
                  $tarjouspyyntoID = $_POST["hylkaa"];
                }
                // Tarkistus ok, eli voidaan yrittää tallennusta tietokantaan.
                $tallenusOnnistui = tallennaTarjouspyynto($tarjouspyyntoID, $tyonkuvaus, $kustannusarvio, $muutaStatus);
                if ($tallenusOnnistui) {
                  $buttonPeruutaNimi = "Palaa takaisin";
                  $tarjouksenmuokkaus = false;
                  $tarjouspyynto = haeTarjouspyynto($_SESSION["muokattavaTarjouspyyntoID"]);
                  if ($muutaStatus == "hyvaksy") $status = "vastattu";
                  if ($muutaStatus == "hylkaa") $status = "hylätty";
                  if ($tarjouspyynto["vastattuPvm"] != "") $vastattuPvm = date("d.m.Y",strtotime($tarjouspyynto["vastattuPvm"]));
                  if ($tarjouspyynto["hyvaksyttyPvm"] != "") $hyvaksyttyPvm = date("d.m.Y",strtotime($tarjouspyynto["hyvaksyttyPvm"]));
                  if ($tarjouspyynto["hylattyPvm"] != "") $hylattyPvm = date("d.m.Y",strtotime($tarjouspyynto["hylattyPvm"]));
                  $otsikko = ucfirst($status) . " työtilaus";
                }
              }
            }
            echo "<h2>$otsikko</h2>";
            ?>
            <form>
              <div class="form-row">
                <div class="form-group col-md-6 mb-2">
                  <label for="toimitusosoiteselect">Toimitusosoite</label>
                  <select class="form-control" id="toimitusosoiteselect" name="toimitusosoiteID" disabled>
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
                  <label for="laskutusosoiteselect">Laskutusosoite</label>
                  <select class="form-control" id="laskutusosoiteselect" name="laskutusosoiteID" disabled>
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
                <div class="col-md-6 mb-2">
                  <label for="validationAsunnonAla">Asunnon ala</label>
                  <input type="text" class="form-control" id="validationAsunnonAla" placeholder="" name="asunnonAla" value="<?php echo $asunnonAla ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationTontinAla">Tontin ala</label>
                  <input type="text" class="form-control" id="validationTontinAla" placeholder="" name="tontinAla" value="<?php echo $tontinAla ?>" readonly>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-1">
                  <label for="textareaTyonkuvaus">Työkuvaus</label>
                  <textarea class="form-control" id="textareaTyonkuvaus" rows="3" minlength="10" maxlength="65526" placeholder="Kerro, milainen työtehtävä on kyseessä" name="tyonkuvaus" <?php echo ($tarjouksenmuokkaus) ? 'required' : 'disabled' ?>><?php echo $tyonkuvaus ?></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationJattoPvm">Jättöpäivämäärä</label>
                  <input type="text" class="form-control" id="validationJattoPvm" placeholder="" name="jattoPvm" value="<?php echo $jattoPvm ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationVastausPvm">Vastauspäivämäärä</label>
                  <input type="text" class="form-control" id="validationVastausPvm" placeholder="" name="vastattuPvm" value="<?php echo $vastattuPvm ?>" readonly>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationHyvaksyttyPvm">Hyväksymispäivämäärä</label>
                  <input type="text" class="form-control" id="validationHyvaksyttyPvm" placeholder="" name="hyvaksyttyPvm" value="<?php echo $hyvaksyttyPvm ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationHylkaysPvm">Hylkäyspäivämäärä</label>
                  <input type="text" class="form-control" id="validationHylkaysPvm" placeholder="" name="hylattyPvm" value="<?php echo $hylattyPvm ?>" readonly>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationKustannusarvio">Kustannusarvio</label>
                  <input type="number" class="form-control" id="validationKustannusarvio" placeholder="" name="kustannusarvio" value="<?php echo $kustannusarvio ?>" <?php echo ($tarjouksenmuokkaus) ? '' : 'disabled' ?>>
                </div>
              </div>
             <input type="hidden" name="tallenna" value="ok">
             <input type="hidden" name="tunnus" value="<?php echo $tunnus ?>">
              <?php
              if ($status == "jätetty") {
                echo "<button class=\"btn $buttonTyyppi\" type=\"submit\" formmethod=\"post\" formaction=\"$formaction\" name=\"hyvaksy\" value=\"$tarjouspyyntoID\">Hyväksy</button>&nbsp;&nbsp;";
                echo "<button class=\"btn btn-danger\" type=\"submit\" formmethod=\"post\" formaction=\"$formaction\" name=\"hylkaa\" value=\"$tarjouspyyntoID\">Hylkää</button>";
              }
              ?>
            </form>
            <form>
              <br />
              <button class="btn btn-outline-primary" type="submit" formmethod="post" formaction="firmantarjouspyynnot.php"><?php echo $buttonPeruutaNimi ?></button>
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
      $connhaetarjous = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
      $connhaetarjous->set_charset("utf8");

      if ( mysqli_connect_errno() )
      {
        // Lopettaa tämän skriptin suorituksen ja tulostaa parametrina tulleen tekstin
        die ("Tietokantapalvelinta ei löydy, syy: " . mysqli_connect_error());
      }
      $query = "SELECT * FROM tarjouspyynto WHERE tarjouspyyntoID='$muokattavaTarjouspyyntoID'";
      $tulos = mysqli_query($connhaetarjous, $query);
      // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
      if ( !$tulos )
      {
        echo "Kysely epäonnistui " . mysqli_error($connhaetarjous);
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

    function tallennaTarjouspyynto($tarjouspyyntoID, $tyonkuvaus, $kustannusarvio, $muutaStatus) {
      //require_once("db.inc");
      // Create connection
      $conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
      $conn->set_charset("utf8");
      // Check connection
      if (!$conn) {
          die("Yhteys epäonnistui: " . mysqli_connect_error());
      }
      // Tehdään kysely
        // Vanhan tiedon päivitys
        if ($muutaStatus == "hyvaksy") {
          $query = "UPDATE tarjouspyynto SET tyonkuvaus = '$tyonkuvaus', kustannusarvio = '$kustannusarvio', vastattuPvm = NOW() WHERE tarjouspyyntoID = $tarjouspyyntoID";
        }
        else if  ($muutaStatus == "hylkaa") {
          $query = "UPDATE tarjouspyynto SET tyonkuvaus = '$tyonkuvaus', hylattyPvm = NOW() WHERE tarjouspyyntoID = $tarjouspyyntoID";
        }
        // suoritetaan tietokantakysely ja kokeillaan vastata tarjouspyyntöön
        if (mysqli_query($conn, $query)) {
          tulostaSuccess("Onnistui!", "Tarjouspyynnön muutokset on nyt tallennettu.");
          mysqli_close($conn);
          return true;
        } else {
          tulostaVirhe("Tarjouspyynnön muutosten tallennus ei onnistunut!<br>" . mysqli_error($conn));
          mysqli_close($conn);
          return false;
        }
    }

    function haeStatus($jattoPvm, $vastattuPvm, $hyvaksyttyPvm, $hylattyPvm) {
      if ($hylattyPvm != "") $status = "hylätty";
      else if ($hyvaksyttyPvm != "") $status = "hyväksytty";
      else if ($vastattuPvm != "") $status = "vastattu";
      else $status = "jätetty";
      return $status;
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
