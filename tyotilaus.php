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
    $otsikko = "Tee uusi työtilaus";
    $_SESSION["muokattavaTyotilausID"] = "";
  }
  if (isset($_POST["nayta"]) && $_POST["nayta"] != "") {
    $otsikko = "Työtilaus";
    $_SESSION["muokattavaTyotilausID"] = $_POST["nayta"];
  }
  else if (isset($_POST["poista"]) && $_POST["poista"] != "") {
    $otsikko = "Poista työtilaus";
    $_SESSION["muokattavaTyotilausID"] = $_POST["poista"];
  }
  else if (isset($_POST["muokkaa"]) && $_POST["muokkaa"] != "") {
    $otsikko = "Muokkaa työtilausta";
    $_SESSION["muokattavaTyotilausID"] = $_POST["muokkaa"];
}
  else if (isset($_POST["hyvaksy"]) && $_POST["hyvaksy"] != "") {
    $otsikko = "Hyväksy työtilaus";
    $_SESSION["muokattavaTyotilausID"] = $_POST["hyvaksy"];
}
if (isset($_SESSION["muokattavaTyotilausID"]) && $_SESSION["muokattavaTyotilausID"] != "") {
  $tyotilaus = haeTyotilaus($_SESSION["muokattavaTyotilausID"]);
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
            // Tämä sisältö näytetään, jos asiakkaalla on molempia osoitetyyppejä.
            
          }

          echo "<br>Post-sisältö: ";
          print_r($_POST);
          echo "<br>Session sisältö: ";
          print_r($_SESSION);
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
        $osoiteID = "";
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
          // Haetaan
          $tyotilaustaulukko["osoiteID"] = $rivi["osoiteID"];
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

    require 'footer.php';
    ?>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>  </body>
</html>
