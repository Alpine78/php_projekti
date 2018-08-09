<?php
  // Sessio-funktion kutsu
  session_start();
  // Ohjataan käyttäjä kirjautumissivulle, jos sivua yritetään käyttää kirjautumatta
  if (!isset($_SESSION['kirjautunut'])) {
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
    <title>Tarjouspyynnöt - Kotitalkkari</title>
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

          <h2>Tarjouspyynnöt</h2>
          <!-- Tässä listataan kirjautuneen asiakkaan kaikki omat tarjouspyynnöt. -->
          <?php

          // Poistetaan valittu tilaus
          if (isset($_POST["poista"]) && $_POST["poista"] != "") {
            $poistettavaID = $_POST["poista"];
            require_once("db.inc");
            // Create connection
            $connpoista = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
            $connpoista->set_charset("utf8");
            // Check connection
            if (!$connpoista) {
                die("Yhteys epäonnistui: " . mysqli_connect_error());
            }
            // Tehdään kysely
            $query = "DELETE FROM Tarjouspyynto WHERE tarjouspyyntoID = '$poistettavaID'";
              // suoritetaan tietokantakysely ja kokeillaan poistaa valittu tarjouspyyntö
              if (mysqli_query($connpoista, $query)) {
                tulostaSuccess("Onnistui!", "Tarjouspyyntö on nyt onnistuneesti poistettu.");
                mysqli_close($connpoista);
              } else {
                tulostaVirhe("Tarjouksen poistaminen ei onnistunut!<br>" . mysqli_error($connpoista));
                mysqli_close($connpoista);
              }
          }

          // Merkitään tilaus hyväksytyksi ja generoidaan siitä uusi työtilaus
          if (isset($_POST["hyvaksy"]) && $_POST["hyvaksy"] != "") {
            $hyvaksyttavaID = $_POST["hyvaksy"];
            require_once("db.inc");
            // Create connection
            $connhyvaksy = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
            $connhyvaksy->set_charset("utf8");
            // Check connection
            if (!$connhyvaksy) {
                die("Yhteys epäonnistui: " . mysqli_connect_error());
            }
            // Tehdään kysely
            $query = "UPDATE Tarjouspyynto SET hyvaksyttyPvm = NOW() WHERE tarjouspyyntoID = '$hyvaksyttavaID'";
              // suoritetaan tietokantakysely ja kokeillaan hyväksyä valittu tarjouspyyntö
              if (mysqli_query($connhyvaksy, $query)) {
                // Tehdään hyväksytystä tarjouspyynnöstä työtilausta
                require_once("db.inc");
                // Create connection
                $connteetilaus = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
                $connteetilaus->set_charset("utf8");
                // Check connection
                if (!$connteetilaus) {
                    die("Yhteys epäonnistui: " . mysqli_connect_error());
                }
                if (isset($_POST["toimitusosoiteID"]) && $_POST["toimitusosoiteID"] != "") $toimitusosoiteID = $_POST["toimitusosoiteID"];
                if (isset($_POST["laskutusosoiteID"]) && $_POST["laskutusosoiteID"] != "") $laskutusosoiteID = $_POST["laskutusosoiteID"];
                if (isset($_POST["tyonkuvaus"]) && $_POST["tyonkuvaus"] != "") $tyonkuvaus = $_POST["tyonkuvaus"];
                if (isset($_POST["kustannusarvio"]) && $_POST["kustannusarvio"] != "") $kustannusarvio = $_POST["kustannusarvio"];
                // Tehdään kysely
                $query = "INSERT INTO tyotilaus (toimitusosoiteID, laskutusosoiteID, tyonkuvaus, kustannusarvio) VALUES
                  ('$toimitusosoiteID', '$laskutusosoiteID', '$tyonkuvaus', '$kustannusarvio')";
                  // suoritetaan tietokantakysely ja kokeillaan hyväksyä valittu tarjouspyyntö
                  if (mysqli_query($connteetilaus, $query)) {
                    tulostaSuccess("Onnistui!", "Tarjous on siirretty onnistuneesti työtilaukseksi.");
                    mysqli_close($connteetilaus);
                  } else {
                    tulostaVirhe("Tarjouksen siirtäminen työtilaukseksi ei onnistunut!<br>" . mysqli_error($connteetilaus));
                    mysqli_close($connteetilaus);
                  }

                // työtilauksen teko loppuu tähän
                tulostaSuccess("Onnistui!", "Tarjouspyyntö on nyt onnistuneesti merkattu hyväksytyksi.");
                mysqli_close($connhyvaksy);
              } else {
                tulostaVirhe("Tarjouspyynnön hyväksyminen ei onnistunut!<br>" . mysqli_error($connhyvaksy));
                mysqli_close($connhyvaksy);
              }
          }

          // Merkitään tilaus hylätyksi
          if (isset($_POST["hylkaa"]) && $_POST["hylkaa"] != "") {
            $hylattavaID = $_POST["hylkaa"];
            require_once("db.inc");
            // Create connection
            $connhyvaksy = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
            $connhyvaksy->set_charset("utf8");
            // Check connection
            if (!$connhyvaksy) {
                die("Yhteys epäonnistui: " . mysqli_connect_error());
            }
            // Tehdään kysely
            $query = "UPDATE Tarjouspyynto SET hylattyPvm = NOW() WHERE tarjouspyyntoID = '$hylattavaID'";
              // suoritetaan tietokantakysely ja kokeillaan hyväksyä valittu tarjouspyyntö
              if (mysqli_query($connhyvaksy, $query)) {
                tulostaSuccess("Onnistui!", "Työtilaus on nyt onnistuneesti merkattu hylätyksi.");
                mysqli_close($connhyvaksy);
              } else {
                tulostaVirhe("Työtilauksen hylkääminen ei onnistunut!<br>" . mysqli_error($connhyvaksy));
                mysqli_close($connhyvaksy);
              }
          }

          // Katsotaan, onko asiakkaalla sekä laskutus-, että toimitusosoitteet ja näytetään sen mukaan sisältöä.
          require("onkoOsoitetta.inc");

          if (isset($_SESSION["onToimitusosoite"]) && $_SESSION["onToimitusosoite"] == true && isset($_SESSION["onLaskutusosoite"]) && $_SESSION["onLaskutusosoite"] == true) {
            // Tämä sisältö näytetään, jos asiakkaalla on molempia osoitetyyppejä.
            require_once("db.inc");
            // suoritetaan tietokantakysely ja kokeillaan hakea asiakkaan tarjouspyynnöt
            $query = "Select * from tarjousnakyma WHERE tunnus='$tunnus'";
            $tulos = mysqli_query($conn, $query);
            // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
            if ( !$tulos )
            {
              echo "Kysely epäonnistui " . mysqli_error($conn);
            }
            else {
              if (mysqli_num_rows($tulos) == 0) {
                // Jos yhtään työtilausta ei ole, näytetään asiasta ilmoitus
                echo "<div class=\"alert alert-warning\" role=\"alert\">Ei löytynyt yhtään tarjouspyyntöä.<br /></div>";
              }
              else {
                date_default_timezone_set("Europe/Helsinki");
                // Muuttujien alustus
                $kuvaus = "";
                $jattoPvm = "";
                $lahiosoite = "";
                $asunnonTyyppi = "";
                $status = "";
                echo "<table class=\"table\"><thead><tr><th scope=\"col\">Kuvaus</th><th scope=\"col\">Jättöpvm</th><th scope=\"col\">Toimitusosoite</th><th scope=\"col\">Asunnon tyyppi</th><th scope=\"col\">Status</th><th scope=\"col\"></th><th scope=\"col\"></th></tr></thead><tbody>";
                while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
                  // Haetaan tilausnäkymästä tilaukset
                  $tarjouspyyntoID = $rivi["tarjouspyyntoID"];
                  $kuvaus = $rivi["kuvaus"];
                  $jattoPvm = date("d.m.Y",strtotime($rivi["jattoPvm"]));
                  $lahiosoite = $rivi["lahiosoite"];
                  $asunnonTyyppi = $rivi["asunnonTyyppi"];
                  $status = $rivi["status"];
                  echo "<tr><td>$kuvaus</td><td>$jattoPvm</td><td>$lahiosoite</td><td>$asunnonTyyppi</td><td>
                  <span class=\"";
                  if ($status == "jätetty") echo "badge badge-success";
                  else if ($status == "vastattu") echo "badge badge-warning";
                  else if ($status == "hyväksytty") echo "badge badge-secondary";
                  else if ($status == "hylätty") echo "badge badge-danger";
                    echo "\">$status</span></td>
                    <td>";
                    if ($status == "jätetty") {
                      echo "<form><button type=\"submit\" class=\"btn btn-success btn-sm\" formaction=\"tarjouspyynto.php\" formmethod=\"post\" name=\"muokkaa\" value=\"$tarjouspyyntoID\">Muokkaa</button></form>";}
                      else if ($status == "vastattu") {
                        echo "<form><button type=\"submit\" class=\"btn btn-primary btn-sm\" formaction=\"tarjouspyynto.php\" formmethod=\"post\" name=\"hyvaksy\" value=\"$tarjouspyyntoID\">Hyväksy</button></form>";}
                      else {
                        echo "<form><button type=\"submit\" class=\"btn btn-info btn-sm\" formaction=\"tarjouspyynto.php\" formmethod=\"post\" name=\"nayta\" value=\"$tarjouspyyntoID\">Näytä</button></form>";
                      }
                    echo "</td><td>";
                    if ($status == "jätetty") {
                      echo "<form><button type=\"submit\" class=\"btn btn-danger btn-sm\" formaction=\"tarjouspyynto.php\" formmethod=\"post\" name=\"poista\" value=\"$tarjouspyyntoID\">Poista</button></form>";}
                    else if ($status == "vastattu") {
                        echo "<form><button type=\"submit\" class=\"btn btn-danger btn-sm\" formaction=\"tarjouspyynto.php\" formmethod=\"post\" name=\"hyvaksy\" value=\"$tarjouspyyntoID\">Hylkää</button></form>";
                      }
                    echo "</td></tr>";
                }
                echo "</tbody></table>";
              }
            }
            echo "<form><button type=\"submit\" class=\"btn btn-primary\" formaction=\"tarjouspyynto.php\" formmethod=\"post\">Tee uusi tarjouspyyntö</button></form><br />";
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
