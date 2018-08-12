<?php
  // Sessio-funktion kutsu
  session_start();
  // Muuttujien alustukset
  $otsikko = "Salasanan resetointi";
  $haku = "";

  if (isset($_POST["haku"]) && $_POST["haku"] != "") {
    $haku = $_POST["haku"];
  }

  if (isset($_POST["nollaa"]) && $_POST["nollaa"] != "") {
    $otsikko = "Haluatko varmasti nollata salasanan?";
  }
  if (isset($_POST["nollaus"]) && $_POST["nollaus"] == "ok") {
    $otsikko = "Salasana nollataan";
  }

?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Kotitalkkari - Kiiteistöhuoltofirma">
    <meta name="author" content="Ilkka Rytkönen">
    <title>Salasanan resetointi - Kotitalkkari</title>
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
          if (!isset($_POST["nollaa"])) {
          ?>
          <form>
            <div class="form-group">
              <label for="formGrouptunnus">Haettava tunnus, nimi tai email</label>
              <input type="text" class="form-control" id="formGrouptunnus" name="haku" value="<?php echo $haku ?>" placeholder="Nimi, tunnus tai email" required>
            </div>
            <button type="submit" class="btn btn-primary" formmethod="post" name="hae">Hae</button>
          </form>

          <?php

          if (isset($_POST["haku"]) && $_POST["haku"] != "") {
            require_once("db.inc");
            // suoritetaan tietokantakysely ja kokeillaan hakea kaikki työtilaukset
            $query = "SELECT * FROM asiakas WHERE tunnus LIKE '%$haku%' OR etunimi LIKE '%$haku%' OR sukunimi LIKE '%$haku%' OR email LIKE '%$haku%'";
            $tulos = mysqli_query($conn, $query);
            // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
            if ( !$tulos )
            {
              echo "Kysely epäonnistui " . mysqli_error($conn);
            }
            else {
              if (mysqli_num_rows($tulos) == 0) {
                // Jos yhtään työtilausta ei ole, näytetään asiasta ilmoitus
                echo "<div class=\"alert alert-warning\" role=\"alert\">Ei löytynyt yhtään asiakasta.<br /></div>";
              }
              else {
                date_default_timezone_set("Europe/Helsinki");
                // Muuttujien alustus
                $tunnus = "";
                $nimi = "";
                $email = "";
                echo "<table class=\"table\"><thead><tr><th scope=\"col\">Tunnus</th><th scope=\"col\">Nimi</th><th scope=\"col\">Email</th></tr></thead><tbody>";
                while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
                  // Haetaan tilausnäkymästä tilaukset
                  $tunnus = $rivi["tunnus"];
                  $nimi = $rivi["etunimi"];
                  $nimi .= " ";
                  $nimi .= $rivi["sukunimi"];
                  $email = $rivi["email"];
                  echo "<tr><td>$tunnus</td><td>$nimi</td><td>$email</td><td>
                  <form>
                  <input type=\"hidden\" name=\"nimi\" value=\"$nimi\">
                  <button type=\"submit\" class=\"btn btn-danger btn-sm\" formmethod=\"post\" name=\"nollaa\" value=\"$tunnus\">Nollaa salasana</button></form></td></tr>";
                }
                echo "</tbody></table>";
              }
            }
          }
        }
        else {
          // tänne
          // Tässä näytetään sisältä ensimmäisen nollauspainikkeen jälkeen
          if (!isset($_POST["nollaus"])) {
            $tunnus = $_POST["nollaa"];
            $nimi = $_POST["nimi"];
            echo "<p>Olet nollaamassa tunnuksen <strong>$tunnus</strong>, käyttäjän <strong>$nimi</strong> salasanaa. Tätä toimenpidettä ei voi perua.</p>";
            echo "<form>
            <input type=\"hidden\" name=\"nimi\" value=\"$nimi\">
            <input type=\"hidden\" name=\"nollaus\" value=\"ok\">
            <button type=\"submit\" class=\"btn btn-danger\" formmethod=\"post\" name=\"nollaa\" value=\"$tunnus\">Nollaa salasana</button>&nbsp;&nbsp;
            <button type=\"submit\" class=\"btn btn-outline-primary\" formmethod=\"post\" name=\"peruuta\">Peruuta</button>
            </form>";
          }
          else {
            // Suoritetaan nollauskoodi
            $tunnus = $_POST["nollaa"];
            $uusisalasana = generateRandomString();
            vaihdasalasana($tunnus, $uusisalasana);
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

    function generateRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function vaihdasalasana($tunnus, $uusisalasana) {
  require_once("db.inc");
  // suoritetaan tietokantakysely ja kokeillaan päivittää salasana
  $query = "UPDATE Asiakas SET salasana='$uusisalasana' WHERE tunnus='$tunnus'";
  $tulos = mysqli_query($conn, $query);
  // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
  if ( !$tulos )
  {
    tulostaVirhe("Salasanan päivitys epäonnistui " . mysqli_error($conn));
    return false;
  }
  else {
    tulostaSuccess("Onnistui!", "Salasana on onnistuneesti vaihdettu.<br >Uusi salasana on: <strong>$uusisalasana</strong>");
    $_SESSION["salasana"] = $uusisalasana;
    return true;
  }
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
