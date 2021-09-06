DROP TABLE IF EXISTS `medona.unitIdentifier`;
DROP TABLE IF EXISTS `medona.messageComment`;
DROP TABLE IF EXISTS `medona.message`;
DROP TABLE IF EXISTS `medona.archivalAgreement`;

CREATE TABLE `medona.archivalAgreement`
(
  `archivalAgreementId` varchar(255) NOT NULL,
  `name` text NOT NULL,
  `reference` varchar(255) NOT NULL,
  `description` text,
  
  `archivalProfileReference` text,
  `serviceLevelReference` text,
  
  `archiverOrgRegNumber` text NOT NULL,
  `depositorOrgRegNumber` text NOT NULL,
  `originatorOrgIds` text,

  `beginDate` date,
  `endDate` date,
  `enabled` boolean,
  
  `allowedFormats` text,
  
  `maxSizeAgreement` integer,
  `maxSizeTransfer` integer,
  `maxSizeDay` integer,
  `maxSizeWeek` integer,
  `maxSizeMonth` integer,
  `maxSizeYear` integer,

  `signed` boolean,
  `autoTransferAcceptance` boolean,
  `processSmallArchive` boolean,

  PRIMARY KEY (`archivalAgreementId`),
  UNIQUE (`reference`)
);

CREATE TABLE `medona.message`
(
  `messageId` varchar(255) NOT NULL,
  `schema` text,
  `type` varchar(255) NOT NULL,
  `status` text NOT NULL,
  
  `date` timestamp(6) NOT NULL,
  `reference` varchar(255) NOT NULL,
  
  `accountId` text,
  `senderOrgRegNumber` varchar(255) NOT NULL,
  `senderOrgName` text,
  `recipientOrgRegNumber` text NOT NULL,
  `recipientOrgName` text,

  `archivalAgreementReference` text,
  `replyCode` text,
  `operationDate` timestamp(6),
  `receptionDate` timestamp(6),
  
  `relatedReference` text,
  `requestReference` text,
  `replyReference` text,
  `authorizationReference` text,
  `authorizationReason` text,
  `authorizationRequesterOrgRegNumber` text,

  `derogation` boolean,
  
  `dataObjectCount` integer,
  `size` numeric,
  
  `data` LONGTEXT,
  
  `active` boolean,
  `archived` boolean,

  PRIMARY KEY (`messageId`),
  UNIQUE (`type`, `reference`, `senderOrgRegNumber`)
);

CREATE TABLE `medona.messageComment`
(
  `messageId` varchar(255),
  `comment` varchar(255),
  `commentId` varchar(255) NOT NULL,
  PRIMARY KEY (`commentId`),
  UNIQUE (`messageId`, comment),
  FOREIGN KEY (`messageId`)
      REFERENCES `medona.message` (`messageId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE `medona.unitIdentifier`
(
  `messageId` varchar(255) NOT NULL,
  `objectClass` varchar(255) NOT NULL,
  `objectId` varchar(255) NOT NULL,
  FOREIGN KEY (`messageId`)
      REFERENCES `medona.message` (`messageId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  UNIQUE (`messageId`, `objectClass`, `objectId`)
);

CREATE TABLE `medona.controlAuthority`
(
  `originatorOrgUnitId` varchar(255) NOT NULL,
  `controlAuthorityOrgUnitId` varchar(255) NOT NULL,
  PRIMARY KEY (`originatorOrgUnitId`)
);