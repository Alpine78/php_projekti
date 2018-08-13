<h1>PHP-ohjelmointi</h1>

Tyyppi 	Opintojakso ammattiopinnot - valinnainen - 5 op

Sisältö 	- PHP:n syntaksi (muuttujat ja tietotyypit, ohjausrakenteet, funktiot, merkkijonojen käsittely, päivämäärien käsittely)
- Formin datan lukeminen (validointi, get/post-metodit, sivusiirtymät)
- tilan hallinta (sessiot, cookiet, hidden-kentät)
- tietokantarajapinta (MySQL) (haut, päivitykset, tietoturva)
Lähtötaso 	- HTML:n perusteiden hallinta
- Jonkun ohjelmointikielen hallinta
- Tietokantojen perusteiden hallinta
Tavoitteet 	PHP-kieli on yksi käytetyimmistä web-sovellusten ohjelmointikielistä. PHP sisältää kaikki modernin verkkosovelluksen tekemiseen tarvittavat ominaisuudet, mutta on siitä huolimatta melko helppo oppia. Opintojakson aikana käydään läpi perusteet nykyaikaisen verkkosovelluksen rakentamiseen PHP:n avulla. Opintojakson esitiedoiksi riittää HTML:n perusteiden hallinta, jonkun ohjelmointikielen hallinta sekä tietokantojen perusteet.

Opintojakson suoritettuaan opiskelija
- osaa käyttää PHP-kieltä web-sivuston toteuttamiseen.
- osaa PHP-kielen perussyntaksin ja tietokantarajapinnan sekä osaa toteuttaa web-sovelluksen tilanhallinnan.
- tietää miten tietoturva otetaan huomioon web-sivustojen suunnittelussa.
- ymmärtää HTTP-protokollan periaatteet ja sen rajoitteet.
Toteutus 	- 100% verkkokurssi (Moodle)

### Harjoitustyö
Harjoitustyönä tehdään PHP-sivusto, jonka avulla omakotiasukas voi tilata itselleen palveluja kiinteistöhuoltofirmalta

#### Vaatimukset
1. Käyttäjän rekisteröinti
- Sama käyttäjä ei saa rekisteröityä kuin kerran (riittää tarkistaa ettei samaa tunnusta ole jo olemassa). Salasanaa EI tarvitse kryptata tietokantaan. Salasana on annettava varmistuksena uudestaan rekisteröitymisvaiheessa
2. Sisäänkirjautuminen
- Jos tunnus ja/tai salasana ovat väärin, ohjataan käyttäjä takaisin kirjautumissivulle.
3. Omien tietojen muuttaminen
- Käyttäjä voi muuttaa omia tietojaan (ks. vaatimus nro 12). Muutoslomakkeella on oltava vanhat tiedot oletuksena kentissä, joita käyttäjä voi editoida haluamallaan tavalla.
4. Uloskirjautuminen
- Käyttäjän pitää pystyä kirjautumaan jotenkin ulos sovelluksesta. Uloskirjauksen jälkeen heitetään käyttäjä takaisin login-sivulle, jossa täytyy kirjautua uudelleen, jos haluaa jatkaa sovelluksen käyttöä.
5. Työtilauksen tekeminen
- Lisätyn tilauksen tila on TILATTU.
6. Työtilausten selaaminen
- Sisäänkirjautumisen jälkeen näkyy käyttäjälle automaattisesti kaikki omat työtilaukset. Tilauksista näytetään kaikki ”järkevät” kentät (avain-kenttiä ei näytetä). Uuden tilauksen status on TILATTU.
7. Työtilauksen muuttaminen
- Käyttäjä voi muuttaa työtilausta vain jos tilaus on TILATTU-tilassa.
8. Työtilauksen poistaminen
- Käyttäjä voi poistaa työtilauksen vain jos tilaus on TILATTU-tilassa.
9. Työtilauksella on status, joka kertoo työn etenemisestä
- Työtilauksella on seuraavat statukset: -TILATTU (kun työtilaus on lisätty järjestelmään) -ALOITETTU (kun toimittaja on aloittanut työn, tällöin toimittaja laittaa  työlle aloituspvm:n) -VALMIS (kun toimittaja on tehnyt työn, tällöin toimittaja laittaa työlle 
valmistumispvm:n) -HYVÄKSYTTY (asiakas hyväksyy työn jotenkin ja työlle tallentuu hyväksymispvm)
10. Yksittäisiä sivuja EI pysty käyttämään ilman rekisteröintiä
- Jos käyttäjä yrittää navigoida sivustoon kuuluvalle yksittäiselle sivulle ILMAN sisäänkirjautumista, heittää järjestelmä käyttäjän automaattisesti login-sivulle.
11. Päivämäärät täytyy näkyä suomalaisessa muodossa
- Sivustolla esiintyvät päivämäärät pitää pystyä syöttämään ja esittämään muodossa pp.kk.vvvv (esimerkiksi 11.12.2008)
12. Jokaisella sivulla näkyy sisäänkirjautuneen käyttäjän nimi (tai tunnus)
- Jokaisen sivun ylälaidassa näkyy jotenkin kirjautuneen käyttäjän tunniste (nimi ja/tai tunnus). Tunnistetta klikkaamalla pääsee editoimaan omia tietoja.
13. Asiakkaalle ovat pakollisia tietoja nimi, käyntiosoite, laskutusosoite ja puhelinnumero sekä email-osoite. Lisäksi voidaan tallettaa 
asunnon tyyppi (omakotitalo, vapaa-ajan asunto, maatila, tyyppejä voi tulla lisääkin), asunnon pinta-ala ja tontin koko.
14. Työtilauksella on pakollisina tietoina tilaaja, työn kuvaus, tilauspvm, aloituspvm, valmistumispvm, hyväksymispvm, vapaamuotoinen kommentti tehdystä työstä, käytetty tuntimäärä sekä vapaamuotoinen selostus kuluneista tarvikkeista. Lisäksi yhtenä kenttänä onkustannusarvio-kenttä, johon toimittaja voi arvoida kustannukset (varsinkin jos tarjouspyyntö johtaa tilaukseen, on tällä kentällä käyttöä).
15. Tarjouspyynnön tekeminen
- Käyttäjä voi jätäää tarjouspyynnön, johon toimittaja vastaa. Tarjouspyynnöllä on joku status-kenttä, josta tiedetään, onko toimittaja 
vastanut tarjouspyyntöön. HUOM! Tarjouspyyntöä pitää pystyä myös muokkaamaan.
16. Tarjouspyynnön poistaminen
- Tarjouspyynnön voi poistaa vain jos  toimittaja EI ole vastannut tarjouspyyntöön.
17. Tarjouspyynnön hyväksyminen
- Käyttäjä voi hyväksyä vain tarjouspyynnön, johon on vastattu (annettu siis kustannusarvio). Hyväksytystä tarjouspyynnöstä generoituu automaattisesti työtilaus. Tarjouspyyntö saa jäädä tietokantaan, mutta sen status täytyy muuttaa hyväkystyksi.
18. Tarjouspyynnön hylkääminen
- Tarjouspyynnön status muutetaan hylätyksi ja tarjouspyyntö saa jäädä tietokantaan.
19. Tarjouspyynnölle pakollisia tietoja ovat tilaaja, kuvaus työstä, jättöpvm, kustannusarvio, vastaamispvm ja status
- Statuksen mahdollisia arvoja ovat: -JÄTETTY (kun tarjouspyyntö on jätetty toimittajalle) -VASTATTU (toimittaja on vastannut tarjouspyyntöön) -HYVÄKSTTY (kun tarjouspyyntö on hyväksytty) -HYLÄTTY (kun tarjouspyyntö on hylätty)
20. Asiakkaan jättämiä työtilauksia pitää pystyä selaamaan
- Eri asiakkaiden työtilauksia täytyy voida selailla. Hakuehtoina voi antaa asiakkaan, statuksen ja tilauspvm:n (haetaan annetun  tlauspvm:n jälkeen tulleita tilauksia). Listauksessa täytyy näkyä kuka asiakas on työn tilannut.
21. Työtilauksen statuksen muuttaminen
- Työtilauksen statusta voidaan muuttaa seuraavasti: -TILATTU  voidaan muuttaa ALOITETUKSI  (tällöin tietokantaan tallentuu automaattisesti aloituspvm) -ALOITETTU  voidaan muuttaa VALMIIKSI  (tietokantaan  tallentuu automaattisesti valmispvm) -VALMIS voidaan 
muutttaa takaisin ALOITETUKSI  (tietokannasta poistuu myös valmispvm)
22. Tarjouspyyntöjen selailu
- Tarjouspyyntöjä voidaan selailla eri hakuehdoilla.  Hakuehtoina ovat mm. status ja jättöpvm. Listauksessa täytyy näkyä kuka asiakas on ko. tarjouspyynnön  jättänyt.
23. Tarjouspyyntöihin vastaaminen
- Tarjouspyyntöön  vastataan syöttämällä tarjoukselle kustannusarvio. Tällöin tarjouksen status muuttuu VASTATTU-tilaksi ja tarjouspyynnölle tallentuu vastauspvm.
24. Asiakkaan salasanan muuttaminen
- Asiakkaan salasana täytyy pystyä muuttamaan (tai resetoimaan)  siltä varalta että asiakas unohtaa sen.
25. Työtilauksen  tietojen muuttaminen
- Työtilaukselle täytyy pystyä lisäämään (ja muuttamaan) kommentteja tehdystä työstä, käytetty tuntimäärä sekä käytetyt tarvikkeet (em 
statuksen pävityksen  lisäksi).
26. Työtilauksen  hylkääminen
- Työtilaus on voitava hylätä (työtä ei esimerkiksi pystytä tekemään). Hylätyt työtilaukset EIVÄT näy oletuksena  kun selataan asiakkaiden työtilauksia (ks. vaatimus nro 20), vaan hakuehdoissa  on täppä, jolla saadaan haettua VAIN hylätyt tilaukset (näytetään samat tiedot kuin muissa tilauksissa). HUOM! Vaatii muutoksen  myös  asiakkaan käyttöliittymään (hylätty työtilauksia ei voi käsitellä mitenkään).
