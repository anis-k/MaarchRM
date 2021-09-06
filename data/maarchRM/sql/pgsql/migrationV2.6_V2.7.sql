-- Migration script for PGSQL from Maarch RM V2.6 to V2.7

UPDATE "lifeCycle"."eventFormat" SET format = 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference code info' WHERE type = 'medona/sending';
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('digitalResource/integrityCheck', 'repositoryReference addressesToCheck checkedAddresses failed', 'Contrôle d''intégrité des ressources présentes dans %6$s', false);

ALTER TABLE "organization"."organization" ADD COLUMN "history" text;

CREATE SEQUENCE IF NOT EXISTS "recordsManagement"."archiverArchiveIdSequence";
