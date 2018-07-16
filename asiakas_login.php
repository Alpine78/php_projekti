<?php
  // Sessio-funktion kutsu
  session_start();
  // Katsotaan, onko sessiossa jo kirjautunut käyttäjä ja otetaan tiedot muuttujaan
  if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != "") {
    $tunnus = $_SESSION["kirjautunut"];
  }
// Jos on painettu uloskirjautumispainiketta toisella sivulla, suoritetaan session poisto
  if (isset($_POST["uloskirjaudu"]) && $_POST["uloskirjaudu"] == "ok") {
    session_unset();
    session_destroy();
  }
  // Kokeillaan kirjautua sivustolle
  if (isset($_POST["kirjaudu"])) {
    // Otetaan tietokanta käyttöön
    require_once("db.inc");
    // Otetaan kirjautumislomakkeen tiedot muuttujiin
    $logintunnus = $_POST["tunnus"];
    $loginsalasana = $_POST["salasana"];
    // suoritetaan tietokantakysely ja kokeillaan hakea salasana
    $query = "Select salasana from asiakas WHERE tunnus='$logintunnus'";
    $tulos = mysqli_query($conn, $query);
    // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
    if ( !$tulos )
    {
      echo "Kysely epäonnistui " . mysqli_error($conn);
    }
    else
    {
      // Alustetaan muuttujat. Jos niin ei tehdä, niin siitä suraa virheilmoitus, mikäli salaanaa ei löydy tietokannasta.
      $salasana = "";
      //käydään läpi löytyneet rivit
      while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
        // Haetaan salasana. Tässä ei sen kummempaa tarvita, kun kannassa voi olla vain yksi merkintä yhtä tunnusta kohden
        $salasana = $rivi["salasana"];
      }
      if ($loginsalasana == $salasana) {
        // Onnistunut kirjautuminen, salasanat täsmäävät
        // Tallennetaan kirjautuminn sessioon
        $_SESSION['kirjautunut'] = $logintunnus;
        $_SESSION['salasana'] = $salasana;
        // Ohjataan asiakas asiakasohjelman pääsivulle, jossa kerrotaan onnistuneesta kirjautumisesta
        header("Location:asiakas.php?kirjauduttu");
        exit();
      }
      else {
        // Jos kirjautumisyritys epäonnistuu, ohjataan käyttäjä uudelleen kirjautumissivulle, jossa myös kerrotaan virheestä
        header("Location:asiakas_login.php?virhe");
        exit();
      }
    }
  }
?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Ilkka Rytkönen">
    <title>Kirjaudu sisään - Kotitalkkari</title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link href="login.css" rel="stylesheet">
  </head>
  <body class="text-center">
    <!-- Kirjautumislomake näytetään vain kirjautumattomalle käyttäjälle-->
    <?php if (!isset($_SESSION["kirjautunut"])): ?>
    <form class="form-signin">
      <img class="mb-4" src="https://getbootstrap.com/docs/4.1/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
      <?php
      // Näytetään infoboksi uloskirjautumisen yhteydessä
      if (isset($_POST["uloskirjaudu"]) && $_POST["uloskirjaudu"] == "ok") { ?>
        <div class="alert info">
          <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
          Olet kirjautunut ulos.
        </div>
        <?php
      }
      // Näytetään infoboksi, jos on syötetty väärä käyttäjätunnus ja salasana
      if (isset($_GET["virhe"])) { ?>
        <div class="alert">
          <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
          Väärä käyttäjätunnus tai salasana.
        </div>
        <?php
      }
      ?>
      <h1 class="h3 mb-3 font-weight-normal">Kirjaudu sisään</h1>
      <label for="inputUser" class="sr-only">Käyttäjätunnus</label>
      <input type="text" id="inputUser" name="tunnus" class="form-control" placeholder="Käyttäjätunnus" required autofocus>
      <label for="inputPassword" class="sr-only">Salasana</label>
      <input type="password" id="inputPassword" name="salasana" class="form-control" placeholder="Salasana" required>
      <div class="checkbox mb-3">
        <label>
          <input type="checkbox" value="muista" name="muistaminut"> Muista minut
        </label>
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit" formaction="asiakas_login.php" formmethod="post" name="kirjaudu">Kirjaudu</button>
      <a class="btn btn-primary btn-block" href="kayttajatiedot.php" role="button">Eikö ole tunnuksia?<br />Rekisteröidy</a>
      <p class="mt-5 mb-3 text-muted">&copy; Ilkka Rytkönen 2018</p>
    </form>
    <?php endif; ?>
    <?php if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != ""): ?>
      <form class="form-signin">
        <img class="mb-4" src="https://getbootstrap.com/docs/4.1/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal">Olet jo kirjautunut sisään</h1>
        <button class="btn btn-lg btn-primary btn-block" type="submit" formaction="asiakas_login.php" formmethod="post" name="uloskirjaudu" value="ok">Kirjaudu ulos</button>
        <button class="btn btn-lg btn-primary btn-block" type="submit" formaction="asiakas.php" formmethod="post">Takaisin pääsivulle</button>
        <p class="mt-5 mb-3 text-muted">&copy; Ilkka Rytkönen 2018</p>
      </form>
    <?php endif; ?>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
  </body>
</html>
