SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`Cuenta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Cuenta` (
  `Id_Cuenta` INT NOT NULL AUTO_INCREMENT,
  `Cuenta` VARCHAR(45) NULL,
  `Pass` VARCHAR(45) NULL,
  `Tipo_Cuenta` VARCHAR(45) NULL,
  `Nombre` VARCHAR(45) NULL,
  `Apellido` VARCHAR(45) NULL,
  `Email` VARCHAR(45) NULL,
  `Confirmacion` SMALLINT NULL,
  PRIMARY KEY (`Id_Cuenta`),
  UNIQUE INDEX `Cuenta_UNIQUE` (`Cuenta` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Organismo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Organismo` (
  `Id_Organismo` INT NOT NULL AUTO_INCREMENT,
  `Organismo` VARCHAR(45) NULL,
  `Ecotipo` VARCHAR(100) NULL,
  PRIMARY KEY (`Id_Organismo`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Libreria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Libreria` (
  `Id_Libreria` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(45) NULL,
  `Palabras_Clave` VARCHAR(45) NULL,
  `Tejido` VARCHAR(45) NULL,
  `Plataforma` VARCHAR(45) NULL,
  `Descripcion` VARCHAR(45) NULL,
  `Fecha` DATETIME NULL,
  `Institucion` VARCHAR(45) NULL,
  `Ruta` VARCHAR(45) NULL,
  `Id_Cuenta` INT NOT NULL,
  `Id_Organismo` INT NOT NULL,
  PRIMARY KEY (`Id_Libreria`),
  INDEX `fk_Libreria_Cuenta_idx` (`Id_Cuenta` ASC),
  INDEX `fk_Libreria_Organismo1_idx` (`Id_Organismo` ASC),
  CONSTRAINT `fk_Libreria_Cuenta`
    FOREIGN KEY (`Id_Cuenta`)
    REFERENCES `mydb`.`Cuenta` (`Id_Cuenta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Libreria_Organismo1`
    FOREIGN KEY (`Id_Organismo`)
    REFERENCES `mydb`.`Organismo` (`Id_Organismo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`HTOP`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`HTOP` (
  `Id_HTOP` INT NOT NULL AUTO_INCREMENT,
  `PID` INT NULL,
  `Activo` INT NULL,
  `Numero_Pipe_Actual` INT NULL,
  `Numero_Pipes` INT NULL,
  `Id_Cuenta` INT NOT NULL,
  `Id_Libreria` INT NOT NULL,
  `Graficos` INT NULL,
  PRIMARY KEY (`Id_HTOP`),
  INDEX `fk_HTOP_Cuenta1_idx` (`Id_Cuenta` ASC),
  INDEX `fk_HTOP_Libreria1_idx` (`Id_Libreria` ASC),
  CONSTRAINT `fk_HTOP_Cuenta1`
    FOREIGN KEY (`Id_Cuenta`)
    REFERENCES `mydb`.`Cuenta` (`Id_Cuenta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_HTOP_Libreria1`
    FOREIGN KEY (`Id_Libreria`)
    REFERENCES `mydb`.`Libreria` (`Id_Libreria`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`PIPE`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`PIPE` (
  `Id_PIPE` INT NOT NULL AUTO_INCREMENT,
  `Comando` VARCHAR(100) NULL,
  `Informacion` INT NULL,
  `Id_HTOP` INT NOT NULL,
  PRIMARY KEY (`Id_PIPE`),
  INDEX `fk_PIPE_HTOP1_idx` (`Id_HTOP` ASC),
  CONSTRAINT `fk_PIPE_HTOP1`
    FOREIGN KEY (`Id_HTOP`)
    REFERENCES `mydb`.`HTOP` (`Id_HTOP`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Genoma`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Genoma` (
  `Id_Genoma` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(45) NULL,
  `Version` VARCHAR(45) NULL,
  `Id_Organismo` INT NOT NULL,
  PRIMARY KEY (`Id_Genoma`),
  INDEX `fk_Genoma_Organismo1_idx` (`Id_Organismo` ASC),
  CONSTRAINT `fk_Genoma_Organismo1`
    FOREIGN KEY (`Id_Organismo`)
    REFERENCES `mydb`.`Organismo` (`Id_Organismo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`AnotacionN`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`AnotacionN` (
  `Id_Biologico` CHAR(45) NOT NULL,
  `Name` VARCHAR(45) NULL,
  `Alias` VARCHAR(45) NULL,
  `Parent` VARCHAR(45) NULL,
  `Target` VARCHAR(45) NULL,
  `Gap` VARCHAR(45) NULL,
  `Derives_from` VARCHAR(45) NULL,
  `Note` VARCHAR(100) NULL,
  `Dbxref` CHAR(45) NULL,
  `Ontology_term` VARCHAR(100) NULL,
  `Is_circular` VARCHAR(100) NULL,
  `Id_Genoma` INT NOT NULL,
  PRIMARY KEY (`Id_Biologico`),
  INDEX `fk_Anotacion_Genoma1_idx` (`Id_Genoma` ASC))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `mydb`.`Grupo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Grupo` (
  `Id_Grupo` INT NOT NULL,
  `Nombre` VARCHAR(45) NULL,
  `Descripcion` VARCHAR(45) NULL,
  `Fecha` DATETIME NULL,
  `Id_Cuenta_Propietario` INT NOT NULL,
  PRIMARY KEY (`Id_Grupo`),
  INDEX `fk_Grupo_Cuenta1_idx` (`Id_Cuenta_Propietario` ASC),
  CONSTRAINT `fk_Grupo_Cuenta1`
    FOREIGN KEY (`Id_Cuenta_Propietario`)
    REFERENCES `mydb`.`Cuenta` (`Id_Cuenta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Miembro_Grupo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Miembro_Grupo` (
  `Id_Grupo` INT NOT NULL,
  `Id_Cuenta` INT NOT NULL,
  PRIMARY KEY (`Id_Grupo`, `Id_Cuenta`),
  INDEX `fk_Grupo_has_Cuenta_Cuenta1_idx` (`Id_Cuenta` ASC),
  INDEX `fk_Grupo_has_Cuenta_Grupo1_idx` (`Id_Grupo` ASC),
  CONSTRAINT `fk_Grupo_has_Cuenta_Grupo1`
    FOREIGN KEY (`Id_Grupo`)
    REFERENCES `mydb`.`Grupo` (`Id_Grupo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Grupo_has_Cuenta_Cuenta1`
    FOREIGN KEY (`Id_Cuenta`)
    REFERENCES `mydb`.`Cuenta` (`Id_Cuenta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`MoleculaN`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`MoleculaN` (
  `Id_MoleculaN` INT NOT NULL AUTO_INCREMENT,
  `Chr` CHAR(10) NULL,
  `Inicio` FLOAT NULL,
  `Fin` FLOAT NULL,
  `Sentido` CHAR(5) NULL,
  `Missmatches` SMALLINT NULL,
  `Secuencia` CHAR(45) NULL,
  `Loci` CHAR(45) NULL,
  `Numero_Moleculas` FLOAT NULL,
  `Tipo` CHAR(45) NULL,
  `Id_Biologico` CHAR(45) NULL,
  `Id_Genoma` INT NOT NULL,
  `Id_Libreria` INT NOT NULL,
  PRIMARY KEY (`Id_MoleculaN`),
  INDEX `fk_MoleculaN_Genoma1_idx` (`Id_Genoma` ASC),
  INDEX `fk_MoleculaN_Libreria1_idx` (`Id_Libreria` ASC))
ENGINE = MyISAM;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `mydb`.`Cuenta`
-- -----------------------------------------------------
START TRANSACTION;
USE `mydb`;
INSERT INTO `mydb`.`Cuenta` (`Id_Cuenta`, `Cuenta`, `Pass`, `Tipo_Cuenta`, `Nombre`, `Apellido`, `Email`, `Confirmacion`) VALUES (NULL, 'Admin', '123456', 'Administrator', 'Admin', 'Admin', 'Admin', 1);

COMMIT;

