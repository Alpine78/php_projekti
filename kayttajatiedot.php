<?php
  // Sessio-funktion kutsu
  session_start();
  // Katsotaan, onko sessiossa jo kirjautunut käyttäjä ja otetaan tiedot muuttujaan
  if (isset($_SESSION["kirjautunut"]) && $_SESSION["kirjautunut"] != "") {
    $tunnus = $_SESSION["kirjautunut"];
    // Otetaan tietokanta käyttöön
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
          <form>
            <div class="form-row">
              <div class="col-md-4 mb-3">
                <label for="validationDefaultUsername">Käyttäjätunnus</label>
                <div class="input-group">
                <input type="text" class="form-control" id="validationDefaultUsername" placeholder="Käyttäjätunnus" value="" required>
                </div>
              </div>
              <div class="col-md-4 mb-3">
                <label for="validationDefault01">Etunimi</label>
                <input type="text" class="form-control" id="validationDefault01" placeholder="Etunimi" value="" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="validationDefault02">Sukunimi</label>
                <input type="text" class="form-control" id="validationDefault02" placeholder="Sukunimi" value="" required>
              </div>
            </div>
            <div class="form-row">
              <div class="col-md-6 mb-2">
                <label for="validationDefault03">Puhelin</label>
                <input type="text" class="form-control" id="validationDefault03" placeholder="Puhelin" required>
              </div>
              <div class="col-md-6 mb-2">
                <label for="validationDefault04">Email</label>
                <input type="email" class="form-control" id="validationDefault04" placeholder="Email" required>
              </div>
            </div>
            <div class="form-row">
              <div class="col-md-6 mb-2">
                <label for="validationDefault03">Salasana</label>
                <input type="password" class="form-control" id="validationDefault03" placeholder="Salasana" required>
              </div>
              <div class="col-md-6 mb-2">
                <label for="validationDefault04">Salasana uudelleen</label>
                <input type="password" class="form-control" id="validationDefault04" placeholder="Salasana uudelleen" required>
              </div>
            </div>
            <?php if (isset($_POST["muokkaa"])) { ?>
              <button class="btn btn-primary" type="submit" formaction="kayttajatiedot.php" formmethod="post" name="muokkaa" value="tallenna">Tallenna muutokset</button>
            <?php }
            else { ?>
              <button class="btn btn-primary" type="submit" formaction="kayttajatiedot.php" formmethod="post" name="rekisteroidy">Rekisteröidy</button>
            <?php } ?>
          </form>
          <form>
          <button class="btn btn-primary" type="submit" formaction="asiakas.php" formmethod="post">Peruuta</button>
          </form>
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
