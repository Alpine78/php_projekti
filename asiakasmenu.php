<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
  <a class="navbar-brand" href=".">Kotitalkkari</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="asiakas.php">Etusivu<span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="tyotilaus.php">Työtilaus</a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <?php
      //<input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
      // Jos ollaan kirjauduttu sisälle, näytetään yläoikealla kirjautuneen tunnus ja uloskirjautumispainike
      if (isset($_SESSION["kirjautunut"])) {
        $tunnus = $_SESSION["kirjautunut"];
        echo "<button type=\"submit\" class=\"btn btn-outline-secondary\" formaction=\"kayttajatiedot.php\" formmethod=\"post\" name=\"muokkaa\">$tunnus (muokkaa)</button>";
        echo "&nbsp;&nbsp;";
        echo "<button class=\"btn btn-primary\" type=\"submit\" formaction=\"asiakas_login.php\" formmethod=\"post\" name=\"uloskirjaudu\" value=\"ok\">Kirjaudu ulos</button>";
        //<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
      }
      ?>
    </form>
  </div>
</nav>
