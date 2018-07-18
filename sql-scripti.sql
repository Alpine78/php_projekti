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
	tunnus VARCHAR(30) NOT NULL,
	tuonkuvaus TEXT NOT NULL,
	tilausPvm DATETIME NOT NULL DEFAULT NOW(),
	aloitusPvm DATETIME,
	valmistumisPvm DATETIME,
	hyvaksyttyPvm DATETIME,
  hylattyPvm DATETIME,
	kommentti VARCHAR(255) NOT NULL,
	tyotunnit INT,
	tarvikeselostus VARCHAR(255),
	kustannusarvio DEC(8,2),
	FOREIGN KEY (osoiteID) REFERENCES Osoite(osoiteID),
	FOREIGN KEY (tunnus) REFERENCES Asiakas(tunnus)
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

-- Lisätään testidataa sovelluksen toiminnan testaukseen

INSERT INTO Asiakas (tunnus, salasana, etunimi, sukunimi, puhelin, email) VALUES
  ('Ilkka', 'ilkka', 'Ilkka', 'Rytkönen', '040-5922842', 'ilkka@ilkansivu.net');
INSERT INTO Asiakas (tunnus, etunimi, sukunimi, puhelin, email, salasana) VALUES
  ('Testi', 'Teppo', 'Testinen', '050-4444444', 'teppo@testinen.com', '123456');

INSERT INTO Osoite (tunnus, lahiosoite, postinumero, postitoimipaikka) VALUES
  ('Ilkka', 'Kaihorannankatu 5', '70420', 'Kuopio');
INSERT INTO Osoite (tunnus, laskutusnimi, lahiosoite, postinumero, postitoimipaikka, asunnonTyyppiID) VALUES
  ('Ilkka', 'Ilkka Rytkönen' 'Kaihorannankatu 5', '70420', 'Kuopio', 1);
