-- Migration script for PG SQL from Maarch RM V2.3 to V2.4

-- Set label as not null for retention rules
ALTER TABLE "recordsManagement"."retentionRule" ALTER COLUMN "label" SET NOT NULL ;

-- Add columns for processing statuses on archival profiles
ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "processingStatuses" jsonb;


-- Add columns for processing statuses on archival profiles
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "processingStatus" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "userOrgRegNumbers" text;


-- DROP constraint on orgid and add primary key constraint on couple (orgid and archivalprofilereference)
ALTER TABLE "organization"."archivalProfileAccess" DROP CONSTRAINT "archivalProfileAccess_orgId_archivalProfileReference_key";
ALTER TABLE "organization"."archivalProfileAccess" ADD PRIMARY KEY ("orgId", "archivalProfileReference");

-- Add columns for processing statuses on org unit archival profiles access
ALTER TABLE "organization"."archivalProfileAccess" ADD COLUMN "userAccess" jsonb;

-- Add columns to display or not, on workflow archives list
ALTER TABLE "recordsManagement"."archiveDescription" ADD COLUMN "isInList" boolean default false;

INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
('recordsManagement/resourceDestruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Destruction de la ressource %9$s'),
('recordsManagement/updateRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation mise à jour avec l''archive %6$s'),
('recordsManagement/restitutionRequest', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Demande de restitution de l''archive %6$s'),
('recordsManagement/restitutionRequestCanceling', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Annulation de la demande de restitution de l''archive %6$s');


-- New search index for special chars
DROP INDEX "recordsManagement"."archive_to_tsvector_idx";
CREATE INDEX "archive_to_tsvector_idx"
  ON "recordsManagement"."archive"
  USING gin
  (to_tsvector('french'::regconfig, translate(text, 'ÀÁÂÃÄÅàáâãäåÆæÞþČčĆćÇçĐđÈÉÊËèéêëÌÍÎÏìíîïÑñÒÓÔÕÖØðòóôõöøœŒŔŕŠšßÙÚÛÜùúûÝýÿŽž'::text, 'AAAAAAaaaaaaAEaeBbCcCcCcDjdjEEEEeeeeIIIIiiiiNnOOOOOOooooooooeOERrSsSsUUUUuuuYyyZz'::text)));
