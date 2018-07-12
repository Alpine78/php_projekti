<?php
  // Sessio-funktion kutsu
  session_start();
  // Ladataan sessiosta kirjautunueen asiakkaan tiedot muuttujiin, jotta ne voidaan asettaa lomakkeelle.
  if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != "") {
    echo "Tallennetaan session tiedot muuttujiin<br>";
    $tunnus = $_SESSION["kirjautunut"];
    $salasana = $_SESSION["salasana"];
    $etunimi = $_SESSION["etunimi"];
    $sukunimi = $_SESSION["sukunimi"];
    $puhelin = $_SESSION["puhelin"];
    $email = $_SESSION["email"];
    $muokkaustila = $_SESSION["muokkaustila"];
  }
  else {
    // Jos tullaan rekisteöitymään, niin muokkaustila ei ole päällä
    $muokkaustila = false;
  }
  if (isset($_POST["muokkaa"]) && $_POST["muokkaa"] == "rekisteroidy" || isset($_POST["muokkaa"]) && $_POST["muokkaa"] == "tallenna") {
    $tunnus = $_POST["tunnus"];
    $etunimi = $_POST["etunimi"];
    $sukunimi = $_POST["sukunimi"];
    $puhelin = $_POST["puhelin"];
    $email = $_POST["email"];
  }
  if (isset($_POST["muokkaa"]) && $_POST["muokkaa"] == "tallenna") {
      // Tarkistetaan ja päivitetään muuttuneet tiedot tietokantaan
      $tulos = tarkistamuutos($etunimi, $sukunimi, $puhelin, $email, $errorText);
      if ( $tulos == true )
      {
        echo "Tallennetaan muutokset<br>";
      }
      else
      {
        tulostaVirhe($errorText);
      }
  }
    else if (isset($_POST["muokkaa"]) && $_POST["muokkaa"] == "rekisteroidy") {
      $salasana = $_POST["salasana"];
      $salasana2 = $_POST["salasana2"];
      $tulos = tarkistarekisterointi($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana, $salasana2, $errorText);
      if ( $tulos == true )
      {
        echo "Rekistereöidytään<br>";
      }
      else
      {
        tulostaVirhe($errorText);
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
          <!-- Rekisteröinti tai tietojen muutos -->
          <form>
            <div class="form-row">
              <div class="col-md-4 mb-3">
                <label for="validationDefaultUsername">Käyttäjätunnus</label>
                <div class="input-group">
                <input type="text" <?php if ($muokkaustila) echo "readonly"; ?> class="form-control" id="validationDefaultUsername" placeholder="Käyttäjätunnus" name="tunnus" value="<?php if ($muokkaustila) echo "$tunnus"; ?>" required>
                </div>
              </div>
              <div class="col-md-4 mb-3">
                <label for="validationDefault01">Etunimi</label>
                <input type="text" class="form-control" id="validationDefault01" placeholder="Etunimi" name="etunimi" value="<?php if ($muokkaustila) echo "$etunimi"; ?>" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="validationDefault02">Sukunimi</label>
                <input type="text" class="form-control" id="validationDefault02" placeholder="Sukunimi" name="sukunimi" value="<?php if ($muokkaustila) echo "$sukunimi"; ?>" required>
              </div>
            </div>
            <div class="form-row">
              <div class="col-md-6 mb-2">
                <label for="validationDefault03">Puhelin</label>
                <input type="text" class="form-control" id="validationDefault03" placeholder="Puhelin" name="puhelin" value="<?php if ($muokkaustila) echo "$puhelin"; ?>" required>
              </div>
              <div class="col-md-6 mb-2">
                <label for="validationDefault04">Email</label>
                <input type="email" class="form-control" id="validationDefault04" placeholder="Email" name="email" value="<?php if ($muokkaustila) echo "$email"; ?>" required>
              </div>
            </div>
            <?php if (!$muokkaustila) { ?>
            <div class="form-row">
              <div class="col-md-6 mb-2">
                <label for="validationDefault05">Salasana</label>
                <input type="password" class="form-control" id="validationDefault05" placeholder="Salasana" name="salasana" required>
              </div>
              <div class="col-md-6 mb-2">
                <label for="validationDefault06">Salasana uudelleen</label>
                <input type="password" class="form-control" id="validationDefault06" placeholder="Salasana uudelleen" name="salasana2" required>
              </div>
            </div>
            <button class="btn btn-primary" type="submit" formaction="kayttajatiedot.php" formmethod="post" name="muokkaa" value="rekisteroidy">Rekisteröidy</button>
            <?php }
            else { ?>
              <button class="btn btn-primary" type="submit" formaction="kayttajatiedot.php" formmethod="post" name="muokkaa" value="tallenna">Tallenna muutokset</button>
            <?php } ?>
          </form>
          <form>
            <br/>
          <button class="btn btn-primary" type="submit" formaction="asiakas.php" formmethod="post">Peruuta</button>
          </form>
      </div>
      <?php
      print_r($_POST);
      print_r($_SESSION); ?>

    </main>
<?php
function tarkistamuutos($etunimi, $sukunimi, $puhelin, $email, &$errorText) {
  // Suoritetaan virhetarkistus lomakkeen tiedoille. Osa on tosin eliminoitu jo lomakkeen lähetyksessä, joten
  // tämä on osin vain varmistus.
  echo "Tarkistetaan tietojen muutos<br>";
  $retcode = true;
  $errorText = "";

  if ( $etunimi == "" )
  {
    $errorText .= " Etunimi puuttuu, se on pakollinen tieto.<br>";
    $retcode = false;
  }

  if ( $sukunimi == "" )
  {
    $errorText .= " Sukunimi puuttuu, se on pakollinen tieto.<br>";
    $retcode = false;
  }

  if ( $puhelin == "" )
  {
    $errorText .= " Puhelinnumero puuttuu, se on pakollinen tieto.<br>";
    $retcode = false;
  }

  if ( $email == "" )
  {
    $errorText .= " Sähköpoistiosoite puuttuu, se on pakollinen tieto.<br>";
    $retcode = false;
  }
return $retcode;
}

function tarkistarekisterointi($tunnus, $etunimi, $sukunimi, $puhelin, $email, $salasana, $salasana2, &$errorText) {
  echo "Tarkistetaan rekisteröinti<br>";
  // hyödynnetään tietojen tarkistuksessa olemassa olevaa funktiota
  $retcode = tarkistamuutos($etunimi, $sukunimi, $puhelin, $email, $errorText);
  if ( $tunnus == "" )
  {
    $errorText .= " Käyttäjätunnus puuttuu, se on pakollinen tieto.<br>";
    $retcode = false;
  }
  else {
    $loytyi = tarkistatunnus($tunnus);
    if ($loytyi) {
      // Sama käyttäjätunnus löytyi jo kannasta.
      $errorText .= "Hakenasi käyttäjätunnus on jo käytössä. Kokeile jotakin toista tunnusvaihtoehtoa.<br>";
      $retcode = false;
    }
  }
  if ( strlen($salasana) < 6 )
  {
    $errorText .= " Salasana on liian lyhyt, käytä vähintään kuutta merkkiä.<br>";
    $retcode = false;
  }

  if ( $salasana != $salasana2 )
  {
    $errorText .= " Salasanat eivät tästää, tarkista tiedot.<br>";
    $retcode = false;
  }
return $retcode;
}

function tarkistatunnus($tarkistettavatunnus) {
  $samaloytyi = false;
  echo "Tarkistetaan tunnusta<br>";
  // Otetaan tietokanta käyttöön
  require_once("db.inc");
  // suoritetaan tietokantakysely ja kokeillaan hakea samaa tunnusta
  $query = "Select tunnus from asiakas WHERE tunnus='$tarkistettavatunnus'";
  echo "Kysely on $query<br>";
  $tulos = mysqli_query($conn, $query);
  // Tarkistetaan onnistuiko kysely (oliko kyselyn syntaksi oikein)
  if ( !$tulos )
  {
    echo "Kysely epäonnistui " . mysqli_error($conn);
  }
  else
  {
    if (mysqli_num_rows($tulos) != 0) {
      $samaloytyi = true;
    }
  }
  return $samaloytyi;
}

function tulostaVirhe($errorText) {
  ?>
  <div class="alert alert-danger" role="alert">
    <h4 class="alert-heading">Virhe!</h4>
    <p><?php echo "$errorText" ?></p>
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
