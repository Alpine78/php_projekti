<?php
	// tietokannan nimi
	define("DB_NAME", "kiinteistopalvelut");
	// käyttäjätunnus, jolla otetaan yhteys tietokantaan. HUOM! root:n tunnusta EI saa koskaan käyttää tuotantosovelluksessa (eikä oikeastaan missään muussakaan)
	define("DB_USER", "root");
	// Salasana
	define("DB_PASSWD", "");
	// Tietokantapalvelimen osoite
	define("DB_HOST", "localhost");

	// Otetaan yhteys tietokantapalvelimelle
	$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWD);
	$conn->set_charset("utf8");

	if ( mysqli_connect_errno() )
	{
		// Lopettaa tämän skriptin suorituksen ja tulostaa parametrina tulleen tekstin
		die ("Tietokantapalvelinta ei löydy, syy: " . mysqli_connect_error());
	}

	// valitaan tietokanta -> Ei välttämätöntä, jos kanta annettu mysqli_connect:ssa
	mysqli_select_db($conn, DB_NAME) or die ("Tietokantaan " . DB_NAME . " ei saada yhteyttä");
?>
