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
    <meta name="author" content="Ilkka Rytkönen">
    <title>Kirjaudu sisään - Kotitalkkari</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link href="login.css" rel="stylesheet">
  </head>

  <body class="text-center">

    <?php if (isset($_POST["uloskirjaudu"]) && $_POST["uloskirjaudu"] == "ok"): ?>
      <p>Kirjaudutaan ulos</p>
    <?php
    session_unset();
    session_destroy();
    endif; ?>

    <!-- Kirjautumislomake -->
    <form class="form-signin" action="asiakas.php" method="post">
      <img class="mb-4" src="https://getbootstrap.com/docs/4.1/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
      <h1 class="h3 mb-3 font-weight-normal">Kirjaudu sisään</h1>
      <label for="inputUser" class="sr-only">Käyttäjätunnus</label>
      <input type="text" id="inputUser" name="tunnus" class="form-control" placeholder="Käyttäjätunnus" required autofocus>
      <label for="inputPassword" class="sr-only">Salasana</label>
      <input type="password" id="inputPassword" name="salasana" class="form-control" placeholder="Salasana" required>
      <div class="checkbox mb-3">
        <label>
          <input type="checkbox" value="muista" name="muistaminut"> Muista minut
        </label>
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit" name="kirjaudu">Kirjaudu</button>
      <p class="mt-5 mb-3 text-muted">&copy; Ilkka Rytkönen 2018</p>
    </form>


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>      </body>
    </html>
