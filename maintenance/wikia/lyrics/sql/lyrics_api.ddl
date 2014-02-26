CREATE DATABASE IF NOT EXISTS lyrics_api;
USE lyrics_api;

CREATE TABLE artist (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  article_id     INT UNSIGNED NOT NULL,
  name           VARCHAR(255),
  romanized_name VARCHAR(255),
  pic            VARCHAR(255),
  official_site  VARCHAR(255),
  myspace        VARCHAR(50),
  twitter        VARCHAR(50),
  facebook       VARCHAR(50),
  wikia          VARCHAR(255),
  wikipedia      VARCHAR(255),
  wikipedia2     VARCHAR(255),
  country        VARCHAR(40),
  state          VARCHAR(50),
  hometown       VARCHAR(50),
  itunes         VARCHAR(50),
  asin           VARCHAR(10),
  allmusic       VARCHAR(12),
  discogs        VARCHAR(10),
  musicbrainz    VARCHAR(36),
  youtube        VARCHAR(30),
  PRIMARY KEY (id),
  UNIQUE KEY (name)
)
  ENGINE =InnoDB;

CREATE TABLE album (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  article_id     INT UNSIGNED NOT NULL,
  artist_id      INT          NOT NULL,
  genres         VARCHAR(255),
  name           VARCHAR(255),
  romanized_name VARCHAR(255),
  year           SMALLINT DEFAULT NULL,
  length         SMALLINT DEFAULT NULL,
  pic            VARCHAR(255),
  wikipedia      VARCHAR(255),
  asin           VARCHAR(10),
  itunes         VARCHAR(30),
  allmusic       VARCHAR(12),
  discogs        VARCHAR(10),
  musicbrainz    VARCHAR(36),
  download       VARCHAR(255),
  PRIMARY KEY (id),
  UNIQUE KEY (artist_id, name)
)
  ENGINE =InnoDB;

CREATE TABLE song (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  article_id     INT UNSIGNED NOT NULL,
  artist_id      INT          NOT NULL,
  name           VARCHAR(255),
  lyrics         TEXT,
  romanized_name VARCHAR(255),
  language       VARCHAR(100),
  youtube        VARCHAR(10),
  goear          VARCHAR(10),
  itunes         VARCHAR(30),
  asin           VARCHAR(10),
  musicbrainz    VARCHAR(36),
  allmusic       VARCHAR(12),
  download       VARCHAR(255),
  PRIMARY KEY (id),
  UNIQUE KEY (artist_id, name)
)
  ENGINE =InnoDB;

CREATE TABLE track (
  album_id     INT UNSIGNED     NOT NULL,
  song_id      INT UNSIGNED     NOT NULL,
  track_number TINYINT UNSIGNED NOT NULL,
  UNIQUE KEY (album_id, song_id)
)
  ENGINE =InnoDB;
