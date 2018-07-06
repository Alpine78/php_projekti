<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Kirjaudu sisään - Kotitalkkari</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="style.css" rel="stylesheet">
  </head>

  <body class="text-center">
    <form class="form-signin">
      <img class="mb-4" src="https://getbootstrap.com/docs/4.1/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
      <h1 class="h3 mb-3 font-weight-normal">Kirjaudu sisään</h1>
      <label for="inputUser" class="sr-only">Käyttäjätunnus</label>
      <input type="text" id="inputUser" class="form-control" placeholder="Käyttäjätunnus" required autofocus>
      <label for="inputPassword" class="sr-only">Salasana</label>
      <input type="password" id="inputPassword" class="form-control" placeholder="Salasana" required>
      <div class="checkbox mb-3">
        <label>
          <input type="checkbox" value="remember-me"> Muista minut
        </label>
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Kirjaudu</button>
      <p class="mt-5 mb-3 text-muted">&copy; Ilkka Rytkönen 2018</p>
    </form>
  </body>
</html>
