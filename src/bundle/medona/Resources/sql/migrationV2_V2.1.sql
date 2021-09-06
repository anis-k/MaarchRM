ALTER TABLE "medona"."message" ADD COLUMN "isIncoming" BOOLEAN;

UPDATE "medona"."message" SET "isIncoming" = TRUE WHERE "type" = 'ArchiveTransfer';