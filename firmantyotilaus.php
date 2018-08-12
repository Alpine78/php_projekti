<?php
  // Sessio-funktion kutsu
  session_start();
  // Tämä sivu näytetään vain sopivalla post-muuttujalla. Muutoin ohjataan tilausten listaukseen.
  if (!isset($_POST["nayta"]) && !isset($_POST["tallenna"]) && !isset($_POST["aloita"]) && !isset($_POST["valmis"]) && !isset($_POST["aloitauudelleen"]) && !isset($_POST["hylkaa"])) {
    header("Location:firma.php");
    exit();
  }
    // Sivun perusjutut, kuten muuttujien alustukset
    $otsikko = "Työtilaus";
    //$_SESSION["muokattavaTyotilausID"] = "";
    $tilauksenmuokkaus = true;
    $hylattyTilaus = false;
    // Alustetaan muuttujat. Näiden muuttujien sisältö näytetään lomakkeen kentissä ja painikkeissa.
    $tyotilausID = "";
    $toimitusosoiteID = "";
    $laskutusosoiteID = "";
    $asunnonAla = "";
    $tontinAla = "";
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
    $muutaStatus = "";
    date_default_timezone_set("Europe/Helsinki");

    if (isset($_POST["tunnus"]) && $_POST["tunnus"] != "") {
      // Haetaan käsittelyssä olevan tilauksen tilaajan tunnus, jotta voidaan tarvittaessa vaihtaa tilaukseen osoite
      $tunnus = $_POST["tunnus"];
    }

    if (isset($_POST["nayta"]) && $_POST["nayta"] != "") {
      $_SESSION["muokattavaTyotilausID"] = $_POST["nayta"];
      $tilauksenmuokkaus = true;
      //$formname = "tallenna";
      //$formvalue = $_POST["nayta"];
      //$buttonNimi = "Tallenna muutokset";
    }

    if (isset($_POST["aloita"]) && $_POST["aloita"] != "") {
      $muutaStatus = "aloita";
    }

    if (isset($_POST["valmis"]) && $_POST["valmis"] != "") {
      $muutaStatus = "valmis";
    }

    if (isset($_POST["aloitauudelleen"]) && $_POST["aloitauudelleen"] != "") {
      $muutaStatus = "aloitauudelleen";
    }

    if (isset($_POST["hylkaa"]) && $_POST["hylkaa"] != "") {
      $muutaStatus = "hylkaa";
    }

    if (isset($_POST["asunnonAla"]) && $_POST["asunnonAla"] != "") {
      $asunnonAla = $_POST["asunnonAla"];
    }
    if (isset($_POST["tontinAla"]) && $_POST["tontinAla"] != "") {
      $tontinAla = $_POST["tontinAla"];
    }


if (isset($_SESSION["muokattavaTyotilausID"]) && $_SESSION["muokattavaTyotilausID"] != "") {
  $tyotilaus = haeTyotilaus($_SESSION["muokattavaTyotilausID"]);
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
  $status = haeStatus($tilausPvm, $aloitusPvm, $valmistumisPvm, $hyvaksyttyPvm, $hylattyPvm);
  $otsikko = ucfirst($status) . " työtilaus";
  if ($status == "hyväksytty" || $status == "hylätty") {
    $buttonPeruutaNimi = "Palaa takaisin";
    $tilauksenmuokkaus = false;
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

          // Käytetään samaa koodia, kuin asiakassovelluksen puolella.
          // Asiakkaallahan on pakosti osoite, koska on voinut tehdä tilauksen.
          // Tässä yhdeydessä ladataan vaan asiakkaan osoitteet, jotta ne voidaan lomakkeella esittää.
          require("onkoOsoitetta.inc");

          if (isset($_SESSION["onToimitusosoite"]) && $_SESSION["onToimitusosoite"] == true && isset($_SESSION["onLaskutusosoite"]) && $_SESSION["onLaskutusosoite"] == true) {
            // Sivun pääsisältö näytetään, jos asiakkaalla on molempia osoitetyyppejä.
            //Ladataan sekä toimitus-, että laskutusosoitteet, jotta ne voidaan esittää alasvetovalikoissa
            require("lataaOsoitteet.inc");

            // Ladataan lomakkelle syötetyt tiedot muuttujiin, jotta ne voidaan asettaa sinne uudelleen sivun päivityksen yhteydessä
            // Nämä siis ylikirjoittavat samat muutujat, joihin jo aiemmin on ladattu tietokannasta tiedot.
            if (isset($_SESSION["muokattavaTyotilausID"]) && $_SESSION["muokattavaTyotilausID"] != "") $tyotilausID = $_SESSION["muokattavaTyotilausID"];
            if (isset($_POST["toimitusosoiteID"]) && $_POST["toimitusosoiteID"] != "") $toimitusosoiteID = $_POST["toimitusosoiteID"];
            if (isset($_POST["laskutusosoiteID"]) && $_POST["laskutusosoiteID"] != "") $laskutusosoiteID = $_POST["laskutusosoiteID"];
            if (isset($_POST["asunnonAla"]) && $_POST["asunnonAla"] != "") $asunnonAla = $_POST["asunnonAla"];
            if (isset($_POST["tontinAla"]) && $_POST["tontinAla"] != "") $tontinAla = $_POST["tontinAla"];
            if (isset($_POST["tyonkuvaus"]) && $_POST["tyonkuvaus"] != "") $tyonkuvaus = $_POST["tyonkuvaus"];
            if (isset($_POST["kommentti"]) && $_POST["kommentti"] != "") $kommentti = $_POST["kommentti"];
            if (isset($_POST["tarvikeselostus"]) && $_POST["tarvikeselostus"] != "") $tarvikeselostus = $_POST["tarvikeselostus"];
            if (isset($_POST["tyotunnit"]) && $_POST["tyotunnit"] != "") $tyotunnit = $_POST["tyotunnit"];
            if (isset($_POST["kustannusarvio"]) && $_POST["kustannusarvio"] != "") $kustannusarvio = $_POST["kustannusarvio"];

            // Lomakkeen tietojen tallennus tietokantaan
            if (isset($_POST["tallenna"]) || isset($_POST["aloita"]) || isset($_POST["valmis"]) || isset($_POST["aloitauudelleen"]) || isset($_POST["hylkaa"])) {
              $pituus = strlen($tyonkuvaus);
              if (strlen($tyonkuvaus) < 10) {
                tulostaVirhe("Työnkuvaus on liian lyhyt. Minimimerkkimäärä on 10. Syötit $pituus merkkiä.");
              }
              else if (strlen($tyonkuvaus) > 65536) {
                tulostaVirhe("Työnkuvaus on liian pitkä. Maksimimerkkimäärä on 65536. Syötit $pituus merkkiä.");
              }
              else {
                // Tarkistus ok, eli voidaan yrittää tallennusta tietokantaan.
                $tallenusOnnistui = tallennaTyotilaus($tyotilausID, $toimitusosoiteID, $laskutusosoiteID, $tyonkuvaus, $kommentti, $tarvikeselostus, $tyotunnit, $kustannusarvio, $muutaStatus);
                if ($tallenusOnnistui) {
                  $buttonPeruutaNimi = "Palaa takaisin";
                  $tyotilaus = haeTyotilaus($_SESSION["muokattavaTyotilausID"]);
                  if ($muutaStatus == "aloita"){
                    $status = "aloitettu";
                    if ($tyotilaus["aloitusPvm"] != "") $aloitusPvm = date("d.m.Y",strtotime($tyotilaus["aloitusPvm"]));
                    if ($tyotilaus["valmistumisPvm"] != "") $valmistumisPvm = date("d.m.Y",strtotime($tyotilaus["valmistumisPvm"]));
                    if ($tyotilaus["hyvaksyttyPvm"] != "") $hyvaksyttyPvm = date("d.m.Y",strtotime($tyotilaus["hyvaksyttyPvm"]));
                    if ($tyotilaus["hylattyPvm"] != "") $hylattyPvm = date("d.m.Y",strtotime($tyotilaus["hylattyPvm"]));
                  }
                  if ($muutaStatus == "valmis") {
                    $status = "valmis";
                    if ($tyotilaus["aloitusPvm"] != "") $aloitusPvm = date("d.m.Y",strtotime($tyotilaus["aloitusPvm"]));
                    if ($tyotilaus["valmistumisPvm"] != "") $valmistumisPvm = date("d.m.Y",strtotime($tyotilaus["valmistumisPvm"]));
                    if ($tyotilaus["hyvaksyttyPvm"] != "") $hyvaksyttyPvm = date("d.m.Y",strtotime($tyotilaus["hyvaksyttyPvm"]));
                    if ($tyotilaus["hylattyPvm"] != "") $hylattyPvm = date("d.m.Y",strtotime($tyotilaus["hylattyPvm"]));
                  }
                  if ($muutaStatus == "aloitauudelleen") {
                    $status = "aloitettu";
                    if ($tyotilaus["aloitusPvm"] != "") $aloitusPvm = date("d.m.Y",strtotime($tyotilaus["aloitusPvm"]));
                    if ($tyotilaus["valmistumisPvm"] != "") $valmistumisPvm = date("d.m.Y",strtotime($tyotilaus["valmistumisPvm"]));
                    if ($tyotilaus["hyvaksyttyPvm"] != "") $hyvaksyttyPvm = date("d.m.Y",strtotime($tyotilaus["hyvaksyttyPvm"]));
                    if ($tyotilaus["hylattyPvm"] != "") $hylattyPvm = date("d.m.Y",strtotime($tyotilaus["hylattyPvm"]));
                  }
                  if ($muutaStatus == "hylkaa") {
                    $status = "hylätty";
                    $hylattyTilaus = true;
                    $tilauksenmuokkaus = false;
                    if ($tyotilaus["aloitusPvm"] != "") $aloitusPvm = date("d.m.Y",strtotime($tyotilaus["aloitusPvm"]));
                    if ($tyotilaus["valmistumisPvm"] != "") $valmistumisPvm = date("d.m.Y",strtotime($tyotilaus["valmistumisPvm"]));
                    if ($tyotilaus["hyvaksyttyPvm"] != "") $hyvaksyttyPvm = date("d.m.Y",strtotime($tyotilaus["hyvaksyttyPvm"]));
                    if ($tyotilaus["hylattyPvm"] != "") $hylattyPvm = date("d.m.Y",strtotime($tyotilaus["hylattyPvm"]));
                  }
                  $otsikko = ucfirst($status) . " työtilaus";
                  //$tilauksenmuokkaus = false;
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
                  <textarea class="form-control" id="textareaTyonkuvaus" rows="3" minlength="10" maxlength="65526" placeholder="Kerro, milainen työtehtävä on kyseessä" name="tyonkuvaus" <?php echo ($tilauksenmuokkaus) ? 'required' : 'disabled' ?>><?php echo $tyonkuvaus ?></textarea>
                </div>
              </div>

              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationKommentti">Kommentti</label>
                  <textarea class="form-control" id="validationKommentti" rows="3" placeholder="" name="kommentti"  <?php echo ($tilauksenmuokkaus) ? '' : 'disabled' ?>><?php echo $kommentti ?></textarea>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationTarvikeselostus">Tarvikeselostus</label>
                  <textarea class="form-control" id="validationTarvikeselostus" rows="3" placeholder="" name="tarvikeselostus"  <?php echo ($tilauksenmuokkaus) ? '' : 'disabled' ?>><?php echo $tarvikeselostus ?></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-6 mb-2">
                  <label for="validationTyotunnit">Työtunnit</label>
                  <input type="number" class="form-control" id="validationTyotunnit" placeholder="" name="tyotunnit" value="<?php echo $tyotunnit ?>"  <?php echo ($tilauksenmuokkaus) ? '' : 'disabled' ?>>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="validationKustannusarvio">Kustannusarvio</label>
                  <input type="number" class="form-control" id="validationKustannusarvio" placeholder="" name="kustannusarvio" value="<?php echo $kustannusarvio ?>"  <?php echo ($tilauksenmuokkaus) ? '' : 'disabled' ?>>
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
             <input type="hidden" name="status" value="<?php echo $status ?>">
             <input type="hidden" name="tunnus" value="<?php echo $tunnus ?>">
              <?php
              if ($tilauksenmuokkaus && $status != "hylatty" && $status != "hyvaksytty") {
                // Eka painike näytetään melkein aina
                echo "<button class=\"btn $buttonTyyppi\" type=\"submit\" formmethod=\"post\" formaction=\"firmantyotilaus.php\" name=\"tallenna\" value=\"$tyotilausID\">Tallenna</button>&nbsp;&nbsp;";
              }
              // Toinen painike vaihtelee paljon statuksen mukaan
              if ($status == "tilattu") {
                echo "<button class=\"btn btn-warning\" type=\"submit\" formmethod=\"post\" formaction=\"firmantyotilaus.php\" name=\"aloita\" value=\"$tyotilausID\">Tallenna ja aloita työ</button>&nbsp;&nbsp;";
                echo "<button class=\"btn btn-danger\" type=\"submit\" formmethod=\"post\" formaction=\"firmantyotilaus.php\" name=\"hylkaa\" value=\"$tyotilausID\">Hylkää</button>";
              }
              else if ($status == "aloitettu") {
                echo "<button class=\"btn btn-warning\" type=\"submit\" formmethod=\"post\" formaction=\"firmantyotilaus.php\" name=\"valmis\" value=\"$tyotilausID\">Tallenna ja merkitse valmiiksi</button>&nbsp;&nbsp;";
                echo "<button class=\"btn btn-danger\" type=\"submit\" formmethod=\"post\" formaction=\"firmantyotilaus.php\" name=\"hylkaa\" value=\"$tyotilausID\">Hylkää</button>";
              }
              else if ($status == "valmis") {
                echo "<button class=\"btn btn-warning\" type=\"submit\" formmethod=\"post\" formaction=\"firmantyotilaus.php\" name=\"aloitauudelleen\" value=\"$tyotilausID\">Tallenna ja aloita uudelleen</button>";
              }
              ?>
            </form>
            <br />
            <form>
              <button class="btn btn-outline-primary" type="submit" formmethod="post" formaction="firma.php"><?php echo $buttonPeruutaNimi ?></button>
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
      $connhaetilaus = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
      $connhaetilaus->set_charset("utf8");

      if ( mysqli_connect_errno() )
      {
        // Lopettaa tämän skriptin suorituksen ja tulostaa parametrina tulleen tekstin
        die ("Tietokantapalvelinta ei löydy, syy: " . mysqli_connect_error());
      }      // suoritetaan tietokantakysely ja kokeillaan hakea työtilaus
      $query = "Select * from tyotilaus WHERE tyotilausiD='$muokattavaTyotilausID'";
      $tulos = mysqli_query($connhaetilaus, $query);
      // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
      if ( !$tulos )
      {
        echo "Kysely epäonnistui " . mysqli_error($connhaetilaus);
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

    function tallennaTyotilaus($tyotilausID, $toimitusosoiteID, $laskutusosoiteID, $tyonkuvaus, $kommentti, $tarvikeselostus, $tyotunnit, $kustannusarvio, $muutaStatus) {
      if ($kustannusarvio == "") $kustannusarvio = "0";
      if ($tyotunnit == "") $tyotunnit = 0;
      //require_once("db.inc");
      // Create connection
      $conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
      $conn->set_charset("utf8");
      // Check connection
      if (!$conn) {
          die("Yhteys epäonnistui: " . mysqli_connect_error());
      }
        // Vanhan tiedon päivitys. Firman sovelluksella ei uusia tilauksia luodakaan
        // Peruskysely, eli kyselyn alkuosa
        $query = "UPDATE tyotilaus SET toimitusosoiteID = '$toimitusosoiteID', laskutusosoiteID = '$laskutusosoiteID', tyonkuvaus = '$tyonkuvaus', kommentti = '$kommentti', tarvikeselostus = '$tarvikeselostus', tyotunnit = '$tyotunnit', kustannusarvio = '$kustannusarvio'";
        if ($muutaStatus != "") {
          // Jos stausta muutetaan, lisätään kyselyyn tavaraa
          if ($muutaStatus == "aloita") $query .= ", aloitusPvm = NOW()";
          if ($muutaStatus == "valmis") $query .= ", valmistumisPvm = NOW()";
          if ($muutaStatus == "aloitauudelleen") $query .= ", valmistumisPvm = NULL";
          if ($muutaStatus == "hylkaa") $query .= ", hylattyPvm = NOW()";
        }
        $query .= " WHERE tyotilausID = $tyotilausID"; // Kyselyn loppuosa (huom. välilyönti alussa)
        // suoritetaan tietokantakysely ja kokeillaan tallentaa uusi työtilaus
        if (mysqli_query($conn, $query)) {
          tulostaSuccess("Onnistui!", "Työtilauksen muutokset on nyt tallennettu.");
          mysqli_close($conn);
          return true;
        } else {
          tulostaVirhe("Työtilauksen muutosten tallennus ei onnistunut!" . mysqli_error($conn));
          mysqli_close($conn);
          return false;
        }
    }

    function haeStatus($tilausPvm, $aloitusPvm, $valmistumisPvm, $hyvaksyttyPvm, $hylattyPvm) {
      if ($hylattyPvm != "") $status = "hylätty";
      else if ($hyvaksyttyPvm != "") $status = "hyväksytty";
      else if ($valmistumisPvm != "") $status = "valmis";
      else if ($aloitusPvm != "") $status = "aloitettu";
      else $status = "tilattu";
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
