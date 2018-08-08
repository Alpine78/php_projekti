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

-- Työtilaus
CREATE TABLE Tyotilaus (
	tyotilausiD INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  toimitusosoiteID INT NOT NULL,
  laskutusosoiteID INT NOT NULL,
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
  FOREIGN KEY (toimitusosoiteID) REFERENCES Osoite(osoiteID),
  FOREIGN KEY (laskutusosoiteID) REFERENCES Osoite(osoiteID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tarjouspyynto
CREATE TABLE Tarjouspyynto (
	tarjouspyyntoID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  toimitusosoiteID INT NOT NULL,
  laskutusosoiteID INT NOT NULL,
	tyonkuvaus TEXT NOT NULL,
	jattoPvm DATETIME NOT NULL DEFAULT NOW(),
  vastattuPvm DATETIME,
  hyvaksyttyPvm DATETIME,
  hylattyPvm DATETIME,
	kustannusarvio DEC(8,2),
  FOREIGN KEY (toimitusosoiteID) REFERENCES Osoite(osoiteID),
  FOREIGN KEY (laskutusosoiteID) REFERENCES Osoite(osoiteID)
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
    CASE
      WHEN hylattyPvm IS NOT NULL THEN 'hylätty'
      WHEN hyvaksyttyPvm IS NOT NULL THEN 'hyväksytty'
      WHEN valmistumisPvm IS NOT NULL THEN 'valmis'
      WHEN aloitusPvm IS NOT NULL THEN 'aloitettu'
      ELSE 'tilattu'
    END AS status
  FROM Tyotilaus
  JOIN Osoite
    ON Osoite.osoiteID = Tyotilaus.toimitusosoiteID
  JOIN Asiakas
    ON Asiakas.tunnus = Osoite.tunnus
  JOIN AsunnonTyyppi
    ON AsunnonTyyppi.asunnonTyyppiID = Osoite.asunnonTyyppiID
  ORDER BY tilausPvm DESC;

CREATE VIEW tarjousnakyma AS
  SELECT
    tarjouspyyntoID,
    Asiakas.tunnus,
    CASE
      WHEN LENGTH(tyonkuvaus) < 20 THEN tyonkuvaus
      ELSE CONCAT (
        SUBSTRING(tyonkuvaus,1,20),
        '...')
    END AS kuvaus,
    jattoPvm,
    lahiosoite,
    asunnonTyyppi,
    CASE
      WHEN hylattyPvm IS NOT NULL THEN 'hylätty'
      WHEN hyvaksyttyPvm IS NOT NULL THEN 'hyväksytty'
      WHEN vastattuPvm IS NOT NULL THEN 'vastattu'
      ELSE 'jätetty'
    END AS status
  FROM Tarjouspyynto
  JOIN Osoite
    ON Osoite.osoiteID = Tarjouspyynto.toimitusosoiteID
  JOIN Asiakas
    ON Asiakas.tunnus = Osoite.tunnus
  JOIN AsunnonTyyppi
    ON AsunnonTyyppi.asunnonTyyppiID = Osoite.asunnonTyyppiID
  ORDER BY jattoPvm DESC;

-- Lisätään testidataa sovelluksen toiminnan testaukseen

INSERT INTO Asiakas (tunnus, salasana, etunimi, sukunimi, puhelin, email) VALUES
  ('Ilkka', 'ilkka', 'Ilkka', 'Rytkönen', '040-5922842', 'ilkka@ilkansivu.net');
INSERT INTO Asiakas (tunnus, etunimi, sukunimi, puhelin, email, salasana) VALUES
  ('Testi', 'Teppo', 'Testinen', '050-4444444', 'teppo@testinen.com', '123456');

-- Laskutusosoitteet
INSERT INTO Osoite (tunnus, laskutusnimi, lahiosoite, postinumero, postitoimipaikka) VALUES
  ('Ilkka', 'Ilkka Rytkönen', 'Kaihorannankatu 5', '70420', 'Kuopio'),
  ('Ilkka', 'Testi Testaaja', 'Testikatu 10', '70100', 'Kuopio');

-- Toimitusosoitteet
INSERT INTO Osoite (tunnus, lahiosoite, postinumero, postitoimipaikka, asunnonTyyppiID) VALUES
  ('Ilkka', 'Kaihorannankatu 5', '70420', 'Kuopio', '1'),
  ('Ilkka', 'Telkänkuja 50', '91100', 'Ii', '2'),
  ('Ilkka', 'Tyrmynniementie 100', '74595', 'Runni', '3');

-- Tilattu työtilaus
INSERT INTO Tyotilaus (toimitusosoiteID, laskutusosoiteID, tyonkuvaus, tilausPvm) VALUES
  ('3', '1', 'Nurmikonleikkaus', '2018-07-31'),
  ('4', '2', 'Ikkunanpesu', '2018-04-02'),
  ('5', '1', 'Ikkunanpesu', '2018-08-08');

-- Aloitettu työtilaus
INSERT INTO Tyotilaus (toimitusosoiteID, laskutusosoiteID, tyonkuvaus, tilausPvm, aloitusPvm) VALUES
  ('4',  '1', 'Kukkien kastelu', '2018-06-15', '2018-06-16'),
  ('3',  '2', 'Rännien putsaus', '2018-08-02', '2018-08-05');

-- Valmistunut työtilaus
INSERT INTO Tyotilaus (toimitusosoiteID, laskutusosoiteID, tyonkuvaus, tilausPvm, aloitusPvm, valmistumisPvm, kommentti, tyotunnit, tarvikeselostus, kustannusarvio) VALUES
  ('5',  '1', 'Kukkien istutus', '2018-06-15', '2018-06-16', '2018-06-16', 'Kukkia istutettu isot rivit', '5', 'Kukantaimia meni kassitolkulla', '1000'),
  ('5',  '2', 'Aidan maalaus', '2015-07-02', '2015-07-16', '2015-07-20', 'Aitaa maalattu punamultamaalilla.', '6', 'Naapurin puolelta ei meinannut onnistua ison koiran vuoksi.', '600');

-- Hyväksytty työtilaus
INSERT INTO Tyotilaus (toimitusosoiteID, laskutusosoiteID, tyonkuvaus, tilausPvm, aloitusPvm, valmistumisPvm, hyvaksyttyPvm, kommentti, tyotunnit, tarvikeselostus, kustannusarvio) VALUES
  ('3',  '1', 'Polttopuiden teko', '2018-06-15', '2018-06-16', '2018-06-16', '2018-06-17', 'Polttopuita hakattu hiki hatussa', '10', 'Ei mennyt tarvikkeita', '800'),
  ('3',  '1', 'Aidan teko', '2016-06-15', '2016-06-16', '2016-06-16', '2016-06-17', 'Aita rakennettu tontin ympäri. Portteja tehty kolme kappaletta.', '20', 'Lautaa, nauloja, saranoita.', '2500');

-- Aloitettu, mutt hylätty työtilaus
INSERT INTO Tyotilaus (toimitusosoiteID, laskutusosoiteID, tyonkuvaus, tilausPvm, aloitusPvm, hylattyPvm) VALUES
  ('4',  '2', 'Kylppäriremontti', '2018-01-11', '2018-01-18', '2018-01-20'),
  ('5',  '1', 'Talon maalaus', '2018-06-15', '2018-06-16', '2018-06-17');

-- Hylätty työtilaus
INSERT INTO Tyotilaus (toimitusosoiteID, laskutusosoiteID, tyonkuvaus, tilausPvm, hylattyPvm) VALUES
  ('3',  '1', 'Talon purkaminen', '2017-05-12', '2017-06-17'),
  ('4',  '2', 'Katon rakentaminen', '2013-03-11', '2013-03-12');

-- Tarjouspyynnöt
INSERT INTO Tarjouspyynto (laskutusosoiteID, toimitusosoiteID, tyonkuvaus, jattoPvm, vastattuPvm, hyvaksyttyPvm, hylattyPvm, kustannusarvio) VALUES
  ('1', '3', 'Suuren nurmikon leikkaus', '2018-08-08', NULL, NULL, NULL, NULL),
  ('2', '5', 'Peltikaton uusinta', '2018-06-13', NULL, NULL, NULL, NULL),
  ('1', '3', 'Salaojan teko talon ympärille', '2018-03-28', '2018-03-31', NULL, NULL, '1000'),
  ('1', '4', 'Aidan teko takapihalle, n. 15 m.', '2018-05-14', '2018-05-15', NULL, NULL, '550'),
  ('2', '5', 'Polttopuiden pilkkominen pitkästä tavarasta', '2018-02-02', '2018-02-03', '2018-02-05', NULL, '250'),
  ('1', '3', 'Rannan siivous ja kaislikon niitto', '2018-07-10', '2018-07-11', '2018-07-14', NULL, '450'),
  ('1', '3', 'Rantapajukon raivaus', '2018-06-12', NULL, NULL, '2018-05-30', NULL),
  ('2', '5', 'Yksityistien ojien kaivaminen', '2018-05-22', NULL, NULL, '2018-05-30', NULL);
