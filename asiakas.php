<?php
  // Sessio-funktion kutsu
  session_start();
?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Kotitalkkari</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

    <link href="style.css" rel="stylesheet">
  </head>

  <body>

    <?php
    // Otetaan tietokanta käyttöön
    require_once("db.inc");

    // Tarkistetaan, ollaanko kirjautuneena tai kirjautumassa ja näytetään sen mukaan sisältöä
    if (isset($_POST["kirjaudu"])) {
      // Otetaan kirjautumislomakkeen tiedot muuttujiin
      $logintunnus = $_POST["tunnus"];
      $loginsalasana = $_POST["salasana"];
      echo "<P>Kokeillaan kirjautua</p>";
      // suoritetaan kysely
	    $query = "Select tunnus, salasana from asiakas WHERE tunnus='$logintunnus'";
      echo "<br />Kysely $query<br />";
      $tulos = mysqli_query($conn, $query);

      // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
      if ( !$tulos )
      {
        echo "Kysely epäonnistui " . mysqli_error($conn);
      }
      else
      {
        // 2 seuraavaa riviä tulostetaan vain TESTI-mielessä!
        echo "<p>Haettiin seuraavat asiakkaat, yhteensä " . mysqli_num_rows($tulos) .  " kpl</p>\n";
        echo "<p>Kenttiä oli " . mysqli_num_fields($tulos) . "<p>\n";

        // Alustetaan muuttujat. Jos niin ei tehdä, niin siitä suraa virheilmoitus, mikäli tunnusta tai salaanaa ei löydy tietokannasta.
        $tunnus = "";
        $salasana = "";

        //käydään läpi löytyneet rivit
        echo "<table><tr><th>Tunnus</th><th>Salasana</th></tr>";
        while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
          //haetaan nimi, hinta ja määrä muuttujiin
          $tunnus =  $rivi["tunnus"];
          $salasana = $rivi["salasana"];
          //tulostetaan taulukon rivi
          echo "<tr><td>$tunnus</td><td>$salasana</td></tr>";
        }
        echo "</table>";
        echo "Lomakkeella syötetty $logintunnus ja $loginsalasana.<br/>";
        if ($loginsalasana == $salasana) {
          // Onnistunut kirjautuminen, salasanat täsmäävät
          echo "<h3>Onnea! Olet kirjautunut sisään</h3>";
          $_SESSION['kirjautunut'] = $tunnus;
        }
        else {
          echo "<h3>Valitettavasti kirjautuminen epäonnistui! Tunnus tai salasana on väärin.</h3>";
        }
      }
    }
    else {
      echo "<P>Nyt ei tulla kirjautumissivulta. Jos ollaan kirjauduttu, niin näytetään kirjautuneen asiakkaat aloitussivu.</p>";
      if ($_SESSION['kirjautunut'] == "") {
        echo "<meta http-equiv=\"refresh\" content=\"0;URL='asiakas_login.php'\" /> ";
      }
      }
    ?>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href=".">Kotitalkkari</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="#">Asiakassovellus<span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Kiinteistöhuoltofirman sovellus</a>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>

    <main role="main" class="container">

      <div class="starter-template">
        <h1>Kotitalkkarin asiakassovellus</h1>




      </div>

    </main>

  <footer class="footer">
  <div class="container">
    <span class="text-muted">&copy; Ilkka Rytkönen 2018</span>
  </div>
  </footer>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>  </body>
</html>
