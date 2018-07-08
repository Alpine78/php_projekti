<?php
  // Sessio-funktion kutsu
  session_start();
  // Katsotaan, onko sessiossa jo kirjautunut käyttäjä ja otetaan tiedot muuttujaan
  if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != "") {
    $tunnus = $_SESSION["kirjautunut"];
  }
  else {
    // Jos sessiossa ei ollut mitää, alustetaan sessiomuuttuja
    $_SESSION["kirjautunut"] = "";
  }
  // Otetaan tietokanta käyttöön
  require_once("db.inc");
  // Jos tälle sivulle tullaan kirjautumissivulta, tehdään tarvittavat asiat tässä
  if (isset($_POST["kirjaudu"])) {
    // Otetaan kirjautumislomakkeen tiedot muuttujiin
    $tunnus = $_POST["tunnus"];
    $loginsalasana = $_POST["salasana"];
    // suoritetaan tietokantakysely ja kokeillaan hakea salasana
    $query = "Select * from asiakas WHERE tunnus='$tunnus'";
    $tulos = mysqli_query($conn, $query);
    // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
    if ( !$tulos )
    {
      echo "Kysely epäonnistui " . mysqli_error($conn);
    }
    else
    {
      // Alustetaan muuttujat. Jos niin ei tehdä, niin siitä suraa virheilmoitus, mikäli tunnusta tai salaanaa ei löydy tietokannasta.
      $salasana = "";
      //käydään läpi löytyneet rivit
      while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
        // Haetaan salasana. Tässä ei sen kummempaa tarvita, kun kannassa voi olla vain yksi merkintä yhtä tunnusta kohden
        $salasana = $rivi["salasana"];
      }
      if ($loginsalasana == $salasana) {
        // Onnistunut kirjautuminen, salasanat täsmäävät
        // Tallennetaan kirjautuminn sessioon
        $_SESSION['kirjautunut'] = $tunnus;
        // Tallennetaan asiakastiedot 
      }
      else {
        // Jos kirjautumisyritys epäonnistuu, ohjataan käyttäjä uudelleen kirjautumissivulle, jossa myös kerrotaan virheestä
        //echo "<meta http-equiv=\"refresh\" content=\"0;URL='asiakas_login.php?virhe'\" /> ";
        header("Location:asiakas_login.php?virhe");
        // Headeria ei voitu kirjoittaa kahteen kertaan, vaan seurasi virheilmoitus. Siksi meta-refresh.
    		exit();
      }
    }
  }
  // Ohjataan käyttäjä kirjautumissivulle, jos sivua yritetään käyttää kirjautumatta
  if (isset($_SESSION['kirjautunut']) && $_SESSION['kirjautunut'] == "") {
    //echo "<meta http-equiv=\"refresh\" content=\"0;URL='asiakas_login.php'\" /> ";
    header("Location:asiakas_login.php");
    exit();
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
        <?php if (isset($_SESSION['kirjautunut']) && $_SESSION['kirjautunut'] != "") {
          // Tarkistetaan, ollaanko kirjautuneena tai kirjautumassa ja näytetään sen mukaan sisältöä ?>
          <h2>Kirjautuneen asiakkaan sisältöä</h2>
          <!-- Tässä listataan kirjautuneen asiakkaan kaikki omat työtilaukset. -->
        <?php } ?>
      </div>
    </main>
<?php require 'footer.php'; ?>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>  </body>
</html>
