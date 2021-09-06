-- Migration script for PGSQL from Maarch RM V2.4 to V2.5

-- add columns for Digital Safe in auth.account
ALTER TABLE "auth"."account" ADD COLUMN "ownerOrgId" text;
ALTER TABLE "auth"."account" ADD COLUMN "isAdmin" boolean;

-- Add columns for facets in descriptionField
ALTER TABLE "recordsManagement"."descriptionField" ADD COLUMN "facets" jsonb;

INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
('recordsManagement/depositNewResource', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format size', FALSE, 'Dépôt d''une ressource dans l''archive %6$s');

INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
('organization/counting', 'orgName ownerOrgId', FALSE, 'Compter le nombre d''objet numérique dans l''activité %6$s');

INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
('organization/listing', 'orgName ownerOrgId', FALSE, 'Lister les identifiants d''objet numérique de l''activité %6$s');

INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
('organization/journal', 'orgName ownerOrgId', FALSE, 'Lecture du journal de l''organisation %6$s');

CREATE SCHEMA "medona";

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
    "comment" text,

    "active" boolean,
    "archived" boolean,
    "isIncoming" boolean,

    PRIMARY KEY ("messageId"),
    UNIQUE ("type", "reference", "senderOrgRegNumber")
)
    WITH (
        OIDS=FALSE
    );

-- Table: medona."messageComment"

-- DROP TABLE medona."messageComment";

CREATE TABLE "medona"."messageComment"
(
    "messageId" text,
    "comment" text,
    "commentId" text NOT NULL,
    PRIMARY KEY ("commentId"),
    UNIQUE ("messageId", "comment"),
    FOREIGN KEY ("messageId")
        REFERENCES "medona"."message" ("messageId") MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE NO ACTION
)
    WITH (
        OIDS=FALSE
    );

CREATE TABLE "medona"."unitIdentifier"
(
    "messageId" text NOT NULL,
    "objectClass" text NOT NULL,
    "objectId" text NOT NULL,
    FOREIGN KEY ("messageId")
        REFERENCES "medona"."message" ("messageId") MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    UNIQUE ("messageId", "objectClass", "objectId")
);

CREATE TABLE "medona"."controlAuthority"
(
    "originatorOrgUnitId" text NOT NULL,
    "controlAuthorityOrgUnitId" text NOT NULL,
    PRIMARY KEY ("originatorOrgUnitId")
);

-- Add indexes
CREATE INDEX "audit_event_instanceName_idx" ON "audit"."event" USING btree ("instanceName");

CREATE INDEX "audit_event_eventDate_idx" ON "audit"."event" USING btree ("eventDate");

CREATE INDEX "digitalResource_digitalResource_archiveId_idx" ON "digitalResource"."digitalResource" USING btree ("archiveId");

CREATE INDEX ON "digitalResource"."digitalResource" USING btree ("relatedResId", "relationshipType");

CREATE INDEX "batchProcessing_logScheduling_schedulingId_idx" ON "batchProcessing"."logScheduling" USING btree ("schedulingId");

CREATE INDEX "filePlan_folder_ownerOrgRegNumber_idx" ON "filePlan"."folder" USING btree ("ownerOrgRegNumber");

CREATE INDEX "lifeCycle_event_objectClass_idx" ON "lifeCycle"."event" USING btree ("objectClass");

CREATE INDEX "lifeCycle_event_instanceName_idx" ON "lifeCycle"."event" USING btree ("instanceName");

CREATE INDEX "lifeCycle_event_eventType_idx" ON "lifeCycle"."event" USING btree ("eventType");

CREATE INDEX "lifeCycle_event_objectClass_objectId_idx" ON "lifeCycle"."event" USING btree ("objectClass", "objectId");

CREATE INDEX "medona_message_recipientOrgRegNumber_idx" ON "medona"."message" USING btree ("recipientOrgRegNumber");

CREATE INDEX "medona_message_date_idx" ON "medona"."message" USING btree ("date");

CREATE INDEX "medona_message_status_active_idx" ON "medona"."message" USING btree ("status", "active");

CREATE INDEX "recordsManagement_archive_originatingDate_idx" ON "recordsManagement"."archive" USING btree ("originatingDate");

CREATE INDEX "recordsManagement_archive_parentArchiveId_idx" ON "recordsManagement"."archive" USING btree ("parentArchiveId");

CREATE INDEX "recordsManagement_archive_descriptionClass_idx" ON "recordsManagement"."archive" USING btree ("descriptionClass");

CREATE INDEX "recordsManagement_archive_originatorOwnerOrgId_idx" ON "recordsManagement"."archive" USING btree ("originatorOwnerOrgId");

CREATE INDEX "recordsManagement_archive_archiverOrgRegNumber_idx" ON "recordsManagement"."archive" USING btree ("archiverOrgRegNumber");

CREATE INDEX "recordsManagement_archive_originatorOrgRegNumber_idx" ON "recordsManagement"."archive" USING btree ("originatorOrgRegNumber");

CREATE INDEX "recordsManagement_archive_originatorArchiveId_idx" ON "recordsManagement"."archive" USING btree ("originatorArchiveId");

CREATE INDEX "recordsManagement_archiveRelationship_archiveId_idx" ON "recordsManagement"."archiveRelationship" USING btree ("archiveId");

CREATE INDEX "recordsManagement_archiveRelationship_relatedArchiveId_idx" ON "recordsManagement"."archiveRelationship" USING btree ("relatedArchiveId");

CREATE INDEX "medona_unitIdentifier_messageId_idx" ON "medona"."unitIdentifier" USING btree ("messageId");

CREATE INDEX "medona_unitIdentifier_objectClass_idx" ON "medona"."unitIdentifier" USING btree ("objectClass", "objectId");
