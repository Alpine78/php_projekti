<?php
  // Sessio-funktion kutsu
  session_start();
  date_default_timezone_set("Europe/Helsinki");
  // Alustetaan muuttujat
  $query = "SELECT * FROM firmantarjouspyynnot";

  $otsikko = "Kaikki tarjouspyynnöt";
  $nimi = "";
  $status = "";
  $jattoPvm = "";

  if (isset($_POST["haku"])) {
    $otsikko = "Haun mukaan rajatut tarjouspyynnöt";
    // Tehdään hakuun sopiva Tietokantakysely
    //$query = "SELECT * FROM firmantarjouspyynnot WHERE ";
    $laskuri = 0;

      if (isset($_POST["nimi"]) && $_POST["nimi"] != "") {
        $query .= " WHERE ";
        $laskuri++;
        $nimi = $_POST["nimi"];
        $query .= " nimi LIKE '%$nimi%'";
      }
      if (isset($_POST["status"]) && $_POST["status"] != "kaikki") {
        if ($laskuri == 0) $query .= " WHERE ";
        if ($laskuri > 0) $query .= " AND ";
        $laskuri++;
        $status = $_POST["status"];
        $query .= " status = '$status'";
      }
      if (isset($_POST["jattoPvm"]) && $_POST["jattoPvm"] != "") {
        if ($laskuri == 0) $query .= " WHERE ";
        if ($laskuri > 0) $query .= " AND ";
        $laskuri++;
        $jattoPvm = $_POST["jattoPvm"];
        $query .= " jattoPvm >= '$jattoPvm'";
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
    <title>Tarjouspyynnöt - Kotitalkkari - Kiinteistöhuoltofirman sovellus</title>
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
          <!-- Tässä listataan kirjautuneen asiakkaan kaikki omat tarjouspyynnöt. -->
          <form>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="inputAsiakas">Asiakas</label>
                <input type="text" name="nimi" value="<?php echo $nimi ?>" class="form-control" id="inputAsiakas" placeholder="Etunimi, sukunimi tai molemmat" aria-describedby="nimivihjeteksti">
                <small id="nimivihjeteksti" class="form-text text-muted">
                  Voit hakea nimen osalla. Esim. "ytkö" hakee kaikki nimet, joissa ko. teksti on jossain kohdassa.
                </small>
              </div>
              <div class="form-group col-md-3">
                <label for="inputdate">Jättöpäivämäärä</label>
                <input type="date" name="jattoPvm" value="<?php echo $jattoPvm ?>" class="form-control" id="inputdate" aria-describedby="passwordHelpBlock">
                <small id="passwordHelpBlock" class="form-text text-muted">
                  Päivämäärähaussa on oltava valittuna sekä pp, kk, että vvvv. Jos et halua päivämäärää hakuun, pyyhi kaikki päivämääräkentät tyhjiksi.
                </small>
              </div>
              <div class="form-group col-md-3">
                <div class="form-group">
                  <label for="inputStatus">Tilauksen status</label>
                  <select class="form-control" name="status" id="inputStatus">
                    <option value="kaikki" <?php if ($status == 'kaikki') echo "selected"; ?>>Kaikki</option>
                    <option value="jätetty" <?php if ($status == 'jätetty') echo "selected"; ?>>Jätetty</option>
                    <option value="vastattu" <?php if ($status == 'vastattu') echo "selected"; ?>>Vastattu</option>
                    <option value="hyväksytty" <?php if ($status == 'hyväksytty') echo "selected"; ?>>Hyväksytty</option>
                    <option value="hylätty" <?php if ($status == 'hylätty') echo "selected"; ?>>Hylätty</option>
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary" formmethod="post" name="haku">Hae</button>&nbsp;
            <button type="submit" class="btn btn-outline-primary" formmethod="post">Nollaa haku</button>
          </form>
          <br />

          <?php

            require_once("db.inc");
            // suoritetaan tietokantakysely ja kokeillaan hakea tarjouspyynnöt hakuehtojen mukaan
            // Kyselylause on muodostettu jo aikaisemmassa vaiheessa
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
                $tarjouspyyntoID = "";
                $tunnus = "";
                $nimi = "";
                $postitoimipaikka = "";
                $asunnonTyyppi = "";
                $kuvaus = "";
                $jattoPvm = "";
                $status = "";
                echo "<table class=\"table\"><thead><tr><th scope=\"col\">Tilaaja</th><th scope=\"col\">Postitoimipaikka</th><th scope=\"col\">Asunnontyyppi</th><th scope=\"col\">Työnkuvaus</th><th scope=\"col\">Jättöpäivämäärä</th><th scope=\"col\">Status</th><th scope=\"col\"></th></tr></thead><tbody>";
                while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
                  // Haetaan tilausnäkymästä tilaukset
                  $tarjouspyyntoID = $rivi["tarjouspyyntoID"];
                  $tunnus = $rivi["tunnus"];
                  $nimi = $rivi["nimi"];
                  $postitoimipaikka = $rivi["postitoimipaikka"];
                  $asunnonTyyppi = $rivi["asunnonTyyppi"];
                  $kuvaus = $rivi["kuvaus"];
                  $jattoPvm = date("d.m.Y",strtotime($rivi["jattoPvm"]));
                  $status = $rivi["status"];
                  echo "<tr><td>$nimi</td><td>$postitoimipaikka</td><td>$asunnonTyyppi</td><td>$kuvaus</td><td>$jattoPvm</td><td>
                  <span class=\"";
                  if ($status == "jätetty") echo "badge badge-success";
                  else if ($status == "vastattu") echo "badge badge-warning";
                  else if ($status == "hyväksytty") echo "badge badge-secondary";
                  else if ($status == "hylätty") echo "badge badge-danger";
                    echo "\">$status</span></td>
                    <td>";
                    echo "<form><button type=\"submit\" class=\"btn btn-primary btn-sm\" formaction=\"tarjouspyynto.php\" formmethod=\"post\" name=\"nayta\" value=\"$tarjouspyyntoID\">Näytä</button></form>";
                    echo "</td></tr>";
                }
                echo "</tbody></table>";
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
