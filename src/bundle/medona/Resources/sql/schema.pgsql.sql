DROP SCHEMA IF EXISTS "medona" CASCADE;

CREATE SCHEMA "medona";

-- Table: "medona"."archivalAgreement"

-- DROP TABLE "medona"."archivalAgreement";

CREATE TABLE "medona"."archivalAgreement"
(
  "archivalAgreementId" text NOT NULL,
  "name" text NOT NULL,
  "reference" text NOT NULL,
  "description" text,

  "archivalProfileReference" text,
  "serviceLevelReference" text,

  "archiverOrgRegNumber" text NOT NULL,
  "depositorOrgRegNumber" text NOT NULL,
  "originatorOrgIds" text,

  "beginDate" date,
  "endDate" date,
  "enabled" boolean,

  "allowedFormats" text,

  "maxSizeAgreement" integer,
  "maxSizeTransfer" integer,
  "maxSizeDay" integer,
  "maxSizeWeek" integer,
  "maxSizeMonth" integer,
  "maxSizeYear" integer,

  "signed" boolean,
  "autoTransferAcceptance" boolean,
  "processSmallArchive" boolean,

  PRIMARY KEY ("archivalAgreementId"),
  UNIQUE ("reference")
)
 WITH (
  OIDS=FALSE
);

-- Table: "medona"."message"

-- DROP TABLE "medona"."message";

CREATE TABLE "medona"."message"
(
  "messageId" text NOT NULL,
  "schema" text,
  "type" text NOT NULL,
  "status" text NOT NULL,

  "date" timestamp NOT NULL,
  "reference" text NOT NULL,

  "accountId" text,
  "senderOrgRegNumber" text NOT NULL,
  "senderOrgName" text,
  "recipientOrgRegNumber" text NOT NULL,
  "recipientOrgName" text,

  "archivalAgreementReference" text,
  "replyCode" text,
  "operationDate" timestamp,
  "receptionDate" timestamp,

  "relatedReference" text,
  "requestReference" text,
  "replyReference" text,
  "authorizationReference" text,
  "authorizationReason" text,
  "authorizationRequesterOrgRegNumber" text,

  "derogation" boolean,

  "dataObjectCount" integer,
  "size" numeric,

  "data" text,
  "path" text,

  "active" boolean,
  "archived" boolean,
  "isIncoming" boolean,

  "comment" text,

  PRIMARY KEY ("messageId"),
  UNIQUE ("type", "reference", "senderOrgRegNumber")
)
WITH (
  OIDS=FALSE
);


-- Table: medona."messageComment"

-- DROP TABLE medona."messageComment";

CREATE TABLE medona."messageComment"
(
  "messageId" text,
  "comment" text,
  "commentId" text NOT NULL,
  PRIMARY KEY ("commentId"),
  UNIQUE ("messageId", comment),
  FOREIGN KEY ("messageId")
      REFERENCES "medona"."message" ("messageId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

CREATE TABLE medona."unitIdentifier"
(
  "messageId" text NOT NULL,
  "objectClass" text NOT NULL,
  "objectId" text NOT NULL,
  FOREIGN KEY ("messageId")
      REFERENCES "medona"."message" ("messageId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  UNIQUE ("messageId", "objectClass", "objectId")
);

CREATE TABLE medona."controlAuthority"
(
  "originatorOrgUnitId" text NOT NULL,
  "controlAuthorityOrgUnitId" text NOT NULL,
  PRIMARY KEY ("originatorOrgUnitId")
);

CREATE INDEX "medona_message_recipientOrgRegNumber_idx" ON "medona"."message" USING btree ("recipientOrgRegNumber");

CREATE INDEX "medona_message_date_idx" ON "medona"."message" USING btree ("date");

CREATE INDEX "medona_message_status_active_idx" ON "medona"."message" USING btree ("status", "active");

CREATE INDEX "medona_unitIdentifier_messageId_idx" ON "medona"."unitIdentifier" USING btree ("messageId");

CREATE INDEX "medona_unitIdentifier_objectClass_idx" ON "medona"."unitIdentifier" USING btree ("objectClass", "objectId");
