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
    <?php require 'perusmenu.php'; ?>
    <main role="main" class="container">
      <div class="starter-template">
        <h1>Kotitalkkari</h1>
        <h2>PHP-kesäkurssin laaja harjoitustyö</h2>
        <hr>
        <div class="row">
          <div class="col-sm-6">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Asiakassovellus</h5>
                <p class="card-text">Asiakkaan näkökulma sovellukseen. Asiakas voi rekisteröityä, tehdä ja muokata työtilauksia sekä tarjouksia.</p>
                <a href="asiakas.php" class="btn btn-primary">Asiakassovellukseen</a>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Kiinteistöhuoltofirman sovellus</h5>
                <p class="card-text">Huoltofirman näkökulma sovellukseen. Työtilausten ja tarjousten käsittely sekä asiakkaan salananan resetointi.</p>
                <a href="firma.php" class="btn btn-primary">Kiinteistöhuoltofirman sovellukseen</a>
              </div>
            </div>
          </div>
        </div>

        <?php
        define("DB_NAME", "kiinteistopalvelut");
        define("DB_USER", "root");
        define("DB_PASSWD", "");
        define("DB_HOST", "localhost");
        $conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
      	//$conn->set_charset("utf8");

      	if ( mysqli_connect_errno() )
      	{
      		// Lopettaa tämän skriptin suorituksen ja tulostaa parametrina tulleen tekstin
          ?>
          <br />
          <div class="alert alert-danger" role="alert">
          Tietokantaa <strong>kiinteistopalvelut</strong> ei ole. Aja tiedosto <strong>sql-scripti.sql</strong>, jotta voit käyttää sovellusta.
          </div>
          <?php
      	}


        ?>

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
