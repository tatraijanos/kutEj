CREATE TABLE prim.prim_feltolt ( 
	id                   smallint UNSIGNED NOT NULL  AUTO_INCREMENT,
	uuid                 varchar(15)  NOT NULL  ,
	nyelv_cd             tinyint UNSIGNED NOT NULL  ,
	feltoltes_datum      datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	meret                mediumint UNSIGNED   ,
	elfogadva_10         bool   DEFAULT true ,
	hiba                 varchar(1024)    ,
	torolt_10            bool  NOT NULL DEFAULT false ,
	CONSTRAINT pk_prim_feltolt PRIMARY KEY ( id ),
	CONSTRAINT unit_prim_feltolt_uuid UNIQUE ( uuid ) 
 );

ALTER TABLE prim.prim_feltolt COMMENT 'Ebben a táblában tároljuk a feltöltéseket.';

ALTER TABLE prim.prim_feltolt MODIFY id smallint UNSIGNED NOT NULL  AUTO_INCREMENT COMMENT 'Egyedi azonosító';

ALTER TABLE prim.prim_feltolt MODIFY uuid varchar(15)  NOT NULL   COMMENT 'A feltöltést ezzel azonosítjuk, ez alapján osztható meg';

ALTER TABLE prim.prim_feltolt MODIFY nyelv_cd tinyint UNSIGNED NOT NULL   COMMENT 'A feltöltött CSV állomány programozási nyelve.
md.csoport = P_NYELV';

ALTER TABLE prim.prim_feltolt MODIFY feltoltes_datum datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP  COMMENT 'Feltöltés időpontja';

ALTER TABLE prim.prim_feltolt MODIFY meret mediumint UNSIGNED    COMMENT 'A feltöltött fájl méretének tárolására szolgáló mező. A mező byte-ban kerül tárolásra.';

ALTER TABLE prim.prim_feltolt MODIFY elfogadva_10 bool   DEFAULT true  COMMENT 'A fájl feltöltődése esetén bejegyzés jön létre a táblában, de a validátor megtagadhatja a fájl mentését. Akkor hamis értéket kap a mező. Igaz esetén a fájl eredetinek bizonyult és adatbázisba kerültek az értékei.';

ALTER TABLE prim.prim_feltolt MODIFY hiba varchar(1024)     COMMENT 'A feldolgozás során keletkező hibák szövege, ha a mező nem null akkor elfogadva_10 értéke false.';

ALTER TABLE prim.prim_feltolt MODIFY torolt_10 bool  NOT NULL DEFAULT false  COMMENT 'Logikailag törölt-e a feltöltés. Ebben az esetben nem jelenik meg a listában.';

CREATE TABLE prim.prim_md ( 
	id                   smallint UNSIGNED NOT NULL  AUTO_INCREMENT,
	csoport              varchar(32)  NOT NULL  ,
	sequence             tinyint UNSIGNED NOT NULL  ,
	leiras               varchar(256)  NOT NULL  ,
	ertek                varchar(16)   DEFAULT "-" ,
	torolt_10            bool  NOT NULL DEFAULT false ,
	CONSTRAINT pk_prim_md PRIMARY KEY ( id )
 ) engine=InnoDB;

CREATE INDEX idx_prim_md_csoport ON prim.prim_md ( csoport );

ALTER TABLE prim.prim_md COMMENT 'Törzsadatok tárolására szolgáló tábla';

ALTER TABLE prim.prim_md MODIFY id smallint UNSIGNED NOT NULL  AUTO_INCREMENT COMMENT 'Egyedi azonosító';

ALTER TABLE prim.prim_md MODIFY csoport varchar(32)  NOT NULL   COMMENT 'Csoport kulcsa, nyomtatott angol ABC betűiből, egy rövid név, ami tükrözi hogy a csoport mire való.';

ALTER TABLE prim.prim_md MODIFY sequence tinyint UNSIGNED NOT NULL   COMMENT 'Ennek a mezőnek mentése történik az adott egyednél, csoportonként 1-től folytonos szám';

ALTER TABLE prim.prim_md MODIFY leiras varchar(256)  NOT NULL   COMMENT 'A törzsadatcsoport "értéke", ez jelenik meg a listában';

ALTER TABLE prim.prim_md MODIFY ertek varchar(16)   DEFAULT "-"  COMMENT 'A PHP motornak lehet szükséges egyes opciókhoz viselkedést társítani, egyfajta változó ez is. Ha nincs ilyen funkció "-"-val jelölendő.';

CREATE TABLE prim.prim_osszefoglalo ( 
	id                   smallint UNSIGNED NOT NULL  AUTO_INCREMENT,
	prim_feltolt_id      smallint UNSIGNED NOT NULL  ,
	metodus_cd           tinyint UNSIGNED NOT NULL  ,
	max_tartomany_cd     tinyint UNSIGNED NOT NULL  ,
	max_szal             tinyint UNSIGNED NOT NULL  ,
	indulas_ido          int UNSIGNED NOT NULL  ,
	teljes_futasi_ido    mediumint UNSIGNED NOT NULL  ,
	CONSTRAINT pk_prim_osszefoglalo PRIMARY KEY ( id )
 ) engine=InnoDB;

ALTER TABLE prim.prim_osszefoglalo COMMENT 'A CSV feldolgozása során az összefoglaló sorok ebbe a táblába kerülnek mentésre. Animálás esetén a fájl kiválasztása esetén ebből a táblából töltjük fel a legördülő elemeket.';

ALTER TABLE prim.prim_osszefoglalo MODIFY id smallint UNSIGNED NOT NULL  AUTO_INCREMENT COMMENT 'Egyedi azonosító';

ALTER TABLE prim.prim_osszefoglalo MODIFY prim_feltolt_id smallint UNSIGNED NOT NULL   COMMENT 'A prim_feltolt tábla rekordjának azonosítója.';

ALTER TABLE prim.prim_osszefoglalo MODIFY metodus_cd tinyint UNSIGNED NOT NULL   COMMENT 'Az összefoglaló sor és az alárendelt rekordok metódusa. 
md.csoport = METODUS';

ALTER TABLE prim.prim_osszefoglalo MODIFY max_tartomany_cd tinyint UNSIGNED NOT NULL   COMMENT 'Az összefoglaló sor és az alárendelt rekordok maximális tartománya. 
md.csoport = TARTOMANY';

ALTER TABLE prim.prim_osszefoglalo MODIFY max_szal tinyint UNSIGNED NOT NULL   COMMENT 'Az összefoglaló sor és az alárendelt rekordok maximális szálainak száma. ';

ALTER TABLE prim.prim_osszefoglalo MODIFY indulas_ido int UNSIGNED NOT NULL   COMMENT 'A kiválasztott mérés az itt meghatározott időponttól indult (unix-idő, milliszekundum). ';

ALTER TABLE prim.prim_osszefoglalo MODIFY teljes_futasi_ido mediumint UNSIGNED NOT NULL   COMMENT 'A kiválasztott mérés az itt meghatározott ideig (milliszekundum) futott.';

CREATE TABLE prim.prim_eredmenyek ( 
	id                   int UNSIGNED NOT NULL  AUTO_INCREMENT,
	prim_osszefoglalo_id smallint UNSIGNED NOT NULL  ,
	szal                 tinyint UNSIGNED NOT NULL  ,
	int_tol              int UNSIGNED NOT NULL  ,
	int_ig               int UNSIGNED NOT NULL  ,
	megtalalt_prim_darab mediumint UNSIGNED NOT NULL  ,
	szal_indulas_ido     int UNSIGNED NOT NULL  ,
	szal_futas_ido       mediumint UNSIGNED NOT NULL  ,
	CONSTRAINT pk_prim_eredmenyek PRIMARY KEY ( id )
 ) engine=InnoDB;

CREATE INDEX idx_prim_eredmenyek ON prim.prim_eredmenyek ( prim_osszefoglalo_id );

ALTER TABLE prim.prim_eredmenyek COMMENT 'A mérés animálásához szükséges szálak adatai. Az összefoglaló sorok alárendeltje, szálanként egy rekord.';

ALTER TABLE prim.prim_eredmenyek MODIFY id int UNSIGNED NOT NULL  AUTO_INCREMENT COMMENT 'Egyedi azonosító';

ALTER TABLE prim.prim_eredmenyek MODIFY prim_osszefoglalo_id smallint UNSIGNED NOT NULL   COMMENT 'Az összefoglaló sor azonosítója. Az ott meghatározott max_ prefix-el rendelkező intervallumot, szálat veheti itt fel maximum értéknek egy sor.';

ALTER TABLE prim.prim_eredmenyek MODIFY szal tinyint UNSIGNED NOT NULL   COMMENT 'A mérésen belüli szál aktuális száma. 1-től az összefoglaló táblában meghatározott számig veheti fel az értékét, egyel növekvően. ';

ALTER TABLE prim.prim_eredmenyek MODIFY int_tol int UNSIGNED NOT NULL   COMMENT 'A szálnak kiosztott tartomány kezdő értéke. Legalább 1 a kezdő értéke.';

ALTER TABLE prim.prim_eredmenyek MODIFY int_ig int UNSIGNED NOT NULL   COMMENT 'A szálnak kiosztott tartomány befejező értéke. Az összefoglaló sorokban meghatározott maximális érték lehet a legnagyobb felvett értéke.';

ALTER TABLE prim.prim_eredmenyek MODIFY megtalalt_prim_darab mediumint UNSIGNED NOT NULL   COMMENT 'A szál a futása során ennyi prímszámot talált meg.';

ALTER TABLE prim.prim_eredmenyek MODIFY szal_indulas_ido int UNSIGNED NOT NULL   COMMENT 'A szál indulási ideje, unix-idő. Miliszekundumban megadott érték. Legkisebb értéke az összefoglaló sorában meghatározott indulási idő.';

ALTER TABLE prim.prim_eredmenyek MODIFY szal_futas_ido mediumint UNSIGNED NOT NULL   COMMENT 'Az adott szál ennyi milliszekundum idő alatt válogatta ki az intervallumából a prímszámokat. Legkisebb értéke 0, legnagyobb az összefoglaló táblában meghatározott teljes futási idő.';

ALTER TABLE prim.prim_eredmenyek ADD CONSTRAINT fk_p_eredmenyek_reference_p_osszefoglalo FOREIGN KEY ( prim_osszefoglalo_id ) REFERENCES prim.prim_osszefoglalo( id ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE prim.prim_osszefoglalo ADD CONSTRAINT fk_p_osszefoglalo_ref_p_feltolt FOREIGN KEY ( prim_feltolt_id ) REFERENCES prim.prim_feltolt( id ) ON DELETE NO ACTION ON UPDATE NO ACTION;

