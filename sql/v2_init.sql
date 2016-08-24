CREATE SCHEMA prim;

CREATE TABLE prim.prim_feltolt ( 
	id                   smallint UNSIGNED NOT NULL  AUTO_INCREMENT,
	uuid                 varchar(15)  NOT NULL  ,
	feltoltes_datum      datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	elfogadva_10         bool    ,
	torolt_10            bool  NOT NULL DEFAULT false ,
	CONSTRAINT pk_prim_feltolt PRIMARY KEY ( id ),
	CONSTRAINT unit_prim_feltolt_uuid UNIQUE ( uuid ) 
 );

ALTER TABLE prim.prim_feltolt COMMENT 'Ebben a táblában tároljuk a feltöltéseket.';

ALTER TABLE prim.prim_feltolt MODIFY id smallint UNSIGNED NOT NULL  AUTO_INCREMENT COMMENT 'Egyedi azonosító';

ALTER TABLE prim.prim_feltolt MODIFY uuid varchar(15)  NOT NULL   COMMENT 'A feltöltést ezzel azonosítjuk, ez alapján osztható meg';

ALTER TABLE prim.prim_feltolt MODIFY feltoltes_datum datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP  COMMENT 'Feltöltés időpontja';

ALTER TABLE prim.prim_feltolt MODIFY elfogadva_10 bool     COMMENT 'A fájl feltöltődése esetén bejegyzés jön létre a táblában, de a validátor megtagadhatja a fájl mentését. Akkor hamis értéket kap a mező. Igaz esetén a fájl eredetinek bizonyult és adatbázisba kerültek az értékei.';

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

