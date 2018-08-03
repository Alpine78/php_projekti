<?php
  // Sessio-funktion kutsu
  session_start();
  // Ohjataan käyttäjä kirjautumissivulle, jos sivua yritetään käyttää kirjautumatta
  if (!isset($_SESSION['kirjautunut'])) {
    header("Location:asiakas_login.php");
    exit();
  }
  // Onnistuneen sisäänkirjautumisen jälkeen haetaan tietokannasta asiakastiedot sessioon
  if (isset($_GET["kirjauduttu"])) {
    // Otetaan tietokanta käyttöön
    require_once("db.inc");
    // suoritetaan tietokantakysely ja kokeillaan hakea salasana
    $tunnus = $_SESSION["kirjautunut"];
    $query = "Select * from asiakas WHERE tunnus='$tunnus'";
    $tulos = mysqli_query($conn, $query);
    // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
    if ( !$tulos )
    {
      echo "Kysely epäonnistui " . mysqli_error($conn);
    }
    else {
      // Alustetaan sessiomuuttujat.
      $_SESSION["etunimi"] = "";
      $_SESSION["sukunimi"] = "";
      $_SESSION["puhelin"] = "";
      $_SESSION["email"] = "";
      //käydään läpi löytyneet rivit
      while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
        // Haetaan
        $_SESSION["etunimi"] = $rivi["etunimi"];
        $_SESSION["sukunimi"] = $rivi["sukunimi"];
        $_SESSION["puhelin"] = $rivi["puhelin"];
        $_SESSION["email"] = $rivi["email"];
      }
      // Kirjautuminen on suoritettu loppuun. Asetetaan muokkaustila päälle, jota hyödynnetään mm. rekisteröintisivun sisällän esityksessä.
      $_SESSION["muokkaustila"] = true;
    }
  }
?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Kotitalkkari - Asiakassovellus">
    <meta name="author" content="Ilkka Rytkönen">
    <title>Kotitalkkari</title>
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
          if (isset($_GET["kirjauduttu"])) { ?>
            <div class="alert info">
              <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
              Olet kirjautunut sisään.
            </div>
            <?php
          }
        ?>
          <h2>Työtilaukset</h2>
          <!-- Tässä listataan kirjautuneen asiakkaan kaikki omat työtilaukset. -->
          <?php
          require_once("db.inc");
          // suoritetaan tietokantakysely ja kokeillaan hakea asiakkaan työtilaukset
          $query = "Select * from tyotilaus WHERE tunnus='$tunnus'";
          $tulos = mysqli_query($conn, $query);
          // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
          if ( !$tulos )
          {
            echo "Kysely epäonnistui " . mysqli_error($conn);
          }
          else {
            if (mysqli_num_rows($tulos) == 0) {
              echo "<div class=\"alert alert-warning\" role=\"alert\">Ei löytynyt yhtään työtilausta.<br /></div>";
              echo "<form><button type=\"submit\" class=\"btn btn-primary\" formaction=\"osoitteet.php\" formmethod=\"post\" name=\"toimitusosoite\" value=\"lisaa\">Tee uusi työtilaus</button></form><br />";

            }
            else {

              while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
                // Haetaan
                $tyonkuvaus = $rivi["tyonkuvaus"];
                $tilausPvm = $rivi["tilausPvm"];
                $aloitusPvm = $rivi["aloitusPvm"];
                $valmistumisPvm = $rivi["valmistumisPvm"];
                $hyvaksyttyPvm = $rivi["hyvaksyttyPvm"];
                $hylattyPvm = $rivi["hylattyPvm"];
                $kommentti = $rivi["kommentti"];
                $tyotunnut = $rivi["tyotunnit"];
                $tarvikeselostus = $rivi["tarvikeselostus"];
                $kustannusarvio = $rivi["kustannusarvio"];
              }
            }
          }

          ?>
      </div>
    </main>
    <!-- Ladataan footer ulkopuolisesta tiedostosta -->
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


    require 'footer.php';
    ?>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>  </body>
</html>
