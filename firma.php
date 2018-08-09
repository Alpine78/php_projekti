<?php
  // Sessio-funktion kutsu
  session_start();
  // Muuttujien alustukset
  $otsikko = "Kaikki työtilaukset";
?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Kotitalkkari - Kiiteistöhuoltofirma">
    <meta name="author" content="Ilkka Rytkönen">
    <title>Työtilaukset - Kotitalkkari</title>
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

          <h2><?php echo $otsikko ?></h2>
          <!-- Tässä listataan asiakkaiden työtilaukset. -->
          <?php

            require_once("db.inc");
            // suoritetaan tietokantakysely ja kokeillaan hakea kaikki työtilaukset
            $query = "SELECT * FROM firmantyotilaukset WHERE NOT status = 'hylätty'";
            $tulos = mysqli_query($conn, $query);
            // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
            if ( !$tulos )
            {
              echo "Kysely epäonnistui " . mysqli_error($conn);
            }
            else {
              if (mysqli_num_rows($tulos) == 0) {
                // Jos yhtään työtilausta ei ole, näytetään asiasta ilmoitus
                echo "<div class=\"alert alert-warning\" role=\"alert\">Ei löytynyt yhtään työtilausta.<br /></div>";
              }
              else {
                date_default_timezone_set("Europe/Helsinki");
                // Muuttujien alustus
                $tyotilausID = "";
                $kuvaus = "";
                $tilausPvm = "";
                $tyotunnut = "";
                $kustannusarvio = "";
                $nimi = "";
                $postitoimipaikka = "";
                $asunnonTyyppi = "";
                $status = "";
                echo "<table class=\"table\"><thead><tr><th scope=\"col\">Työnkuvaus</th><th scope=\"col\">Tilauspvm</th><th scope=\"col\">Työtunnit</th><th scope=\"col\">Kustannusarvio</th><th scope=\"col\">Tilaaja</th><th scope=\"col\">postitoimipaikka</th><th scope=\"col\">Asunnon tyyppi</th><th scope=\"col\">Status</th><th scope=\"col\"></th></tr></thead><tbody>";
                while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
                  // Haetaan tilausnäkymästä tilaukset
                  $tyotilausID = $rivi["tyotilausID"];
                  $kuvaus = $rivi["kuvaus"];
                  $pvm = strtotime($rivi["tilausPvm"]);
                  $tyotunnut = $rivi["tyotunnit"];
                  $tilausPvm = date("d.m.Y",$pvm);
                  $nimi = $rivi["nimi"];
                  $kustannusarvio = $rivi["kustannusarvio"];
                  $postitoimipaikka = $rivi["postitoimipaikka"];
                  $asunnonTyyppi = $rivi["asunnonTyyppi"];
                  $status = $rivi["status"];
                  echo "<tr><td>$kuvaus</td><td>$tilausPvm</td><td>$tyotunnut</td><td>$kustannusarvio</td><td>$nimi</td><td>$postitoimipaikka</td><td>$asunnonTyyppi</td><td>
                  <span class=\"";
                  if ($status == "tilattu") echo "badge badge-success";
                  else if ($status == "aloitettu") echo "badge badge-warning";
                  else if ($status == "valmis") echo "badge badge-primary";
                  else if ($status == "hyväksytty") echo "badge badge-secondary";
                  else if ($status == "hylätty") echo "badge badge-danger";
                    echo "\">$status</span></td>";
                  echo "<td><form><button type=\"submit\" class=\"btn btn-primary btn-sm\" formaction=\"firmantyotilaus.php\" formmethod=\"post\" name=\"nayta\" value=\"$tyotilausID\">Näytä</button></form>";
                    echo "</td></tr>";
                }
                echo "</tbody></table>";
              }
            }
            //echo "<form><button type=\"submit\" class=\"btn btn-primary\" formaction=\"tyotilaus.php\" formmethod=\"post\">Jätä uusi työtilaus</button></form><br />";
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
