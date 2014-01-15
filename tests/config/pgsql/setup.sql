CREATE TABLE bohemian_rhapsody(
  is_this  VARCHAR(1),
  the_real VARCHAR(1),
  life     VARCHAR(1),
  PRIMARY KEY (is_this)
);

INSERT INTO bohemian_rhapsody VALUES ('A', 'B', 'C');

CREATE TABLE killer_queen(
  killer     INTEGER,
  queen      INTEGER,
  gunpowder  INTEGER,
  gelatine   CHAR(1),
  dynamite   CHAR(1),
  laser_beam CHAR(1),
  PRIMARY KEY (killer, queen)
);

INSERT INTO killer_queen VALUES (1, 2, 3, 'X', 'Y', 'Z');

CREATE TABLE slightly_mad(
  it       INTEGER,
  finally  INTEGER,
  happened INTEGER
);

INSERT INTO slightly_mad VALUES (6, 6, 6);
