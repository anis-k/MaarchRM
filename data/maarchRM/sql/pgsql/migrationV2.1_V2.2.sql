INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
  ('recordsManagement/outgoingTransfer', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Transfert sortant de l''archive %6$s');

ALTER TABLE "recordsManagement"."retentionRule" ADD COLUMN "implementationDate" date;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "retentionRuleStatus" text;
INSERT INTO "auth"."servicePrivilege"("accountId", "serviceURI") VALUES ('System', 'recordsManagement/archives/updateArchivesretentionrule');

DROP TABLE "organization"."orgRole";

ALTER TABLE "recordsManagement"."descriptionField" ADD COLUMN "isArray" boolean default false;
ALTER TABLE "recordsManagement"."archiveDescription" ADD COLUMN "isImmutable" boolean default false;
ALTER TABLE "recordsManagement"."archiveDescription" ADD COLUMN "isRetained" boolean default true;

ALTER TABLE "auth"."account" ADD COLUMN "authentication" jsonb;
ALTER TABLE "auth"."account" ADD COLUMN "preferences" jsonb;

ALTER TABLE "recordsManagement"."archive" DROP CONSTRAINT "archive_retentionRuleCode_fkey";

CREATE INDEX ON "lifeCycle"."event" ("objectId");
CREATE INDEX ON "lifeCycle"."event" ("timestamp");