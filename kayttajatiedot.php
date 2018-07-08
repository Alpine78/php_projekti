<?php
  // Sessio-funktion kutsu
  session_start();
  // Katsotaan, onko sessiossa jo kirjautunut käyttäjä ja otetaan tiedot muuttujaan
  if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != "") {
    $tunnus = $_SESSION["kirjautunut"];
    // Otetaan tietokanta käyttöön
    require_once("db.inc");
    // Haetaan tietokannasta asiakastiedot
    $kysely = "SELECT * FROM asiakas WHERE tunnus='$tunnus'";
    //suoritetaan kysely
    $haku = mysqli_query($conn, $kysely);
    if ( !$haku )
    {
        echo "Kysely epäonnistui " . mysqli_error($conn);
    }
    else
    {
      //käydään tietueet läpi
      while ($rivi = mysqli_fetch_array($haku, MYSQLI_ASSOC)) {
        //haetaan avain, nimi ja osoite muuttujiin
        $etunimi = $rivi["etunimi"];
        $sukunimi = $rivi["sukunimi"];
        $osoiteID = $rivi["osoiteID"];
        $puhelin = $rivi["puhelin"];
        $email = $rivi["email"];
        }
    }
  }
  else {
    // Jos sessiossa ei ollut mitää, alustetaan sessiomuuttuja
    $_SESSION["kirjautunut"] = "";
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
    // Ladataan päävalikko ulkopuolisesta tiedostosta.
    // Menu on erilainen kirjautuneelle ja kirjautumattomalle käyttäjälle.
    if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != "") {
      require 'asiakasmenu.php';
    }
    else {
      // Kirjautumatton käyttäjän menu
      require 'perusmenu.php';
    }
    ?>
    <main role="main" class="container">
      <div class="starter-template">
        <h1>Kotitalkkarin asiakassovellus</h1>
        <h2>Käyttäjän tiedot lomakkeella</h>
          <!-- Rekisteröinti tai tietojen muutos -->
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
