--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 7.4.201.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 06.09.2019 16:35:02
-- Версия сервера: 5.7.14
-- Версия клиента: 4.1
--

-- 
-- Отключение внешних ключей
-- 
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- 
-- Установить режим SQL (SQL mode)
-- 
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 
-- Установка кодировки, с использованием которой клиент будет посылать запросы на сервер
--
SET NAMES 'utf8';

CREATE DATABASE iazs
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

--
-- Установка базы данных по умолчанию
--
USE iazs;

--
-- Создать таблицу `azs`
--
CREATE TABLE azs (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL,
  note varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 2,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `name` для объекта типа таблица `azs`
--
ALTER TABLE azs
ADD UNIQUE INDEX name (name);

--
-- Создать таблицу `terminals`
--
CREATE TABLE terminals (
  id int(11) NOT NULL AUTO_INCREMENT,
  azsId int(11) NOT NULL,
  login varchar(20) NOT NULL,
  psw varchar(20) NOT NULL,
  name varchar(20) NOT NULL,
  active tinyint(1) NOT NULL,
  config varchar(255) DEFAULT NULL,
  transNo int(11) NOT NULL DEFAULT 0,
  eventNo int(11) NOT NULL,
  sn varchar(10) DEFAULT NULL,
  syncDate timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 2,
AVG_ROW_LENGTH = 16384,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `login` для объекта типа таблица `terminals`
--
ALTER TABLE terminals
ADD UNIQUE INDEX login (login);

--
-- Создать внешний ключ
--
ALTER TABLE terminals
ADD CONSTRAINT FK_terminals_azs_id FOREIGN KEY (azsId)
REFERENCES azs (id) ON UPDATE CASCADE;

--
-- Создать таблицу `terminalsynchistory`
--
CREATE TABLE terminalsynchistory (
  id int(11) NOT NULL AUTO_INCREMENT,
  terminalId int(11) NOT NULL,
  date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  message varchar(255) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать внешний ключ
--
ALTER TABLE terminalsynchistory
ADD CONSTRAINT FK_terminalsync_terminals_id FOREIGN KEY (terminalId)
REFERENCES terminals (id) ON UPDATE CASCADE;

--
-- Создать таблицу `terminalevents`
--
CREATE TABLE terminalevents (
  id int(11) NOT NULL AUTO_INCREMENT,
  date timestamp NULL DEFAULT NULL,
  terminalId int(11) NOT NULL,
  eventNo int(11) NOT NULL,
  type smallint(6) NOT NULL,
  msg varchar(255) NOT NULL,
  sysInfo varchar(255) DEFAULT NULL,
  tankId int(11) DEFAULT NULL,
  tankOperVolume decimal(8, 2) DEFAULT NULL,
  tankPumpVolume decimal(8, 2) DEFAULT NULL,
  tankStateId int(11) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `date` для объекта типа таблица `terminalevents`
--
ALTER TABLE terminalevents
ADD INDEX date (date);

--
-- Создать индекс `UK_terminalevents` для объекта типа таблица `terminalevents`
--
ALTER TABLE terminalevents
ADD INDEX UK_terminalevents (date);

--
-- Создать внешний ключ
--
ALTER TABLE terminalevents
ADD CONSTRAINT FK_terminalevents_terminals_id FOREIGN KEY (terminalId)
REFERENCES terminals (id) ON UPDATE CASCADE;

--
-- Создать таблицу `fuels`
--
CREATE TABLE fuels (
  id int(11) NOT NULL,
  code int(11) NOT NULL,
  name varchar(20) NOT NULL,
  color varchar(20) DEFAULT NULL,
  note varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AVG_ROW_LENGTH = 4096,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `code` для объекта типа таблица `fuels`
--
ALTER TABLE fuels
ADD UNIQUE INDEX code (code);

--
-- Создать индекс `name` для объекта типа таблица `fuels`
--
ALTER TABLE fuels
ADD UNIQUE INDEX name (name);

--
-- Создать таблицу `tanks`
--
CREATE TABLE tanks (
  id int(11) NOT NULL AUTO_INCREMENT,
  azsId int(11) NOT NULL,
  terminalId int(11) NOT NULL,
  fuelId int(11) NOT NULL,
  number int(11) NOT NULL,
  name varchar(20) DEFAULT NULL,
  height decimal(4, 0) NOT NULL,
  capacity decimal(5, 0) NOT NULL,
  minVolume decimal(5, 0) NOT NULL,
  maxVolume decimal(5, 0) NOT NULL,
  realStateId int(11) DEFAULT NULL,
  visible smallint(6) NOT NULL DEFAULT 1,
  bookStateId int(11) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 2,
AVG_ROW_LENGTH = 16384,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `FK_tanks_fuels_id` для объекта типа таблица `tanks`
--
ALTER TABLE tanks
ADD INDEX FK_tanks_fuels_id (fuelId);

--
-- Создать индекс `FK_tanks_tankrealstates_id` для объекта типа таблица `tanks`
--
ALTER TABLE tanks
ADD INDEX FK_tanks_tankrealstates_id (realStateId);

--
-- Создать индекс `FK_tanks_terminals_id` для объекта типа таблица `tanks`
--
ALTER TABLE tanks
ADD INDEX FK_tanks_terminals_id (terminalId);

--
-- Создать внешний ключ
--
ALTER TABLE tanks
ADD CONSTRAINT FK_tanks_azs_id FOREIGN KEY (azsId)
REFERENCES azs (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tanks
ADD CONSTRAINT tanks_ibfk_1 FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tanks
ADD CONSTRAINT tanks_ibfk_3 FOREIGN KEY (terminalId)
REFERENCES terminals (id) ON UPDATE CASCADE;

--
-- Создать таблицу `tankmove`
--
CREATE TABLE tankmove (
  id int(11) NOT NULL AUTO_INCREMENT,
  date date NOT NULL,
  tankFromId int(11) NOT NULL,
  tankToId int(11) DEFAULT NULL,
  fuelId int(11) NOT NULL,
  doc varchar(255) DEFAULT NULL,
  volume decimal(8, 2) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `IX_tankmove_date` для объекта типа таблица `tankmove`
--
ALTER TABLE tankmove
ADD INDEX IX_tankmove_date (date);

--
-- Создать внешний ключ
--
ALTER TABLE tankmove
ADD CONSTRAINT FK_tankmove_fuels_id FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tankmove
ADD CONSTRAINT FK_tankmove_tanks_id FOREIGN KEY (tankFromId)
REFERENCES tanks (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tankmove
ADD CONSTRAINT FK_tankmove_tanks_id2 FOREIGN KEY (tankToId)
REFERENCES tanks (id) ON UPDATE CASCADE;

--
-- Создать таблицу `tankinventory`
--
CREATE TABLE tankinventory (
  id int(11) NOT NULL AUTO_INCREMENT,
  date date NOT NULL,
  tankId int(11) NOT NULL,
  fuelId int(11) NOT NULL,
  doc varchar(255) DEFAULT NULL,
  oldVolume decimal(8, 2) NOT NULL,
  operVolume decimal(8, 2) NOT NULL,
  operRest decimal(8, 2) NOT NULL,
  volume decimal(8, 2) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `IX_tankinventory_date` для объекта типа таблица `tankinventory`
--
ALTER TABLE tankinventory
ADD INDEX IX_tankinventory_date (date);

--
-- Создать внешний ключ
--
ALTER TABLE tankinventory
ADD CONSTRAINT FK_tankinventory_fuels_id FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tankinventory
ADD CONSTRAINT FK_tankinventory_tanks_id FOREIGN KEY (tankId)
REFERENCES tanks (id) ON UPDATE CASCADE;

--
-- Создать таблицу `pumps`
--
CREATE TABLE pumps (
  id int(11) NOT NULL AUTO_INCREMENT,
  terminalId int(11) NOT NULL,
  pumpNo tinyint(4) NOT NULL,
  nozzleNo tinyint(4) NOT NULL,
  counter decimal(10, 2) DEFAULT NULL,
  tankId int(11) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 2,
AVG_ROW_LENGTH = 16384,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `UK_pumps` для объекта типа таблица `pumps`
--
ALTER TABLE pumps
ADD UNIQUE INDEX UK_pumps (terminalId, nozzleNo, pumpNo);

--
-- Создать внешний ключ
--
ALTER TABLE pumps
ADD CONSTRAINT FK_pumps_tanks_id FOREIGN KEY (tankId)
REFERENCES tanks (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE pumps
ADD CONSTRAINT FK_pumps_terminals_id FOREIGN KEY (terminalId)
REFERENCES terminals (id) ON UPDATE CASCADE;

--
-- Создать таблицу `pumpcounters`
--
CREATE TABLE pumpcounters (
  id int(11) NOT NULL AUTO_INCREMENT,
  pumpId int(11) NOT NULL,
  tankId int(11) DEFAULT NULL,
  fuelId int(11) DEFAULT NULL,
  date date NOT NULL,
  counter decimal(10, 2) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `fuelId` для объекта типа таблица `pumpcounters`
--
ALTER TABLE pumpcounters
ADD INDEX fuelId (fuelId);

--
-- Создать индекс `tankId` для объекта типа таблица `pumpcounters`
--
ALTER TABLE pumpcounters
ADD INDEX tankId (tankId);

--
-- Создать индекс `UK_pumpCounters` для объекта типа таблица `pumpcounters`
--
ALTER TABLE pumpcounters
ADD UNIQUE INDEX UK_pumpCounters (pumpId, tankId, fuelId, date);

--
-- Создать внешний ключ
--
ALTER TABLE pumpcounters
ADD CONSTRAINT FK_pumpCounters_pumps_id FOREIGN KEY (pumpId)
REFERENCES pumps (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE pumpcounters
ADD CONSTRAINT pumpcounters_ibfk_1 FOREIGN KEY (tankId)
REFERENCES tanks (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE pumpcounters
ADD CONSTRAINT pumpcounters_ibfk_2 FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON UPDATE CASCADE;

--
-- Создать таблицу `tankrealstates`
--
CREATE TABLE tankrealstates (
  id int(11) NOT NULL AUTO_INCREMENT,
  tankId int(11) NOT NULL,
  fuelId int(11) DEFAULT NULL,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status tinyint(1) NOT NULL,
  fuelLevel decimal(5, 1) DEFAULT NULL,
  fuelVolume decimal(5, 0) DEFAULT NULL,
  fuelMass decimal(5, 0) DEFAULT NULL,
  temperature decimal(3, 1) DEFAULT NULL,
  density decimal(4, 4) DEFAULT NULL,
  waterLevel decimal(5, 1) DEFAULT NULL,
  waterVolume decimal(5, 0) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `IX_tankrealstate_date` для объекта типа таблица `tankrealstates`
--
ALTER TABLE tankrealstates
ADD INDEX IX_tankrealstate_date (date);

--
-- Создать внешний ключ
--
ALTER TABLE tankrealstates
ADD CONSTRAINT FK_tankrealstate_tanks_id FOREIGN KEY (tankId)
REFERENCES tanks (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tankrealstates
ADD CONSTRAINT FK_tankrealstates_fuels_id FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tanks
ADD CONSTRAINT tanks_ibfk_2 FOREIGN KEY (realStateId)
REFERENCES tankrealstates (id) ON UPDATE CASCADE;

DELIMITER $$

--
-- Создать процедуру `terminalSetTankState`
--
CREATE DEFINER = 'root'@'localhost'
PROCEDURE terminalSetTankState (IN `in_terminalId` int, IN `in_tankNumber` int, IN `in_date` timestamp, IN `in_status` int, IN `in_fuelLevel` decimal(5, 1), IN `in_fuelVolume` decimal(5, 0), IN `in_fuelMass` decimal(5, 0), IN `in_temperature` decimal(3, 1), IN `in_density` decimal(4, 4), IN `in_waterLevel` decimal(5, 1), IN `in_waterVolume` decimal(5, 0))
BEGIN
LABEL_EXEC:
  BEGIN
    DECLARE l_date timestamp DEFAULT NULL;
    DECLARE l_tankId int DEFAULT NULL;
    DECLARE l_fuelId int DEFAULT NULL;
    DECLARE l_stateId int DEFAULT NULL;

    SELECT
      COALESCE(in_date, CURRENT_TIMESTAMP);
    SELECT
      id,
      fuelId INTO l_tankId, l_fuelId
    FROM tanks
    WHERE terminalId = in_terminalId
    AND number = in_tankNumber LIMIT 1;

    IF l_tankId IS NULL THEN
      LEAVE LABEL_EXEC;
    END IF;


    IF (in_status = 0) THEN
      INSERT INTO tankrealstates (tankId, fuelId, `date`, `status`, fuelLevel, fuelVolume, fuelMass, temperature, density, waterLevel, waterVolume)
        VALUES (l_tankId, l_fuelId, l_date, in_status, in_fuelLevel, in_fuelVolume, in_fuelMass, in_temperature, in_density, in_waterLevel, in_waterVolume);
    ELSE
      INSERT INTO tankrealstates (tankId, fuelId, `date`, `status`, fuelLevel, fuelVolume, fuelMass, temperature, density, waterLevel, waterVolume)
        VALUES (l_tankId, l_fuelId, l_date, in_status, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
    END IF;

    SELECT
      LAST_INSERT_ID() INTO l_stateId;
    UPDATE tanks
    SET realStateId = l_stateId
    WHERE id = l_tankId;

  END;
END
$$

--
-- Создать процедуру `tankSetRealState`
--
CREATE DEFINER = 'root'@'localhost'
PROCEDURE tankSetRealState (IN `in_tankId` int, IN `in_date` timestamp, IN `in_status` int, IN `in_fuelLevel` decimal(5, 1), IN `in_fuelVolume` decimal(5, 0), IN `in_fuelMass` decimal(5, 0), IN `in_temperature` decimal(3, 1), IN `in_density` decimal(4, 4), IN `in_waterLevel` decimal(5, 1), IN `in_waterVolume` decimal(5, 0))
BEGIN
  DECLARE l_stateId int DEFAULT NULL;
  DECLARE l_fuelId int DEFAULT NULL;

  IF (in_date IS NULL) THEN
    SET in_date = CURRENT_TIMESTAMP;
  END IF;
  IF (in_status <> 0) THEN
    SET in_fuelLevel = NULL;
    SET in_fuelVolume = NULL;
    SET in_fuelMass = NULL;
    SET in_temperature = NULL;
    SET in_density = NULL;
    SET in_waterLevel = NULL;
    SET in_waterVolume = NULL;
  END IF;
  SELECT
    fuelId INTO l_fuelId
  FROM tanks
  WHERE id = in_tankId;

  SELECT
    id INTO l_stateId
  FROM tankrealstates
  WHERE tankId = in_tankId
  AND fuelId = l_fuelId
  AND CAST(`date` AS date) = CAST(in_date AS date)
  ORDER BY id DESC
  LIMIT 1;

  IF l_stateId IS NULL THEN
    INSERT INTO tankrealstates (tankId, fuelId, `date`, `status`, fuelLevel, fuelVolume, fuelMass, temperature, density, waterLevel, waterVolume)
      VALUES (in_tankId, l_fuelId, in_date, in_status, in_fuelLevel, in_fuelVolume, in_fuelMass, in_temperature, in_density, in_waterLevel, in_waterVolume);
    SELECT
      LAST_INSERT_ID() INTO l_stateId;
    UPDATE tanks
    SET realStateId = l_stateId
    WHERE id = in_tankId;
  ELSE
    UPDATE tankrealstates
    SET `date` = in_date,
        `status` = in_status,
        fuelLevel = in_fuelLevel,
        fuelVolume = in_fuelVolume,
        fuelMass = in_fuelMass,
        temperature = in_temperature,
        density = in_density,
        waterLevel = in_waterLevel,
        waterVolume = in_waterVolume
    WHERE id = l_stateId;
  END IF;

END
$$

DELIMITER ;

--
-- Создать таблицу `tankbookstates`
--
CREATE TABLE tankbookstates (
  id int(11) NOT NULL AUTO_INCREMENT,
  date date NOT NULL,
  tankId int(11) NOT NULL,
  fuelId int(11) NOT NULL,
  incomeVolume decimal(8, 2) NOT NULL DEFAULT 0.00,
  moveInVolume decimal(8, 2) NOT NULL DEFAULT 0.00,
  moveOutVolume decimal(8, 2) NOT NULL DEFAULT 0.00,
  saleVolume decimal(8, 2) NOT NULL DEFAULT 0.00,
  fuelVolume decimal(8, 2) NOT NULL DEFAULT 0.00,
  serviceVolume decimal(8, 2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `fuelId` для объекта типа таблица `tankbookstates`
--
ALTER TABLE tankbookstates
ADD INDEX fuelId (fuelId);

--
-- Создать индекс `IX_tankbookstate_date` для объекта типа таблица `tankbookstates`
--
ALTER TABLE tankbookstates
ADD INDEX IX_tankbookstate_date (date);

--
-- Создать индекс `UK_tankbookstates` для объекта типа таблица `tankbookstates`
--
ALTER TABLE tankbookstates
ADD UNIQUE INDEX UK_tankbookstates (tankId, date);

--
-- Создать внешний ключ
--
ALTER TABLE tankbookstates
ADD CONSTRAINT FK_tankbookstate_tanks_id FOREIGN KEY (tankId)
REFERENCES tanks (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tankbookstates
ADD CONSTRAINT tankbookstates_ibfk_1 FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tanks
ADD CONSTRAINT FK_tanks_tankbookstates_id FOREIGN KEY (bookStateId)
REFERENCES tankbookstates (id) ON UPDATE CASCADE;

DELIMITER $$

--
-- Создать процедуру `tankBookStateUpd`
--
CREATE DEFINER = 'root'@'localhost'
PROCEDURE tankBookStateUpd (IN `in_date` date, IN `in_oper` int, IN `in_tankId` int, IN `in_volume` decimal(8, 2))
BEGIN


  DECLARE l_tankStateId int DEFAULT NULL;
  DECLARE l_fuelId int DEFAULT NULL;
  DECLARE l_volume decimal(8, 2) DEFAULT NULL;
  DECLARE l_v decimal(8, 2) DEFAULT NULL;
  DECLARE l_v1 decimal(8, 2) DEFAULT NULL;
  DECLARE l_cut_state_date date DEFAULT NULL;

  SELECT
    id INTO l_tankStateId
  FROM tankbookstates
  WHERE tankId = in_tankId
  AND `date` = in_date;

  IF (l_tankStateId IS NULL) THEN
    SELECT
      fuelId INTO l_fuelId
    FROM tanks
    WHERE id = in_tankId;
    SELECT
      fuelVolume INTO l_volume
    FROM tankbookstates
    WHERE tankId = in_tankId
    AND `date` < in_date
    ORDER BY `date` DESC LIMIT 1;
    IF (l_volume IS NULL) THEN
      SET l_volume = 0;
    END IF;
    INSERT INTO tankbookstates (`date`, tankId, fuelId, fuelVolume)
      VALUES (in_date, in_tankId, l_fuelId, l_volume);
    SELECT
      LAST_INSERT_ID() INTO l_tankStateId;
  END IF;

  IF (in_oper = 0) THEN
    SET l_volume = in_volume;
  ELSEIF (in_oper = 1) THEN
    UPDATE tankbookstates
    SET incomeVolume = incomeVolume + in_volume
    WHERE id = l_tankStateId;
    SET l_volume = in_volume;
  ELSEIF (in_oper = 2) THEN
    UPDATE tankbookstates
    SET moveInVolume = moveInVolume + in_volume
    WHERE id = l_tankStateId;
    SET l_volume = in_volume;
  ELSEIF (in_oper = 3) THEN
    UPDATE tankbookstates
    SET moveOutVolume = moveOutVolume + in_volume
    WHERE id = l_tankStateId;
    SET l_volume = -in_volume;
  ELSEIF (in_oper = 4) THEN
    UPDATE tankbookstates
    SET saleVolume = saleVolume + in_volume
    WHERE id = l_tankStateId;
    SET l_volume = -in_volume;
  ELSEIF (in_oper = 5) THEN
    UPDATE tankbookstates
    SET serviceVolume = serviceVolume + in_volume
    WHERE id = l_tankStateId;
    SET l_volume = 0;
  END IF;

  UPDATE tankbookstates
  SET fuelvolume = fuelvolume + l_volume
  WHERE tankId = in_tankId
  AND `date` >= in_date;
  SELECT
    s.`date` INTO l_cut_state_date
  FROM tanks t,
       tankbookstates s
  WHERE t.id = in_tankId
  AND s.id = t.bookStateId;

  IF ((l_cut_state_date IS NULL)
    OR (in_date >= l_cut_state_date)) THEN
    UPDATE tanks
    SET bookStateId = l_tankStateId
    WHERE id = in_tankId;
  END IF;
END
$$

--
-- Создать процедуру `tankBookStateIdByDate`
--
CREATE DEFINER = 'root'@'localhost'
PROCEDURE tankBookStateIdByDate (IN `in_date` date, IN `in_tankId` int, OUT `out_stateId` int)
BEGIN
  DECLARE l_fuelId int DEFAULT NULL;
  DECLARE l_volume decimal(8, 2) DEFAULT NULL;

  SELECT
    id INTO out_stateId
  FROM tankbookstates
  WHERE tankId = in_tankId
  AND `date` = in_date;

  IF (out_stateId IS NULL) THEN
    SELECT
      fuelId INTO l_fuelId
    FROM tanks
    WHERE id = in_tankId;
    SELECT
      fuelVolume INTO l_volume
    FROM tankbookstates
    WHERE tankId = in_tankId
    AND `date` < in_date
    ORDER BY `date` DESC LIMIT 1;
    IF (l_volume IS NULL) THEN
      SET l_volume = 0;
    END IF;
    INSERT INTO tankbookstates (`date`, tankId, fuelId, fuelVolume)
      VALUES (in_date, in_tankId, l_fuelId, l_volume);
    SELECT
      LAST_INSERT_ID() INTO out_stateId;
  END IF;
END
$$

DELIMITER ;

--
-- Создать таблицу `organizations`
--
CREATE TABLE organizations (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  description varchar(255) DEFAULT NULL,
  controlBalance tinyint(4) NOT NULL DEFAULT 0,
  version int(11) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AVG_ROW_LENGTH = 819,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `name` для объекта типа таблица `organizations`
--
ALTER TABLE organizations
ADD UNIQUE INDEX name (name);

--
-- Создать таблицу `organizationoperations`
--
CREATE TABLE organizationoperations (
  id int(11) NOT NULL AUTO_INCREMENT,
  type smallint(6) NOT NULL,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  orgId int(11) DEFAULT NULL,
  fuelId int(11) NOT NULL,
  volume decimal(8, 2) NOT NULL,
  balance decimal(8, 2) NOT NULL,
  note varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AVG_ROW_LENGTH = 4096,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать внешний ключ
--
ALTER TABLE organizationoperations
ADD CONSTRAINT FK_organizationoperations_fuels_id FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE organizationoperations
ADD CONSTRAINT FK_organizationoperations_organizations_id FOREIGN KEY (orgId)
REFERENCES organizations (id) ON UPDATE CASCADE;

--
-- Создать таблицу `organizationbalance`
--
CREATE TABLE organizationbalance (
  id int(11) NOT NULL AUTO_INCREMENT,
  orgId int(11) NOT NULL,
  fuelId int(11) NOT NULL,
  volume decimal(10, 2) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AVG_ROW_LENGTH = 8192,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать внешний ключ
--
ALTER TABLE organizationbalance
ADD CONSTRAINT FK_organizationbalance_fuels_id FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE organizationbalance
ADD CONSTRAINT FK_organizationbalance_organizations_id FOREIGN KEY (orgId)
REFERENCES organizations (id) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Создать таблицу `cards`
--
CREATE TABLE cards (
  id int(11) NOT NULL AUTO_INCREMENT,
  number varchar(20) NOT NULL,
  state tinyint(4) NOT NULL,
  type tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 - СЃС‚Р°РЅРґР°СЂС‚РЅР°СЏ
9 - Р±РµР·Р»РёРјРёС‚РЅР°СЏ',
  expire date NOT NULL,
  ownerType smallint(6) NOT NULL DEFAULT 0,
  orgId int(11) DEFAULT NULL,
  tankToId int(11) DEFAULT NULL,
  owner varchar(60) NOT NULL,
  description varchar(255) DEFAULT NULL,
  update_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  pin char(4) DEFAULT NULL,
  version int(11) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `number` для объекта типа таблица `cards`
--
ALTER TABLE cards
ADD UNIQUE INDEX number (number);

--
-- Создать индекс `ownerType` для объекта типа таблица `cards`
--
ALTER TABLE cards
ADD INDEX ownerType (ownerType);

--
-- Создать индекс `tankToId` для объекта типа таблица `cards`
--
ALTER TABLE cards
ADD INDEX tankToId (tankToId);

--
-- Создать внешний ключ
--
ALTER TABLE cards
ADD CONSTRAINT cards_ibfk_1 FOREIGN KEY (orgId)
REFERENCES organizations (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE cards
ADD CONSTRAINT cards_ibfk_2 FOREIGN KEY (tankToId)
REFERENCES tanks (id) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Создать таблицу `cardoperations`
--
CREATE TABLE cardoperations (
  id int(11) NOT NULL AUTO_INCREMENT,
  operationType smallint(6) NOT NULL COMMENT '1 - РїРѕРїРѕР»РЅРµРЅРёРµ РѕРїРµСЂР°С‚РѕСЂРѕРј
',
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cardId int(11) NOT NULL,
  orgId int(11) DEFAULT NULL,
  autoCardId int(11) DEFAULT NULL,
  fuelId int(11) NOT NULL,
  volume decimal(8, 2) NOT NULL,
  balance decimal(8, 2) NOT NULL,
  state tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 - РїСЂРѕРІРµРґРµРЅРѕ, 1 - РІ РїСЂРѕС†РµСЃСЃРµ',
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `autoCardId` для объекта типа таблица `cardoperations`
--
ALTER TABLE cardoperations
ADD INDEX autoCardId (autoCardId);

--
-- Создать индекс `FK_cardoperations_cardbalance_id` для объекта типа таблица `cardoperations`
--
ALTER TABLE cardoperations
ADD INDEX FK_cardoperations_cardbalance_id (balance);

--
-- Создать индекс `IX_cardoperations_date` для объекта типа таблица `cardoperations`
--
ALTER TABLE cardoperations
ADD INDEX IX_cardoperations_date (date);

--
-- Создать индекс `orgId` для объекта типа таблица `cardoperations`
--
ALTER TABLE cardoperations
ADD INDEX orgId (orgId);

--
-- Создать внешний ключ
--
ALTER TABLE cardoperations
ADD CONSTRAINT FK_cardoperations_cards_id FOREIGN KEY (cardId)
REFERENCES cards (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE cardoperations
ADD CONSTRAINT FK_cardoperations_fuels_id FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE cardoperations
ADD CONSTRAINT cardoperations_ibfk_1 FOREIGN KEY (autoCardId)
REFERENCES cards (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE cardoperations
ADD CONSTRAINT cardoperations_ibfk_2 FOREIGN KEY (orgId)
REFERENCES organizations (id) ON UPDATE CASCADE;

--
-- Создать таблицу `pumptransactions`
--
CREATE TABLE pumptransactions (
  operId int(11) NOT NULL,
  state tinyint(4) NOT NULL,
  terminalId int(11) NOT NULL,
  pumpId int(11) DEFAULT NULL,
  counterBegin decimal(10, 2) DEFAULT NULL,
  counterEnd decimal(10, 2) DEFAULT NULL,
  tankId int(11) DEFAULT NULL,
  fuelId int(11) NOT NULL,
  transNo int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (operId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `fuelId` для объекта типа таблица `pumptransactions`
--
ALTER TABLE pumptransactions
ADD INDEX fuelId (fuelId);

--
-- Создать индекс `pumpId` для объекта типа таблица `pumptransactions`
--
ALTER TABLE pumptransactions
ADD INDEX pumpId (pumpId);

--
-- Создать индекс `tankId` для объекта типа таблица `pumptransactions`
--
ALTER TABLE pumptransactions
ADD INDEX tankId (tankId);

--
-- Создать внешний ключ
--
ALTER TABLE pumptransactions
ADD CONSTRAINT FK_pumptransactions_cardoperations_id FOREIGN KEY (operId)
REFERENCES cardoperations (id) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE pumptransactions
ADD CONSTRAINT FK_pumptransactions_terminals_id FOREIGN KEY (terminalId)
REFERENCES terminals (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE pumptransactions
ADD CONSTRAINT pumptransactions_ibfk_1 FOREIGN KEY (pumpId)
REFERENCES pumps (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE pumptransactions
ADD CONSTRAINT pumptransactions_ibfk_2 FOREIGN KEY (tankId)
REFERENCES tanks (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE pumptransactions
ADD CONSTRAINT pumptransactions_ibfk_3 FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON UPDATE CASCADE;

DELIMITER $$

--
-- Создать процедуру `makeBookState`
--
CREATE DEFINER = 'root'@'localhost'
PROCEDURE makeBookState ()
BEGIN
  DECLARE done int;
  DECLARE `date` timestamp;
  DECLARE tankId int;
  DECLARE volume decimal(8, 2);
  DECLARE cur CURSOR FOR
  SELECT
    o.`date`,
    t.tankId,
    -o.volume
  FROM pumptransactions t,
       cardoperations o
  WHERE o.id = t.operId
  AND o.operationType = 2
  AND t.tankId IS NOT NULL
  ORDER BY o.`date`;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  UPDATE tankbookstates
  SET fuelVolume = fuelVolume + saleVolume,
      saleVolume = 0;

  SET done = 0;
  OPEN cur;
  FETCH cur INTO `date`, tankId, volume;
  WHILE done = 0 DO
    CALL tankBookStateUpd(`date`, 4, tankId, volume);
    FETCH cur INTO `date`, tankId, volume;
  END WHILE;
  CLOSE cur;

END
$$

DELIMITER ;

--
-- Создать таблицу `cardlimits`
--
CREATE TABLE cardlimits (
  id int(11) NOT NULL AUTO_INCREMENT,
  cardId int(11) NOT NULL,
  limitType tinyint(4) NOT NULL,
  fuelId int(11) NOT NULL,
  orderVolume decimal(8, 2) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать внешний ключ
--
ALTER TABLE cardlimits
ADD CONSTRAINT FK_cardlimits_cards_id FOREIGN KEY (cardId)
REFERENCES cards (id) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE cardlimits
ADD CONSTRAINT FK_cardlimits_fuels_id FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Создать таблицу `cardbalance`
--
CREATE TABLE cardbalance (
  id int(11) NOT NULL AUTO_INCREMENT,
  cardId int(11) NOT NULL,
  fuelId int(11) NOT NULL,
  volume decimal(8, 2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `FK_cardbalance_fuels_id` для объекта типа таблица `cardbalance`
--
ALTER TABLE cardbalance
ADD INDEX FK_cardbalance_fuels_id (fuelId);

--
-- Создать индекс `UK_cardbalance` для объекта типа таблица `cardbalance`
--
ALTER TABLE cardbalance
ADD UNIQUE INDEX UK_cardbalance (cardId, fuelId);

--
-- Создать внешний ключ
--
ALTER TABLE cardbalance
ADD CONSTRAINT cardbalance_ibfk_1 FOREIGN KEY (cardId)
REFERENCES cards (id) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE cardbalance
ADD CONSTRAINT cardbalance_ibfk_2 FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$

--
-- Создать функцию `getEnabledVolumeByCard`
--
CREATE DEFINER = 'root'@'localhost'
FUNCTION getEnabledVolumeByCard (`in_cardId` int, `in_fuelId` int)
RETURNS decimal(8, 2)
BEGIN
  DECLARE l_orgId int DEFAULT NULL;
  DECLARE l_cardType int DEFAULT NULL;
  DECLARE l_state int DEFAULT NULL;
  DECLARE l_volume decimal(8, 2) DEFAULT NULL;

  SELECT
    `type`,
    state,
    orgId INTO l_cardType, l_state, l_orgId
  FROM cards
  WHERE id = in_cardId;
  IF l_state <> 0 THEN
    RETURN 0;
  END IF;

  IF l_cardType = 0 THEN /* debit */
    SELECT
      volume INTO l_volume
    FROM cardbalance
    WHERE (cardId = in_cardId)
    AND (fuelId = in_fuelId);

  ELSEIF l_cardType = 3 THEN /* limited */
  BEGIN
    DECLARE l_limit_type int;
    DECLARE l_limit_volume decimal(8, 2);
    DECLARE c_limits_eof int DEFAULT 0;
    DECLARE c_limits CURSOR FOR
    SELECT
      limitType,
      orderVolume
    FROM cardlimits
    WHERE cardId = in_cardId
    AND fuelId = in_fuelId;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET c_limits_eof = 1;

    SET l_volume = NULL;
    OPEN c_limits;
    FETCH c_limits INTO l_limit_type, l_limit_volume;
    WHILE c_limits_eof = 0 DO
    BEGIN
      DECLARE v decimal(8, 2) DEFAULT NULL;

      IF l_limit_type = 1 THEN /* summary */
        SET v = l_limit_volume;
      ELSEIF l_limit_type = 2 THEN /* one */
        SET v = l_limit_volume;
      ELSEIF l_limit_type = 3 THEN /* day */
      BEGIN
        DECLARE v1 decimal(8, 2) DEFAULT NULL;
        SELECT
          SUM(-volume) INTO v1
        FROM cardoperations
        WHERE (CAST(`date` AS date) = CURDATE())
        AND (cardId = in_cardId)
        AND (fuelId = in_fuelId);
        SET v = l_limit_volume - COALESCE(v1, 0);
      END;
      END IF;

      IF v IS NOT NULL THEN
        IF l_volume IS NULL THEN
          SET l_volume = v;
        ELSEIF v < l_volume THEN
          SET l_volume = v;
        END IF;
      END IF;
    END;
      FETCH c_limits INTO l_limit_type, l_limit_volume;
    END WHILE;
    CLOSE c_limits;
  END;

  ELSEIF l_cardType = 4 THEN /* service */
    SET l_volume = 999999.99;

  ELSEIF l_cardType = 5 THEN /* move */
    SET l_volume = 999999.99;
  END IF;


  IF (l_orgId IS NOT NULL
    AND (l_cardType = 0
    OR l_cardType = 3)) THEN
  BEGIN
    DECLARE l_org_volume decimal(8, 2) DEFAULT NULL;
    SELECT
      COALESCE(b.volume, 0) INTO l_org_volume
    FROM organizationbalance b
    WHERE b.orgId = l_orgId
    AND b.fuelId = in_fuelId;
    IF l_volume IS NULL
      OR l_volume > l_org_volume THEN
      SET l_volume = l_org_volume;
    END IF;
  END;
  END IF;


  RETURN l_volume;
END
$$

--
-- Создать процедуру `terminalAddTransaction`
--
CREATE DEFINER = 'root'@'localhost'
PROCEDURE terminalAddTransaction (IN `in_terminalId` int, IN `in_number` int, IN `in_operId` int, IN `in_date` timestamp, IN `in_cardNumber` varchar(12), IN `in_fuelCode` int, IN `in_volume` decimal(8, 2), IN `in_pumpNo` int, IN `in_nozzleNo` int, IN `in_tankNo` int, IN `in_pumpCounter` decimal(10, 2), IN `in_autoCardNumber` varchar(12))
BEGIN
LABEL_EXEC:
  BEGIN
    DECLARE l_azsId int DEFAULT NULL;
    DECLARE l_terminalId int DEFAULT NULL;
    DECLARE l_lastTransNo int DEFAULT NULL;
    DECLARE l_orgId int DEFAULT NULL;
    DECLARE l_cardId int DEFAULT NULL;
    DECLARE l_cardType int DEFAULT NULL;
    DECLARE l_autoCardId int DEFAULT NULL;
    DECLARE l_autoCardType int DEFAULT NULL;
    DECLARE l_fuelId int DEFAULT NULL;
    DECLARE l_tankId int DEFAULT NULL;
    DECLARE l_pumpId int DEFAULT NULL;
    DECLARE l_cardBalance decimal(8, 2);
    DECLARE l_operId int DEFAULT NULL;
    DECLARE l_operType int DEFAULT NULL;

    SELECT
      SUBSTR(in_cardNumber, 5, 8) INTO in_cardNumber;
    SELECT
      SUBSTR(in_autoCardNumber, 5, 8) INTO in_autoCardNumber;
    SELECT
      id,
      transNo,
      azsId INTO l_terminalId, l_lastTransNo, l_azsId
    FROM terminals
    WHERE id = in_terminalId;

    IF l_lastTransNo + 1 <> in_number THEN
      LEAVE LABEL_EXEC;
    END IF;

    SELECT
      id,
      orgId,
      `type` INTO l_cardId, l_orgId, l_cardType
    FROM cards
    WHERE number LIKE CONCAT('%', in_cardNumber);
    IF l_cardId IS NULL THEN
      INSERT INTO cards (number, state, `type`, expire, ownerType, `owner`)
        VALUES (CONCAT('0000', in_cardNumber), 1, 0, '2000-01-01 00:00:00', 0, '<н/д>');
      SELECT
        LAST_INSERT_ID() INTO l_cardId;
    END IF;

    IF (in_autoCardNumber IS NOT NULL)
      AND (in_autoCardNumber <> '')
      AND (in_autoCardNumber <> '00000000') THEN
      SELECT
        id,
        `type` INTO l_autoCardId, l_autoCardType
      FROM cards
      WHERE number LIKE CONCAT('%', in_autoCardNumber);
      IF l_autoCardId IS NULL THEN
        INSERT INTO cards (number, state, `type`, expire, ownerType, `owner`)
          VALUES (CONCAT('0000', in_autoCardNumber), 1, 0, '2000-01-01 00:00:00', 2, '<н/д>');
        SELECT
          LAST_INSERT_ID() INTO l_autoCardId;
      END IF;
    END IF;

    SELECT
      id INTO l_fuelId
    FROM fuels
    WHERE code = in_fuelCode;

    SELECT
      id INTO l_tankId
    FROM tanks
    WHERE terminalId = l_terminalId
    AND number = in_tankNo LIMIT 1;
    IF l_tankId IS NULL THEN
      SELECT
        id INTO l_tankId
      FROM tanks
      WHERE azsId = l_azsId
      AND number = in_tankNo LIMIT 1;
    END IF;

    SELECT
      id INTO l_pumpId
    FROM pumps
    WHERE terminalId = l_terminalId
    AND pumpNo = in_pumpNo
    AND nozzleNo = in_nozzleNo;
    IF (l_pumpId IS NOT NULL)
      AND (l_tankId IS NOT NULL) THEN
      UPDATE pumps
      SET tankId = l_tankId
      WHERE id = l_pumpId;
    END IF;

    SELECT
      getEnabledVolumeByCard(l_cardId, l_fuelId) INTO l_cardBalance;


    IF l_cardType = 4 THEN
      SET l_operType = 4;
    ELSEIF l_cardType = 5 THEN
      SET l_operType = 5;
    ELSEIF in_operId = 1 THEN
      SET l_operType = 2;
    ELSEIF in_operId = 9 THEN
      SET l_operType = 3;
    END IF;

    INSERT INTO cardoperations (operationType, `date`, orgId, cardId, autoCardId, fuelId, volume, balance, state)
      VALUES (l_operType, in_date, l_orgId, l_cardId, l_autoCardId, l_fuelId, -in_volume, l_cardBalance - in_volume, 0);
    SELECT
      LAST_INSERT_ID() INTO l_operId;



    INSERT INTO pumptransactions (operId, state, terminalId, pumpId, counterBegin, counterEnd, tankId, fuelId, transNo)
      VALUES (l_operId, 0, l_terminalId, l_pumpId, in_pumpCounter - in_volume, in_pumpCounter, l_tankId, l_fuelId, in_number);


    IF l_operType = 2 THEN
      IF l_cardType = 0 THEN
        UPDATE cardbalance
        SET volume = volume - in_volume
        WHERE cardId = l_cardId
        AND fuelId = l_fuelId;
      ELSEIF l_cardType = 3 THEN
        UPDATE cardlimits
        SET orderVolume = orderVolume - in_volume
        WHERE cardId = l_cardId
        AND fuelId = l_fuelId
        AND limitType = 1;
      END IF;

      IF l_autoCardType = 0 THEN
        UPDATE cardbalance
        SET volume = volume - in_volume
        WHERE cardId = l_autoCardId
        AND fuelId = l_fuelId;
      ELSEIF l_autoCardType = 3 THEN
        UPDATE cardlimits
        SET orderVolume = orderVolume - in_volume
        WHERE cardId = l_autoCardId
        AND fuelId = l_fuelId
        AND limitType = 1;
      END IF;
    END IF;

    IF (l_orgId IS NOT NULL)
      AND (l_cardType = 0
      OR l_cardType = 3) THEN
      UPDATE organizationbalance b
      SET b.volume = b.volume - in_volume
      WHERE b.orgId = l_orgId
      AND b.fuelId = l_fuelId;
    END IF;

    UPDATE organizations
    SET `version` = NULL
    WHERE id = l_orgId;
    UPDATE cards
    SET `version` = NULL
    WHERE id = l_cardId;
    UPDATE pumps
    SET counter = in_pumpCounter
    WHERE id = l_pumpId;
    UPDATE terminals
    SET transNo = in_number
    WHERE id = l_terminalId;

  END;
END
$$

DELIMITER ;

--
-- Создать таблицу `users`
--
CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(20) NOT NULL,
  password varchar(20) NOT NULL,
  fullname varchar(50) NOT NULL,
  description varchar(255) DEFAULT NULL,
  token varchar(255) DEFAULT NULL,
  last_activity timestamp NULL DEFAULT NULL,
  last_ip char(15) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 4,
AVG_ROW_LENGTH = 5461,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `login` для объекта типа таблица `users`
--
ALTER TABLE users
ADD UNIQUE INDEX login (username);

--
-- Создать таблицу `cardrefill`
--
CREATE TABLE cardrefill (
  operId int(11) NOT NULL,
  userId int(11) NOT NULL,
  document varchar(255) DEFAULT NULL,
  PRIMARY KEY (operId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать внешний ключ
--
ALTER TABLE cardrefill
ADD CONSTRAINT FK_cardrefill_cardoperations_id FOREIGN KEY (operId)
REFERENCES cardoperations (id) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE cardrefill
ADD CONSTRAINT FK_cardrefill_users_id FOREIGN KEY (userId)
REFERENCES users (id) ON UPDATE CASCADE;

DELIMITER $$

--
-- Создать процедуру `cardRefill2`
--
CREATE DEFINER = 'root'@'localhost'
PROCEDURE cardRefill2 (IN in_date timestamp, IN `in_userId` int(11), IN `in_cardId` int(11), IN `in_fuelId` int(11), IN `in_volume` decimal(8, 2), IN `in_document` varchar(255))
BEGIN
  DECLARE l_cardBalanceId int DEFAULT NULL;
  DECLARE l_cardBalanceVolume decimal(8, 2) DEFAULT NULL;
  DECLARE l_operId int DEFAULT NULL;

  SELECT
    id,
    volume INTO l_cardBalanceId, l_cardBalanceVolume
  FROM cardbalance
  WHERE cardId = in_cardId
  AND fuelId = in_fuelId;

  IF l_cardBalanceId IS NULL THEN
    INSERT INTO cardbalance (cardId, fuelId, volume)
      VALUES (in_cardId, in_fuelId, in_volume);
    SELECT
      LAST_INSERT_ID() INTO l_cardBalanceId;
    SET l_cardBalanceVolume = 0;
  END IF;

  SET l_cardBalanceVolume = l_cardBalanceVolume + in_volume;
  INSERT INTO cardoperations (date, operationType, cardId, fuelId, volume, balance)
    VALUES (in_date, 1, in_cardId, in_fuelId, in_volume, l_cardBalanceVolume);
  SELECT
    LAST_INSERT_ID() INTO l_operId;
  INSERT INTO cardrefill (operId, userId, document)
    VALUES (l_operId, in_userId, in_document);


  UPDATE cardbalance
  SET volume = l_cardBalanceVolume
  WHERE id = l_cardBalanceId;

END
$$

DELIMITER ;

--
-- Создать таблицу `suppliers`
--
CREATE TABLE suppliers (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  description varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `name` для объекта типа таблица `suppliers`
--
ALTER TABLE suppliers
ADD UNIQUE INDEX name (name);

--
-- Создать таблицу `tankincome`
--
CREATE TABLE tankincome (
  id int(11) NOT NULL AUTO_INCREMENT,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tankId int(11) NOT NULL,
  fuelId int(11) NOT NULL,
  doc varchar(255) DEFAULT NULL,
  supplierId int(11) NOT NULL,
  volume decimal(6, 0) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `IX_tankincome_date` для объекта типа таблица `tankincome`
--
ALTER TABLE tankincome
ADD INDEX IX_tankincome_date (date);

--
-- Создать индекс `IX_tankincome_doc` для объекта типа таблица `tankincome`
--
ALTER TABLE tankincome
ADD INDEX IX_tankincome_doc (doc);

--
-- Создать внешний ключ
--
ALTER TABLE tankincome
ADD CONSTRAINT FK_tankincome_fuels_id FOREIGN KEY (fuelId)
REFERENCES fuels (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tankincome
ADD CONSTRAINT FK_tankincome_suppliers_id FOREIGN KEY (supplierId)
REFERENCES suppliers (id) ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE tankincome
ADD CONSTRAINT FK_tankincome_tanks_id FOREIGN KEY (tankId)
REFERENCES tanks (id) ON UPDATE CASCADE;

--
-- Создать таблицу `authitem`
--
CREATE TABLE authitem (
  name varchar(64) NOT NULL DEFAULT '',
  type int(11) NOT NULL,
  description text DEFAULT NULL,
  bizrule text DEFAULT NULL,
  data text DEFAULT NULL,
  PRIMARY KEY (name)
)
ENGINE = INNODB,
AVG_ROW_LENGTH = 1489,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать таблицу `authitemchild`
--
CREATE TABLE authitemchild (
  parent varchar(64) NOT NULL DEFAULT '',
  child varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (parent, child)
)
ENGINE = INNODB,
AVG_ROW_LENGTH = 1092,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать внешний ключ
--
ALTER TABLE authitemchild
ADD CONSTRAINT FK_authitemchild_authitem_name FOREIGN KEY (parent)
REFERENCES authitem (name) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Создать внешний ключ
--
ALTER TABLE authitemchild
ADD CONSTRAINT FK_authitemchild_authitem_name2 FOREIGN KEY (child)
REFERENCES authitem (name) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Создать таблицу `authassignment`
--
CREATE TABLE authassignment (
  itemname varchar(64) NOT NULL DEFAULT '',
  userid varchar(64) NOT NULL DEFAULT '',
  bizrule text DEFAULT NULL,
  data text DEFAULT NULL,
  PRIMARY KEY (itemname, userid)
)
ENGINE = INNODB,
AVG_ROW_LENGTH = 5461,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать внешний ключ
--
ALTER TABLE authassignment
ADD CONSTRAINT FK_authassignment_authitem_name FOREIGN KEY (itemname)
REFERENCES authitem (name);

--
-- Создать таблицу `generators`
--
CREATE TABLE generators (
  name varchar(255) NOT NULL DEFAULT '',
  value int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (name)
)
ENGINE = INNODB,
AVG_ROW_LENGTH = 16384,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

DELIMITER $$

--
-- Создать функцию `gen_id`
--
CREATE DEFINER = 'root'@'localhost'
FUNCTION gen_id (`in_gen_name` varchar(255))
RETURNS int(11)
BEGIN
  DECLARE res int DEFAULT NULL;

  SELECT
    `value` INTO res
  FROM generators
  WHERE `name` = in_gen_name;
  SET res = res + 1;
  UPDATE generators
  SET `value` = res
  WHERE `name` = in_gen_name;
  RETURN res;
END
$$

DELIMITER ;

--
-- Создать таблицу `syslog`
--
CREATE TABLE syslog (
  id int(11) NOT NULL AUTO_INCREMENT,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  user varchar(20) DEFAULT NULL,
  message varchar(1000) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AVG_ROW_LENGTH = 585,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

-- 
-- Вывод данных для таблицы azs
--
INSERT INTO azs VALUES
(1, 'АЗС1', NULL);

-- 
-- Вывод данных для таблицы terminals
--
INSERT INTO terminals VALUES
(1, 1, 'TM1', 'TM1', 'TM1', 1, '', 0, 0, '00000', NULL);

-- 
-- Вывод данных для таблицы fuels
--
INSERT INTO fuels VALUES
(1, 777, 'ДТ', '#333333', NULL),
(2, 80, 'А80', 'red', NULL),
(3, 92, 'А92', '#009933', NULL),
(4, 95, 'А95', 'yellow', NULL);

-- 
-- Вывод данных для таблицы tanks
--
INSERT INTO tanks VALUES
(1, 1, 1, 1, 1, 'Р1', 2800, 39406, 0, 39406, NULL, 1, NULL);

-- 
-- Вывод данных для таблицы organizations
--
-- Таблица iazs.organizations не содержит данных

-- 
-- Вывод данных для таблицы cards
--
INSERT INTO cards VALUES
(0, '000000000000', 1, 0, '2000-01-01', 0, NULL, NULL, '<служебная>', NULL, '2015-10-07 20:56:35', NULL, 1);

-- 
-- Вывод данных для таблицы suppliers
--
-- Таблица iazs.suppliers не содержит данных

-- 
-- Вывод данных для таблицы pumps
--
INSERT INTO pumps VALUES
(1, 1, 1, 1, 0.00, 1);

-- 
-- Вывод данных для таблицы users
--
INSERT INTO users VALUES
(1, 'user', 'user', 'user', NULL, NULL, NULL, NULL),
(2, 'manager', 'manager', 'manager', NULL, NULL, NULL, NULL),
(3, 'admin', 'admin', 'admin', NULL, NULL, NULL, NULL);

-- 
-- Вывод данных для таблицы cardoperations
--
-- Таблица iazs.cardoperations не содержит данных

-- 
-- Вывод данных для таблицы authitem
--
INSERT INTO authitem VALUES
('admin', 2, '', NULL, 'N;'),
('admin.tank', 0, '', NULL, 'N;'),
('cards.refill', 0, '', NULL, 'N;'),
('cards.update', 0, '', NULL, 'N;'),
('config', 0, NULL, NULL, 'N;'),
('fuel.update', 0, '', NULL, 'N;'),
('list.update', 0, '', NULL, 'N;'),
('manager', 2, '', NULL, 'N;'),
('tanks.update_real_state', 0, '', NULL, 'N;'),
('user', 2, '', NULL, 'N;'),
('users.cabinet', 0, '', NULL, 'N;'),
('users.passwd', 0, '', NULL, 'N;');

-- 
-- Вывод данных для таблицы terminalsynchistory
--
-- Таблица iazs.terminalsynchistory не содержит данных

-- 
-- Вывод данных для таблицы terminalevents
--
-- Таблица iazs.terminalevents не содержит данных

-- 
-- Вывод данных для таблицы tankrealstates
--
-- Таблица iazs.tankrealstates не содержит данных

-- 
-- Вывод данных для таблицы tankmove
--
-- Таблица iazs.tankmove не содержит данных

-- 
-- Вывод данных для таблицы tankinventory
--
-- Таблица iazs.tankinventory не содержит данных

-- 
-- Вывод данных для таблицы tankincome
--
-- Таблица iazs.tankincome не содержит данных

-- 
-- Вывод данных для таблицы tankbookstates
--
-- Таблица iazs.tankbookstates не содержит данных

-- 
-- Вывод данных для таблицы syslog
--
-- Таблица iazs.syslog не содержит данных

-- 
-- Вывод данных для таблицы pumptransactions
--
-- Таблица iazs.pumptransactions не содержит данных

-- 
-- Вывод данных для таблицы pumpcounters
--
-- Таблица iazs.pumpcounters не содержит данных

-- 
-- Вывод данных для таблицы organizationoperations
--
-- Таблица iazs.organizationoperations не содержит данных

-- 
-- Вывод данных для таблицы organizationbalance
--
-- Таблица iazs.organizationbalance не содержит данных

-- 
-- Вывод данных для таблицы generators
--
INSERT INTO generators VALUES
('dbver', 1);

-- 
-- Вывод данных для таблицы cardrefill
--
-- Таблица iazs.cardrefill не содержит данных

-- 
-- Вывод данных для таблицы cardlimits
--
-- Таблица iazs.cardlimits не содержит данных

-- 
-- Вывод данных для таблицы cardbalance
--
-- Таблица iazs.cardbalance не содержит данных

-- 
-- Вывод данных для таблицы authitemchild
--
INSERT INTO authitemchild VALUES
('manager', 'admin.tank'),
('admin', 'cards.refill'),
('manager', 'cards.refill'),
('admin', 'cards.update'),
('manager', 'cards.update'),
('admin', 'config'),
('admin', 'fuel.update'),
('manager', 'fuel.update'),
('admin', 'list.update'),
('manager', 'list.update'),
('admin', 'users.cabinet'),
('manager', 'users.cabinet'),
('user', 'users.cabinet'),
('admin', 'users.passwd'),
('manager', 'users.passwd'),
('user', 'users.passwd');

-- 
-- Вывод данных для таблицы authassignment
--
INSERT INTO authassignment VALUES
('admin', '3', NULL, 'N;');

--
-- Установка базы данных по умолчанию
--
USE iazs;

DELIMITER $$

--
-- Создать триггер `tankmove_del`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER tankmove_del
AFTER DELETE
ON tankmove
FOR EACH ROW
BEGIN
  IF old.tankToId IS NOT NULL THEN
    CALL tankBookStateUpd(old.`date`, 2, old.tankToId, -old.volume);
  END IF;
  IF old.tankFromId IS NOT NULL THEN
    CALL tankBookStateUpd(old.`date`, 3, old.tankFromId, -old.volume);
  END IF;
END
$$

--
-- Создать триггер `tankmove_ins`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER tankmove_ins
AFTER INSERT
ON tankmove
FOR EACH ROW
BEGIN
  IF new.tankToId IS NOT NULL THEN
    CALL tankBookStateUpd(new.`date`, 2, new.tankToId, new.volume);
  END IF;
  IF new.tankFromId IS NOT NULL THEN
    CALL tankBookStateUpd(new.`date`, 3, new.tankFromId, new.volume);
  END IF;
END
$$

--
-- Создать триггер `tankmove_upd`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER tankmove_upd
AFTER UPDATE
ON tankmove
FOR EACH ROW
BEGIN
  IF old.tankToId IS NOT NULL THEN
    CALL tankBookStateUpd(old.`date`, 2, old.tankToId, -old.volume);
  END IF;
  IF old.tankFromId IS NOT NULL THEN
    CALL tankBookStateUpd(old.`date`, 3, old.tankFromId, -old.volume);
  END IF;
  IF new.tankToId IS NOT NULL THEN
    CALL tankBookStateUpd(new.`date`, 2, new.tankToId, new.volume);
  END IF;
  IF new.tankFromId IS NOT NULL THEN
    CALL tankBookStateUpd(new.`date`, 3, new.tankFromId, new.volume);
  END IF;
END
$$

--
-- Создать триггер `organizations_upd`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER organizations_upd
BEFORE UPDATE
ON organizations
FOR EACH ROW
BEGIN
  DECLARE l_ver int DEFAULT NULL;

  SELECT
    gen_id("dbver") INTO l_ver;
  SET new.`version` = l_ver;
END
$$

--
-- Создать триггер `organization_ins`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER organization_ins
BEFORE INSERT
ON organizations
FOR EACH ROW
BEGIN
  DECLARE l_ver int DEFAULT NULL;

  SELECT
    gen_id("dbver") INTO l_ver;
  SET new.`version` = l_ver;
END
$$

--
-- Создать триггер `organizationbalance`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER organizationbalance
BEFORE INSERT
ON organizationbalance
FOR EACH ROW
BEGIN
  DECLARE l_ver int DEFAULT NULL;

  SELECT
    gen_id("dbver") INTO l_ver;
  UPDATE organizations
  SET `version` = l_ver
  WHERE id = new.orgId;
END
$$

--
-- Создать триггер `organizationbalance_del`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER organizationbalance_del
BEFORE DELETE
ON organizationbalance
FOR EACH ROW
BEGIN
  DECLARE l_ver int DEFAULT NULL;

  SELECT
    gen_id("dbver") INTO l_ver;
  UPDATE organizations
  SET `version` = l_ver
  WHERE id = old.orgId;
END
$$

--
-- Создать триггер `organizationbalance_upd`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER organizationbalance_upd
BEFORE UPDATE
ON organizationbalance
FOR EACH ROW
BEGIN
  DECLARE l_ver int DEFAULT NULL;

  SELECT
    gen_id("dbver") INTO l_ver;
  UPDATE organizations
  SET `version` = l_ver
  WHERE id = new.orgId;
END
$$

--
-- Создать триггер `cards_ins`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER cards_ins
BEFORE INSERT
ON cards
FOR EACH ROW
BEGIN
  DECLARE l_ver int DEFAULT NULL;

  SELECT
    gen_id("dbver") INTO l_ver;
  SET new.`version` = l_ver;
END
$$

--
-- Создать триггер `cards_upd`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER cards_upd
BEFORE UPDATE
ON cards
FOR EACH ROW
BEGIN
  DECLARE l_ver int DEFAULT NULL;

  SELECT
    gen_id("dbver") INTO l_ver;
  SET new.`version` = l_ver;
END
$$

--
-- Создать триггер `pumptransactions_ins`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER pumptransactions_ins
AFTER INSERT
ON pumptransactions
FOR EACH ROW
BEGIN
  DECLARE l_date date DEFAULT NULL;
  DECLARE l_cardId int DEFAULT NULL;
  DECLARE l_volume decimal(8, 2) DEFAULT NULL;
  DECLARE l_fuelId int DEFAULT NULL;
  DECLARE l_operType int DEFAULT NULL;
  DECLARE l_doc varchar(255) DEFAULT NULL;
  DECLARE l_cardNumber varchar(20) DEFAULT NULL;
  DECLARE l_cardOwner varchar(60) DEFAULT NULL;
  DECLARE l_tankToId int DEFAULT NULL;

  SELECT
    `date`,
    -volume,
    fuelId,
    operationType,
    cardId INTO l_date, l_volume, l_fuelId, l_operType, l_cardId
  FROM cardoperations
  WHERE id = new.operId;
  IF ((l_date IS NOT NULL)
    AND (new.tankId IS NOT NULL)) THEN
    IF (l_operType = 2) THEN
      CALL tankBookStateUpd(l_date, 4, new.tankId, l_volume);
    ELSEIF (l_operType = 4) THEN
      CALL tankBookStateUpd(l_date, 5, new.tankId, l_volume);
    ELSEIF (l_operType = 5) THEN
      SELECT
        number,
        `owner`,
        tankToId INTO l_cardNumber, l_cardOwner, l_tankToId
      FROM cards
      WHERE id = l_cardId;

      SET l_doc = CONCAT('карта #', l_cardNumber, ' / ', l_cardOwner);
      INSERT INTO tankmove (`date`, tankFromId, tankToId, fuelId, doc, volume)
        VALUES (l_date, new.tankId, l_tankToId, l_fuelId, l_doc, l_volume);
    END IF;
  END IF;
END
$$

--
-- Создать триггер `cardlimits_del`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER cardlimits_del
AFTER DELETE
ON cardlimits
FOR EACH ROW
BEGIN
  DECLARE l_ver int DEFAULT NULL;

  SELECT
    gen_id("dbver") INTO l_ver;
  UPDATE cards
  SET `version` = l_ver
  WHERE id = old.cardId;
END
$$

--
-- Создать триггер `cardlimits_ins`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER cardlimits_ins
BEFORE INSERT
ON cardlimits
FOR EACH ROW
BEGIN
  DECLARE l_ver int DEFAULT NULL;

  SELECT
    gen_id("dbver") INTO l_ver;
  UPDATE cards
  SET `version` = l_ver
  WHERE id = new.cardId;
END
$$

--
-- Создать триггер `cardlimits_upd`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER cardlimits_upd
BEFORE UPDATE
ON cardlimits
FOR EACH ROW
BEGIN
  DECLARE l_ver int DEFAULT NULL;

  SELECT
    gen_id("dbver") INTO l_ver;
  UPDATE cards
  SET `version` = l_ver
  WHERE id = new.cardId;
END
$$

--
-- Создать триггер `tankinventory_del`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER tankinventory_del
AFTER DELETE
ON tankinventory
FOR EACH ROW
BEGIN
  CALL tankBookStateUpd(old.`date`, 0, old.tankId, -(old.volume - old.oldVolume));
END
$$

--
-- Создать триггер `tankinventory_ins`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER tankinventory_ins
BEFORE INSERT
ON tankinventory
FOR EACH ROW
BEGIN
  DECLARE l_volume decimal(10, 2) DEFAULT NULL;
  SELECT
    COALESCE(fuelVolume, 0) INTO l_volume
  FROM tankbookstates
  WHERE tankId = new.tankId
  AND `date` <= new.`date`
  ORDER BY `date` DESC LIMIT 1;
  SET new.oldVolume = l_volume;

  CALL tankBookStateUpd(new.`date`, 0, new.tankId, new.volume - new.oldVolume);
END
$$

--
-- Создать триггер `tankinventory_upd`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER tankinventory_upd
BEFORE UPDATE
ON tankinventory
FOR EACH ROW
BEGIN
  DECLARE l_volume decimal(10, 2) DEFAULT NULL;
  SELECT
    COALESCE(fuelVolume, 0) INTO l_volume
  FROM tankbookstates
  WHERE tankId = new.tankId
  AND `date` <= new.`date`
  ORDER BY `date` DESC LIMIT 1;
  SET new.oldVolume = l_volume;

  CALL tankBookStateUpd(old.`date`, 0, old.tankId, -(old.volume - old.oldVolume));
  CALL tankBookStateUpd(new.`date`, 0, new.tankId, new.volume - new.oldVolume);
END
$$

--
-- Создать триггер `tankincome_del`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER tankincome_del
AFTER DELETE
ON tankincome
FOR EACH ROW
BEGIN
  CALL tankBookStateUpd(old.`date`, 1, old.tankId, -old.volume);
END
$$

--
-- Создать триггер `tankincome_ins`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER tankincome_ins
AFTER INSERT
ON tankincome
FOR EACH ROW
BEGIN
  CALL tankBookStateUpd(new.`date`, 1, new.tankId, new.volume);
END
$$

--
-- Создать триггер `tankincome_upd`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER tankincome_upd
AFTER UPDATE
ON tankincome
FOR EACH ROW
BEGIN
  CALL tankBookStateUpd(old.`date`, 1, old.tankId, -old.volume);
  CALL tankBookStateUpd(new.`date`, 1, new.tankId, new.volume);
END
$$

DELIMITER ;

-- 
-- Восстановить предыдущий режим SQL (SQL mode)
-- 
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

-- 
-- Включение внешних ключей
-- 
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;