<?php
  // Sessio-funktion kutsu
  session_start();
  // Ohjataan käyttäjä kirjautumissivulle, jos sivua yritetään käyttää kirjautumatta
  if (!isset($_SESSION['kirjautunut'])) {
    header("Location:asiakas_login.php");
    exit();
  }
  // Ladataan sessiosta kirjautunueen asiakkaan tiedot muuttujiin, jotta ne voidaan asettaa lomakkeelle.
  if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != "") {
    $tunnus = $_SESSION["kirjautunut"];
    $salasana = $_SESSION["salasana"];
    $etunimi = $_SESSION["etunimi"];
    $sukunimi = $_SESSION["sukunimi"];
    $puhelin = $_SESSION["puhelin"];
    $email = $_SESSION["email"];
    $muokkaustila = $_SESSION["muokkaustila"];
    // Alustetaan osoitemuuttujat
    $laskutusnimi = "";
    $lahiosoite = "";
    $postinumero ="";
    $postitoimipaikka = "";
    $asunnonTyyppi = "";
    $asunnonAla = "";
    $tontinAla = "";
    $toimitusosoite = false;
    $laskutusosoite = false;
    $uusi = false;
  }
  if (isset($_POST["poista"]) && $_POST["poista"] != "") {
    // Valitun osoitteen poistaminen
    echo "Poistetaan valittu osoite. Voi olla toimitus- tai laskutusosoite.<br>";
  }
  else if (isset($_POST["toimitusosoite"]) && $_POST["toimitusosoite"] == "lisaa") {
    // Uuden toimitusosoitteen lisäys
    echo "Lisätään uusi toimitusosoite<br>";
    $toimitusosoite = true;
    $uusi = true;
    $otsikko = "Lisätään uusi toimitusosoite";
  }
  else if (isset($_POST["toimitusosoite"]) && $_POST["toimitusosoite"] != "") {
    // Toimitusosoitteen muokkaus
    echo "Muokataan toimitusosoitetta.<br>";
    $toimitusosoite = true;
    $osoiteID = $_POST["toimitusosoite"];
    echo "OsoiteID = $osoiteID";
    $otsikko = "Muokataan toimitusosoitetta";
  }
  elseif (isset($_POST["laskutusosoite"]) && $_POST["laskutusosoite"] == "lisaa") {
    // Lisätään laskutusosoitetta.
    echo "Lisätään laskutusosoitetta.<br>";
    $uusi = true;
    $laskutusosoite = true;
    $otsikko = "Lisätään uusi laskutusosoite";
  }
  elseif (isset($_POST["laskutusosoite"]) && $_POST["laskutusosoite"] != "") {
    // Muokataan laskutusosoitetta
    echo "Muokataan laskutusosoitetta<br>";
    $laskutusosoite = true;
    $osoiteID = $_POST["laskutusosoite"];
    echo "OsoiteID = $osoiteID";
    $otsikko = "Muokataan laskutusosoitetta";
  }
  else if (isset($_POST["tallenna"]) && $_POST["tallenna"] != "") {

  }
  else {
    // Jos tälle sivulle tullaan suoraan, ohjataan käyttäjä profiilisivulle
    header("Location:kayttajatiedot.php");
    exit();
  }

?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Osoitteet - Kotitalkkari - Asiakassovellus">
    <meta name="author" content="Ilkka Rytkönen">
    <title>Osoitteet - Kotitalkkari</title>
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
          <h2><?php echo $otsikko ?></h2>

          <?php
          // Otetaan tietokanta käyttöön
          require_once("db.inc");
          // suoritetaan tietokantakysely ja kokeillaan hakea salasana
          $query = "Select * from AsunnonTyyppi";
          $tulos = mysqli_query($conn, $query);
          // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
          if ( !$tulos )
          {
            echo "Kysely epäonnistui " . mysqli_error($conn);
          }
          else {
            //käydään läpi löytyneet rivit ja tallennetaan tiedot taulukkoon
            $asuntotyypit = array();
            while ($rivi = mysqli_fetch_array($tulos, MYSQLI_ASSOC)) {
              // Haetaan
              $asunnonTyyppiID = $rivi["asunnonTyyppiID"];
              $asunnonTyyppi = $rivi["asunnonTyyppi"];
              $asuntotyypit[$asunnonTyyppiID] = $asunnonTyyppi;
            }
          }
          ?>

          <form>
            <div class="form-row">
              <div class="col-md-5 mb-3">
                <label for="validationDefault01"><?php echo ($laskutusosoite) ? 'Laskutusnimi' : 'Asiakas'  ?></label>
                <input type="text" class="form-control" id="validationDefault01" placeholder="Etunimi Sukunimi" value="<?php echo ($laskutusosoite) ? $laskutusnimi . '"' : $etunimi . ' ' . $sukunimi . '" readonly'?> required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="validationDefault02">Lähiosoite</label>
                <input type="text" class="form-control" id="validationDefault02" placeholder="Lähiosoite" value="<?php echo "$lahiosoite"; ?>" required>
              </div>
              <div class="col-md-3 mb-3">
                <label for="validationDefault03">Postinumero</label>
                <input type="number" size="5" min="0" max="99999" maxlength="5" class="form-control" id="validationDefault03" placeholder="Postinumero" value="<?php echo "$postinumero"; ?>" required>
              </div>
            </div>
            <div class="form-row">
              <?php if ($toimitusosoite): ?>
              <div class="col-md-5 mb-3">
                <label for="validationDefault04">Asunnon tyyppi</label>
                <select class="form-control" id="validationDefault04" >
                  <?php foreach ($asuntotyypit as $asunnonTyyppiID => $asunnonTyyppi) {
                    echo "<option value=\"$asunnonTyyppiID\">$asunnonTyyppi</option>";
                  } ?>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="validationDefault05">Asunnon pinta-ala</label>
                <input type="number" class="form-control" id="validationDefault05" placeholder="Asunnon pinta-ala" value="<?php echo "$asunnonAla"; ?>" required>
              </div>
              <div class="col-md-3 mb-3">
                <label for="validationDefault06">Tontin pinta-ala</label>
                <input type="number" class="form-control" id="validationDefault06" placeholder="Tontin pinta-ala" value="<?php echo "$tontinAla"; ?>" required>
              </div>
            <?php endif; ?>
            </div>
            <button class="btn btn-primary" type="submit" formaction="osoitteet.php" formmethod="post" name="tallenna">Tallenna</button>
          </form>
          <form>
          <button class="btn btn-outline-primary" type="submit" formaction="kayttajatiedot.php" formmethod="post" name="osoitteet">Peruuta</button>
          </form>
      <?php
      echo "Post-sisältö: ";
      print_r($_POST);
      echo "<br>Session sisältö: ";
      print_r($_SESSION); ?>
    </div>

    </main>
    <!-- Ladataan footer ulkopuolisesta tiedostosta -->
    <?php require 'footer.php'; ?>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>  </body>
</html>
