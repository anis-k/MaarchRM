--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.12
-- Dumped by pg_dump version 11.5 (Ubuntu 11.5-1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE ONLY "recordsManagement".archive DROP CONSTRAINT "archive_parentArchiveId_fkey";
ALTER TABLE ONLY "recordsManagement".archive DROP CONSTRAINT "archive_accessRuleCode_fkey";
ALTER TABLE ONLY "recordsManagement"."archiveRelationship" DROP CONSTRAINT "archiveRelationship_relatedArchiveId_fkey";
ALTER TABLE ONLY "recordsManagement"."archiveRelationship" DROP CONSTRAINT "archiveRelationship_archiveId_fkey";
ALTER TABLE ONLY "recordsManagement"."archiveDescription" DROP CONSTRAINT "archiveDescription_archivalProfileId_fkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfile" DROP CONSTRAINT "archivalProfile_retentionRuleCode_fkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfile" DROP CONSTRAINT "archivalProfile_accessRuleCode_fkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfileContents" DROP CONSTRAINT "archivalProfileContents_parentProfileId_fkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfileContents" DROP CONSTRAINT "archivalProfileContents_containedProfileId_fkey";
ALTER TABLE ONLY organization."userPosition" DROP CONSTRAINT "userPosition_orgId_fkey";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT "organization_parentOrgId_fkey";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT "organization_ownerOrgId_fkey";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT "organization_orgTypeCode_fkey";
ALTER TABLE ONLY organization."orgContact" DROP CONSTRAINT "orgContact_orgId_fkey";
ALTER TABLE ONLY organization."archivalProfileAccess" DROP CONSTRAINT "archivalProfileAccess_orgId_fkey";
ALTER TABLE ONLY medona."unitIdentifier" DROP CONSTRAINT "unitIdentifier_messageId_fkey";
ALTER TABLE ONLY medona."messageComment" DROP CONSTRAINT "messageComment_messageId_fkey";
ALTER TABLE ONLY "filePlan"."position" DROP CONSTRAINT "position_filePlan_fkey";
ALTER TABLE ONLY "filePlan".folder DROP CONSTRAINT "folderId_filePlan_fkey";
ALTER TABLE ONLY "digitalResource"."packedResource" DROP CONSTRAINT "packedResource_resId_fkey";
ALTER TABLE ONLY "digitalResource"."packedResource" DROP CONSTRAINT "packedResource_packageId_fkey";
ALTER TABLE ONLY "digitalResource".package DROP CONSTRAINT "package_packageId_fkey";
ALTER TABLE ONLY "digitalResource"."digitalResource" DROP CONSTRAINT "digitalResource_relatedResId_fkey";
ALTER TABLE ONLY "digitalResource"."digitalResource" DROP CONSTRAINT "digitalResource_clusterId_fkey";
ALTER TABLE ONLY "digitalResource"."digitalResource" DROP CONSTRAINT "digitalResource_archiveId_fkey";
ALTER TABLE ONLY "digitalResource"."clusterRepository" DROP CONSTRAINT "clusterRepository_repositoryId_fkey";
ALTER TABLE ONLY "digitalResource"."clusterRepository" DROP CONSTRAINT "clusterRepository_clusterId_fkey";
ALTER TABLE ONLY "digitalResource".address DROP CONSTRAINT "address_resId_fkey";
ALTER TABLE ONLY "digitalResource".address DROP CONSTRAINT "address_repositoryId_fkey";
ALTER TABLE ONLY contact.communication DROP CONSTRAINT "communication_contactId_fkey";
ALTER TABLE ONLY contact.communication DROP CONSTRAINT "communication_comMeanCode_fkey";
ALTER TABLE ONLY contact.address DROP CONSTRAINT "address_contactId_fkey";
ALTER TABLE ONLY auth."servicePrivilege" DROP CONSTRAINT "servicePrivilege_accountId_fkey";
ALTER TABLE ONLY auth."roleMember" DROP CONSTRAINT "roleMember_userAccountId_fkey";
ALTER TABLE ONLY auth."roleMember" DROP CONSTRAINT "roleMember_roleId_fkey";
ALTER TABLE ONLY auth.privilege DROP CONSTRAINT "privilege_roleId_fkey";
DROP INDEX "recordsManagement"."recordsManagement_archive_parentArchiveId_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_originatorOwnerOrgId_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_originatorOrgRegNumber_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_originatorArchiveId_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_originatingDate_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_descriptionClass_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_archiverOrgRegNumber_idx";
DROP INDEX "recordsManagement"."recordsManagement_archiveRelationship_relatedArchiveId_idx";
DROP INDEX "recordsManagement"."recordsManagement_archiveRelationship_archiveId_idx";
DROP INDEX "recordsManagement".archive_to_tsvector_idx;
DROP INDEX "recordsManagement".archive_status_idx;
DROP INDEX "recordsManagement"."archive_originatorOrgRegNumber_originatorArchiveId_idx";
DROP INDEX "recordsManagement"."archive_filePlanPosition_idx";
DROP INDEX "recordsManagement"."archive_disposalDate_idx";
DROP INDEX "recordsManagement"."archive_archivalProfileReference_idx";
DROP INDEX medona."medona_unitIdentifier_objectClass_idx";
DROP INDEX medona."medona_unitIdentifier_messageId_idx";
DROP INDEX medona.medona_message_status_active_idx;
DROP INDEX medona."medona_message_recipientOrgRegNumber_idx";
DROP INDEX medona.medona_message_date_idx;
DROP INDEX "lifeCycle"."lifeCycle_event_objectClass_objectId_idx";
DROP INDEX "lifeCycle"."lifeCycle_event_objectClass_idx";
DROP INDEX "lifeCycle"."lifeCycle_event_instanceName_idx";
DROP INDEX "lifeCycle"."lifeCycle_event_eventType_idx";
DROP INDEX "lifeCycle".event_timestamp_idx;
DROP INDEX "lifeCycle"."event_objectId_idx";
DROP INDEX "filePlan"."filePlan_folder_ownerOrgRegNumber_idx";
DROP INDEX "digitalResource"."digitalResource_digitalResource_relatedResId__relationshipType_";
DROP INDEX "digitalResource"."digitalResource_digitalResource_archiveId_idx";
DROP INDEX "batchProcessing"."batchProcessing_logScheduling_schedulingId_idx";
DROP INDEX audit."audit_event_instanceName_idx";
DROP INDEX audit."audit_event_eventDate_idx";
ALTER TABLE ONLY "recordsManagement"."storageRule" DROP CONSTRAINT "storageRule_pkey";
ALTER TABLE ONLY "recordsManagement"."serviceLevel" DROP CONSTRAINT "serviceLevel_reference_key";
ALTER TABLE ONLY "recordsManagement"."serviceLevel" DROP CONSTRAINT "serviceLevel_pkey";
ALTER TABLE ONLY "recordsManagement"."retentionRule" DROP CONSTRAINT "retentionRule_pkey";
ALTER TABLE ONLY "recordsManagement".log DROP CONSTRAINT log_pkey;
ALTER TABLE ONLY "recordsManagement"."descriptionField" DROP CONSTRAINT "descriptionField_pkey";
ALTER TABLE ONLY "recordsManagement"."descriptionClass" DROP CONSTRAINT "descriptionClass_pkey";
ALTER TABLE ONLY "recordsManagement".archive DROP CONSTRAINT archive_pkey;
ALTER TABLE ONLY "recordsManagement"."archiveRelationship" DROP CONSTRAINT "archiveRelationship_pkey";
ALTER TABLE ONLY "recordsManagement"."archiveDescription" DROP CONSTRAINT "archiveDescription_pkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfile" DROP CONSTRAINT "archivalProfile_reference_key";
ALTER TABLE ONLY "recordsManagement"."archivalProfile" DROP CONSTRAINT "archivalProfile_pkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfileContents" DROP CONSTRAINT "archivalProfileContents_pkey";
ALTER TABLE ONLY "recordsManagement"."accessRule" DROP CONSTRAINT "accessRule_pkey";
ALTER TABLE ONLY organization."userPosition" DROP CONSTRAINT "userPosition_pkey";
ALTER TABLE ONLY organization."servicePosition" DROP CONSTRAINT "servicePosition_pkey";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT "organization_taxIdentifier_key";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT "organization_registrationNumber_key";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT organization_pkey;
ALTER TABLE ONLY organization."orgType" DROP CONSTRAINT "orgType_pkey";
ALTER TABLE ONLY organization."orgContact" DROP CONSTRAINT "orgContact_pkey";
ALTER TABLE ONLY organization."archivalProfileAccess" DROP CONSTRAINT "archivalProfileAccess_pkey";
ALTER TABLE ONLY medona."unitIdentifier" DROP CONSTRAINT "unitIdentifier_messageId_objectClass_objectId_key";
ALTER TABLE ONLY medona.message DROP CONSTRAINT "message_type_reference_senderOrgRegNumber_key";
ALTER TABLE ONLY medona.message DROP CONSTRAINT message_pkey;
ALTER TABLE ONLY medona."messageComment" DROP CONSTRAINT "messageComment_pkey";
ALTER TABLE ONLY medona."messageComment" DROP CONSTRAINT "messageComment_messageId_comment_key";
ALTER TABLE ONLY medona."controlAuthority" DROP CONSTRAINT "controlAuthority_pkey";
ALTER TABLE ONLY medona."archivalAgreement" DROP CONSTRAINT "archivalAgreement_reference_key";
ALTER TABLE ONLY medona."archivalAgreement" DROP CONSTRAINT "archivalAgreement_pkey";
ALTER TABLE ONLY "lifeCycle".event DROP CONSTRAINT event_pkey;
ALTER TABLE ONLY "lifeCycle"."eventFormat" DROP CONSTRAINT "eventFormat_pkey";
ALTER TABLE ONLY "filePlan".folder DROP CONSTRAINT folder_pkey;
ALTER TABLE ONLY "filePlan".folder DROP CONSTRAINT "filePlan_name_parentFolderId_key";
ALTER TABLE ONLY "digitalResource".repository DROP CONSTRAINT "repository_repositoryUri_key";
ALTER TABLE ONLY "digitalResource".repository DROP CONSTRAINT "repository_repositoryReference_key";
ALTER TABLE ONLY "digitalResource".repository DROP CONSTRAINT repository_pkey;
ALTER TABLE ONLY "digitalResource".package DROP CONSTRAINT package_pkey;
ALTER TABLE ONLY "digitalResource"."digitalResource" DROP CONSTRAINT "digitalResource_pkey";
ALTER TABLE ONLY "digitalResource"."conversionRule" DROP CONSTRAINT "conversionRule_puid_key";
ALTER TABLE ONLY "digitalResource"."conversionRule" DROP CONSTRAINT "conversionRule_pkey";
ALTER TABLE ONLY "digitalResource"."contentType" DROP CONSTRAINT "contentType_pkey";
ALTER TABLE ONLY "digitalResource".cluster DROP CONSTRAINT cluster_pkey;
ALTER TABLE ONLY "digitalResource"."clusterRepository" DROP CONSTRAINT "clusterRepository_pkey";
ALTER TABLE ONLY "digitalResource".address DROP CONSTRAINT address_pkey;
ALTER TABLE ONLY contact.contact DROP CONSTRAINT contact_pkey;
ALTER TABLE ONLY contact.communication DROP CONSTRAINT communication_pkey;
ALTER TABLE ONLY contact.communication DROP CONSTRAINT "communication_contactId_purpose_comMeanCode_key";
ALTER TABLE ONLY contact."communicationMean" DROP CONSTRAINT "communicationMean_pkey";
ALTER TABLE ONLY contact."communicationMean" DROP CONSTRAINT "communicationMean_name_key";
ALTER TABLE ONLY contact.address DROP CONSTRAINT address_pkey;
ALTER TABLE ONLY contact.address DROP CONSTRAINT "address_contactId_purpose_key";
ALTER TABLE ONLY "batchProcessing".scheduling DROP CONSTRAINT scheduling_pkey;
ALTER TABLE ONLY "batchProcessing".notification DROP CONSTRAINT notification_pkey;
ALTER TABLE ONLY "batchProcessing"."logScheduling" DROP CONSTRAINT "logScheduling_pkey";
ALTER TABLE ONLY auth."servicePrivilege" DROP CONSTRAINT "servicePrivilege_accountId_serviceURI_key";
ALTER TABLE ONLY auth.role DROP CONSTRAINT role_pkey;
ALTER TABLE ONLY auth."roleMember" DROP CONSTRAINT "roleMember_roleId_userAccountId_key";
ALTER TABLE ONLY auth.privilege DROP CONSTRAINT "privilege_roleId_userStory_key";
ALTER TABLE ONLY auth.account DROP CONSTRAINT account_pkey;
ALTER TABLE ONLY auth.account DROP CONSTRAINT "account_accountName_key";
ALTER TABLE ONLY audit.event DROP CONSTRAINT event_pkey;
DROP TABLE "recordsManagement"."storageRule";
DROP TABLE "recordsManagement"."serviceLevel";
DROP TABLE "recordsManagement"."retentionRule";
DROP TABLE "recordsManagement".log;
DROP TABLE "recordsManagement"."descriptionField";
DROP TABLE "recordsManagement"."descriptionClass";
DROP TABLE "recordsManagement"."archiveRelationship";
DROP TABLE "recordsManagement"."archiveDescription";
DROP TABLE "recordsManagement".archive;
DROP TABLE "recordsManagement"."archivalProfileContents";
DROP TABLE "recordsManagement"."archivalProfile";
DROP TABLE "recordsManagement"."accessRule";
DROP TABLE organization."userPosition";
DROP TABLE organization."servicePosition";
DROP TABLE organization.organization;
DROP TABLE organization."orgType";
DROP TABLE organization."orgContact";
DROP TABLE organization."archivalProfileAccess";
DROP TABLE medona."unitIdentifier";
DROP TABLE medona."messageComment";
DROP TABLE medona.message;
DROP TABLE medona."controlAuthority";
DROP TABLE medona."archivalAgreement";
DROP TABLE "lifeCycle"."eventFormat";
DROP TABLE "lifeCycle".event;
DROP TABLE "filePlan"."position";
DROP TABLE "filePlan".folder;
DROP TABLE "digitalResource".repository;
DROP TABLE "digitalResource"."packedResource";
DROP TABLE "digitalResource".package;
DROP TABLE "digitalResource"."digitalResource";
DROP TABLE "digitalResource"."conversionRule";
DROP TABLE "digitalResource"."contentType";
DROP TABLE "digitalResource"."clusterRepository";
DROP TABLE "digitalResource".cluster;
DROP TABLE "digitalResource".address;
DROP TABLE contact.contact;
DROP TABLE contact."communicationMean";
DROP TABLE contact.communication;
DROP TABLE contact.address;
DROP TABLE "batchProcessing".scheduling;
DROP TABLE "batchProcessing".notification;
DROP TABLE "batchProcessing"."logScheduling";
DROP TABLE auth."servicePrivilege";
DROP TABLE auth."roleMember";
DROP TABLE auth.role;
DROP TABLE auth.privilege;
DROP TABLE auth.account;
DROP TABLE audit.event;
DROP SCHEMA "recordsManagement";
DROP SCHEMA organization;
DROP SCHEMA medona;
DROP SCHEMA "lifeCycle";
DROP SCHEMA "filePlan";
DROP SCHEMA "digitalResource";
DROP SCHEMA contact;
DROP SCHEMA "batchProcessing";
DROP SCHEMA auth;
DROP SCHEMA audit;
--
-- TOC entry 7 (class 2615 OID 602484)
-- Name: audit; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA audit;


ALTER SCHEMA audit OWNER TO maarch;

--
-- TOC entry 8 (class 2615 OID 602496)
-- Name: auth; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA auth;


ALTER SCHEMA auth OWNER TO maarch;

--
-- TOC entry 9 (class 2615 OID 602564)
-- Name: batchProcessing; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA "batchProcessing";


ALTER SCHEMA "batchProcessing" OWNER TO maarch;

--
-- TOC entry 10 (class 2615 OID 602591)
-- Name: contact; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA contact;


ALTER SCHEMA contact OWNER TO maarch;

--
-- TOC entry 13 (class 2615 OID 602876)
-- Name: digitalResource; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA "digitalResource";


ALTER SCHEMA "digitalResource" OWNER TO maarch;

--
-- TOC entry 14 (class 2615 OID 603006)
-- Name: filePlan; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA "filePlan";


ALTER SCHEMA "filePlan" OWNER TO maarch;

--
-- TOC entry 15 (class 2615 OID 603034)
-- Name: lifeCycle; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA "lifeCycle";


ALTER SCHEMA "lifeCycle" OWNER TO maarch;

--
-- TOC entry 11 (class 2615 OID 602646)
-- Name: medona; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA medona;


ALTER SCHEMA medona OWNER TO postgres;

--
-- TOC entry 16 (class 2615 OID 603058)
-- Name: organization; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA organization;


ALTER SCHEMA organization OWNER TO maarch;

--
-- TOC entry 12 (class 2615 OID 602708)
-- Name: recordsManagement; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA "recordsManagement";


ALTER SCHEMA "recordsManagement" OWNER TO maarch;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 190 (class 1259 OID 602485)
-- Name: event; Type: TABLE; Schema: audit; Owner: maarch
--

CREATE TABLE audit.event (
                             "eventId" text NOT NULL,
                             "eventDate" timestamp without time zone NOT NULL,
                             "accountId" text NOT NULL,
                             "orgRegNumber" text,
                             "orgUnitRegNumber" text,
                             path text,
                             variables json,
                             input json,
                             output text,
                             status boolean DEFAULT true,
                             info text,
                             "instanceName" text
);


ALTER TABLE audit.event OWNER TO maarch;

--
-- TOC entry 192 (class 1259 OID 602506)
-- Name: account; Type: TABLE; Schema: auth; Owner: maarch
--

CREATE TABLE auth.account (
                              "accountId" text NOT NULL,
                              "accountName" text NOT NULL,
                              "displayName" text NOT NULL,
                              "accountType" text DEFAULT 'user'::text,
                              "emailAddress" text NOT NULL,
                              enabled boolean DEFAULT true,
                              password text,
                              "passwordChangeRequired" boolean DEFAULT true,
                              "passwordLastChange" timestamp without time zone,
                              locked boolean DEFAULT false,
                              "lockDate" timestamp without time zone,
                              "badPasswordCount" integer,
                              "lastLogin" timestamp without time zone,
                              "lastIp" text,
                              "replacingUserAccountId" text,
                              "firstName" text,
                              "lastName" text,
                              title text,
                              salt text,
                              "tokenDate" timestamp without time zone,
                              authentication jsonb,
                              preferences jsonb,
                              "ownerOrgId" text,
                              "isAdmin" boolean
);


ALTER TABLE auth.account OWNER TO maarch;

--
-- TOC entry 194 (class 1259 OID 602538)
-- Name: privilege; Type: TABLE; Schema: auth; Owner: maarch
--

CREATE TABLE auth.privilege (
                                "roleId" text,
                                "userStory" text
);


ALTER TABLE auth.privilege OWNER TO maarch;

--
-- TOC entry 191 (class 1259 OID 602497)
-- Name: role; Type: TABLE; Schema: auth; Owner: maarch
--

CREATE TABLE auth.role (
                           "roleId" text NOT NULL,
                           "roleName" text NOT NULL,
                           description text,
                           "securityLevel" text,
                           enabled boolean DEFAULT true
);


ALTER TABLE auth.role OWNER TO maarch;

--
-- TOC entry 193 (class 1259 OID 602520)
-- Name: roleMember; Type: TABLE; Schema: auth; Owner: maarch
--

CREATE TABLE auth."roleMember" (
                                   "roleId" text,
                                   "userAccountId" text NOT NULL
);


ALTER TABLE auth."roleMember" OWNER TO maarch;

--
-- TOC entry 195 (class 1259 OID 602551)
-- Name: servicePrivilege; Type: TABLE; Schema: auth; Owner: maarch
--

CREATE TABLE auth."servicePrivilege" (
                                         "accountId" text,
                                         "serviceURI" text
);


ALTER TABLE auth."servicePrivilege" OWNER TO maarch;

--
-- TOC entry 197 (class 1259 OID 602573)
-- Name: logScheduling; Type: TABLE; Schema: batchProcessing; Owner: maarch
--

CREATE TABLE "batchProcessing"."logScheduling" (
                                                   "logId" text NOT NULL,
                                                   "schedulingId" text NOT NULL,
                                                   "executedBy" text NOT NULL,
                                                   "launchedBy" text NOT NULL,
                                                   "logDate" timestamp without time zone NOT NULL,
                                                   status boolean DEFAULT true,
                                                   info text
);


ALTER TABLE "batchProcessing"."logScheduling" OWNER TO maarch;

--
-- TOC entry 198 (class 1259 OID 602582)
-- Name: notification; Type: TABLE; Schema: batchProcessing; Owner: maarch
--

CREATE TABLE "batchProcessing".notification (
                                                "notificationId" text NOT NULL,
                                                receivers text NOT NULL,
                                                message text NOT NULL,
                                                title text NOT NULL,
                                                "createdDate" timestamp without time zone NOT NULL,
                                                "createdBy" text,
                                                status text NOT NULL,
                                                "sendDate" timestamp without time zone,
                                                "sendBy" text
);


ALTER TABLE "batchProcessing".notification OWNER TO maarch;

--
-- TOC entry 196 (class 1259 OID 602565)
-- Name: scheduling; Type: TABLE; Schema: batchProcessing; Owner: maarch
--

CREATE TABLE "batchProcessing".scheduling (
                                              "schedulingId" text NOT NULL,
                                              name text NOT NULL,
                                              "taskId" text NOT NULL,
                                              frequency text NOT NULL,
                                              parameters text,
                                              "executedBy" text NOT NULL,
                                              "lastExecution" timestamp without time zone,
                                              "nextExecution" timestamp without time zone,
                                              status text
);


ALTER TABLE "batchProcessing".scheduling OWNER TO maarch;

--
-- TOC entry 200 (class 1259 OID 602601)
-- Name: address; Type: TABLE; Schema: contact; Owner: maarch
--

CREATE TABLE contact.address (
                                 "addressId" text NOT NULL,
                                 "contactId" text NOT NULL,
                                 purpose text NOT NULL,
                                 room text,
                                 floor text,
                                 building text,
                                 number text,
                                 street text,
                                 "postBox" text,
                                 block text,
                                 "citySubDivision" text,
                                 "postCode" text,
                                 city text,
                                 country text
);


ALTER TABLE contact.address OWNER TO maarch;

--
-- TOC entry 202 (class 1259 OID 602626)
-- Name: communication; Type: TABLE; Schema: contact; Owner: maarch
--

CREATE TABLE contact.communication (
                                       "communicationId" text NOT NULL,
                                       "contactId" text NOT NULL,
                                       purpose text NOT NULL,
                                       "comMeanCode" text NOT NULL,
                                       value text NOT NULL,
                                       info text
);


ALTER TABLE contact.communication OWNER TO maarch;

--
-- TOC entry 201 (class 1259 OID 602616)
-- Name: communicationMean; Type: TABLE; Schema: contact; Owner: maarch
--

CREATE TABLE contact."communicationMean" (
                                             code text NOT NULL,
                                             name text NOT NULL,
                                             enabled boolean
);


ALTER TABLE contact."communicationMean" OWNER TO maarch;

--
-- TOC entry 199 (class 1259 OID 602592)
-- Name: contact; Type: TABLE; Schema: contact; Owner: maarch
--

CREATE TABLE contact.contact (
                                 "contactId" text NOT NULL,
                                 "contactType" text DEFAULT 'person'::text NOT NULL,
                                 "orgName" text,
                                 "firstName" text,
                                 "lastName" text,
                                 title text,
                                 function text,
                                 service text,
                                 "displayName" text
);


ALTER TABLE contact.contact OWNER TO maarch;

--
-- TOC entry 223 (class 1259 OID 602920)
-- Name: address; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource".address (
                                           "resId" text NOT NULL,
                                           "repositoryId" text NOT NULL,
                                           path text NOT NULL,
                                           "lastIntegrityCheck" timestamp without time zone,
                                           "integrityCheckResult" boolean,
                                           packed boolean DEFAULT false,
                                           created timestamp without time zone NOT NULL
);


ALTER TABLE "digitalResource".address OWNER TO maarch;

--
-- TOC entry 220 (class 1259 OID 602877)
-- Name: cluster; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource".cluster (
                                           "clusterId" text NOT NULL,
                                           "clusterName" text,
                                           "clusterDescription" text
);


ALTER TABLE "digitalResource".cluster OWNER TO maarch;

--
-- TOC entry 224 (class 1259 OID 602939)
-- Name: clusterRepository; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource"."clusterRepository" (
                                                       "clusterId" text NOT NULL,
                                                       "repositoryId" text NOT NULL,
                                                       "writePriority" integer,
                                                       "readPriority" integer,
                                                       "deletePriority" integer
);


ALTER TABLE "digitalResource"."clusterRepository" OWNER TO maarch;

--
-- TOC entry 227 (class 1259 OID 602986)
-- Name: contentType; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource"."contentType" (
                                                 name text NOT NULL,
                                                 mediatype text NOT NULL,
                                                 description text,
                                                 puids text,
                                                 "validationMode" text,
                                                 "conversionMode" text,
                                                 "textExtractionMode" text,
                                                 "metadataExtractionMode" text
);


ALTER TABLE "digitalResource"."contentType" OWNER TO maarch;

--
-- TOC entry 228 (class 1259 OID 602994)
-- Name: conversionRule; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource"."conversionRule" (
                                                    "conversionRuleId" text NOT NULL,
                                                    puid text NOT NULL,
                                                    "conversionService" text NOT NULL,
                                                    "targetPuid" text NOT NULL
);


ALTER TABLE "digitalResource"."conversionRule" OWNER TO maarch;

--
-- TOC entry 221 (class 1259 OID 602885)
-- Name: digitalResource; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource"."digitalResource" (
                                                     "archiveId" text NOT NULL,
                                                     "resId" text NOT NULL,
                                                     "clusterId" text NOT NULL,
                                                     size bigint NOT NULL,
                                                     puid text,
                                                     mimetype text,
                                                     hash text,
                                                     "hashAlgorithm" text,
                                                     "fileExtension" text,
                                                     "fileName" text,
                                                     "mediaInfo" text,
                                                     created timestamp without time zone NOT NULL,
                                                     updated timestamp without time zone,
                                                     "relatedResId" text,
                                                     "relationshipType" text
);


ALTER TABLE "digitalResource"."digitalResource" OWNER TO maarch;

--
-- TOC entry 225 (class 1259 OID 602957)
-- Name: package; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource".package (
                                           "packageId" text NOT NULL,
                                           method text NOT NULL
);


ALTER TABLE "digitalResource".package OWNER TO maarch;

--
-- TOC entry 226 (class 1259 OID 602970)
-- Name: packedResource; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource"."packedResource" (
                                                    "packageId" text NOT NULL,
                                                    "resId" text NOT NULL,
                                                    name text NOT NULL
);


ALTER TABLE "digitalResource"."packedResource" OWNER TO maarch;

--
-- TOC entry 222 (class 1259 OID 602908)
-- Name: repository; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource".repository (
                                              "repositoryId" text NOT NULL,
                                              "repositoryName" text NOT NULL,
                                              "repositoryReference" text NOT NULL,
                                              "repositoryType" text NOT NULL,
                                              "repositoryUri" text NOT NULL,
                                              parameters text,
                                              "maxSize" integer,
                                              enabled boolean
);


ALTER TABLE "digitalResource".repository OWNER TO maarch;

--
-- TOC entry 229 (class 1259 OID 603007)
-- Name: folder; Type: TABLE; Schema: filePlan; Owner: maarch
--

CREATE TABLE "filePlan".folder (
                                   "folderId" text NOT NULL,
                                   name text NOT NULL,
                                   "parentFolderId" text,
                                   description text,
                                   "ownerOrgRegNumber" text,
                                   closed boolean
);


ALTER TABLE "filePlan".folder OWNER TO maarch;

--
-- TOC entry 230 (class 1259 OID 603022)
-- Name: position; Type: TABLE; Schema: filePlan; Owner: maarch
--

CREATE TABLE "filePlan"."position" (
                                       "folderId" text NOT NULL,
                                       "archiveId" text NOT NULL
);


ALTER TABLE "filePlan"."position" OWNER TO maarch;

--
-- TOC entry 231 (class 1259 OID 603035)
-- Name: event; Type: TABLE; Schema: lifeCycle; Owner: maarch
--

CREATE TABLE "lifeCycle".event (
                                   "eventId" text NOT NULL,
                                   "eventType" text NOT NULL,
                                   "timestamp" timestamp without time zone NOT NULL,
                                   "instanceName" text NOT NULL,
                                   "orgRegNumber" text,
                                   "orgUnitRegNumber" text,
                                   "accountId" text,
                                   "objectClass" text NOT NULL,
                                   "objectId" text NOT NULL,
                                   "operationResult" boolean,
                                   description text,
                                   "eventInfo" text
);


ALTER TABLE "lifeCycle".event OWNER TO maarch;

--
-- TOC entry 232 (class 1259 OID 603043)
-- Name: eventFormat; Type: TABLE; Schema: lifeCycle; Owner: maarch
--

CREATE TABLE "lifeCycle"."eventFormat" (
                                           type text NOT NULL,
                                           format text NOT NULL,
                                           message text NOT NULL,
                                           notification boolean DEFAULT false
);


ALTER TABLE "lifeCycle"."eventFormat" OWNER TO maarch;

--
-- TOC entry 203 (class 1259 OID 602647)
-- Name: archivalAgreement; Type: TABLE; Schema: medona; Owner: maarch
--

CREATE TABLE medona."archivalAgreement" (
                                            "archivalAgreementId" text NOT NULL,
                                            name text NOT NULL,
                                            reference text NOT NULL,
                                            description text,
                                            "archivalProfileReference" text,
                                            "serviceLevelReference" text,
                                            "archiverOrgRegNumber" text NOT NULL,
                                            "depositorOrgRegNumber" text NOT NULL,
                                            "originatorOrgIds" text,
                                            "beginDate" date,
                                            "endDate" date,
                                            enabled boolean,
                                            "allowedFormats" text,
                                            "maxSizeAgreement" integer,
                                            "maxSizeTransfer" integer,
                                            "maxSizeDay" integer,
                                            "maxSizeWeek" integer,
                                            "maxSizeMonth" integer,
                                            "maxSizeYear" integer,
                                            signed boolean,
                                            "autoTransferAcceptance" boolean,
                                            "processSmallArchive" boolean
);


ALTER TABLE medona."archivalAgreement" OWNER TO maarch;

--
-- TOC entry 207 (class 1259 OID 602695)
-- Name: controlAuthority; Type: TABLE; Schema: medona; Owner: maarch
--

CREATE TABLE medona."controlAuthority" (
                                           "originatorOrgUnitId" text NOT NULL,
                                           "controlAuthorityOrgUnitId" text NOT NULL
);


ALTER TABLE medona."controlAuthority" OWNER TO maarch;

--
-- TOC entry 204 (class 1259 OID 602657)
-- Name: message; Type: TABLE; Schema: medona; Owner: maarch
--

CREATE TABLE medona.message (
                                "messageId" text NOT NULL,
                                schema text,
                                type text NOT NULL,
                                status text NOT NULL,
                                date timestamp without time zone NOT NULL,
                                reference text NOT NULL,
                                "accountId" text,
                                "senderOrgRegNumber" text NOT NULL,
                                "senderOrgName" text,
                                "recipientOrgRegNumber" text NOT NULL,
                                "recipientOrgName" text,
                                "archivalAgreementReference" text,
                                "replyCode" text,
                                "operationDate" timestamp without time zone,
                                "receptionDate" timestamp without time zone,
                                "relatedReference" text,
                                "requestReference" text,
                                "replyReference" text,
                                "authorizationReference" text,
                                "authorizationReason" text,
                                "authorizationRequesterOrgRegNumber" text,
                                derogation boolean,
                                "dataObjectCount" integer,
                                size numeric,
                                data text,
                                path text,
                                active boolean,
                                archived boolean,
                                "isIncoming" boolean,
                                comment text
);


ALTER TABLE medona.message OWNER TO maarch;

--
-- TOC entry 205 (class 1259 OID 602667)
-- Name: messageComment; Type: TABLE; Schema: medona; Owner: maarch
--

CREATE TABLE medona."messageComment" (
                                         "messageId" text,
                                         comment text,
                                         "commentId" text NOT NULL
);


ALTER TABLE medona."messageComment" OWNER TO maarch;

--
-- TOC entry 206 (class 1259 OID 602682)
-- Name: unitIdentifier; Type: TABLE; Schema: medona; Owner: maarch
--

CREATE TABLE medona."unitIdentifier" (
                                         "messageId" text NOT NULL,
                                         "objectClass" text NOT NULL,
                                         "objectId" text NOT NULL
);


ALTER TABLE medona."unitIdentifier" OWNER TO maarch;

--
-- TOC entry 238 (class 1259 OID 603128)
-- Name: archivalProfileAccess; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization."archivalProfileAccess" (
                                                      "orgId" text NOT NULL,
                                                      "archivalProfileReference" text NOT NULL,
                                                      "originatorAccess" boolean DEFAULT true,
                                                      "serviceLevelReference" text,
                                                      "userAccess" jsonb
);


ALTER TABLE organization."archivalProfileAccess" OWNER TO maarch;

--
-- TOC entry 237 (class 1259 OID 603115)
-- Name: orgContact; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization."orgContact" (
                                           "contactId" text NOT NULL,
                                           "orgId" text NOT NULL,
                                           "isSelf" boolean
);


ALTER TABLE organization."orgContact" OWNER TO maarch;

--
-- TOC entry 233 (class 1259 OID 603059)
-- Name: orgType; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization."orgType" (
                                        code text NOT NULL,
                                        name text
);


ALTER TABLE organization."orgType" OWNER TO maarch;

--
-- TOC entry 234 (class 1259 OID 603067)
-- Name: organization; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization.organization (
                                           "orgId" text NOT NULL,
                                           "orgName" text NOT NULL,
                                           "otherOrgName" text,
                                           "displayName" text NOT NULL,
                                           "registrationNumber" text NOT NULL,
                                           "beginDate" date,
                                           "endDate" date,
                                           "legalClassification" text,
                                           "businessType" text,
                                           description text,
                                           "orgTypeCode" text,
                                           "orgRoleCodes" text,
                                           "taxIdentifier" text,
                                           "parentOrgId" text,
                                           "ownerOrgId" text,
                                           "isOrgUnit" boolean,
                                           enabled boolean
);


ALTER TABLE organization.organization OWNER TO maarch;

--
-- TOC entry 236 (class 1259 OID 603107)
-- Name: servicePosition; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization."servicePosition" (
                                                "serviceAccountId" text NOT NULL,
                                                "orgId" text NOT NULL
);


ALTER TABLE organization."servicePosition" OWNER TO maarch;

--
-- TOC entry 235 (class 1259 OID 603094)
-- Name: userPosition; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization."userPosition" (
                                             "userAccountId" text NOT NULL,
                                             "orgId" text NOT NULL,
                                             function text,
                                             "default" boolean
);


ALTER TABLE organization."userPosition" OWNER TO maarch;

--
-- TOC entry 208 (class 1259 OID 602709)
-- Name: accessRule; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."accessRule" (
                                                  code text NOT NULL,
                                                  duration text,
                                                  description text NOT NULL
);


ALTER TABLE "recordsManagement"."accessRule" OWNER TO maarch;

--
-- TOC entry 210 (class 1259 OID 602725)
-- Name: archivalProfile; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."archivalProfile" (
                                                       "archivalProfileId" text NOT NULL,
                                                       reference text NOT NULL,
                                                       name text NOT NULL,
                                                       "descriptionSchema" text,
                                                       "descriptionClass" text,
                                                       "retentionStartDate" text,
                                                       "retentionRuleCode" text,
                                                       description text,
                                                       "accessRuleCode" text,
                                                       "acceptUserIndex" boolean DEFAULT false,
                                                       "acceptArchiveWithoutProfile" boolean DEFAULT true,
                                                       "fileplanLevel" text,
                                                       "processingStatuses" jsonb
);


ALTER TABLE "recordsManagement"."archivalProfile" OWNER TO maarch;

--
-- TOC entry 211 (class 1259 OID 602747)
-- Name: archivalProfileContents; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."archivalProfileContents" (
                                                               "parentProfileId" text NOT NULL,
                                                               "containedProfileId" text NOT NULL
);


ALTER TABLE "recordsManagement"."archivalProfileContents" OWNER TO maarch;

--
-- TOC entry 215 (class 1259 OID 602800)
-- Name: archive; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement".archive (
                                             "archiveId" text NOT NULL,
                                             "originatorArchiveId" text,
                                             "depositorArchiveId" text,
                                             "archiverArchiveId" text,
                                             "archiveName" text,
                                             "storagePath" text,
                                             "filePlanPosition" text,
                                             "fileplanLevel" text,
                                             "originatingDate" date,
                                             "descriptionClass" text,
                                             description jsonb,
                                             text text,
                                             "originatorOrgRegNumber" text NOT NULL,
                                             "originatorOwnerOrgId" text,
                                             "originatorOwnerOrgRegNumber" text,
                                             "depositorOrgRegNumber" text,
                                             "archiverOrgRegNumber" text,
                                             "userOrgRegNumbers" text,
                                             "archivalProfileReference" text,
                                             "archivalAgreementReference" text,
                                             "serviceLevelReference" text,
                                             "retentionRuleCode" text,
                                             "retentionStartDate" date,
                                             "retentionDuration" text,
                                             "finalDisposition" text,
                                             "disposalDate" date,
                                             "retentionRuleStatus" text,
                                             "accessRuleCode" text,
                                             "accessRuleDuration" text,
                                             "accessRuleStartDate" date,
                                             "accessRuleComDate" date,
                                             "storageRuleCode" text,
                                             "storageRuleDuration" text,
                                             "storageRuleStartDate" date,
                                             "storageRuleEndDate" date,
                                             "classificationRuleCode" text,
                                             "classificationRuleDuration" text,
                                             "classificationRuleStartDate" date,
                                             "classificationEndDate" date,
                                             "classificationLevel" text,
                                             "classificationOwner" text,
                                             "depositDate" timestamp without time zone NOT NULL,
                                             "lastCheckDate" timestamp without time zone,
                                             "lastDeliveryDate" timestamp without time zone,
                                             "lastModificationDate" timestamp without time zone,
                                             status text NOT NULL,
                                             "processingStatus" text,
                                             "parentArchiveId" text,
                                             "fullTextIndexation" text DEFAULT 'none'::text
);


ALTER TABLE "recordsManagement".archive OWNER TO maarch;

--
-- TOC entry 213 (class 1259 OID 602774)
-- Name: archiveDescription; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."archiveDescription" (
                                                          "archivalProfileId" text NOT NULL,
                                                          "fieldName" text NOT NULL,
                                                          required boolean,
                                                          "position" integer,
                                                          "isImmutable" boolean DEFAULT false,
                                                          "isRetained" boolean DEFAULT true,
                                                          "isInList" boolean DEFAULT false
);


ALTER TABLE "recordsManagement"."archiveDescription" OWNER TO maarch;

--
-- TOC entry 216 (class 1259 OID 602825)
-- Name: archiveRelationship; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."archiveRelationship" (
                                                           "archiveId" text NOT NULL,
                                                           "relatedArchiveId" text NOT NULL,
                                                           "typeCode" text NOT NULL,
                                                           description jsonb
);


ALTER TABLE "recordsManagement"."archiveRelationship" OWNER TO maarch;

--
-- TOC entry 218 (class 1259 OID 602851)
-- Name: descriptionClass; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."descriptionClass" (
                                                        name text NOT NULL,
                                                        label text NOT NULL
);


ALTER TABLE "recordsManagement"."descriptionClass" OWNER TO maarch;

--
-- TOC entry 212 (class 1259 OID 602765)
-- Name: descriptionField; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."descriptionField" (
                                                        name text NOT NULL,
                                                        label text,
                                                        type text,
                                                        "default" text,
                                                        "minLength" smallint,
                                                        "maxLength" smallint,
                                                        "minValue" numeric,
                                                        "maxValue" numeric,
                                                        enumeration text,
                                                        facets jsonb,
                                                        pattern text,
                                                        "isArray" boolean DEFAULT false
);


ALTER TABLE "recordsManagement"."descriptionField" OWNER TO maarch;

--
-- TOC entry 217 (class 1259 OID 602843)
-- Name: log; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement".log (
                                         "archiveId" text NOT NULL,
                                         "fromDate" timestamp without time zone NOT NULL,
                                         "toDate" timestamp without time zone NOT NULL,
                                         "processId" text,
                                         "processName" text,
                                         type text NOT NULL,
                                         "ownerOrgRegNumber" text
);


ALTER TABLE "recordsManagement".log OWNER TO maarch;

--
-- TOC entry 209 (class 1259 OID 602717)
-- Name: retentionRule; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."retentionRule" (
                                                     code text NOT NULL,
                                                     duration text NOT NULL,
                                                     "finalDisposition" text,
                                                     description text,
                                                     label text NOT NULL,
                                                     "implementationDate" date
);


ALTER TABLE "recordsManagement"."retentionRule" OWNER TO maarch;

--
-- TOC entry 214 (class 1259 OID 602790)
-- Name: serviceLevel; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."serviceLevel" (
                                                    "serviceLevelId" text NOT NULL,
                                                    reference text NOT NULL,
                                                    "digitalResourceClusterId" text NOT NULL,
                                                    control text,
                                                    "default" boolean,
                                                    "samplingFrequency" integer,
                                                    "samplingRate" integer
);


ALTER TABLE "recordsManagement"."serviceLevel" OWNER TO maarch;

--
-- TOC entry 219 (class 1259 OID 602859)
-- Name: storageRule; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."storageRule" (
                                                   code text NOT NULL,
                                                   duration text NOT NULL,
                                                   description text,
                                                   label text
);


ALTER TABLE "recordsManagement"."storageRule" OWNER TO maarch;

--
-- TOC entry 2599 (class 0 OID 602485)
-- Dependencies: 190
-- Data for Name: event; Type: TABLE DATA; Schema: audit; Owner: maarch
--

INSERT INTO audit.event ("eventId", "eventDate", "accountId", "orgRegNumber", "orgUnitRegNumber", path, variables, input, output, status, info, "instanceName") VALUES ('maarchRM_q2cezf-1a9b-azwrml', '2019-12-11 10:11:39.681147', 'maarchRM_pwop1b-0ced-pmp8jy', NULL, NULL, 'digitalSafe/digitalSafe/create_originatorOwnerOrgRegNumber__originatorOrgRegNumber_', '{"originatorOwnerOrgRegNumber":"ACME","originatorOrgRegNumber":"RH"}', NULL, 'Object of class auth/account identified by maarchRM_pwop1b-0ced-pmp8jy was not found', false, '{"remoteIp":"127.0.0.1"}', 'maarchRM');
INSERT INTO audit.event ("eventId", "eventDate", "accountId", "orgRegNumber", "orgUnitRegNumber", path, variables, input, output, status, info, "instanceName") VALUES ('maarchRM_q2cezf-1bd4-tf69fo', '2019-12-11 10:11:39.712418', 'maarchRM_pwop1b-0ced-pmp8jy', NULL, NULL, 'digitalSafe/digitalSafe/read_originatorOwnerOrgRegNumber__originatorOrgRegNumber__archiveId_', '{"originatorOwnerOrgRegNumber":"ACME","originatorOrgRegNumber":"RH","archiveId":"Metadata"}', NULL, 'Object of class auth/account identified by maarchRM_pwop1b-0ced-pmp8jy was not found', false, '{"remoteIp":"127.0.0.1"}', 'maarchRM');
INSERT INTO audit.event ("eventId", "eventDate", "accountId", "orgRegNumber", "orgUnitRegNumber", path, variables, input, output, status, info, "instanceName") VALUES ('maarchRM_q2cezf-1ebf-l4v59c', '2019-12-11 10:11:39.787195', 'maarchRM_pwop1b-0ced-pmp8jy', NULL, NULL, 'digitalSafe/digitalSafe/read_originatorOwnerOrgRegNumber__originatorOrgRegNumber__archiveId_', '{"originatorOwnerOrgRegNumber":"ACME","originatorOrgRegNumber":"RH","archiveId":"Integritycheck"}', NULL, 'Object of class auth/account identified by maarchRM_pwop1b-0ced-pmp8jy was not found', false, '{"remoteIp":"127.0.0.1"}', 'maarchRM');
INSERT INTO audit.event ("eventId", "eventDate", "accountId", "orgRegNumber", "orgUnitRegNumber", path, variables, input, output, status, info, "instanceName") VALUES ('maarchRM_q2cezf-1ff9-ecimy9', '2019-12-11 10:11:39.818575', 'maarchRM_pwop1b-0ced-pmp8jy', NULL, NULL, 'digitalSafe/digitalSafe/read_originatorOwnerOrgRegNumber__originatorOrgRegNumber_Count', '{"originatorOwnerOrgRegNumber":"ACME","originatorOrgRegNumber":"RH"}', NULL, 'Object of class auth/account identified by maarchRM_pwop1b-0ced-pmp8jy was not found', false, '{"remoteIp":"127.0.0.1"}', 'maarchRM');
INSERT INTO audit.event ("eventId", "eventDate", "accountId", "orgRegNumber", "orgUnitRegNumber", path, variables, input, output, status, info, "instanceName") VALUES ('maarchRM_q2cezf-213a-bhtwgz', '2019-12-11 10:11:39.850639', 'maarchRM_pwop1b-0ced-pmp8jy', NULL, NULL, 'digitalSafe/digitalSafe/read_originatorOwnerOrgRegNumber__originatorOrgRegNumber_', '{"originatorOwnerOrgRegNumber":"ACME","originatorOrgRegNumber":"RH"}', NULL, 'Object of class auth/account identified by maarchRM_pwop1b-0ced-pmp8jy was not found', false, '{"remoteIp":"127.0.0.1"}', 'maarchRM');
INSERT INTO audit.event ("eventId", "eventDate", "accountId", "orgRegNumber", "orgUnitRegNumber", path, variables, input, output, status, info, "instanceName") VALUES ('maarchRM_q2cezf-232a-xcgetl', '2019-12-11 10:11:39.900222', 'maarchRM_pwop1b-0ced-pmp8jy', NULL, NULL, 'digitalSafe/digitalSafe/read_originatorOwnerOrgRegNumber_events', '{"originatorOwnerOrgRegNumber":"ACME"}', NULL, 'Object of class auth/account identified by maarchRM_pwop1b-0ced-pmp8jy was not found', false, '{"remoteIp":"127.0.0.1"}', 'maarchRM');


--
-- TOC entry 2601 (class 0 OID 602506)
-- Dependencies: 192
-- Data for Name: account; Type: TABLE DATA; Schema: auth; Owner: maarch
--

INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('aackermann', 'aackermann', 'Amanda ACKERMANN', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Amanda', 'ACKERMANN', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('aadams', 'aadams', 'Amy ADAMS', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, '2019-03-15 10:20:33.964708', '127.0.0.1', NULL, 'Amy', 'ADAMS', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('aalambic', 'aalambic', 'Alain ALAMBIC', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, '2019-09-23 14:00:29.059065', '127.0.0.1', NULL, 'Alain', 'ALAMBIC', 'M.', NULL, NULL, '{"csrf": {"2019-09-23T14:05:44,406456Z": "a45c4a7784bd8ed0d7c25f74ce3029e24693f112e03fd606f38f508a61f9554b"}}', NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('aastier', 'aastier', 'Alexandre ASTIER', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, '2019-03-15 10:19:10.46925', '127.0.0.1', NULL, 'Alexandre', 'ASTIER', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('bbain', 'bbain', 'Barbara BAIN', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Barbara', 'BAIN', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('bbardot', 'bbardot', 'Brigitte BARDOT', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', true, '2019-03-14 15:55:48.901327', false, NULL, 0, NULL, NULL, NULL, 'Brigitte', 'BARDOT', 'Mme', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('bblier', 'bblier', 'Bernard BLIER', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, '2019-09-25 15:16:30.662578', '127.0.0.1', NULL, 'Bernard', 'BLIER', 'M.', NULL, NULL, '{"csrf": {"2019-09-25T15:17:12,323803Z": "ce9030d24aceabfb1f1ba14895f7231822efdd979281ccf699b71c23a9ace679"}}', NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('bboule', 'bboule', 'Bruno BOULE', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Bruno', 'BOULE', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ccamus', 'ccamus', 'Cyril CAMUS', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Cyril', 'CAMUS', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('cchaplin', 'cchaplin', 'Charlie CHAPLIN', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Charlie', 'CHAPLIN', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ccharles', 'ccharles', 'Charlotte CHARLES', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Charlotte', 'CHARLES', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ccordy', 'ccordy', 'Chloé CORDY', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, '2019-09-18 16:09:53.920859', '127.0.0.1', NULL, 'Chloé', 'CORDY', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ccox', 'ccox', 'Courtney COX', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Courtney', 'COX', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ddaull', 'ddaull', 'Denis DAULL', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Denis', 'DAULL', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ddenis', 'ddenis', 'Didier DENIS', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Didier', 'DENIS', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ddur', 'ddur', 'Dominique DUR', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Dominique', 'DUR', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('eerina', 'eerina', 'Edith ERINA', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Edith', 'ERINA', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ggrand', 'ggrand', 'George GRAND', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, '2019-09-25 12:07:44.170506', '127.0.0.1', NULL, 'George', 'GRAND', 'M.', NULL, NULL, '{"csrf": {"2019-09-25T12:07:45,573625Z": "ba8478523dd582f81bd42c3052b7f5ce32db1245e3b2b9cf2b0226dd4baa428c"}}', NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('hhier', 'hhier', 'Hubert HIER', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Hubert', 'HIER', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('jjane', 'jjane', 'Jenny JANE', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Jenny', 'JANE', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('jjonasz', 'jjonasz', 'Jean JONASZ', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Jean', 'JONASZ', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('kkaar', 'kkaar', 'Katy KAAR', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Katy', 'KAAR', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('kkrach', 'kkrach', 'Kevin KRACH', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, '2019-03-15 08:30:03.559857', '127.0.0.1', NULL, 'Kevin', 'KRACH', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('mmanfred', 'mmanfred', 'Martin MANFRED', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Martin', 'MANFRED', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('nnataly', 'nnataly', 'Nancy NATALY', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, '2019-03-15 11:07:45.462923', '127.0.0.1', NULL, 'Nancy', 'NATALY', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', true);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ppacioli', 'ppacioli', 'Paolo PACIOLI', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Paolo', 'PACIOLI', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ppetit', 'ppetit', 'Patricia PETIT', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Patricia', 'PETIT', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ppreboist', 'ppreboist', 'Paul PREBOIST', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Paul', 'PREBOIST', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ppruvost', 'ppruvost', 'Pierre PRUVOST', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Pierre', 'PRUVOST', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('rrenaud', 'rrenaud', 'Robert RENAUD', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Robert', 'RENAUD', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('rreynolds', 'rreynolds', 'Ryan REYNOLDS', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Ryan', 'REYNOLDS', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ssaporta', 'ssaporta', 'Sabrina SAPORTA', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Sabrina', 'SAPORTA', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ssissoko', 'ssissoko', 'Sylvain SISSOKO', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Sylvain', 'SISSOKO', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('sstallone', 'sstallone', 'Sylvester STALLONE', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Sylvester', 'STALLONE', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('sstar', 'sstar', 'Suzanne STAR', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Suzanne', 'STAR', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('superadmin', 'superadmin', 'super admin', 'user', 'info@maarch.org', true, '186cf774c97b60a1c106ef718d10970a6a06e06bef89553d9ae65d938a886eae', false, NULL, false, NULL, 0, '2019-09-25 15:17:16.573215', '127.0.0.1', NULL, 'SUPERAdmin', 'Super', 'M.', NULL, NULL, '{"csrf": {"2019-09-25T15:17:21,027830Z": "68989a4ec19ac24a18ae116f229da4760f10b74f038237c79ffcdc35be259012"}}', NULL, NULL, true);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('System', 'Systeme', 'Systeme', 'service', '', true, 'RJpzB36bmR+iuz/aHN9Zl9PDn8tZEs4mzsz9OXNeNIrej2+v3UMzAsF3PSzDUlZ73kPvgqbQmZvza0eZO062uQu57Rdah9z3mdbTh6NBiiR8FQTnW6eVgQ==', true, NULL, false, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '63ce15235abe97db0182e6857c1da763', '2019-03-19 07:54:33.464846', NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('SystemDepositor', 'Systeme versant', 'Systeme versant', 'service', '', true, 'RJpzB36bmR+iuz/aHN9Zl9PDn8tZEs4mzsz9ORUXZpbMim/ilUMpE9FzYG3TW0Eii0Oy1PaFyJ35aBqcMU3gvAq4v0ZY0Z/r0cPVzbAaymd1UEnsAe3MjqGLt7BxvxiHJQ==', true, NULL, false, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '87eda47a7218326af0e3f4eaad7c2c22', '2019-03-19 07:56:55.287696', NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ttong', 'ttong', 'Tony TONG', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Tony', 'TONG', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('sstone', 'sstone', 'Sharon STONE', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, '2019-03-15 08:30:27.732025', '127.0.0.1', NULL, 'Sharon', 'STONE', 'Mme.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('ttule', 'ttule', 'Thierry TULE', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Thierry', 'TULE', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);
INSERT INTO auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") VALUES ('vvictoire', 'vvictoire', 'Victor VICTOIRE', 'user', 'info@maarch.org', true, 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d', false, NULL, false, NULL, 0, NULL, NULL, NULL, 'Victor', 'VICTOIRE', 'M.', NULL, NULL, NULL, NULL, 'ACME', false);


--
-- TOC entry 2603 (class 0 OID 602538)
-- Dependencies: 194
-- Data for Name: privilege; Type: TABLE DATA; Schema: auth; Owner: maarch
--

INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_FONCTIONNEL', 'adminFunc/adminAuthorization');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_FONCTIONNEL', 'adminFunc/adminOrganization');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_FONCTIONNEL', 'adminFunc/adminOrgContact');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_FONCTIONNEL', 'adminFunc/adminOrgUser');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_FONCTIONNEL', 'adminFunc/adminServiceaccount');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_FONCTIONNEL', 'adminFunc/adminUseraccount');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_FONCTIONNEL', 'adminFunc/batchScheduling');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_GENERAL', 'adminFunc/adminOrganization');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_GENERAL', 'adminFunc/adminAuthorization');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_GENERAL', 'adminFunc/adminUseraccount');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_GENERAL', 'adminTech/*');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('ADMIN_GENERAL', 'journal/audit');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'adminArchive/adminAccessRule');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'adminArchive/adminRetentionRule');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'adminArchive/archivalProfile');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'adminArchive/descriptionField');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'adminFunc/AdminArchivalProfileAccess');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'adminFunc/adminOrgContact');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'adminFunc/adminOrganization');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'adminTech/adminFormat');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'archiveDeposit/deposit');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'archiveManagement/addResource');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'archiveManagement/checkIntegrity');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'archiveManagement/filePlan');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'archiveManagement/migration');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'archiveManagement/modifyDescription');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'archiveManagement/modify');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'archiveManagement/retrieve');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'destruction/destructionRequest');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'journal/certificate');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'journal/lifeCycleJournal');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('CORRESPONDANT_ARCHIVES', 'journal/searchLogArchive');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('PRODUCTEUR', 'archiveDeposit/deposit');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('PRODUCTEUR', 'archiveManagement/filePlan');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('PRODUCTEUR', 'archiveManagement/modifyDescription');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('RESPONSABLE_ACTIVITE', 'archiveDeposit/deposit');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('RESPONSABLE_ACTIVITE', 'archiveManagement/checkIntegrity');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('RESPONSABLE_ACTIVITE', 'archiveManagement/filePlan');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('RESPONSABLE_ACTIVITE', 'archiveManagement/modifyDescription');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('RESPONSABLE_ACTIVITE', 'archiveManagement/modify');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('RESPONSABLE_ACTIVITE', 'archiveManagement/retrieve');
INSERT INTO auth.privilege ("roleId", "userStory") VALUES ('RESPONSABLE_ACTIVITE', 'destruction/destructionRequest');


--
-- TOC entry 2600 (class 0 OID 602497)
-- Dependencies: 191
-- Data for Name: role; Type: TABLE DATA; Schema: auth; Owner: maarch
--

INSERT INTO auth.role ("roleId", "roleName", description, "securityLevel", enabled) VALUES ('ADMIN_FONCTIONNEL', 'Administrateur fonctionnel', 'Groupe des administrateurs fonctionnels du système', 'func_admin', true);
INSERT INTO auth.role ("roleId", "roleName", description, "securityLevel", enabled) VALUES ('ADMIN_GENERAL', 'Administrateur général', 'Groupe des administrateurs techniques du système', 'gen_admin', true);
INSERT INTO auth.role ("roleId", "roleName", description, "securityLevel", enabled) VALUES ('CORRESPONDANT_ARCHIVES', 'Correspondant d''archives', 'Groupe des archivistes / records managers / référents d''archives / administrateur fonctionnels', 'user', true);
INSERT INTO auth.role ("roleId", "roleName", description, "securityLevel", enabled) VALUES ('PRODUCTEUR', 'Producteur', 'Groupe des producteurs, versants', 'user', true);
INSERT INTO auth.role ("roleId", "roleName", description, "securityLevel", enabled) VALUES ('RESPONSABLE_ACTIVITE', 'Responsable d''activité', 'Groupe des responsables de service et des activités', 'user', true);
INSERT INTO auth.role ("roleId", "roleName", description, "securityLevel", enabled) VALUES ('UTILISATEUR', 'Utilisateur', 'Groupe des utilisateurs, consultation et navigation', 'user', true);


--
-- TOC entry 2602 (class 0 OID 602520)
-- Dependencies: 193
-- Data for Name: roleMember; Type: TABLE DATA; Schema: auth; Owner: maarch
--

INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('ADMIN_FONCTIONNEL', 'nnataly');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('ADMIN_GENERAL', 'superadmin');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('CORRESPONDANT_ARCHIVES', 'bblier');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('CORRESPONDANT_ARCHIVES', 'ccharles');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('CORRESPONDANT_ARCHIVES', 'ddenis');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'aalambic');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'bbain');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'bbardot');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ccamus');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ccordy');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ddaull');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ddur');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ggrand');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'hhier');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'jjane');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'jjonasz');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'kkaar');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'kkrach');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'mmanfred');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ppacioli');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ppetit');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ppreboist');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'rrenaud');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'rreynolds');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ssaporta');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ssissoko');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'sstar');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ttong');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'ttule');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('PRODUCTEUR', 'vvictoire');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('RESPONSABLE_ACTIVITE', 'aackermann');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('RESPONSABLE_ACTIVITE', 'aadams');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('RESPONSABLE_ACTIVITE', 'aastier');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('RESPONSABLE_ACTIVITE', 'ccox');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('RESPONSABLE_ACTIVITE', 'eerina');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('RESPONSABLE_ACTIVITE', 'nnataly');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('RESPONSABLE_ACTIVITE', 'sstone');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('UTILISATEUR', 'bboule');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('UTILISATEUR', 'cchaplin');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('UTILISATEUR', 'ccordy');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('UTILISATEUR', 'ppruvost');
INSERT INTO auth."roleMember" ("roleId", "userAccountId") VALUES ('UTILISATEUR', 'sstallone');


--
-- TOC entry 2604 (class 0 OID 602551)
-- Dependencies: 195
-- Data for Name: servicePrivilege; Type: TABLE DATA; Schema: auth; Owner: maarch
--

INSERT INTO auth."servicePrivilege" ("accountId", "serviceURI") VALUES ('System', 'audit/event/createChainjournal');
INSERT INTO auth."servicePrivilege" ("accountId", "serviceURI") VALUES ('System', 'batchProcessing/scheduling/updateProcess');
INSERT INTO auth."servicePrivilege" ("accountId", "serviceURI") VALUES ('System', 'lifeCycle/journal/createChainjournal');
INSERT INTO auth."servicePrivilege" ("accountId", "serviceURI") VALUES ('System', 'recordsmanagement/archivecompliance/readperiodic');
INSERT INTO auth."servicePrivilege" ("accountId", "serviceURI") VALUES ('System', 'recordsManagement/archives/deleteDisposablearchives');
INSERT INTO auth."servicePrivilege" ("accountId", "serviceURI") VALUES ('System', 'recordsManagement/archives/updateArchivesretentionrule');
INSERT INTO auth."servicePrivilege" ("accountId", "serviceURI") VALUES ('System', 'recordsManagement/archives/updateIndexfulltext');
INSERT INTO auth."servicePrivilege" ("accountId", "serviceURI") VALUES ('SystemDepositor', '*');


--
-- TOC entry 2606 (class 0 OID 602573)
-- Dependencies: 197
-- Data for Name: logScheduling; Type: TABLE DATA; Schema: batchProcessing; Owner: maarch
--

INSERT INTO "batchProcessing"."logScheduling" ("logId", "schedulingId", "executedBy", "launchedBy", "logDate", status, info) VALUES ('maarchRM_5k7cwmpb4-0000-7v4gda', 'chainJournalAudit', 'System', 'superadmin', '2019-09-19 09:24:29.660848', true, '[{"message":"Timestamp file generated","fullMessage":"Timestamp file generated"},{"message":"New journal identifier : %s","variables":"maarchRM_py2ngt-16cc-ylua9s","fullMessage":"New journal identifier : maarchRM_py2ngt-16cc-ylua9s"}]');


--
-- TOC entry 2607 (class 0 OID 602582)
-- Dependencies: 198
-- Data for Name: notification; Type: TABLE DATA; Schema: batchProcessing; Owner: maarch
--



--
-- TOC entry 2605 (class 0 OID 602565)
-- Dependencies: 196
-- Data for Name: scheduling; Type: TABLE DATA; Schema: batchProcessing; Owner: maarch
--

INSERT INTO "batchProcessing".scheduling ("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) VALUES ('chainJournalAudit', 'Chaînage audit', '01', '00;20;;;;;;;', NULL, 'System', '2019-09-19 09:24:29.658821', '2019-09-20 18:00:00', 'scheduled');
INSERT INTO "batchProcessing".scheduling ("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) VALUES ('chainJournalLifeCycle', 'Chaînage du journal du cycle de vie', '02', '00;20;;;;;;;', NULL, 'System', '2019-03-14 17:17:08.959422', '2019-03-15 19:00:00', 'scheduled');
INSERT INTO "batchProcessing".scheduling ("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) VALUES ('integrity', 'Intégrité', '03', '00;01;;;;4;H;00;20', NULL, 'System', '2019-03-14 17:17:41.825506', '2019-03-14 21:17:41.825513', 'scheduled');
INSERT INTO "batchProcessing".scheduling ("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) VALUES ('processdestruction', 'Traiter les destructions', '04', '00;04;;;;;;;', NULL, 'System', NULL, NULL, 'paused');
INSERT INTO "batchProcessing".scheduling ("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) VALUES ('purge', 'Purge', '05', '00;08;;;;;;;', NULL, 'System', NULL, NULL, 'paused');


--
-- TOC entry 2609 (class 0 OID 602601)
-- Dependencies: 200
-- Data for Name: address; Type: TABLE DATA; Schema: contact; Owner: maarch
--



--
-- TOC entry 2611 (class 0 OID 602626)
-- Dependencies: 202
-- Data for Name: communication; Type: TABLE DATA; Schema: contact; Owner: maarch
--



--
-- TOC entry 2610 (class 0 OID 602616)
-- Dependencies: 201
-- Data for Name: communicationMean; Type: TABLE DATA; Schema: contact; Owner: maarch
--

INSERT INTO contact."communicationMean" (code, name, enabled) VALUES ('AH', 'World Wide Web', false);
INSERT INTO contact."communicationMean" (code, name, enabled) VALUES ('AL', 'Téléphone mobile', true);
INSERT INTO contact."communicationMean" (code, name, enabled) VALUES ('AO', 'URL', true);
INSERT INTO contact."communicationMean" (code, name, enabled) VALUES ('AU', 'FTP', true);
INSERT INTO contact."communicationMean" (code, name, enabled) VALUES ('EM', 'E-mail', true);
INSERT INTO contact."communicationMean" (code, name, enabled) VALUES ('FX', 'Fax', true);
INSERT INTO contact."communicationMean" (code, name, enabled) VALUES ('TE', 'Téléphone', true);


--
-- TOC entry 2608 (class 0 OID 602592)
-- Dependencies: 199
-- Data for Name: contact; Type: TABLE DATA; Schema: contact; Owner: maarch
--



--
-- TOC entry 2632 (class 0 OID 602920)
-- Dependencies: 223
-- Data for Name: address; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--



--
-- TOC entry 2629 (class 0 OID 602877)
-- Dependencies: 220
-- Data for Name: cluster; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

INSERT INTO "digitalResource".cluster ("clusterId", "clusterName", "clusterDescription") VALUES ('archives', 'Digital_resource_cluster_for_archives', 'Digital resource cluster for archives');


--
-- TOC entry 2633 (class 0 OID 602939)
-- Dependencies: 224
-- Data for Name: clusterRepository; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

INSERT INTO "digitalResource"."clusterRepository" ("clusterId", "repositoryId", "writePriority", "readPriority", "deletePriority") VALUES ('archives', 'archives_1', 1, 1, 1);
INSERT INTO "digitalResource"."clusterRepository" ("clusterId", "repositoryId", "writePriority", "readPriority", "deletePriority") VALUES ('archives', 'archives_2', 1, 2, 2);


--
-- TOC entry 2636 (class 0 OID 602986)
-- Dependencies: 227
-- Data for Name: contentType; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--



--
-- TOC entry 2637 (class 0 OID 602994)
-- Dependencies: 228
-- Data for Name: conversionRule; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

INSERT INTO "digitalResource"."conversionRule" ("conversionRuleId", puid, "conversionService", "targetPuid") VALUES ('workflow_pod75x-151b-v9jsef', 'fmt/412', 'dependency/fileSystem/plugins/libreOffice', 'fmt/95');
INSERT INTO "digitalResource"."conversionRule" ("conversionRuleId", puid, "conversionService", "targetPuid") VALUES ('workflow_pod763-1691-dli2t0', 'fmt/291', 'dependency/fileSystem/plugins/libreOffice', 'fmt/18');


--
-- TOC entry 2630 (class 0 OID 602885)
-- Dependencies: 221
-- Data for Name: digitalResource; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--



--
-- TOC entry 2634 (class 0 OID 602957)
-- Dependencies: 225
-- Data for Name: package; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--



--
-- TOC entry 2635 (class 0 OID 602970)
-- Dependencies: 226
-- Data for Name: packedResource; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--



--
-- TOC entry 2631 (class 0 OID 602908)
-- Dependencies: 222
-- Data for Name: repository; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

INSERT INTO "digitalResource".repository ("repositoryId", "repositoryName", "repositoryReference", "repositoryType", "repositoryUri", parameters, "maxSize", enabled) VALUES ('archives_1', 'Digital resource repository for archives', 'repository_1', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/archives_1', NULL, NULL, true);
INSERT INTO "digitalResource".repository ("repositoryId", "repositoryName", "repositoryReference", "repositoryType", "repositoryUri", parameters, "maxSize", enabled) VALUES ('archives_2', 'Digital resource repository for archives 2', 'repository_2', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/archives_2', NULL, NULL, true);


--
-- TOC entry 2638 (class 0 OID 603007)
-- Dependencies: 229
-- Data for Name: folder; Type: TABLE DATA; Schema: filePlan; Owner: maarch
--

INSERT INTO "filePlan".folder ("folderId", name, "parentFolderId", description, "ownerOrgRegNumber", closed) VALUES ('maarchRM_5k7cwmowy-0000-sodmil', 'Journal de l''application', NULL, NULL, 'GIC', false);
INSERT INTO "filePlan".folder ("folderId", name, "parentFolderId", description, "ownerOrgRegNumber", closed) VALUES ('maarchRM_5k7cwmoxs-0000-j6ngdd', '2019', 'maarchRM_5k7cwmowy-0000-sodmil', NULL, 'GIC', false);
INSERT INTO "filePlan".folder ("folderId", name, "parentFolderId", description, "ownerOrgRegNumber", closed) VALUES ('maarchRM_5k7cwmoyi-0000-0y7aua', '09', 'maarchRM_5k7cwmoxs-0000-j6ngdd', NULL, 'GIC', false);


--
-- TOC entry 2639 (class 0 OID 603022)
-- Dependencies: 230
-- Data for Name: position; Type: TABLE DATA; Schema: filePlan; Owner: maarch
--



--
-- TOC entry 2640 (class 0 OID 603035)
-- Dependencies: 231
-- Data for Name: event; Type: TABLE DATA; Schema: lifeCycle; Owner: maarch
--



--
-- TOC entry 2641 (class 0 OID 603043)
-- Dependencies: 232
-- Data for Name: eventFormat; Type: TABLE DATA; Schema: lifeCycle; Owner: maarch
--

INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('medona/acceptance', 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference', 'Message %14$s de type %9$s accepté par %13$s (%12$s)', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('medona/acknowledgement', 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference code info', 'Acquittement du message %14$s : %16$s (%15$s)', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('medona/processing', 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference', 'Traitement du message %14$s de type %9$s de %11$s (%10$s) par %13$s (%12$s)', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('medona/reception', 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference', 'Réception du message %14$s de type %9$s de %11$s (%10$s) par %13$s (%12$s)', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('medona/rejection', 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference', 'Message %14$s de type %9$s rejeté par %13$s (%12$s)', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('medona/retry', 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference', 'Message %14$s de type %9$s réinitialisé par %13$s (%12$s)', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('medona/sending', 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference', 'Envoi du message %14$s de type %9$s de %11$s (%10$s) à %13$s (%12$s)', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('medona/validation', 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference code info', 'Validation du message %14$s : %16$s (%15$s)', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('organization/counting', 'orgName ownerOrgId', 'Compter le nombre d''objets numériques dans l''activité %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('organization/journal', 'orgName ownerOrgId', 'Lecture du journal de l''organisation %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('organization/listing', 'orgName ownerOrgId', 'Lister les identifiants d''objets numériques de l''activité %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/accessRuleModification', 'resId hashAlgorithm hash address accessRuleStartDate accessRuleDuration previousAccessRuleStartDate previousAccessRuleDuration originatorOrgRegNumber archiverOrgRegNumber originatorArchiveId', 'Modification de la règle de communicabilité de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/addRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId originatorArchiveId', 'Relation ajoutée avec l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/archivalProfileModification', 'archivalProfileReference', 'Modification du profil %6$s.', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/consultation', 'resId hash hashAlgorith address size originatorArchiveId', 'Consultation de la ressource %9$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/conversion', 'resId hashAlgorithm hash address convertedResId convertedHashAlgorithm convertedHash convertedAddress software docId size originatorArchiveId', 'Conversion du document %18$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/deleteRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId originatorArchiveId', 'Relation avec l''archive %6$s supprimée', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/delivery', 'resId hashAlgorithm hash address requesterOrgRegNumber archiverOrgRegNumber size originatorArchiveId', 'Communication de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/deposit', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format size originatorArchiveId', 'Dépôt de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/depositNewResource', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format size originatorArchiveId', 'Dépôt d''une ressource dans l''archive', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/destruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size originatorArchiveId', 'Destruction de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/destructionRequest', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size originatorArchiveId', 'Demande de destruction de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/destructionRequestCanceling', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size originatorArchiveId', 'Annulation de la demande de destruction de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/elimination', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size originatorArchiveId', 'Élimination de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/freeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber originatorArchiveId', 'Gel de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/integrityCheck', 'resId hash hashAlgorithm address requesterOrgRegNumber info originatorArchiveId', 'Validation d''intégrité', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/metadataModification', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber originatorArchiveId', 'Modification des métadonnées de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/periodicIntegrityCheck', 'startDatetime endDatetime nbArchivesToCheck nbArchivesInSample archivesChecked originatorArchiveId', 'Validation périodique de l''intégrité', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/profileCreation', 'archivalProfileReference', 'Création du profil %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/profileDestruction', 'archivalProfileReference', 'Destruction du profil %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/resourceDestruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber originatorArchiveId', 'Destruction de la ressource %9$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/restitution', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size originatorArchiveId', 'Restitution de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/retentionRuleModification', 'resId hashAlgorithm hash address retentionStartDate retentionDuration finalDisposition previousStartDate previousDuration previousFinalDisposition originatorOrgRegNumber archiverOrgRegNumber originatorArchiveId', 'Modification de la règle de conservation de l''archive %6$s', false);
INSERT INTO "lifeCycle"."eventFormat" (type, format, message, notification) VALUES ('recordsManagement/unfreeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber originatorArchiveId', 'Dégel de l''archive %6$s', false);


--
-- TOC entry 2612 (class 0 OID 602647)
-- Dependencies: 203
-- Data for Name: archivalAgreement; Type: TABLE DATA; Schema: medona; Owner: maarch
--



--
-- TOC entry 2616 (class 0 OID 602695)
-- Dependencies: 207
-- Data for Name: controlAuthority; Type: TABLE DATA; Schema: medona; Owner: maarch
--



--
-- TOC entry 2613 (class 0 OID 602657)
-- Dependencies: 204
-- Data for Name: message; Type: TABLE DATA; Schema: medona; Owner: maarch
--



--
-- TOC entry 2614 (class 0 OID 602667)
-- Dependencies: 205
-- Data for Name: messageComment; Type: TABLE DATA; Schema: medona; Owner: maarch
--



--
-- TOC entry 2615 (class 0 OID 602682)
-- Dependencies: 206
-- Data for Name: unitIdentifier; Type: TABLE DATA; Schema: medona; Owner: maarch
--



--
-- TOC entry 2647 (class 0 OID 603128)
-- Dependencies: 238
-- Data for Name: archivalProfileAccess; Type: TABLE DATA; Schema: organization; Owner: maarch
--

INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DAF', 'FACACH', false, '', '{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}, "VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DIP', 'DOSIP', true, '', NULL);
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DSI', 'FACACH', false, '', '{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}, "VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('FOUR', 'FACACH', true, '', '{"history": {}, "subProfile": {}, "processingStatuses": {"NEW": {"actions": {"qualify": {}, "cancelQualify": {}}}, "APPROVED": {"actions": {"pay": {}, "updateMetadata": {}}}, "REJECTED": {"actions": {"cancelQualify": {}, "sendValidation": {}, "updateMetadata": {}, "sendToApprobation": {}}}, "MISQUALIFIED": {"actions": {"qualify": {}}}}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('FOUR', 'FACJU', true, '', NULL);
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('SALES', 'FACVEN', true, '', NULL);
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DSG', 'FACACH', false, '', '{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}, "VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('PAIE', 'BULPAI', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DAF', 'NOTSER', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DCIAL', 'NOTSER', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('RH', '*', true, '', NULL);
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DSI', 'NOTSER', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('GIC', 'NOTSER', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('TENDER', 'PM', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('TENDER', 'COUNM', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DOCSOC', 'FICCR', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DOCSOC', 'LETC', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DOCSOC', 'FICI', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('MARK', 'FACACH', false, '', '{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DCIAL', 'FACACH', false, '', '{"subProfile": {}, "processingStatuses": {"VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}');


--
-- TOC entry 2646 (class 0 OID 603115)
-- Dependencies: 237
-- Data for Name: orgContact; Type: TABLE DATA; Schema: organization; Owner: maarch
--



--
-- TOC entry 2642 (class 0 OID 603059)
-- Dependencies: 233
-- Data for Name: orgType; Type: TABLE DATA; Schema: organization; Owner: maarch
--

INSERT INTO organization."orgType" (code, name) VALUES ('Collectivite', 'Collectivité');
INSERT INTO organization."orgType" (code, name) VALUES ('Direction', 'Direction d''une entreprise ou d''une collectivité');
INSERT INTO organization."orgType" (code, name) VALUES ('Division', 'Division d''une entreprise');
INSERT INTO organization."orgType" (code, name) VALUES ('Service', 'Service d''une entreprise ou d''une collectivité');
INSERT INTO organization."orgType" (code, name) VALUES ('Societe', 'Société');


--
-- TOC entry 2643 (class 0 OID 603067)
-- Dependencies: 234
-- Data for Name: organization; Type: TABLE DATA; Schema: organization; Owner: maarch
--

INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('ACME', 'Archives Conservation et Mémoire Électronique', NULL, 'Archives Conservation et Mémoire Électronique', 'ACME', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('DAF', 'Direction Administrative et Financière', NULL, 'Direction Administrative et Financière', 'DAF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ACME', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('DSG', 'Services généraux', NULL, 'Direction des Services Généraux', 'DSG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DAF', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('ACHAT', 'Achats Groupe', NULL, 'Achats Groupe', 'ACHAT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DSG', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('AUTO', 'Gestion parc Auto', NULL, 'Gestion du Parc Automobile', 'AUTO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DSG', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('NETT', 'Nettoyage des Locaux', NULL, 'Nettoyage des Locaux', 'NETT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DSG', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('SJ', 'Service Juridique', NULL, 'Service Juridique', 'SJ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DAF', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('ASSU', 'Assurances Groupe', NULL, 'Assurances du Groupe', 'ASSU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SJ', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('CONCOM', 'Contrats Commerciaux', NULL, 'Contrats Commerciaux', 'CONCOM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SJ', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('DSOC', 'Droit des sociétés', NULL, 'Droit des Sociétés', 'DSOC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SJ', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('CTBLE', 'Service Comptable', NULL, 'Service Comptable', 'CTBLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DAF', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('BILAN', 'Comptes de bilan', NULL, 'Comptes de Bilan et Clôture d''Exercice', 'BILAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('FISCA', 'Fiscalité', NULL, 'Fiscalité', 'FISCA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('FOUR', 'Achats/Fournisseurs', NULL, 'Comptabilité Achats/Fournisseurs', 'FOUR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('GEN', 'Comptabilité Générale', NULL, 'Comptabilité Générale/Éditions comptables', 'GEN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('SALES', 'Ventes/Clients', NULL, 'Comptabilité Ventes/Clients', 'SALES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('TRESO', 'Trésorerie', NULL, 'Trésorerie', 'TRESO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('DG', 'Direction Générale', NULL, 'Direction Générale', 'DG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DAF', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('DOCSOC', 'Documents de société', NULL, 'Document de société', 'DOCSOC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DG', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('LITIGES', 'Suivi litiges Contentieux', NULL, 'Suivi des litiges et contentieux', 'LITIGES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DG', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('DCIAL', 'Direction Commerciale', NULL, 'Direction Commerciale', 'DCIAL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ACME', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('CUST', 'Gestion Clients', NULL, 'Gestion Clients', 'CUST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DCIAL', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('DEAL', 'Offres Commerciales', NULL, 'Offres Commerciales', 'DEAL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DCIAL', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('MARK', 'Marketing', NULL, 'Marketing', 'MARK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DCIAL', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('TENDER', 'Appels d''offres', NULL, 'Réponses aux Appels d''Offres/Collectivités', 'TENDER', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DCIAL', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('DSI', 'Direction des SI', NULL, 'Direction des Systèmes d''Information', 'DSI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ACME', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('SIG', 'Gestion systèmes d''informations', NULL, 'Gestion des Systèmes d''Information', 'SIG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DSI', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('SYSRES', 'Système et Réseaux', NULL, 'Système et Réseaux', 'SYSRES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DSI', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('SUPP', 'Support', NULL, 'Support', 'SUPP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DSI', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('GIC', 'Gestion et Conservation de l''Information', NULL, 'Gestion et Conservation de l''Information', 'GIC', NULL, NULL, NULL, NULL, NULL, NULL, 'owner', NULL, 'ACME', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('RH', 'Direction des Ressources Humaines', NULL, 'Direction des Ressources Humaines', 'RH', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ACME', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('DIP', 'Dossiers du personnel', NULL, 'Dossiers Individuels du Personnel', 'DIP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RH', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('MUT', 'Prévoyance/Mutuelle', NULL, 'Prévoyance/Mutuelle', 'MUT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RH', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('NOTFRA', 'Notes Frais', NULL, 'Gestion des Notes de Frais', 'NOTFRA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RH', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('PAIE', 'Rémunération et Paie', NULL, 'Rémunération et Paie', 'PAIE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RH', 'ACME', true, NULL);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit", enabled) VALUES ('SOC', 'Charges Sociales', NULL, 'Charges Sociales', 'SOC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RH', 'ACME', true, NULL);


--
-- TOC entry 2645 (class 0 OID 603107)
-- Dependencies: 236
-- Data for Name: servicePosition; Type: TABLE DATA; Schema: organization; Owner: maarch
--

INSERT INTO organization."servicePosition" ("serviceAccountId", "orgId") VALUES ('System', 'GIC');
INSERT INTO organization."servicePosition" ("serviceAccountId", "orgId") VALUES ('SystemDepositor', 'RH');


--
-- TOC entry 2644 (class 0 OID 603094)
-- Dependencies: 235
-- Data for Name: userPosition; Type: TABLE DATA; Schema: organization; Owner: maarch
--

INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('aackermann', 'FOUR', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('aadams', 'DAF', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('aalambic', 'FOUR', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('aastier', 'DSG', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('bbain', 'SJ', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('bbardot', 'GEN', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('bblier', 'GIC', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('bboule', 'SALES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ccamus', 'TRESO', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('cchaplin', 'RH', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ccharles', 'GIC', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ccordy', 'FOUR', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ccox', 'SJ', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ddaull', 'DAF', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ddenis', 'GIC', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ddur', 'SALES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('eerina', 'SALES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ggrand', 'RH', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('hhier', 'PAIE', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('jjane', 'DOCSOC', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('jjonasz', 'LITIGES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('kkaar', 'TENDER', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('kkrach', 'MARK', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('mmanfred', 'DEAL', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ppacioli', 'NETT', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ppetit', 'CTBLE', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ppreboist', 'DSI', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ppruvost', 'DSI', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('rrenaud', 'DSG', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('rreynolds', 'ACHAT', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ssaporta', 'AUTO', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ssissoko', 'CUST', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('sstallone', 'SUPP', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('sstar', 'SUPP', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('sstone', 'DCIAL', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ttong', 'SALES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ttule', 'SYSRES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('vvictoire', 'RH', NULL, true);


--
-- TOC entry 2617 (class 0 OID 602709)
-- Dependencies: 208
-- Data for Name: accessRule; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--



--
-- TOC entry 2619 (class 0 OID 602725)
-- Dependencies: 210
-- Data for Name: archivalProfile; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('1', 'COUA', 'Courrier Administratif', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('2', 'PRVN', 'Procès-Verbal de Négociation', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('3', 'PRVIF', 'Procès-verbal à Incidence Financière', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('4', 'ETAR', 'État de Rapprochement', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('5', 'RELCC', 'Relevé de Contrôle de Caisse', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('6', 'CTRF', 'Contrat Fournisseur', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('7', 'DCLTVA', 'Déclaration de TVA', NULL, NULL, 'originatingDate', 'IMP', NULL, NULL, true, true, 'file', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('8', 'QUTP', 'Quittance de Paiement', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('9', 'FICIC', 'Fiche d''Imputation Comptable', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('10', 'FACJU', 'Facture Justificative', NULL, NULL, 'originatingDate', 'COM', NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('11', 'FICREC', 'Fiche Récapitulative', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('12', 'DOSC', 'Dossiers Caisse', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'file', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('13', 'PIECD', 'Pièce de Caisse-Dépense', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('14', 'PIEJ', 'Pièce Justificative', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('15', 'DOSB', 'Dossiers Banque', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'file', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('16', 'PIEBD', 'Pièce de Banque-Dépense', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('17', 'FICDG', 'Fiche DG', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('18', 'NOTSER', 'Note de service', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('19', 'PM', 'Passation de marché', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'file', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('20', 'BORT', 'Bordereau de transmission', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('21', 'COUNM', 'Courrier de notification de marché', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('22', 'PRVA', 'Procès-Verbal d''Attribution', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('23', 'PRVOP', 'Proces-Verbal d''Ouverture des Plis', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('24', 'RAPEO', 'Rapport d’Évaluation des Offres', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('26', 'DEMC', 'Demande de Cotation', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('27', 'RAPFOR', 'Rapport de Formation', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('28', 'DOSIP', 'Dossier Individuel du Personnel', NULL, NULL, 'originatingDate', 'DIP', '', NULL, true, true, 'file', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('29', 'ETAC', 'Etat Civil', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('30', 'CURV', 'Curriculum Vitae', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('31', 'EXTAN', 'Extrait d''Acte de Naissance', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('33', 'CASJU', 'Casier Judiciaire', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('34', 'ATTSU', 'Attestation de succès', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('35', 'CTRTRV', 'Contrat de Travail', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('37', 'ATTT', 'Attestation de Travail', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('39', 'CNSS', 'Caisse Nationale de Sécurité Sociale', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('41', 'CAR', 'Carrière', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('42', 'DECIN', 'Décision de nomination', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('43', 'DECIR', 'Décision de redéploiement', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('47', 'ATTF', 'Attestation de formation', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('53', 'COURRN', 'Courrier Répartition du Résultat Net', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('54', 'COUDS', 'Courrier Domiciliation de Salaire', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('55', 'FICP', 'Fiche de Poste', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('56', 'FICF', 'Fiche de Fonction', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('57', 'DEMA', 'Demandes Administratives', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('58', 'COUAA', 'Courrier Autorisation d''Absence', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('59', 'COUCA', 'Courrier Congés Administratifs', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('60', 'COUDE', 'Courrier Demande d''Emploi', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('61', 'DOSETU', 'Dossier de Synthèse et d’Étude', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'file', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('62', 'FICRM', 'Fiche de Remontée Mensuelle', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('63', 'RAPT', 'Rapport Trimestriel', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('64', 'RAPMOE', '	Rapport de Mise en Œuvre', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('65', 'RAPA', 'Rapport d''Activité', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('66', 'RAPSE', 'Rapport de Suivi et Évaluation', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('67', 'RAPGES', 'Rapport de Gestion', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('68', 'TDRE', 'Termes de Références des Études', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('69', 'DCRN', 'Décret de Nomination', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('70', 'FICCR', 'Fiche de compte rendu', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('71', 'LETC', 'Lettre circulaire', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('72', 'FICI', 'Fiche d''instruction', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('73', 'RAPAMI', 'Rapport d''Audit et Missions Internes', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('74', 'RAPAE', 'Rapport d''Audit Externe', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('75', 'RAPER', 'Rapport d’Études et Recherches', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('76', 'COUABID', 'Courrier Arrivée BID', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('77', 'SMIROP', 'Fiche de visite SMIROP', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('78', 'VISA', 'Visas obtenus', NULL, NULL, 'originatingDate', NULL, NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('79', 'FACVEN', 'Facture de vente', NULL, NULL, 'originatingDate', 'COM', NULL, NULL, true, true, 'item', NULL);
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('80', 'FACACH', 'Facture d''achat', NULL, '', 'description/dueDate', 'COM', '', NULL, true, false, 'item', '{"NEW": {"type": "initial", "label": "Nouvelle(s) facture(s)", "actions": {"qualify": {}, "cancelQualify": {}}, "default": true, "position": 0, "filterUserAccess": false}, "PAYED": {"type": "final", "label": "Payée", "actions": {}, "default": false, "position": 6, "filterUserAccess": false}, "APPROVED": {"type": "intermediate", "label": "À payer", "actions": {"pay": {}, "updateMetadata": {}}, "default": false, "position": 5, "filterUserAccess": true}, "REJECTED": {"type": "intermediate", "label": "Rejetée(s)", "actions": {"sendValidation": {}, "updateMetadata": {}, "sendToApprobation": {}}, "default": false, "position": 3, "filterUserAccess": true}, "CANCELLED": {"type": "final", "label": "Annulée", "actions": {}, "default": false, "position": 7, "filterUserAccess": false}, "QUALIFIED": {"type": "intermediate", "label": "À valider", "actions": {"reject": {}, "redirect": {}, "validate": {}}, "default": false, "position": 1, "filterUserAccess": true}, "VALIDATED": {"type": "intermediate", "label": "À approuver", "actions": {"reject": {}, "approve": {}}, "default": false, "position": 4, "filterUserAccess": true}, "MISQUALIFIED": {"type": "intermediate", "label": "À requalifier", "actions": {"qualify": {}}, "default": false, "position": 2, "filterUserAccess": true}}');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") VALUES ('81', 'BULPAI', 'Bulletins de paie', NULL, '', NULL, 'BULPAI', '', NULL, false, false, 'item', '{}');


--
-- TOC entry 2620 (class 0 OID 602747)
-- Dependencies: 211
-- Data for Name: archivalProfileContents; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('7', '8');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('7', '9');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('7', '10');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('7', '11');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('12', '9');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('12', '13');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('12', '14');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('15', '9');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('15', '14');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('15', '16');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '2');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '18');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '20');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '21');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '22');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '23');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '24');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '26');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '1');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '29');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '30');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '31');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '33');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '34');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '35');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '37');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '39');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '41');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '42');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '43');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '47');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '53');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '54');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '55');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '56');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '57');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '58');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '59');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '60');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '77');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '78');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '1');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '17');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '63');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '64');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '65');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '66');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '67');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '68');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '69');


--
-- TOC entry 2624 (class 0 OID 602800)
-- Dependencies: 215
-- Data for Name: archive; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--



--
-- TOC entry 2622 (class 0 OID 602774)
-- Dependencies: 213
-- Data for Name: archiveDescription; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('1', 'org', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('2', 'org', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('3', 'org', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('4', 'org', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('5', 'org', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('6', 'org', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('28', 'empid', true, 1, false, NULL, NULL);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('28', 'fullname', false, 0, false, NULL, NULL);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('28', 'reference_addresses', false, 2, false, NULL, NULL);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('61', 'service', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('69', 'service', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('70', 'service', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('71', 'service', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('72', 'service', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('73', 'service', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('74', 'service', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('75', 'service', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('76', 'service', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('79', 'customer', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('79', 'salesPerson', false, 0, false, false, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('80', 'dueDate', false, 4, false, NULL, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('80', 'netPayable', false, 3, false, NULL, true);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('80', 'orderNumber', false, 5, false, NULL, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('80', 'service', false, 0, false, NULL, true);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('80', 'supplier', false, 2, false, NULL, true);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('80', 'taxIdentifier', true, 1, false, NULL, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('81', 'empid', true, 0, false, NULL, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('81', 'fullname', true, 1, false, NULL, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('81', 'service', false, 2, false, NULL, false);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") VALUES ('81', 'org', true, 3, false, NULL, false);


--
-- TOC entry 2625 (class 0 OID 602825)
-- Dependencies: 216
-- Data for Name: archiveRelationship; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--



--
-- TOC entry 2627 (class 0 OID 602851)
-- Dependencies: 218
-- Data for Name: descriptionClass; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--



--
-- TOC entry 2621 (class 0 OID 602765)
-- Dependencies: 212
-- Data for Name: descriptionField; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('customer', 'Client', 'text', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('documentId', 'Identifiant de document', 'name', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('dueDate', 'Date d''échéance', 'date', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('empid', 'Matricule', 'name', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('fullname', 'Nom complet', 'name', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('netPayable', 'Net à payer', 'number', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('orderNumber', 'Numéro de commande', 'name', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('org', 'Organisation', 'name', '', NULL, NULL, NULL, NULL, '["ACME Paris","ACME Dakar","ACME Cotonou"]', NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('salesPerson', 'Vendeur', 'text', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('service', 'Service Concerné', 'name', '', NULL, NULL, NULL, NULL, '["MARK","DSG","DSI","DAF"]', NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('supplier', 'Fournisseur', 'text', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);
INSERT INTO "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") VALUES ('taxIdentifier', 'N° TVA Intraco.', 'name', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);


--
-- TOC entry 2626 (class 0 OID 602843)
-- Dependencies: 217
-- Data for Name: log; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--



--
-- TOC entry 2618 (class 0 OID 602717)
-- Dependencies: 209
-- Data for Name: retentionRule; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

INSERT INTO "recordsManagement"."retentionRule" (code, duration, "finalDisposition", description, label, "implementationDate") VALUES ('BULPAI', 'P5Y', 'destruction', 'Code du Travail, art. L3243-4 - Code de la Sécurité Sociale, art. L243-12', 'Bulletins de paie', NULL);
INSERT INTO "recordsManagement"."retentionRule" (code, duration, "finalDisposition", description, label, "implementationDate") VALUES ('COM', 'P10Y', 'destruction', 'Code du commerce, Article L123-22', 'Documents comptables', NULL);
INSERT INTO "recordsManagement"."retentionRule" (code, duration, "finalDisposition", description, label, "implementationDate") VALUES ('DIP', 'P90Y', 'destruction', 'Convention Collective nationale de retraite et de prévoyance des cadres, art. 23', 'Dossier individuel du personnel', NULL);
INSERT INTO "recordsManagement"."retentionRule" (code, duration, "finalDisposition", description, label, "implementationDate") VALUES ('IMP', 'P6Y', 'destruction', 'Livre des Procédures Fiscales, art 102 B et L 169 : Livres, registres, documents ou pièces sur lesquels peuvent s''exercer les droits de communication, d''enquête et de contrôle de l''administration', 'Contrôle de l''impôt', NULL);
INSERT INTO "recordsManagement"."retentionRule" (code, duration, "finalDisposition", description, label, "implementationDate") VALUES ('IMPA', 'P3Y', 'destruction', 'Livre des Procédures Fiscales, art 102 B et L 169 alinea 3', 'Taxe professionnelle', NULL);
INSERT INTO "recordsManagement"."retentionRule" (code, duration, "finalDisposition", description, label, "implementationDate") VALUES ('IMPS', 'P10Y', 'destruction', 'Livre des Procédures Fiscales, art 102 B et L 169 alinea 2: Les registres tenus en application du 9 de l''article 298 sexdecies F du code général des impôts et du 5 de l''article 298 sexdecies G du même code', 'Impôt sur les sociétés et liasses fiscales', NULL);
INSERT INTO "recordsManagement"."retentionRule" (code, duration, "finalDisposition", description, label, "implementationDate") VALUES ('GES', 'P5Y', 'destruction', 'Documents de gestion', 'Documents de gestion', NULL);


--
-- TOC entry 2623 (class 0 OID 602790)
-- Dependencies: 214
-- Data for Name: serviceLevel; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

INSERT INTO "recordsManagement"."serviceLevel" ("serviceLevelId", reference, "digitalResourceClusterId", control, "default", "samplingFrequency", "samplingRate") VALUES ('ServiceLevel_001', 'serviceLevel_001', 'archives', 'formatDetection formatValidation virusCheck convertOnDeposit', false, 2, 50);
INSERT INTO "recordsManagement"."serviceLevel" ("serviceLevelId", reference, "digitalResourceClusterId", control, "default", "samplingFrequency", "samplingRate") VALUES ('ServiceLevel_002', 'serviceLevel_002', 'archives', '', true, 2, 50);


--
-- TOC entry 2628 (class 0 OID 602859)
-- Dependencies: 219
-- Data for Name: storageRule; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--



--
-- TOC entry 2296 (class 2606 OID 602493)
-- Name: event event_pkey; Type: CONSTRAINT; Schema: audit; Owner: maarch
--

ALTER TABLE ONLY audit.event
    ADD CONSTRAINT event_pkey PRIMARY KEY ("eventId");


--
-- TOC entry 2300 (class 2606 OID 602519)
-- Name: account account_accountName_key; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth.account
    ADD CONSTRAINT "account_accountName_key" UNIQUE ("accountName");


--
-- TOC entry 2302 (class 2606 OID 602517)
-- Name: account account_pkey; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth.account
    ADD CONSTRAINT account_pkey PRIMARY KEY ("accountId");


--
-- TOC entry 2306 (class 2606 OID 602545)
-- Name: privilege privilege_roleId_userStory_key; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth.privilege
    ADD CONSTRAINT "privilege_roleId_userStory_key" UNIQUE ("roleId", "userStory");


--
-- TOC entry 2304 (class 2606 OID 602527)
-- Name: roleMember roleMember_roleId_userAccountId_key; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth."roleMember"
    ADD CONSTRAINT "roleMember_roleId_userAccountId_key" UNIQUE ("roleId", "userAccountId");


--
-- TOC entry 2298 (class 2606 OID 602505)
-- Name: role role_pkey; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth.role
    ADD CONSTRAINT role_pkey PRIMARY KEY ("roleId");


--
-- TOC entry 2308 (class 2606 OID 602558)
-- Name: servicePrivilege servicePrivilege_accountId_serviceURI_key; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth."servicePrivilege"
    ADD CONSTRAINT "servicePrivilege_accountId_serviceURI_key" UNIQUE ("accountId", "serviceURI");


--
-- TOC entry 2313 (class 2606 OID 602581)
-- Name: logScheduling logScheduling_pkey; Type: CONSTRAINT; Schema: batchProcessing; Owner: maarch
--

ALTER TABLE ONLY "batchProcessing"."logScheduling"
    ADD CONSTRAINT "logScheduling_pkey" PRIMARY KEY ("logId");


--
-- TOC entry 2315 (class 2606 OID 602589)
-- Name: notification notification_pkey; Type: CONSTRAINT; Schema: batchProcessing; Owner: maarch
--

ALTER TABLE ONLY "batchProcessing".notification
    ADD CONSTRAINT notification_pkey PRIMARY KEY ("notificationId");


--
-- TOC entry 2310 (class 2606 OID 602572)
-- Name: scheduling scheduling_pkey; Type: CONSTRAINT; Schema: batchProcessing; Owner: maarch
--

ALTER TABLE ONLY "batchProcessing".scheduling
    ADD CONSTRAINT scheduling_pkey PRIMARY KEY ("schedulingId");


--
-- TOC entry 2319 (class 2606 OID 602610)
-- Name: address address_contactId_purpose_key; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.address
    ADD CONSTRAINT "address_contactId_purpose_key" UNIQUE ("contactId", purpose);


--
-- TOC entry 2321 (class 2606 OID 602608)
-- Name: address address_pkey; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.address
    ADD CONSTRAINT address_pkey PRIMARY KEY ("addressId");


--
-- TOC entry 2323 (class 2606 OID 602625)
-- Name: communicationMean communicationMean_name_key; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact."communicationMean"
    ADD CONSTRAINT "communicationMean_name_key" UNIQUE (name);


--
-- TOC entry 2325 (class 2606 OID 602623)
-- Name: communicationMean communicationMean_pkey; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact."communicationMean"
    ADD CONSTRAINT "communicationMean_pkey" PRIMARY KEY (code);


--
-- TOC entry 2327 (class 2606 OID 602635)
-- Name: communication communication_contactId_purpose_comMeanCode_key; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.communication
    ADD CONSTRAINT "communication_contactId_purpose_comMeanCode_key" UNIQUE ("contactId", purpose, "comMeanCode");


--
-- TOC entry 2329 (class 2606 OID 602633)
-- Name: communication communication_pkey; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.communication
    ADD CONSTRAINT communication_pkey PRIMARY KEY ("communicationId");


--
-- TOC entry 2317 (class 2606 OID 602600)
-- Name: contact contact_pkey; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.contact
    ADD CONSTRAINT contact_pkey PRIMARY KEY ("contactId");


--
-- TOC entry 2407 (class 2606 OID 602928)
-- Name: address address_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".address
    ADD CONSTRAINT address_pkey PRIMARY KEY ("resId", "repositoryId");


--
-- TOC entry 2409 (class 2606 OID 602946)
-- Name: clusterRepository clusterRepository_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."clusterRepository"
    ADD CONSTRAINT "clusterRepository_pkey" PRIMARY KEY ("clusterId", "repositoryId");


--
-- TOC entry 2395 (class 2606 OID 602884)
-- Name: cluster cluster_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".cluster
    ADD CONSTRAINT cluster_pkey PRIMARY KEY ("clusterId");


--
-- TOC entry 2413 (class 2606 OID 602993)
-- Name: contentType contentType_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."contentType"
    ADD CONSTRAINT "contentType_pkey" PRIMARY KEY (name);


--
-- TOC entry 2415 (class 2606 OID 603001)
-- Name: conversionRule conversionRule_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."conversionRule"
    ADD CONSTRAINT "conversionRule_pkey" PRIMARY KEY ("conversionRuleId");


--
-- TOC entry 2417 (class 2606 OID 603003)
-- Name: conversionRule conversionRule_puid_key; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."conversionRule"
    ADD CONSTRAINT "conversionRule_puid_key" UNIQUE (puid);


--
-- TOC entry 2399 (class 2606 OID 602892)
-- Name: digitalResource digitalResource_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."digitalResource"
    ADD CONSTRAINT "digitalResource_pkey" PRIMARY KEY ("resId");


--
-- TOC entry 2411 (class 2606 OID 602964)
-- Name: package package_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".package
    ADD CONSTRAINT package_pkey PRIMARY KEY ("packageId");


--
-- TOC entry 2401 (class 2606 OID 602915)
-- Name: repository repository_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".repository
    ADD CONSTRAINT repository_pkey PRIMARY KEY ("repositoryId");


--
-- TOC entry 2403 (class 2606 OID 602917)
-- Name: repository repository_repositoryReference_key; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".repository
    ADD CONSTRAINT "repository_repositoryReference_key" UNIQUE ("repositoryReference");


--
-- TOC entry 2405 (class 2606 OID 602919)
-- Name: repository repository_repositoryUri_key; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".repository
    ADD CONSTRAINT "repository_repositoryUri_key" UNIQUE ("repositoryUri");


--
-- TOC entry 2420 (class 2606 OID 603016)
-- Name: folder filePlan_name_parentFolderId_key; Type: CONSTRAINT; Schema: filePlan; Owner: maarch
--

ALTER TABLE ONLY "filePlan".folder
    ADD CONSTRAINT "filePlan_name_parentFolderId_key" UNIQUE (name, "parentFolderId");


--
-- TOC entry 2422 (class 2606 OID 603014)
-- Name: folder folder_pkey; Type: CONSTRAINT; Schema: filePlan; Owner: maarch
--

ALTER TABLE ONLY "filePlan".folder
    ADD CONSTRAINT folder_pkey PRIMARY KEY ("folderId");


--
-- TOC entry 2432 (class 2606 OID 603051)
-- Name: eventFormat eventFormat_pkey; Type: CONSTRAINT; Schema: lifeCycle; Owner: maarch
--

ALTER TABLE ONLY "lifeCycle"."eventFormat"
    ADD CONSTRAINT "eventFormat_pkey" PRIMARY KEY (type);


--
-- TOC entry 2425 (class 2606 OID 603042)
-- Name: event event_pkey; Type: CONSTRAINT; Schema: lifeCycle; Owner: maarch
--

ALTER TABLE ONLY "lifeCycle".event
    ADD CONSTRAINT event_pkey PRIMARY KEY ("eventId");


--
-- TOC entry 2331 (class 2606 OID 602654)
-- Name: archivalAgreement archivalAgreement_pkey; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."archivalAgreement"
    ADD CONSTRAINT "archivalAgreement_pkey" PRIMARY KEY ("archivalAgreementId");


--
-- TOC entry 2333 (class 2606 OID 602656)
-- Name: archivalAgreement archivalAgreement_reference_key; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."archivalAgreement"
    ADD CONSTRAINT "archivalAgreement_reference_key" UNIQUE (reference);


--
-- TOC entry 2350 (class 2606 OID 602702)
-- Name: controlAuthority controlAuthority_pkey; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."controlAuthority"
    ADD CONSTRAINT "controlAuthority_pkey" PRIMARY KEY ("originatorOrgUnitId");


--
-- TOC entry 2342 (class 2606 OID 602676)
-- Name: messageComment messageComment_messageId_comment_key; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."messageComment"
    ADD CONSTRAINT "messageComment_messageId_comment_key" UNIQUE ("messageId", comment);


--
-- TOC entry 2344 (class 2606 OID 602674)
-- Name: messageComment messageComment_pkey; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."messageComment"
    ADD CONSTRAINT "messageComment_pkey" PRIMARY KEY ("commentId");


--
-- TOC entry 2338 (class 2606 OID 602664)
-- Name: message message_pkey; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona.message
    ADD CONSTRAINT message_pkey PRIMARY KEY ("messageId");


--
-- TOC entry 2340 (class 2606 OID 602666)
-- Name: message message_type_reference_senderOrgRegNumber_key; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona.message
    ADD CONSTRAINT "message_type_reference_senderOrgRegNumber_key" UNIQUE (type, reference, "senderOrgRegNumber");


--
-- TOC entry 2348 (class 2606 OID 602689)
-- Name: unitIdentifier unitIdentifier_messageId_objectClass_objectId_key; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."unitIdentifier"
    ADD CONSTRAINT "unitIdentifier_messageId_objectClass_objectId_key" UNIQUE ("messageId", "objectClass", "objectId");


--
-- TOC entry 2448 (class 2606 OID 603136)
-- Name: archivalProfileAccess archivalProfileAccess_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."archivalProfileAccess"
    ADD CONSTRAINT "archivalProfileAccess_pkey" PRIMARY KEY ("orgId", "archivalProfileReference");


--
-- TOC entry 2446 (class 2606 OID 603122)
-- Name: orgContact orgContact_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."orgContact"
    ADD CONSTRAINT "orgContact_pkey" PRIMARY KEY ("contactId", "orgId");


--
-- TOC entry 2434 (class 2606 OID 603066)
-- Name: orgType orgType_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."orgType"
    ADD CONSTRAINT "orgType_pkey" PRIMARY KEY (code);


--
-- TOC entry 2436 (class 2606 OID 603074)
-- Name: organization organization_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT organization_pkey PRIMARY KEY ("orgId");


--
-- TOC entry 2438 (class 2606 OID 603076)
-- Name: organization organization_registrationNumber_key; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT "organization_registrationNumber_key" UNIQUE ("registrationNumber");


--
-- TOC entry 2440 (class 2606 OID 603078)
-- Name: organization organization_taxIdentifier_key; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT "organization_taxIdentifier_key" UNIQUE ("taxIdentifier");


--
-- TOC entry 2444 (class 2606 OID 603114)
-- Name: servicePosition servicePosition_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."servicePosition"
    ADD CONSTRAINT "servicePosition_pkey" PRIMARY KEY ("serviceAccountId", "orgId");


--
-- TOC entry 2442 (class 2606 OID 603101)
-- Name: userPosition userPosition_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."userPosition"
    ADD CONSTRAINT "userPosition_pkey" PRIMARY KEY ("userAccountId", "orgId");


--
-- TOC entry 2352 (class 2606 OID 602716)
-- Name: accessRule accessRule_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."accessRule"
    ADD CONSTRAINT "accessRule_pkey" PRIMARY KEY (code);


--
-- TOC entry 2360 (class 2606 OID 602754)
-- Name: archivalProfileContents archivalProfileContents_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfileContents"
    ADD CONSTRAINT "archivalProfileContents_pkey" PRIMARY KEY ("parentProfileId", "containedProfileId");


--
-- TOC entry 2356 (class 2606 OID 602734)
-- Name: archivalProfile archivalProfile_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfile"
    ADD CONSTRAINT "archivalProfile_pkey" PRIMARY KEY ("archivalProfileId");


--
-- TOC entry 2358 (class 2606 OID 602736)
-- Name: archivalProfile archivalProfile_reference_key; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfile"
    ADD CONSTRAINT "archivalProfile_reference_key" UNIQUE (reference);


--
-- TOC entry 2364 (class 2606 OID 602784)
-- Name: archiveDescription archiveDescription_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archiveDescription"
    ADD CONSTRAINT "archiveDescription_pkey" PRIMARY KEY ("archivalProfileId", "fieldName");


--
-- TOC entry 2385 (class 2606 OID 602832)
-- Name: archiveRelationship archiveRelationship_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archiveRelationship"
    ADD CONSTRAINT "archiveRelationship_pkey" PRIMARY KEY ("archiveId", "relatedArchiveId", "typeCode");


--
-- TOC entry 2374 (class 2606 OID 602808)
-- Name: archive archive_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement".archive
    ADD CONSTRAINT archive_pkey PRIMARY KEY ("archiveId");


--
-- TOC entry 2391 (class 2606 OID 602858)
-- Name: descriptionClass descriptionClass_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."descriptionClass"
    ADD CONSTRAINT "descriptionClass_pkey" PRIMARY KEY (name);


--
-- TOC entry 2362 (class 2606 OID 602773)
-- Name: descriptionField descriptionField_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."descriptionField"
    ADD CONSTRAINT "descriptionField_pkey" PRIMARY KEY (name);


--
-- TOC entry 2389 (class 2606 OID 602850)
-- Name: log log_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement".log
    ADD CONSTRAINT log_pkey PRIMARY KEY ("archiveId");


--
-- TOC entry 2354 (class 2606 OID 602724)
-- Name: retentionRule retentionRule_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."retentionRule"
    ADD CONSTRAINT "retentionRule_pkey" PRIMARY KEY (code);


--
-- TOC entry 2366 (class 2606 OID 602797)
-- Name: serviceLevel serviceLevel_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."serviceLevel"
    ADD CONSTRAINT "serviceLevel_pkey" PRIMARY KEY ("serviceLevelId");


--
-- TOC entry 2368 (class 2606 OID 602799)
-- Name: serviceLevel serviceLevel_reference_key; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."serviceLevel"
    ADD CONSTRAINT "serviceLevel_reference_key" UNIQUE (reference);


--
-- TOC entry 2393 (class 2606 OID 602866)
-- Name: storageRule storageRule_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."storageRule"
    ADD CONSTRAINT "storageRule_pkey" PRIMARY KEY (code);


--
-- TOC entry 2293 (class 1259 OID 602495)
-- Name: audit_event_eventDate_idx; Type: INDEX; Schema: audit; Owner: maarch
--

CREATE INDEX "audit_event_eventDate_idx" ON audit.event USING btree ("eventDate");


--
-- TOC entry 2294 (class 1259 OID 602494)
-- Name: audit_event_instanceName_idx; Type: INDEX; Schema: audit; Owner: maarch
--

CREATE INDEX "audit_event_instanceName_idx" ON audit.event USING btree ("instanceName");


--
-- TOC entry 2311 (class 1259 OID 602590)
-- Name: batchProcessing_logScheduling_schedulingId_idx; Type: INDEX; Schema: batchProcessing; Owner: maarch
--

CREATE INDEX "batchProcessing_logScheduling_schedulingId_idx" ON "batchProcessing"."logScheduling" USING btree ("schedulingId");


--
-- TOC entry 2396 (class 1259 OID 603004)
-- Name: digitalResource_digitalResource_archiveId_idx; Type: INDEX; Schema: digitalResource; Owner: maarch
--

CREATE INDEX "digitalResource_digitalResource_archiveId_idx" ON "digitalResource"."digitalResource" USING btree ("archiveId");


--
-- TOC entry 2397 (class 1259 OID 603005)
-- Name: digitalResource_digitalResource_relatedResId__relationshipType_; Type: INDEX; Schema: digitalResource; Owner: maarch
--

CREATE INDEX "digitalResource_digitalResource_relatedResId__relationshipType_" ON "digitalResource"."digitalResource" USING btree ("relatedResId", "relationshipType");


--
-- TOC entry 2418 (class 1259 OID 603033)
-- Name: filePlan_folder_ownerOrgRegNumber_idx; Type: INDEX; Schema: filePlan; Owner: maarch
--

CREATE INDEX "filePlan_folder_ownerOrgRegNumber_idx" ON "filePlan".folder USING btree ("ownerOrgRegNumber");


--
-- TOC entry 2423 (class 1259 OID 603052)
-- Name: event_objectId_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX "event_objectId_idx" ON "lifeCycle".event USING btree ("objectId");


--
-- TOC entry 2426 (class 1259 OID 603053)
-- Name: event_timestamp_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX event_timestamp_idx ON "lifeCycle".event USING btree ("timestamp");


--
-- TOC entry 2427 (class 1259 OID 603056)
-- Name: lifeCycle_event_eventType_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX "lifeCycle_event_eventType_idx" ON "lifeCycle".event USING btree ("eventType");


--
-- TOC entry 2428 (class 1259 OID 603055)
-- Name: lifeCycle_event_instanceName_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX "lifeCycle_event_instanceName_idx" ON "lifeCycle".event USING btree ("instanceName");


--
-- TOC entry 2429 (class 1259 OID 603054)
-- Name: lifeCycle_event_objectClass_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX "lifeCycle_event_objectClass_idx" ON "lifeCycle".event USING btree ("objectClass");


--
-- TOC entry 2430 (class 1259 OID 603057)
-- Name: lifeCycle_event_objectClass_objectId_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX "lifeCycle_event_objectClass_objectId_idx" ON "lifeCycle".event USING btree ("objectClass", "objectId");


--
-- TOC entry 2334 (class 1259 OID 602704)
-- Name: medona_message_date_idx; Type: INDEX; Schema: medona; Owner: maarch
--

CREATE INDEX medona_message_date_idx ON medona.message USING btree (date);


--
-- TOC entry 2335 (class 1259 OID 602703)
-- Name: medona_message_recipientOrgRegNumber_idx; Type: INDEX; Schema: medona; Owner: maarch
--

CREATE INDEX "medona_message_recipientOrgRegNumber_idx" ON medona.message USING btree ("recipientOrgRegNumber");


--
-- TOC entry 2336 (class 1259 OID 602705)
-- Name: medona_message_status_active_idx; Type: INDEX; Schema: medona; Owner: maarch
--

CREATE INDEX medona_message_status_active_idx ON medona.message USING btree (status, active);


--
-- TOC entry 2345 (class 1259 OID 602706)
-- Name: medona_unitIdentifier_messageId_idx; Type: INDEX; Schema: medona; Owner: maarch
--

CREATE INDEX "medona_unitIdentifier_messageId_idx" ON medona."unitIdentifier" USING btree ("messageId");


--
-- TOC entry 2346 (class 1259 OID 602707)
-- Name: medona_unitIdentifier_objectClass_idx; Type: INDEX; Schema: medona; Owner: maarch
--

CREATE INDEX "medona_unitIdentifier_objectClass_idx" ON medona."unitIdentifier" USING btree ("objectClass", "objectId");


--
-- TOC entry 2369 (class 1259 OID 602820)
-- Name: archive_archivalProfileReference_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "archive_archivalProfileReference_idx" ON "recordsManagement".archive USING btree ("archivalProfileReference");


--
-- TOC entry 2370 (class 1259 OID 602823)
-- Name: archive_disposalDate_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "archive_disposalDate_idx" ON "recordsManagement".archive USING btree ("disposalDate");


--
-- TOC entry 2371 (class 1259 OID 602819)
-- Name: archive_filePlanPosition_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "archive_filePlanPosition_idx" ON "recordsManagement".archive USING btree ("filePlanPosition");


--
-- TOC entry 2372 (class 1259 OID 602822)
-- Name: archive_originatorOrgRegNumber_originatorArchiveId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "archive_originatorOrgRegNumber_originatorArchiveId_idx" ON "recordsManagement".archive USING btree ("originatorOrgRegNumber", "originatorArchiveId");


--
-- TOC entry 2375 (class 1259 OID 602821)
-- Name: archive_status_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX archive_status_idx ON "recordsManagement".archive USING btree (status);


--
-- TOC entry 2376 (class 1259 OID 602824)
-- Name: archive_to_tsvector_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX archive_to_tsvector_idx ON "recordsManagement".archive USING gin (to_tsvector('french'::regconfig, translate(text, 'ÀÁÂÃÄÅàáâãäåÆæÞþČčĆćÇçĐđÈÉÊËèéêëÌÍÎÏìíîïÑñÒÓÔÕÖØðòóôõöøœŒŔŕŠšßÙÚÛÜùúûÝýÿŽž'::text, 'AAAAAAaaaaaaAEaeBbCcCcCcDjdjEEEEeeeeIIIIiiiiNnOOOOOOooooooooeOERrSsSsUUUUuuuYyyZz'::text)));


--
-- TOC entry 2386 (class 1259 OID 602874)
-- Name: recordsManagement_archiveRelationship_archiveId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archiveRelationship_archiveId_idx" ON "recordsManagement"."archiveRelationship" USING btree ("archiveId");


--
-- TOC entry 2387 (class 1259 OID 602875)
-- Name: recordsManagement_archiveRelationship_relatedArchiveId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archiveRelationship_relatedArchiveId_idx" ON "recordsManagement"."archiveRelationship" USING btree ("relatedArchiveId");


--
-- TOC entry 2377 (class 1259 OID 602871)
-- Name: recordsManagement_archive_archiverOrgRegNumber_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_archiverOrgRegNumber_idx" ON "recordsManagement".archive USING btree ("archiverOrgRegNumber");


--
-- TOC entry 2378 (class 1259 OID 602869)
-- Name: recordsManagement_archive_descriptionClass_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_descriptionClass_idx" ON "recordsManagement".archive USING btree ("descriptionClass");


--
-- TOC entry 2379 (class 1259 OID 602867)
-- Name: recordsManagement_archive_originatingDate_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_originatingDate_idx" ON "recordsManagement".archive USING btree ("originatingDate");


--
-- TOC entry 2380 (class 1259 OID 602873)
-- Name: recordsManagement_archive_originatorArchiveId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_originatorArchiveId_idx" ON "recordsManagement".archive USING btree ("originatorArchiveId");


--
-- TOC entry 2381 (class 1259 OID 602872)
-- Name: recordsManagement_archive_originatorOrgRegNumber_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_originatorOrgRegNumber_idx" ON "recordsManagement".archive USING btree ("originatorOrgRegNumber");


--
-- TOC entry 2382 (class 1259 OID 602870)
-- Name: recordsManagement_archive_originatorOwnerOrgId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_originatorOwnerOrgId_idx" ON "recordsManagement".archive USING btree ("originatorOwnerOrgId");


--
-- TOC entry 2383 (class 1259 OID 602868)
-- Name: recordsManagement_archive_parentArchiveId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_parentArchiveId_idx" ON "recordsManagement".archive USING btree ("parentArchiveId");


--
-- TOC entry 2451 (class 2606 OID 602546)
-- Name: privilege privilege_roleId_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth.privilege
    ADD CONSTRAINT "privilege_roleId_fkey" FOREIGN KEY ("roleId") REFERENCES auth.role("roleId");


--
-- TOC entry 2449 (class 2606 OID 602528)
-- Name: roleMember roleMember_roleId_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth."roleMember"
    ADD CONSTRAINT "roleMember_roleId_fkey" FOREIGN KEY ("roleId") REFERENCES auth.role("roleId");


--
-- TOC entry 2450 (class 2606 OID 602533)
-- Name: roleMember roleMember_userAccountId_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth."roleMember"
    ADD CONSTRAINT "roleMember_userAccountId_fkey" FOREIGN KEY ("userAccountId") REFERENCES auth.account("accountId");


--
-- TOC entry 2452 (class 2606 OID 602559)
-- Name: servicePrivilege servicePrivilege_accountId_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth."servicePrivilege"
    ADD CONSTRAINT "servicePrivilege_accountId_fkey" FOREIGN KEY ("accountId") REFERENCES auth.account("accountId");


--
-- TOC entry 2453 (class 2606 OID 602611)
-- Name: address address_contactId_fkey; Type: FK CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.address
    ADD CONSTRAINT "address_contactId_fkey" FOREIGN KEY ("contactId") REFERENCES contact.contact("contactId");


--
-- TOC entry 2455 (class 2606 OID 602641)
-- Name: communication communication_comMeanCode_fkey; Type: FK CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.communication
    ADD CONSTRAINT "communication_comMeanCode_fkey" FOREIGN KEY ("comMeanCode") REFERENCES contact."communicationMean"(code);


--
-- TOC entry 2454 (class 2606 OID 602636)
-- Name: communication communication_contactId_fkey; Type: FK CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.communication
    ADD CONSTRAINT "communication_contactId_fkey" FOREIGN KEY ("contactId") REFERENCES contact.contact("contactId");


--
-- TOC entry 2471 (class 2606 OID 602934)
-- Name: address address_repositoryId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".address
    ADD CONSTRAINT "address_repositoryId_fkey" FOREIGN KEY ("repositoryId") REFERENCES "digitalResource".repository("repositoryId");


--
-- TOC entry 2470 (class 2606 OID 602929)
-- Name: address address_resId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".address
    ADD CONSTRAINT "address_resId_fkey" FOREIGN KEY ("resId") REFERENCES "digitalResource"."digitalResource"("resId");


--
-- TOC entry 2472 (class 2606 OID 602947)
-- Name: clusterRepository clusterRepository_clusterId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."clusterRepository"
    ADD CONSTRAINT "clusterRepository_clusterId_fkey" FOREIGN KEY ("clusterId") REFERENCES "digitalResource".cluster("clusterId");


--
-- TOC entry 2473 (class 2606 OID 602952)
-- Name: clusterRepository clusterRepository_repositoryId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."clusterRepository"
    ADD CONSTRAINT "clusterRepository_repositoryId_fkey" FOREIGN KEY ("repositoryId") REFERENCES "digitalResource".repository("repositoryId");


--
-- TOC entry 2467 (class 2606 OID 602893)
-- Name: digitalResource digitalResource_archiveId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."digitalResource"
    ADD CONSTRAINT "digitalResource_archiveId_fkey" FOREIGN KEY ("archiveId") REFERENCES "recordsManagement".archive("archiveId");


--
-- TOC entry 2468 (class 2606 OID 602898)
-- Name: digitalResource digitalResource_clusterId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."digitalResource"
    ADD CONSTRAINT "digitalResource_clusterId_fkey" FOREIGN KEY ("clusterId") REFERENCES "digitalResource".cluster("clusterId");


--
-- TOC entry 2469 (class 2606 OID 602903)
-- Name: digitalResource digitalResource_relatedResId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."digitalResource"
    ADD CONSTRAINT "digitalResource_relatedResId_fkey" FOREIGN KEY ("relatedResId") REFERENCES "digitalResource"."digitalResource"("resId");


--
-- TOC entry 2474 (class 2606 OID 602965)
-- Name: package package_packageId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".package
    ADD CONSTRAINT "package_packageId_fkey" FOREIGN KEY ("packageId") REFERENCES "digitalResource"."digitalResource"("resId");


--
-- TOC entry 2475 (class 2606 OID 602976)
-- Name: packedResource packedResource_packageId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."packedResource"
    ADD CONSTRAINT "packedResource_packageId_fkey" FOREIGN KEY ("packageId") REFERENCES "digitalResource".package("packageId");


--
-- TOC entry 2476 (class 2606 OID 602981)
-- Name: packedResource packedResource_resId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."packedResource"
    ADD CONSTRAINT "packedResource_resId_fkey" FOREIGN KEY ("resId") REFERENCES "digitalResource"."digitalResource"("resId");


--
-- TOC entry 2477 (class 2606 OID 603017)
-- Name: folder folderId_filePlan_fkey; Type: FK CONSTRAINT; Schema: filePlan; Owner: maarch
--

ALTER TABLE ONLY "filePlan".folder
    ADD CONSTRAINT "folderId_filePlan_fkey" FOREIGN KEY ("parentFolderId") REFERENCES "filePlan".folder("folderId");


--
-- TOC entry 2478 (class 2606 OID 603028)
-- Name: position position_filePlan_fkey; Type: FK CONSTRAINT; Schema: filePlan; Owner: maarch
--

ALTER TABLE ONLY "filePlan"."position"
    ADD CONSTRAINT "position_filePlan_fkey" FOREIGN KEY ("folderId") REFERENCES "filePlan".folder("folderId");


--
-- TOC entry 2456 (class 2606 OID 602677)
-- Name: messageComment messageComment_messageId_fkey; Type: FK CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."messageComment"
    ADD CONSTRAINT "messageComment_messageId_fkey" FOREIGN KEY ("messageId") REFERENCES medona.message("messageId");


--
-- TOC entry 2457 (class 2606 OID 602690)
-- Name: unitIdentifier unitIdentifier_messageId_fkey; Type: FK CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."unitIdentifier"
    ADD CONSTRAINT "unitIdentifier_messageId_fkey" FOREIGN KEY ("messageId") REFERENCES medona.message("messageId");


--
-- TOC entry 2484 (class 2606 OID 603137)
-- Name: archivalProfileAccess archivalProfileAccess_orgId_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."archivalProfileAccess"
    ADD CONSTRAINT "archivalProfileAccess_orgId_fkey" FOREIGN KEY ("orgId") REFERENCES organization.organization("orgId");


--
-- TOC entry 2483 (class 2606 OID 603123)
-- Name: orgContact orgContact_orgId_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."orgContact"
    ADD CONSTRAINT "orgContact_orgId_fkey" FOREIGN KEY ("orgId") REFERENCES organization.organization("orgId");


--
-- TOC entry 2479 (class 2606 OID 603079)
-- Name: organization organization_orgTypeCode_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT "organization_orgTypeCode_fkey" FOREIGN KEY ("orgTypeCode") REFERENCES organization."orgType"(code);


--
-- TOC entry 2481 (class 2606 OID 603089)
-- Name: organization organization_ownerOrgId_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT "organization_ownerOrgId_fkey" FOREIGN KEY ("ownerOrgId") REFERENCES organization.organization("orgId");


--
-- TOC entry 2480 (class 2606 OID 603084)
-- Name: organization organization_parentOrgId_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT "organization_parentOrgId_fkey" FOREIGN KEY ("parentOrgId") REFERENCES organization.organization("orgId");


--
-- TOC entry 2482 (class 2606 OID 603102)
-- Name: userPosition userPosition_orgId_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."userPosition"
    ADD CONSTRAINT "userPosition_orgId_fkey" FOREIGN KEY ("orgId") REFERENCES organization.organization("orgId");


--
-- TOC entry 2461 (class 2606 OID 602760)
-- Name: archivalProfileContents archivalProfileContents_containedProfileId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfileContents"
    ADD CONSTRAINT "archivalProfileContents_containedProfileId_fkey" FOREIGN KEY ("containedProfileId") REFERENCES "recordsManagement"."archivalProfile"("archivalProfileId");


--
-- TOC entry 2460 (class 2606 OID 602755)
-- Name: archivalProfileContents archivalProfileContents_parentProfileId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfileContents"
    ADD CONSTRAINT "archivalProfileContents_parentProfileId_fkey" FOREIGN KEY ("parentProfileId") REFERENCES "recordsManagement"."archivalProfile"("archivalProfileId");


--
-- TOC entry 2458 (class 2606 OID 602737)
-- Name: archivalProfile archivalProfile_accessRuleCode_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfile"
    ADD CONSTRAINT "archivalProfile_accessRuleCode_fkey" FOREIGN KEY ("accessRuleCode") REFERENCES "recordsManagement"."accessRule"(code);


--
-- TOC entry 2459 (class 2606 OID 602742)
-- Name: archivalProfile archivalProfile_retentionRuleCode_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfile"
    ADD CONSTRAINT "archivalProfile_retentionRuleCode_fkey" FOREIGN KEY ("retentionRuleCode") REFERENCES "recordsManagement"."retentionRule"(code);


--
-- TOC entry 2462 (class 2606 OID 602785)
-- Name: archiveDescription archiveDescription_archivalProfileId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archiveDescription"
    ADD CONSTRAINT "archiveDescription_archivalProfileId_fkey" FOREIGN KEY ("archivalProfileId") REFERENCES "recordsManagement"."archivalProfile"("archivalProfileId");


--
-- TOC entry 2465 (class 2606 OID 602833)
-- Name: archiveRelationship archiveRelationship_archiveId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archiveRelationship"
    ADD CONSTRAINT "archiveRelationship_archiveId_fkey" FOREIGN KEY ("archiveId") REFERENCES "recordsManagement".archive("archiveId");


--
-- TOC entry 2466 (class 2606 OID 602838)
-- Name: archiveRelationship archiveRelationship_relatedArchiveId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archiveRelationship"
    ADD CONSTRAINT "archiveRelationship_relatedArchiveId_fkey" FOREIGN KEY ("relatedArchiveId") REFERENCES "recordsManagement".archive("archiveId");


--
-- TOC entry 2464 (class 2606 OID 602814)
-- Name: archive archive_accessRuleCode_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement".archive
    ADD CONSTRAINT "archive_accessRuleCode_fkey" FOREIGN KEY ("accessRuleCode") REFERENCES "recordsManagement"."accessRule"(code);


--
-- TOC entry 2463 (class 2606 OID 602809)
-- Name: archive archive_parentArchiveId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement".archive
    ADD CONSTRAINT "archive_parentArchiveId_fkey" FOREIGN KEY ("parentArchiveId") REFERENCES "recordsManagement".archive("archiveId");


-- Completed on 2019-12-11 11:13:11 CET

--
-- PostgreSQL database dump complete
--
