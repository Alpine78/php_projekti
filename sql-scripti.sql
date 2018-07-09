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

-- Osoitetyyppi
CREATE TABLE Osoitetyyppi (
    osoitetyyppiID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    osoitetyyppi VARCHAR(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8; -- Valitaan tietokantamoottori ja merkistö

-- Osoite
CREATE TABLE Osoite (
	osoiteID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  tunnus VARCHAR(30) NOT NULL,
	osoitetyyppiID INT NOT NULL,
	laskutusnimi VARCHAR(50),
	lahiosoite VARCHAR(50) NOT NULL,
	postinumero VARCHAR(5) NOT NULL,
	postitoimipaikka VARCHAR(40),
  FOREIGN KEY (tunnus) REFERENCES Asiakas(tunnus) ON DELETE CASCADE,
	FOREIGN KEY (osoitetyyppiID) REFERENCES Osoitetyyppi(osoitetyyppiID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- AsunnonTyyppi
CREATE TABLE AsunnonTyyppi (
	asunnonTyyppiID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	asunnonTyyppi VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Asunto
CREATE TABLE Asunto (
	asuntoID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	asunnonTyyppiID INT NOT NULL,
	tunnus VARCHAR(30) NOT NULL,
	asunnonNimi VARCHAR(30),
	asunnonAla INT,
	tontinAla INT,
	FOREIGN KEY (asunnonTyyppiID) REFERENCES AsunnonTyyppi(asunnonTyyppiID),
	FOREIGN KEY (tunnus) REFERENCES Asiakas(tunnus) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TilauksenStatus
CREATE TABLE TilauksenStatus (
	tilStatusID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	tilStatus VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tyotilaus
CREATE TABLE Tyotilaus (
	tyotilausiD INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	asuntoID INT NOT NULL,
	tunnus VARCHAR(30) NOT NULL,
	tilStatusID INT NOT NULL,
	tuonkuvaus TEXT NOT NULL,
	tilausPvm DATETIME NOT NULL DEFAULT NOW(),
	aloitusPvm DATETIME,
	valmistumisPvm DATETIME,
	hyvaksymisPvm DATETIME,
	kommentti VARCHAR(255) NOT NULL,
	tyotunnit INT,
	tarvikeselostus VARCHAR(255),
	kustannusarvio DEC(8,2),
	FOREIGN KEY (asuntoID) REFERENCES Asunto(asuntoID),
	FOREIGN KEY (tunnus) REFERENCES Asiakas(tunnus),
	FOREIGN KEY (tilStatusID) REFERENCES TilauksenStatus(tilStatusID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- TarjouksenStatus
CREATE TABLE TarjouksenStatus (
	tarStatusID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	tarStatus VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tarjouspyynto
CREATE TABLE Tarjouspyynto (
	tarjouspyyntoID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	tunnus VARCHAR(30) NOT NULL,
	tarStatusID INT NOT NULL,
	tyonkuvaus TEXT NOT NULL,
	jatttoPvm DATETIME NOT NULL DEFAULT NOW(),
	kustannusarvio DEC(8,2),
	asuntoID INT NOT NULL,
	vastaamisPvm DATETIME,
	FOREIGN KEY (tunnus) REFERENCES Asiakas(tunnus),
	FOREIGN KEY (tarStatusID) REFERENCES TarjouksenStatus(tarStatusID),
	FOREIGN KEY (asuntoID) REFERENCES Asunto(asuntoID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Listätään tarpeellinen perusdata sovelluksen käyttöön

INSERT INTO AsunnonTyyppi (asunnonTyyppi) VALUES
	('omakotitalo'),
	('kesämökki'),
	('maatila');

INSERT INTO Osoitetyyppi (osoitetyyppi) VALUES
	('käyntiosoite'),
	('laskutusosoite');

INSERT INTO TilauksenStatus (tilStatus) VALUES
	('tilattu'),
	('aloitettu'),
	('valmis'),
	('hyväksytty'),
	('hylätty');

INSERT INTO TarjouksenStatus (tarStatus) VALUES
	('jätetty'),
	('vastattu'),
	('hyväksytty'),
	('hylätty');

  -- Lisätään testidataa sovelluksen toiminnan testaukseen

INSERT INTO Asiakas (tunnus, salasana, etunimi, sukunimi, puhelin, email) VALUES
  ('Ilkka', 'ilkka', 'Ilkka', 'Rytkönen', '040-5922842', 'ilkka@ilkansivu.net');
