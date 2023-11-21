-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema rhyme
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `rhyme` ;

-- -----------------------------------------------------
-- Schema rhyme
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `rhyme` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `rhyme` ;

-- -----------------------------------------------------
-- Table `rhyme`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`users` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(256) CHARACTER SET 'ascii' COLLATE 'ascii_general_ci' NOT NULL,
  `name` TEXT NOT NULL,
  `password_hash` VARCHAR(256) CHARACTER SET 'ascii' COLLATE 'ascii_general_ci' NOT NULL,
  `is_admin` TINYINT NOT NULL DEFAULT 0,
  `deleted` TINYINT NOT NULL DEFAULT 0,
  `image_link` VARCHAR(2048) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rhyme`.`genres`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`genres` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`genres` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `added_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `added_by` INT NOT NULL,
  `updated_by` INT NOT NULL,
  `image_link` VARCHAR(2048) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) VISIBLE,
  INDEX `fk_genres_users_id_idx` (`updated_by` ASC) VISIBLE,
  INDEX `fk_genres_added_by_users_id_idx` (`added_by` ASC) VISIBLE,
  CONSTRAINT `fk_genres_updated_by_users_id`
    FOREIGN KEY (`updated_by`)
    REFERENCES `rhyme`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_genres_added_by_users_id`
    FOREIGN KEY (`added_by`)
    REFERENCES `rhyme`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rhyme`.`artists`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`artists` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`artists` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(512) NOT NULL,
  `added_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `added_by` INT NOT NULL,
  `updated_by` INT NOT NULL,
  `image_link` VARCHAR(2048) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_artists_users_id_idx` (`added_by` ASC) VISIBLE,
  INDEX `fk_artists_updated_by_users_id_idx` (`updated_by` ASC) VISIBLE,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) VISIBLE,
  CONSTRAINT `fk_artists_added_by_users_id`
    FOREIGN KEY (`added_by`)
    REFERENCES `rhyme`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_artists_updated_by_users_id`
    FOREIGN KEY (`updated_by`)
    REFERENCES `rhyme`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rhyme`.`songs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`songs` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`songs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` TEXT NOT NULL,
  `audio_link` VARCHAR(2048) CHARACTER SET 'ascii' COLLATE 'ascii_general_ci' NOT NULL,
  `added_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `added_by` INT NOT NULL,
  `updated_by` INT NOT NULL,
  `streams` BIGINT(100) NOT NULL,
  `image_link` VARCHAR(2048) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `audio_link_UNIQUE` (`audio_link` ASC) VISIBLE,
  INDEX `fk_songs_added_by_users_id_idx` (`added_by` ASC) VISIBLE,
  INDEX `fk_songs_updated_by_users_id_idx` (`updated_by` ASC) VISIBLE,
  CONSTRAINT `fk_songs_added_by_users_id`
    FOREIGN KEY (`added_by`)
    REFERENCES `rhyme`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_songs_updated_by_users_id`
    FOREIGN KEY (`updated_by`)
    REFERENCES `rhyme`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rhyme`.`playlists`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`playlists` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`playlists` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(512) NOT NULL,
  `owned_by` INT NOT NULL,
  `is_public` TINYINT NOT NULL,
  `added_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `image_link` VARCHAR(2048) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_playlists_users_id_idx` (`owned_by` ASC) VISIBLE,
  CONSTRAINT `fk_playlists_users_id`
    FOREIGN KEY (`owned_by`)
    REFERENCES `rhyme`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rhyme`.`artist_song`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`artist_song` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`artist_song` (
  `artist_id` INT NOT NULL,
  `song_id` INT NOT NULL,
  INDEX `fk_song_artist_songs_id_idx` (`song_id` ASC) VISIBLE,
  INDEX `fk_song_artist_artists_id_idx` (`artist_id` ASC) VISIBLE,
  PRIMARY KEY (`artist_id`, `song_id`),
  CONSTRAINT `fk_song_artist_songs_id`
    FOREIGN KEY (`song_id`)
    REFERENCES `rhyme`.`songs` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_song_artist_artists_id`
    FOREIGN KEY (`artist_id`)
    REFERENCES `rhyme`.`artists` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rhyme`.`playlist_song`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`playlist_song` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`playlist_song` (
  `playlist_id` INT NOT NULL,
  `song_id` INT NOT NULL,
  INDEX `fk_playlist_song_playlists_id_idx` (`playlist_id` ASC) VISIBLE,
  INDEX `fk_playlist_song_songs_id_idx` (`song_id` ASC) VISIBLE,
  PRIMARY KEY (`playlist_id`, `song_id`),
  CONSTRAINT `fk_playlist_song_playlists_id`
    FOREIGN KEY (`playlist_id`)
    REFERENCES `rhyme`.`playlists` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_playlist_song_songs_id`
    FOREIGN KEY (`song_id`)
    REFERENCES `rhyme`.`songs` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rhyme`.`genre_song`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`genre_song` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`genre_song` (
  `genre_id` INT NOT NULL,
  `song_id` INT NOT NULL,
  INDEX `fk_genre_song_genres_id_idx` (`genre_id` ASC) VISIBLE,
  INDEX `fk_genre_song_songs_id_idx` (`song_id` ASC) VISIBLE,
  PRIMARY KEY (`genre_id`, `song_id`),
  CONSTRAINT `fk_genre_song_genres_id`
    FOREIGN KEY (`genre_id`)
    REFERENCES `rhyme`.`genres` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_genre_song_songs_id`
    FOREIGN KEY (`song_id`)
    REFERENCES `rhyme`.`songs` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rhyme`.`favorite_song_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`favorite_song_user` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`favorite_song_user` (
  `favorite_song_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  INDEX `fk_favorite_song_user_songs_id_idx` (`favorite_song_id` ASC) VISIBLE,
  INDEX `fk_favorite_song_user_users_id_idx` (`user_id` ASC) VISIBLE,
  PRIMARY KEY (`favorite_song_id`, `user_id`),
  CONSTRAINT `fk_favorite_song_user_songs_id`
    FOREIGN KEY (`favorite_song_id`)
    REFERENCES `rhyme`.`songs` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_favorite_song_user_users_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `rhyme`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rhyme`.`favorite_playlist_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`favorite_playlist_user` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`favorite_playlist_user` (
  `favorite_playlist_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  INDEX `fk_favorite_playlist_user_playlists_id_idx` (`favorite_playlist_id` ASC) VISIBLE,
  INDEX `fk_favorite_playlist_user_users_id_idx` (`user_id` ASC) VISIBLE,
  PRIMARY KEY (`favorite_playlist_id`, `user_id`),
  CONSTRAINT `fk_favorite_playlist_user_playlists_id`
    FOREIGN KEY (`favorite_playlist_id`)
    REFERENCES `rhyme`.`playlists` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_favorite_playlist_user_users_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `rhyme`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rhyme`.`tokens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rhyme`.`tokens` ;

CREATE TABLE IF NOT EXISTS `rhyme`.`tokens` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `issued_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_tokens_users_id_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_tokens_users_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `rhyme`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
