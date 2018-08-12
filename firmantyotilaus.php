<?php
  // Sessio-funktion kutsu
  session_start();
    // Sivun perusjutut, kuten muuttujien alustukset
    $otsikko = "Työtilaus";
    //$_SESSION["muokattavaTyotilausID"] = "";
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
    $formaction = "firmantyotilaus.php";
    $formname = "tallenna";
    $formvalue = "";
    $buttonNimi = "Jätä uusi tilaus";
    $buttonTyyppi = "btn-primary";
    $buttonPeruutaNimi = "Peruuta";
    $tallenusOnnistui = "";
    $status = "";
    date_default_timezone_set("Europe/Helsinki");

    if (isset($_POST["tunnus"]) && $_POST["tunnus"] != "") {
      // Haetaan käsittelyssä olevan tilauksen tilaajan tunnus, jotta voidaan tarvittaessa vaihtaa tilaukseen osoite
      $tunnus = $_POST["tunnus"];
    }

    if (isset($_POST["nayta"]) && $_POST["nayta"] != "") {
      $_SESSION["muokattavaTyotilausID"] = $_POST["nayta"];
      $tilauksenmuokkaus = true;
      $formname = "tallenna";
      $formvalue = $_POST["nayta"];
      $buttonNimi = "Tallenna muutokset";
    }

    if (isset($_POST["status"])) {
      // Haetaan käsittelyssä olevan tilauksen status, koska tieto on jo valmiina olemassa. Ei tarvitse if-lauseita enää enempää sen takia.
      // Tilauksen statuksen perusteella näytetään sitten sopivat vaihtoehdot jatkokäsittelyyn.
      $status = $_POST["status"];
      $otsikko = ucfirst($status) . " työtilaus";
      if ($status == "tilattu") {
        $buttonPeruutaNimi = "Peruuta";
      }
      else if ($status == "aloitettu") {
        $buttonPeruutaNimi = "Peruuta";
      }
      else if ($status == "valmis") {
        $buttonPeruutaNimi = "Peruuta";
      }
      else if ($status == "hyvaksytty") {
        $buttonPeruutaNimi = "Peruuta";
        $tilauksenmuokkaus = false;
      }
      else {
        $buttonPeruutaNimi = "Palaa takaisin";
        $tilauksenmuokkaus = false;
      }
    }

if (isset($_SESSION["muokattavaTyotilausID"]) && $_SESSION["muokattavaTyotilausID"] != "") {
  $tyotilaus = haeTyotilaus($_SESSION["muokattavaTyotilausID"]);
  $uusitilaus = false;
  $toimitusosoiteID = $tyotilaus["toimitusosoiteID"];
  $laskutusosoiteID = $tyotilaus["laskutusosoiteID"];
  $tyonkuvaus = $tyotilaus["tyonkuvaus"];
  $kommentti = $tyotilaus["kommentti"];
  $tyotunnit = $tyotilaus["tyotunnit"];
  $tarvikeselostus = $tyotilaus["tarvikeselostus"];
  $kustannusarvio = $tyotilaus["kustannusarvio"];
  if ($tyotilaus["tilausPvm"] != "") $tilausPvm = date("d.m.Y",strtotime($tyotilaus["tilausPvm"]));
  if ($tyotilaus["aloitusPvm"] != "") $aloitusPvm = date("d.m.Y",strtotime($tyotilaus["aloitusPvm"]));
  if ($tyotilaus["valmistumisPvm"] != "") $valmistumisPvm = date("d.m.Y",strtotime($tyotilaus["valmistumisPvm"]));
  if ($tyotilaus["hyvaksyttyPvm"] != "") $hyvaksyttyPvm = date("d.m.Y",strtotime($tyotilaus["hyvaksyttyPvm"]));
  if ($tyotilaus["hylattyPvm"] != "") $hylattyPvm = date("d.m.Y",strtotime($tyotilaus["hylattyPvm"]));
  if ($hylattyPvm != "") $hylattyTilaus = true;
  print_r($tyotilaus);
}
  //if (isset())
?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Kotitalkkari - Kiinteistöhuoltofirman sovellus">
    <meta name="author" content="Ilkka Rytkönen">
    <title>Työtilaus - Kotitalkkari</title>
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
          echo "<h2>$otsikko</h2>";

          // Käytetään samaa koodia, kuin asiakassovelluksen puolella.
          // Asiakkaallahan on pakosti osoite, koska on voinut tehdä tilauksen.
          // Tässä yhdeydessä ladataan vaan asiakkaan osoitteet, jotta ne voidaan lomakkeella esittää.
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
            if (isset($_POST["kommentti"]) && $_POST["kommentti"] != "") $kommentti = $_POST["kommentti"];
            if (isset($_POST["tarvikeselostus"]) && $_POST["tarvikeselostus"] != "") $tarvikeselostus = $_POST["tarvikeselostus"];
            if (isset($_POST["tyotunnit"]) && $_POST["tyotunnit"] != "") $tyonkuvaus = $_POST["tyotunnit"];
            if (isset($_POST["kustannusarvio"]) && $_POST["kustannusarvio"] != "") $tyonkuvaus = $_POST["kustannusarvio"];

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
                  <label for="toimitusosoiteselect">Toimitusosoite</label>
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
                  <label for="laskutusosoiteselect">Laskutusosoite</label>
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

              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationKommentti">Kommentti</label>
                  <textarea class="form-control" id="validationKommentti" rows="3" placeholder=""  <?php echo ($tilauksenmuokkaus) ? '' : 'disabled' ?>><?php echo $kommentti ?></textarea>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationTarvikeselostus">Tarvikeselostus</label>
                  <textarea class="form-control" id="validationTarvikeselostus" rows="3" placeholder=""  <?php echo ($tilauksenmuokkaus) ? '' : 'disabled' ?>><?php echo $tarvikeselostus ?></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationTyotunnit">Työtunnit</label>
                  <input type="number" class="form-control" id="validationTyotunnit" placeholder="" value="<?php echo $tyotunnit ?>"  <?php echo ($tilauksenmuokkaus) ? '' : 'disabled' ?>>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationKustannusarvio">Kustannusarvio</label>
                  <input type="number" class="form-control" id="validationKustannusarvio" placeholder="" value="<?php echo $kustannusarvio ?>"  <?php echo ($tilauksenmuokkaus) ? '' : 'disabled' ?>>
                </div>
              </div>
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
             <input type="hidden" name="tallenna" value="ok">
             <input type="hidden" name="status" value="<?php echo $status ?>">
             <input type="hidden" name="tunnus" value="<?php echo $tunnus ?>">
              <?php
              if ($tilauksenmuokkaus && $status != "hylatty" && $status != "hyvaksytty") {
                // Eka painike näytetään melkein aina
                echo "<button class=\"btn $buttonTyyppi\" type=\"submit\" formmethod=\"post\" formaction=\"$formaction\" name=\"$formname\" value=\"$formvalue\">$buttonNimi</button>&nbsp;&nbsp;";
              }
              // Toinen painike vaihtelee paljon statuksen mukaan
              if ($status == "tilattu") {
                echo "<button class=\"btn $buttonTyyppi\" type=\"submit\" formmethod=\"post\" formaction=\"firmantyotilaus.php\" name=\"aloita\" value=\"$tyotilausID\">Tallenna ja merkitse työ aloitetuksi</button>";
              }
              else if ($status == "aloitettu") {
                echo "<button class=\"btn $buttonTyyppi\" type=\"submit\" formmethod=\"post\" formaction=\"firmantyotilaus.php\" name=\"aloita\" value=\"$tyotilausID\">Tallenna ja merkitse työ valmiiksi</button>";
              }
              else if ($status == "valmis") {
                echo "<button class=\"btn $buttonTyyppi\" type=\"submit\" formmethod=\"post\" formaction=\"firmantyotilaus.php\" name=\"aloita\" value=\"$tyotilausID\">Tallenna ja merkitse työ uudelleen aloitetuksi</button>";
              }
              ?>
            </form>
            <form>
              <button class="btn btn-outline-primary" type="submit" formmethod="post" formaction="firma.php"><?php echo $buttonPeruutaNimi ?></button>
            </form>
            <?php
          }

          echo "Post: <br>";
          print_r($_POST);
          ?>

      </div>
    </main>
    <!-- Ladataan footer ulkopuolisesta tiedostosta -->
    <?php

    function haeTyotilaus($muokattavaTyotilausID) {
      // Otetaan tietokanta käyttöön
      require_once("db.inc");
      // suoritetaan tietokantakysely ja kokeillaan hakea työtilaus
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
