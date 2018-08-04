-- Ilkka Rytkönen
-- ETA17SP
-- KES18ETP4520 PHP-ohjelmointi
-- 7.7.2018

-- Jos löytyy, niin poistetaan ensin kiinteistopalvelut-niminen tietokanta ja luodaan sitten se uudelleen
DROP DATABASE IF EXISTS kiinteistopalvelut;
CREATE DATABASE kiinteistopalvelut
  CHARACTER SET utf8
  COLLATE utf8_general_ci;
USE kiinteistopalvelut; -- Otetaan uusi kanta käyttöön

-- Luodaan tarvittavat taulut ER-kaavion mukaisesti

-- Asiakas
CREATE TABLE Asiakas (
	tunnus VARCHAR(30) PRIMARY KEY NOT NULL,
	salasana VARCHAR(30) NOT NULL,
	etunimi VARCHAR(40) NOT NULL,
	sukunimi VARCHAR(50) NOT NULL,
	puhelin VARCHAR(13) NOT NULL,
	email VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- AsunnonTyyppi
CREATE TABLE AsunnonTyyppi (
	asunnonTyyppiID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	asunnonTyyppi VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Osoite
CREATE TABLE Osoite (
	osoiteID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  tunnus VARCHAR(30) NOT NULL,
	laskutusnimi VARCHAR(50),
	lahiosoite VARCHAR(50) NOT NULL,
	postinumero VARCHAR(5) NOT NULL,
	postitoimipaikka VARCHAR(40) NOT NULL,
  asunnonTyyppiID INT,
  asunnonAla INT,
  tontinAla INT,
  FOREIGN KEY (tunnus) REFERENCES Asiakas(tunnus) ON DELETE CASCADE,
  FOREIGN KEY (asunnonTyyppiID) REFERENCES asunnonTyyppi(asunnonTyyppiID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tyotilaus
CREATE TABLE Tyotilaus (
	tyotilausiD INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	osoiteID INT NOT NULL,
	tyonkuvaus TEXT NOT NULL,
	tilausPvm DATETIME NOT NULL DEFAULT NOW(),
	aloitusPvm DATETIME,
	valmistumisPvm DATETIME,
	hyvaksyttyPvm DATETIME,
  hylattyPvm DATETIME,
	kommentti VARCHAR(255),
	tyotunnit INT,
	tarvikeselostus VARCHAR(255),
	kustannusarvio DEC(8,2),
	FOREIGN KEY (osoiteID) REFERENCES Osoite(osoiteID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tarjouspyynto
CREATE TABLE Tarjouspyynto (
	tarjouspyyntoID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	tunnus VARCHAR(30) NOT NULL,
	tyonkuvaus TEXT NOT NULL,
	jatttoPvm DATETIME NOT NULL DEFAULT NOW(),
  vastattuPvm DATETIME,
  hyvaksyttyPvm DATETIME,
  hylattyPvm DATETIME,
	kustannusarvio DEC(8,2),
	osoiteID INT NOT NULL,
	FOREIGN KEY (tunnus) REFERENCES Asiakas(tunnus),
	FOREIGN KEY (osoiteID) REFERENCES Osoite(osoiteID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Listätään tarpeellinen perusdata sovelluksen käyttöön

INSERT INTO AsunnonTyyppi (asunnonTyyppi) VALUES
	('omakotitalo'),
	('kesämökki'),
	('maatila');

-- Luodaan näkymät

CREATE VIEW toimitusosoite AS
  SELECT
    osoiteID,
    tunnus,
    laskutusnimi,
    lahiosoite,
    postinumero,
    postitoimipaikka,
    Osoite.asunnonTyyppiID,
    asunnonTyyppi
FROM osoite
JOIN AsunnonTyyppi ON Osoite.asunnonTyyppiID = AsunnonTyyppi.asunnonTyyppiID;

CREATE VIEW laskutusosoite AS
  SELECT * FROM osoite WHERE laskutusnimi IS NOT NULL;

CREATE VIEW tilausnakyma AS
  SELECT
    tyotilausiD,
    Asiakas.tunnus,
    CASE
      WHEN LENGTH(tyonkuvaus) < 20 THEN tyonkuvaus
      ELSE CONCAT (
        SUBSTRING(tyonkuvaus,1,20),
        '...')
    END AS kuvaus,
    tilausPvm,
    lahiosoite,
    asunnonTyyppi,
    tyotunnit,
    kustannusarvio,
    hylattyPvm,
    CASE
      WHEN hylattyPvm IS NOT NULL THEN 'hylätty'
      WHEN hyvaksyttyPvm IS NOT NULL THEN 'hyväksytty'
      WHEN valmistumisPvm IS NOT NULL THEN 'valmis'
      WHEN aloitusPvm IS NOT NULL THEN 'aloitettu'
      ELSE 'tilattu'
    END AS status
  FROM Tyotilaus
  JOIN Osoite
    ON Osoite.osoiteID = Tyotilaus.osoiteID
  JOIN Asiakas
    ON Asiakas.tunnus = Osoite.tunnus
  JOIN AsunnonTyyppi
    ON AsunnonTyyppi.asunnonTyyppiID = Osoite.asunnonTyyppiID
  ORDER BY tilausPvm DESC;

-- Lisätään testidataa sovelluksen toiminnan testaukseen

INSERT INTO Asiakas (tunnus, salasana, etunimi, sukunimi, puhelin, email) VALUES
  ('Ilkka', 'ilkka', 'Ilkka', 'Rytkönen', '040-5922842', 'ilkka@ilkansivu.net');
INSERT INTO Asiakas (tunnus, etunimi, sukunimi, puhelin, email, salasana) VALUES
  ('Testi', 'Teppo', 'Testinen', '050-4444444', 'teppo@testinen.com', '123456');

INSERT INTO Osoite (tunnus, laskutusnimi, lahiosoite, postinumero, postitoimipaikka) VALUES
  ('Ilkka', 'Ilkka Rytkönen', 'Kaihorannankatu 5', '70420', 'Kuopio');

INSERT INTO Osoite (tunnus, lahiosoite, postinumero, postitoimipaikka, asunnonTyyppiID) VALUES
  ('Ilkka', 'Kaihorannankatu 5', '70420', 'Kuopio', '1'),
  ('Ilkka', 'Telkänkuja 50', '91100', 'Ii', '2'),
  ('Ilkka', 'Tyrmynniementie 100', '74595', 'Runni', '3');

INSERT INTO Tyotilaus (osoiteID, tyonkuvaus, tilausPvm) VALUES
  ('2', 'Nurmikonleikkaus', '2018-07-31');

INSERT INTO Tyotilaus (osoiteID, tyonkuvaus, tilausPvm, aloitusPvm) VALUES
  ('3', 'Kukkien kastelu', '2018-06-15', '2018-06-16');

INSERT INTO Tyotilaus (osoiteID, tyonkuvaus, tilausPvm, aloitusPvm, valmistumisPvm, kommentti, tyotunnit, tarvikeselostus, kustannusarvio) VALUES
  ('4', 'Kukkien istutus', '2018-06-15', '2018-06-16', '2018-06-16', 'Kukkia istutettu isot rivit', '5', 'Kukantaimia meni kassitolkulla', '1000');

INSERT INTO Tyotilaus (osoiteID, tyonkuvaus, tilausPvm, aloitusPvm, valmistumisPvm, hyvaksyttyPvm, kommentti, tyotunnit, tarvikeselostus, kustannusarvio) VALUES
  ('2', 'Polttopuiden teko', '2018-06-15', '2018-06-16', '2018-06-16', '2018-06-17', 'Polttopuita hakattu hiki hatussa', '10', 'Ei mennyt tarvikkeita', '800');

INSERT INTO Tyotilaus (osoiteID, tyonkuvaus, tilausPvm, aloitusPvm, valmistumisPvm, hylattyPvm) VALUES
  ('3', 'Talon maalaus', '2018-06-15', '2018-06-16', '2018-06-16', '2018-06-17');

INSERT INTO Tyotilaus (osoiteID, tyonkuvaus, tilausPvm, hylattyPvm) VALUES
  ('2', 'Talon purkaminen', '2017-05-12', '2017-06-17');
