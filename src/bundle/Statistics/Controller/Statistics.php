<?php

/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\Statistics\Controller;

/**
 * Managemet of the access rule of an archive
 *
 * @author Jérôme Boucher <jerome.boucher@maarch.org>
 */
class Statistics
{
    public $sdoFactory;
    protected $minDate;
    protected $maxDate;
    protected $sizeFilter;
    protected $sizeCategories;
    protected $translator;

    /**
     * Constructor of access control class
     *
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->sdoFactory = $sdoFactory;
        $this->pdo = $sdoFactory->das->pdo;
        $this->sizeCategories = ["Octets", "Ko", "Mo", "Go"];
        $this->translator = $translator;
        $this->translator->setCatalog("Statistics/Statistics");
    }

    /**
     * Retrieve default stats for screen
     *
     * @return array Associative array of properties
     */
    public function index()
    {
        return $this->retrieve(null, null, null, null, 3);
    }

    /**
     * Retrieve filtered stats
     *
     * @param  string $operation  Type of operation to count or sum
     * @param  string $startDate  Start date
     * @param  string $endDate    End date
     * @param  string $filter     Filtering parameter to group query by
     * @param  float  $sizeFilter Power of 10 to divide by
     *
     * @return array              Associative array of properties
     */
    public function retrieve($operation = null, $startDate = null, $endDate = null, $filter = null, $sizeFilter = 0)
    {
        if (!empty($startDate)) {
            $startDate = \laabs::newDateTime($startDate);
        } else {
            $startDate = null;
        }
        if (!empty($endDate)) {
            $endDate = \laabs::newDateTime($endDate);
        } else {
            $endDate = null;
        }

        if (!is_null($startDate) && is_null($endDate)) {
            throw new \core\Exception\BadRequestException($this->translator->getText("End Date is mandatory if start date is filled"));
        } elseif (is_null($startDate) && !is_null($endDate)) {
            throw new \core\Exception\BadRequestException($this->translator->getText("Start Date is mandatory if end date is filled"));
        }

        if ($startDate == $endDate && !is_null($startDate)) {
            $endDate->setTime(23, 59, 59);
        } elseif ($startDate > $endDate) {
            throw new \core\Exception\BadRequestException($this->translator->getText("Start Date cannot be past end date"));
        }

        if (!empty($startDate)) {
            $startDate = (string) $startDate->format('Y-m-d 00:00:00');
            $endDate = (string) $endDate->format('Y-m-d 23:59:59');
        }

        if ($sizeFilter > 3 || $sizeFilter < 0) {
            throw new \core\Exception\BadRequestException($this->translator->getText("The sizeFilter parameter must be between 0 and 3 included"));
        }

        $this->sizeFilter = $sizeFilter;

        $statistics = ["unit" => $this->sizeCategories[$this->sizeFilter]];
        if (is_null($operation) || empty($operation)) {
            $statistics = $this->defaultStats($startDate, $endDate, $statistics);
        } elseif (!empty($operation) && !in_array($operation, ['deposit', 'deleted', 'conserved', 'restituted', 'transfered', 'communicated'])) {
            throw new \core\Exception\BadRequestException($this->translator->getText("Operation type not supported"));
        } elseif (!empty($operation) && is_null($filter)) {
            throw new \core\Exception\BadRequestException($this->translator->getText("Filter cannot be null if an operation is selected"));
        }

        if (!empty($operation)) {
            switch ($operation) {
                case 'deposit':
                    $statistics = $this->depositStats($startDate, $endDate, $filter, $statistics);
                    break;
                case 'deleted':
                    $statistics = $this->deletedStats($startDate, $endDate, $filter, $statistics);
                    break;
                case 'conserved':
                    $statistics = $this->conservedStats($endDate, $filter, $statistics);
                    break;
                case 'restituted':
                    $statistics = $this->restitutedStats($startDate, $endDate, $filter, $statistics);
                    break;
                case 'transfered':
                    $statistics = $this->transferedStats($startDate, $endDate, $filter, $statistics);
                    break;
                case 'communicated':
                    $statistics = $this->communicatedStats($startDate, $endDate, $filter, $statistics);
                    break;
            }
        }

        return $statistics;
    }

    /**
     * Basics stats to return for homepage
     *
     * @param  datetime $startDate  Starting Date
     * @param  datetime $endDate    End date
     * @param  array    $statistics Array of statistics
     *
     * @return array                Associative array of statistics
     */
    protected function defaultStats($startDate, $endDate, $statistics = [])
    {
        $statistics['depositMemorySize'] = floatval(str_replace(" ", "", $this->getSizeByEventType('ArchiveTransfer', ['recordsManagement/deposit', 'recordsManagement/depositNewResource'], $jsonColumnNumber = 8, $startDate, $endDate, true)))
                                        + floatval(str_replace(" ", "", $this->getSizeForDirectEvent('recordsManagement/deposit', 8, null, $startDate, $endDate)));
        $statistics['depositMemorySize'] = $this->formatSize($statistics['depositMemorySize'], false);
        $statistics['depositMemoryCount'] = $this->getCountByEventType('ArchiveTransfer', $startDate, $endDate, true)
                                        + $this->getCountForDirectEvent('recordsManagement/deposit', null, $startDate, $endDate);

        $statistics['deletedMemorySize'] = floatval(str_replace(" ", "", $this->getSizeByEventType('ArchiveDestructionRequest', ['recordsManagement/destruction'], $jsonColumnNumber = 6, $startDate, $endDate)))
                                        + floatval(str_replace(" ", "", $this->getSizeForDirectEvent('recordsManagement/destruction', 6, null, $startDate, $endDate)));
        $statistics['deletedMemorySize'] = $this->formatSize($statistics['deletedMemorySize'], false);
        $statistics['deletedMemoryCount'] = $this->getCountByEventType('ArchiveDestructionRequest', $startDate, $endDate)
                                        + $this->getCountForDirectEvent('recordsManagement/destruction', null, $startDate, $endDate);

        if (\laabs::configuration('medona')['transaction']) {
            $statistics['transferedMemorySize'] = $this->getSizeByEventType('ArchiveTransfer', ['recordsManagement/outgoingTransfer'], $jsonColumnNumber = 6, $startDate, $endDate);
            $statistics['transferedMemoryCount'] = $this->getCountByEventType('ArchiveTransfer', $startDate, $endDate);
            $statistics['restitutionMemorySize'] = $this->getSizeByEventType('ArchiveRestitutionRequest', ['recordsManagement/restitution'], $jsonColumnNumber = 6, $startDate, $endDate);
            $statistics['restitutionMemoryCount'] = $this->getCountByEventType('ArchiveRestitutionRequest', $startDate, $endDate);
            $statistics['communicatedMemorySize'] = $this->getSizeByEventType('ArchiveDeliveryRequest', ['recordsManagement/delivery'], $jsonColumnNumber = 6, $startDate, $endDate);
            $statistics['communicatedMemoryCount'] = $this->getCountByEventType('ArchiveDeliveryRequest', $startDate, $endDate);
        }

        $statistics['currentMemorySize'] = $this->getArchiveSize($endDate);
        $statistics['currentMemoryCount'] = $this->getArchiveCount($endDate);

        if ($startDate) {
            $statistics['evolutionSize'] = $statistics['currentMemorySize'] - $this->getArchiveSize($startDate);
            $statistics['evolutionCount'] = $statistics['currentMemoryCount'] - $this->getArchiveCount($startDate);
            if ($statistics['evolutionSize'] != (integer)$statistics['evolutionSize']) {
                $statistics['evolutionSize'] = number_format($statistics['evolutionSize'], 3, ".", " ");
            }
        }

        if ($statistics['currentMemorySize'] != (integer)$statistics['currentMemorySize']) {
            $statistics['currentMemorySize'] = number_format($statistics['currentMemorySize'], 3, ".", " ");
        }

        return $statistics;
    }

    protected function addDirectStats($stats, $directStats, $filter, $resultType)
    {
        foreach ($directStats as $groupBy => $result) {
            $groupByFound = false;
            for ($i = 0; $i < count($stats); $i++) {
                if ($stats[$i][$filter] == $groupBy) {
                    if ($resultType == 'sum') {
                        $result1 = floatval(str_replace(" ", "", $stats[$i][$resultType]));
                        $result2 = floatval(str_replace(" ", "", $result));
                    }
                    $stats[$i][$resultType] = $this->formatSize($resultType == 'sum' ? $result1 + $result2 : $stats[$i][$resultType] + $result);
                    $groupByFound = true;
                    break;
                }
            }
            if (!$groupByFound) {
                $stats[] = [$filter => $groupBy, $resultType => $result];
            }
        }
        return $stats;
    }

    /**
     * Statistics aggregator for deposit event
     *
     * @param  datetime $startDate starting date
     * @param  datetime $endDate   End date
     * @param  string   $filter    Group by argument
     * @param  array    $statistics Array of statistics
     *
     * @return array               Associative of statistics
     */
    protected function depositStats($startDate, $endDate, $filter, $statistics = [])
    {
        $statistics['groupedDepositMemorySize'] = $this->getSizeByEventTypeOrdered('ArchiveTransfer', ['recordsManagement/deposit', 'recordsManagement/depositNewResource'], 8, $startDate, $endDate, $filter, true);
        $directArchiveTransferStatsSize = $this->getSizeForDirectEvent('recordsManagement/deposit', 8, $filter, $startDate, $endDate);
        $statistics['groupedDepositMemorySize'] = $this->addDirectStats($statistics['groupedDepositMemorySize'], $directArchiveTransferStatsSize, strtolower($filter), 'sum');

        $statistics['groupedDepositMemoryCount'] = $this->getCountByEventTypeOrdered('ArchiveTransfer', $startDate, $endDate, $filter, true);
        $directArchiveTransferStatsCount = $this->getCountForDirectEvent('recordsManagement/deposit', $filter, $startDate, $endDate);
        $statistics['groupedDepositMemoryCount'] = $this->addDirectStats($statistics['groupedDepositMemoryCount'], $directArchiveTransferStatsCount, strtolower($filter), 'count');

        return $statistics;
    }

    /**
     * Statistics aggregator for deleted event
     *
     * @param  datetime $startDate starting date
     * @param  datetime $endDate   End date
     * @param  string   $filter    Group by argument
     * @param  array    $statistics Array of statistics
     *
     * @return array               Associative of statistics
     */
    protected function deletedStats($startDate, $endDate, $filter, $statistics = [])
    {
        $statistics['deletedGroupedMemorySize'] = $this->getSizeByEventTypeOrdered('ArchiveDestructionRequest', ['recordsManagement/destruction', 'recordsManagement/elimination'], 6, $startDate, $endDate, $filter);
        $directDeletedStatsSize = $this->getSizeForDirectEvent('recordsManagement/destruction', 6, $filter, $startDate, $endDate);
        $statistics['deletedGroupedMemorySize'] = $this->addDirectStats($statistics['deletedGroupedMemorySize'], $directDeletedStatsSize, strtolower($filter), 'sum');

        $statistics['deletedGroupedMemoryCount'] = $this->getCountByEventTypeOrdered('ArchiveDestructionRequest', $startDate, $endDate, $filter);
        $directDeletedStatsCount = $this->getCountForDirectEvent('recordsManagement/destruction', $filter, $startDate, $endDate);
        $statistics['deletedGroupedMemoryCount'] = $this->addDirectStats($statistics['deletedGroupedMemoryCount'], $directDeletedStatsCount, strtolower($filter), 'count');

        return $statistics;
    }

    /**
     * Statistics aggregator for conserved archive
     *
     * @param  datetime $endDate    End date
     * @param  string   $filter     Group by argument
     * @param  array    $statistics Array of statistics
     *
     * @return array                Associative of statistics
     */
    protected function conservedStats($endDate, $filter, $statistics = [])
    {
        $statistics['groupedArchiveSize'] = $this->getArchiveSizeOrdered($filter, $endDate);
        $statistics['groupedArchiveCount'] = $this->getArchiveCountOrdered($filter, $endDate);

        return $statistics;
    }

    /**
     * Statistics aggregator for restituted event
     *
     * @param  datetime $startDate  starting date
     * @param  datetime $endDate    End date
     * @param  string   $filter     Group by argument
     * @param  array    $statistics Array of statistics
     *
     * @return array                Associative of statistics
     */
    protected function restitutedStats($startDate, $endDate, $filter, $statistics = [])
    {
        $jsonSizeColumnNumber = 6;
        $statistics['restitutedGroupedMemorySize'] = $this->getSizeByEventTypeOrdered('ArchiveRestitutionRequest', ['recordsManagement/restitution'], $jsonSizeColumnNumber, $startDate, $endDate, $filter);
        $statistics['restitutedGroupedMemoryCount'] = $this->getCountByEventTypeOrdered('ArchiveRestitutionRequest', $startDate, $endDate, $filter);

        return $statistics;
    }

    /**
     * Statistics aggregator for transfered event
     *
     * @param  datetime $startDate  starting date
     * @param  datetime $endDate    End date
     * @param  string   $filter     Group by argument
     * @param  array    $statistics Array of statistics
     *
     * @return array                Associative of statistics
     */
    protected function transferedStats($startDate, $endDate, $filter, $statistics = [])
    {
        $jsonSizeColumnNumber = 6;
        $statistics['transferedGroupedMemorySize'] = $this->getSizeByEventTypeOrdered('Deletion', ['recordsManagement/outgoingTransfer'], $jsonSizeColumnNumber, $startDate, $endDate, $filter);
        $statistics['transferedGroupedMemoryCount'] = $this->getCountByEventTypeOrdered('Deletion', $startDate, $endDate, $filter);

        return $statistics;
    }

    /**
     * Statistics aggregator for communicated event
     *
     * @param  datetime $startDate  starting date
     * @param  datetime $endDate    End date
     * @param  string   $filter     Group by argument
     * @param  array    $statistics Array of statistics
     *
     * @return array                Associative of statistics
     */
    protected function communicatedStats($startDate, $endDate, $filter, $statistics = [])
    {
        $jsonSizeColumnNumber = 6;
        $statistics['communicatedGroupedMemorySize'] = $this->getSizeByEventTypeOrdered('ArchiveDeliveryRequest', ['recordsManagement/delivery'], $jsonSizeColumnNumber, $startDate, $endDate, $filter);
        $statistics['communicatedGroupedMemoryCount'] = $this->getCountByEventTypeOrdered('ArchiveDeliveryRequest', $startDate, $endDate, $filter);

        return $statistics;
    }

    /**
     * Sum all event info for a particular event
     *
     * @param  array    $messageType      The type of the message
     * @param  array    $eventTypes       Array of event types
     * @param  integer  $jsonColumnNumber json Column number for size parameter in lifeCycle event table
     * @param  datetime $startDate        Starting Date
     * @param  datetime $endDate          End date
     * @param  boolean  $isIncoming       Is the message incoming if type is Archive Transfer
     *
     * @return integer                    Sum of size for events
     */
    protected function getSizeByEventType($messageType, $eventTypes, $jsonColumnNumber, $startDate = null, $endDate = null, $isIncoming = false)
    {
        $explodingEventTypes = $this->stringifyEventTypes($eventTypes);
        $in = $explodingEventTypes['in'];
        $inParams = $explodingEventTypes['inParams'];

        if ($messageType == "ArchiveTransfer") {
            $isIncomingCondition = '';
            if (!$isIncoming) {
                $isIncomingCondition .= ' OR "message"."status" = \'validated\'';
            }
            $isIncomingCondition .= ') AND "message"."isIncoming" = ' . ($isIncoming ? 'TRUE' : 'FALSE');
        }

        $query = 'WITH RECURSIVE get_children_size(archive_id, volume) AS (
            SELECT "archive"."archiveId", "event"."eventInfo"::json->>'.$jsonColumnNumber.'
            FROM "medona"."unitIdentifier" "unitIdentifier"
            INNER JOIN "medona"."message" "message"
            ON "message"."messageId" = "unitIdentifier"."messageId" AND "message"."type" = \''.$messageType.'\' AND ("message"."status" = \'processed\''. (isset($isIncomingCondition) ? $isIncomingCondition : ')') .
            ($startDate ? ' AND "message"."date">\''.$startDate.'\'::timestamp AND "message"."date"<\''.$endDate.'\'::timestamp' : '').'
            INNER JOIN "recordsManagement"."archive" "archive"
            ON "archive"."archiveId" = "unitIdentifier"."objectId" AND ("archive"."parentArchiveId" is null or "archive"."parentArchiveId" not in (
                SELECT "unitIdentifier"."objectId"
                FROM "medona"."unitIdentifier" "unitIdentifier"
                INNER JOIN "medona"."message" "message"
                ON "message"."messageId" = "unitIdentifier"."messageId" AND "message"."type" = \''.$messageType.'\' AND ("message"."status" = \'processed\''. (isset($isIncomingCondition) ? $isIncomingCondition : ')') .
                ($startDate ? ' AND "message"."date">\''.$startDate.'\'::timestamp AND "message"."date"<\''.$endDate.'\'::timestamp' : '').'
            ))
            INNER JOIN "lifeCycle"."event" "event" ON "event"."objectId" = "archive"."archiveId" AND "event"."eventType" IN ('.$in.')
                UNION ALL
            SELECT "archive"."archiveId", "event"."eventInfo"::json->>8
            FROM "recordsManagement"."archive" "archive"
            JOIN get_children_size "archive_size" ON 1=1
            LEFT JOIN "lifeCycle"."event" "event" ON "event"."objectId" = "archive"."archiveId" AND "event"."eventType" IN ('.$in.')'.
            ($startDate ? ' AND "event"."timestamp">\''.$startDate.'\'::timestamp AND "event"."timestamp"<\''.$endDate.'\'::timestamp' : '').'
            WHERE "archive"."parentArchiveId" = "archive_size"."archive_id"
        )
        SELECT SUM(CAST(NULLIF("archive_size"."volume", \'\') AS INTEGER))
        FROM get_children_size "archive_size"';

        $result = $this->executeQuery($query, $inParams, $eventTypes[0] == 'recordsManagement/deposit');
        $sum = 0;
        if (isset($result[0]['sum'])) {
            $sum = $result[0]['sum'];
        }

        return $sum;
    }

    /**
     * Count all event info for particular event(s)
     *
     * @param  array    $messageType           The type of the message
     * @param  datetime $startDate             Starting Date
     * @param  datetime $endDate               End date
     * @param  boolean  $isIncoming            Is the message incoming if type is Archive Transfer
     *
     * @return integer                         Count of size for events
     */
    protected function getCountByEventType($messageType, $startDate = null, $endDate = null, $isIncoming = false)
    {
        if ($messageType == "ArchiveTransfer") {
            $isIncomingCondition = '';
            if (!$isIncoming) {
                $isIncomingCondition .= ' OR "message"."status" = \'validated\'';
            }
            $isIncomingCondition .= ') AND "message"."isIncoming" = ' . ($isIncoming ? 'TRUE' : 'FALSE');
        }

        $query = 'SELECT  COUNT("unitIdentifier"."objectId")
            FROM "medona"."message" "message"
            INNER JOIN "medona"."unitIdentifier" "unitIdentifier"
            ON "unitIdentifier"."messageId" = "message"."messageId"
            INNER JOIN "recordsManagement"."archive" "archive"
            ON "archive"."archiveId" = "unitIdentifier"."objectId"
            AND ("archive"."parentArchiveId" IS NULL OR NOT "archive"."parentArchiveId" IN (
                SELECT "unitIdentifier"."objectId"
                FROM "medona"."message" "message"
                INNER JOIN "medona"."unitIdentifier" "unitIdentifier"
                ON "unitIdentifier"."messageId" = "message"."messageId"
                WHERE "message"."type" = \''.$messageType.'\'
                AND ("message"."status" = \'processed\''.
                (isset($isIncomingCondition) ? $isIncomingCondition : ')').'
            ))
            WHERE "message"."type" = \''.$messageType.'\' 
            AND ("message"."status" = \'processed\''. (isset($isIncomingCondition) ? $isIncomingCondition : ')') .
            ($startDate ? ' AND "message"."date">\''.$startDate.'\'::timestamp AND "message"."date"<\''.$endDate.'\'::timestamp' : '');

        $count = $this->executeQuery($query)[0]['count'];
        return $count;
    }

    /**
     * Count all event info for particular event(s) ordered by another event
     *
     * @param  array    $messageType           The type of the message
     * @param  datetime $startDate             Starting Date
     * @param  datetime $endDate               End date
     * @param  string   $groupBy               Name of Group By
     * @param  boolean  $isIncoming            Is the message incoming if type is Archive Transfer
     *
     * @return integer                         Count of size for events
     */
    protected function getCountByEventTypeOrdered($messageType, $startDate = null, $endDate = null, $groupBy = null, $isIncoming = false)
    {
        $isArchivalProfile = $groupBy == "archivalProfile";

        if ($messageType == "ArchiveTransfer") {
            $isIncomingCondition = '';
            if (!$isIncoming) {
                $isIncomingCondition .= ' OR "message"."status" = \'validated\'';
            }
            $isIncomingCondition .= ') AND "message"."isIncoming" = ' . ($isIncoming ? 'TRUE' : 'FALSE');
        }

        $query = 'SELECT '.($isArchivalProfile ? 'COALESCE("archivalProfile"."name", \'Without profile\')' : '"organization"."displayName"').' as '.$groupBy.', COUNT("unitIdentifier"."objectId")
        FROM "medona"."message" "message"
        INNER JOIN "medona"."unitIdentifier" "unitIdentifier"
        ON "unitIdentifier"."messageId" = "message"."messageId"
        INNER JOIN "recordsManagement"."archive" "archive"
        ON "archive"."archiveId" = "unitIdentifier"."objectId" AND ("archive"."parentArchiveId" IS NULL OR NOT "archive"."parentArchiveId" IN (
            SELECT "unitIdentifier"."objectId"
            FROM "medona"."message" "message"
            INNER JOIN "medona"."unitIdentifier" "unitIdentifier"
            ON "unitIdentifier"."messageId" = "message"."messageId"
            WHERE "message"."type" = \''.$messageType.'\'
            AND ("message"."status" = \'processed\''. (isset($isIncomingCondition) ? $isIncomingCondition : ')') .
            ($startDate ? ' AND "message"."date">\''.$startDate.'\'::timestamp AND "message"."date"<\''.$endDate.'\'::timestamp' : '').'
        ))'.
        ($isArchivalProfile
            ? ' LEFT JOIN "recordsManagement"."archivalProfile" "archivalProfile"
                ON "archivalProfile"."reference" = "archive"."archivalProfileReference"'
            : ' INNER JOIN "organization"."organization" "organization"
                ON "organization"."registrationNumber" = "archive"."archiverOrgRegNumber"').
        ' WHERE "message"."type" = \''.$messageType.'\'
        AND ("message"."status" = \'processed\''. (isset($isIncomingCondition) ? $isIncomingCondition : ')') .
        ($startDate ? ' AND "message"."date">\''.$startDate.'\'::timestamp AND "message"."date"<\''.$endDate.'\'::timestamp' : '').'
        GROUP BY '.($isArchivalProfile ? '"archivalProfile"."name"' : '"organization"."displayName"');

        $sum = $this->executeQuery($query);
        return $sum;
    }

    /**
     * Sum all events info for particular event(s) ordered by another event
     *
     * @param  array    $messageType           The type of the message
     * @param  array    $eventTypes            Array of event types
     * @param  integer  $jsonColumnNumber      json Column number for size parameter in lifeCycle event table
     * @param  datetime $startDate             Starting Date
     * @param  datetime $endDate               End date
     * @param  string   $groupBy               Name of Group By
     * @param  boolean  $isIncoming            Is the message incoming if type is Archive Transfer
     *
     * @return integer                         Sum of size for events
     */
    protected function getSizeByEventTypeOrdered($messageType, $eventTypes, $jsonColumnNumber, $startDate = null, $endDate = null, $groupBy = null, $isIncoming = false)
    {
        $explodingEventTypes = $this->stringifyEventTypes($eventTypes);
        $in = $explodingEventTypes['in'];
        $inParams = $explodingEventTypes['inParams'];
        $isArchivalProfile = $groupBy == "archivalProfile";

        if ($messageType == "ArchiveTransfer") {
            $isIncomingCondition = '';
            if (!$isIncoming) {
                $isIncomingCondition .= ' OR "message"."status" = \'validated\'';
            }
            $isIncomingCondition .= ') AND "message"."isIncoming" = ' . ($isIncoming ? 'TRUE' : 'FALSE');
        }

        $query = 'WITH RECURSIVE get_children_size(archive_id, volume, '.($isArchivalProfile ? "profile" : "org_reg").') AS (
            SELECT "archive"."archiveId", "event"."eventInfo"::json->>'.$jsonColumnNumber.', '.($isArchivalProfile ? 'COALESCE("archive"."archivalProfileReference", \'\')' : '"archive"."archiverOrgRegNumber"').'
            FROM "medona"."unitIdentifier" "unitIdentifier"
            INNER JOIN "medona"."message" "message"
            ON "message"."messageId" = "unitIdentifier"."messageId" and "message"."type" = \''.$messageType.'\' AND ("message"."status" = \'processed\''. (isset($isIncomingCondition) ? $isIncomingCondition : ')') .
            ($startDate ? ' AND "message"."date">\''.$startDate.'\'::timestamp AND "message"."date"<\''.$endDate.'\'::timestamp' : '').'
            INNER JOIN "recordsManagement"."archive" "archive"
            ON "archive"."archiveId" = "unitIdentifier"."objectId" and ("archive"."parentArchiveId" is null or "archive"."parentArchiveId" not in (
                SELECT "unitIdentifier"."objectId"
                FROM "medona"."unitIdentifier" "unitIdentifier"
                INNER JOIN "medona"."message" "message"
                ON "message"."messageId" = "unitIdentifier"."messageId" and "message"."type" = \''.$messageType.'\' AND ("message"."status" = \'processed\''. (isset($isIncomingCondition) ? $isIncomingCondition : ')') .
                ($startDate ? ' AND "message"."date">\''.$startDate.'\'::timestamp AND "message"."date"<\''.$endDate.'\'::timestamp' : '').'
            ))
            INNER JOIN "lifeCycle"."event" "event" ON "event"."objectId" = "archive"."archiveId" AND "event"."eventType" IN ('.$in.')
                UNION ALL
            SELECT "archive"."archiveId", "event"."eventInfo"::json->>'.$jsonColumnNumber.', '.($isArchivalProfile ? 'coalesce("archive"."archivalProfileReference", "archive_size"."profile", \'\')' : '"archive"."archiverOrgRegNumber"').'
            FROM "recordsManagement"."archive" "archive"
            JOIN get_children_size "archive_size" ON 1=1
            LEFT JOIN "lifeCycle"."event" "event" ON "event"."objectId" = "archive"."archiveId" AND "event"."eventType" IN ('.$in.')'.
            ($startDate ? ' AND "event"."timestamp">\''.$startDate.'\'::timestamp AND "event"."timestamp"<\''.$endDate.'\'::timestamp' : '').'
            WHERE "archive"."parentArchiveId" = "archive_size"."archive_id"
        )
        SELECT '.($isArchivalProfile ? 'COALESCE("archivalProfile"."name", \'Without profile\')' : '"organization"."displayName"').' as '.$groupBy.', SUM(CAST(NULLIF("archive_size"."volume", \'\') AS INTEGER))
        FROM get_children_size "archive_size"'.
        ($isArchivalProfile
            ? ' LEFT JOIN "recordsManagement"."archivalProfile" "archivalProfile" on "archivalProfile"."reference" = "archive_size"."profile"'
            : ' INNER JOIN "organization"."organization" "organization" on "organization"."registrationNumber" = "archive_size"."org_reg"'
        ).'
        GROUP BY '.($isArchivalProfile ? '"archivalProfile"."name"' : '"organization"."displayName"');

        $sum = $this->executeQuery($query, $inParams);

        return $sum;
    }

    /**
     * Retrieve count of archives unto a specific date
     *
     * @param  datetime $endDate End date
     *
     * @return integer           Size of archive
     */
    protected function getArchiveCount($endDate = null)
    {
        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        }

        $query = 'SELECT COUNT(*)
                FROM "recordsManagement"."archive"
                WHERE "depositDate"<\''.$endDate.'\'::timestamp
                AND ("status" = \'preserved\' OR ("lastModificationDate" IS NOT NULL AND "lastModificationDate">\''.$endDate.'\'::timestamp))
                AND "parentArchiveId" IS NULL';

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch()['count'];

        return $result;
    }

    /**
     * Retrieve size of digital resources unto a specific date
     *
     * @param  datetime $endDate End date
     *
     * @return integer           Size of archive
     */
    protected function getArchiveSize($endDate = null)
    {
        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        }

        $query = <<<EOT
SELECT SUM("digitalResource"."size")
FROM "digitalResource"."digitalResource"
INNER JOIN "recordsManagement"."archive" ON "archive"."archiveId" = "digitalResource"."archiveId"
WHERE "archive"."depositDate"<'$endDate'::timestamp AND ("archive"."status" = 'preserved' OR ("archive"."lastModificationDate" IS NOT NULL AND "archive"."lastModificationDate">'$endDate'::timestamp));
EOT;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch()['sum'];
        $sum = (integer)$result / pow(1000, $this->sizeFilter);

        return $sum;
    }

    /**
     * Retrieve size of digital resources unto a specific date group by a parameter
     *
     * @param  string   $groupBy Ordering parameter
     * @param  datetime $endDate End date
     *
     * @return array             Size of archive ordered by parameter
     */
    protected function getArchiveSizeOrdered($groupBy, $endDate = null)
    {
        $isArchivalProfile = false;
        switch ($groupBy) {
            case 'archivalProfile':
                $tableProperty = "archivalProfileReference";
                $isArchivalProfile = true;
                break;
            case 'originatingOrg':
                $tableProperty = "originatorOrgRegNumber";
                break;
        }

        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        }

        $query = 'WITH RECURSIVE get_children_size(archive_id, volume, group_by) AS (
            SELECT "archive"."archiveId", "digitalResource"."size", "archive"."'.$tableProperty.'"
            FROM "recordsManagement"."archive" "archive"
            LEFT JOIN "digitalResource"."digitalResource" "digitalResource" ON "digitalResource"."archiveId" = "archive"."archiveId"
            WHERE "archive"."parentArchiveId" IS NULL AND "archive"."depositDate" < \''.$endDate.'\'::timestamp AND ("status" = \'preserved\' OR ("lastModificationDate" IS NOT NULL AND "lastModificationDate">\''.$endDate.'\'::timestamp))
          UNION ALL
            SELECT "archive"."archiveId", "digitalResource"."size", "archive_size"."group_by"
            FROM "recordsManagement"."archive" "archive"
            JOIN get_children_size "archive_size" ON 1=1
            LEFT JOIN "digitalResource"."digitalResource" "digitalResource" ON "digitalResource"."archiveId" = "archive"."archiveId"
            WHERE "archive"."parentArchiveId" = "archive_size"."archive_id"
        )
        SELECT '.($isArchivalProfile ? '"archivalProfile"."name"' : '"organization"."displayName"').' AS '.$groupBy.', SUM(CAST("archive_size"."volume" AS INTEGER))
        FROM get_children_size "archive_size"'.(
            $isArchivalProfile
            ? ' INNER JOIN "recordsManagement"."archivalProfile" "archivalProfile" ON "archivalProfile"."reference" = "archive_size"."group_by"'
            : ' INNER JOIN "organization"."organization" "organization" ON "organization"."registrationNumber" = "archive_size"."group_by"'
        ).
        ' GROUP BY '.($isArchivalProfile ? '"archivalProfile"."name"' : '"organization"."displayName"');
        
        $results = $this->executeQuery($query);

        return $results;
    }

    /**
     * Retrieve count of digital resources unto a specific date group by a parameter
     *
     * @param  string   $groupBy Ordering parameter
     * @param  datetime $endDate End date
     *
     * @return array             Count of archive ordered by parameter
     */
    protected function getArchiveCountOrdered($groupBy, $endDate = null)
    {
        $isArchivalProfile = false;
        switch ($groupBy) {
            case 'archivalProfile':
                $isArchivalProfile = true;
                $tableProperty = "archivalProfileReference";
                break;
            case 'originatingOrg':
                $tableProperty = "originatorOrgRegNumber";
                break;
        }

        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        }

        $query = 'SELECT '.($isArchivalProfile ? '"archivalProfile"."name"' : '"organization"."displayName"').' AS '.$groupBy.', COUNT ("archive".*)
                FROM "recordsManagement"."archive" "archive"'.
                (
                    $isArchivalProfile
                    ? ' INNER JOIN "recordsManagement"."archivalProfile" "archivalProfile" ON "archivalProfile"."reference" = "archive"."'.$tableProperty.'"'
                    : ' INNER JOIN "organization"."organization" "organization" ON "organization"."registrationNumber" = "archive"."'.$tableProperty.'"'
                ).
                ' WHERE "depositDate" < \''.$endDate.'\'::timestamp AND ("status" = \'preserved\' OR ("lastModificationDate" IS NOT NULL AND "lastModificationDate">\''.$endDate.'\'::timestamp)) AND "archive"."parentArchiveId" IS NULL
                GROUP BY '.($isArchivalProfile ? '"archivalProfile"."name"' : '"organization"."displayName"');

        $results = $this->executeQuery($query);

        return $results;
    }

    /**
     * Sum all archives size for direct archive transfer
     *
     * @param  string   $eventType             Type of event
     * @param  integer  $jsonSizeColumnNumber  json Column number for size parameter in lifeCycle event table
     * @param  string   $groupBy               Ordering parameter
     * @param  datetime $startDate             Starting Date
     * @param  datetime $endDate               End date
     *
     * @return integer                         Sum of size for events
     */
    protected function getSizeForDirectEvent($eventType, $jsonSizeColumnNumber, $groupBy = null, $startDate = null, $endDate = null)
    {
        if ($groupBy) {
            $selectCondition = ($groupBy == 'archivalProfile') ? 'COALESCE("archivalProfile"."name", \'Without profile\')' : '"organization"."displayName"';
            $groupByCondition = ($groupBy == 'archivalProfile') ? '"archivalProfile"."name"' : '"organization"."displayName"';
            $joinCondition = ($groupBy == 'archivalProfile')
                ? ' LEFT JOIN "recordsManagement"."archivalProfile" "archivalProfile"
                ON "archivalProfile"."reference" = "event"."eventInfo"::json->>10'
                : ' INNER JOIN "organization"."organization" "organization"
                ON "organization"."registrationNumber" = "event"."eventInfo"::json->>4';
        }

        $query = 'SELECT '.($groupBy ? $selectCondition . ' AS "'.$groupBy.'", ' : '').'SUM(CAST(COALESCE(NULLIF("event"."eventInfo"::json->>'.$jsonSizeColumnNumber.', \'\'), \'0\') AS INTEGER))
        FROM "lifeCycle"."event" "event"'.
        ($groupBy ? $joinCondition : '').'
        WHERE "event"."eventType" IN (\''.$eventType.'\')
        AND "event"."objectId" NOT IN (
            SELECT "objectId"
            FROM "medona"."unitIdentifier"
        )'.
        ($startDate ? ' AND "event"."timestamp">\''.$startDate.'\'::timestamp AND "event"."timestamp"<\''.$endDate.'\'::timestamp' : '').
        ($groupBy ? ' GROUP BY ' . $groupByCondition : '');

        $result = $this->executeQuery($query);

        $sum = 0;
        if ($groupBy) {
            $sum = [];
            foreach ($result as $row) {
                if (isset($row[$groupBy])) {
                    $sum[$row[$groupBy]] = $row["sum"];
                }
            }
        } elseif (isset($result[0]['sum'])) {
            $sum = $result[0]['sum'];
        }

        return $sum;
    }

    /**
     * Count all archives for direct archive transfer
     *
     * @param  string   $eventType        Type of event
     * @param  string   $groupBy          Ordering parameter
     * @param  datetime $startDate        Starting Date
     * @param  datetime $endDate          End date
     *
     * @return integer                    Sum of size for events
     */
    protected function getCountForDirectEvent($eventType, $groupBy = false, $startDate = null, $endDate = null)
    {
        if ($groupBy) {
            $selectCondition = $groupBy == 'archivalProfile' ? 'COALESCE("archivalProfile"."name", \'Without profile\')' : '"organization"."displayName"';
            $groupByCondition = $groupBy == 'archivalProfile' ? '"archivalProfile"."name"' : '"organization"."displayName"';
            $joinCondition = $groupBy == 'archivalProfile'
                ? ' LEFT JOIN "recordsManagement"."archivalProfile" "archivalProfile"
                ON "archivalProfile"."reference" = "event"."eventInfo"::json->>10'
                : ' INNER JOIN "organization"."organization" "organization"
                ON "organization"."registrationNumber" = "event"."eventInfo"::json->>4';
        }

        $query = 'SELECT '.($groupBy ? $selectCondition . ' AS "'.$groupBy.'", ' : '').'COUNT("event"."eventId")
        FROM "lifeCycle"."event" "event"'.
        ($groupBy ? $joinCondition : '').'
        WHERE "event"."eventType" IN (\''.$eventType.'\')
        AND "event"."objectId" NOT IN (
            SELECT "objectId"
            FROM "medona"."unitIdentifier"
        )'.
        ($startDate ? ' AND "event"."timestamp">\''.$startDate.'\'::timestamp AND "event"."timestamp"<\''.$endDate.'\'::timestamp' : '').
        ($groupBy ? ' GROUP BY ' . $groupByCondition : '');

        $result = $this->executeQuery($query);
        
        $count = 0;
        if ($groupBy) {
            $count = [];
            foreach ($result as $row) {
                if (isset($row[$groupBy])) {
                    $count[$row[$groupBy]] = $row["count"];
                }
            }
        } elseif (isset($result[0]['count'])) {
            $count = $result[0]['count'];
        }

        return $count;
    }

    /**
     * Stringify array of type of events for better sql query
     *
     * @param array $eventTypes Types of event
     *
     * @return array
     */
    protected function stringifyEventTypes($eventTypes)
    {
        $in = "";
        $inParams = [];
        foreach ($eventTypes as $key => $eventType) {
            $k = ":eventType" . $key;
            $in .= "$k,";
            $inParams[$k] = $eventType;
        }
        $in = rtrim($in, ",");

        return [
            'in' => $in,
            'inParams' => $inParams
        ];
    }

    /**
     * format a size
     *
     * @param float     $size       Size to format
     *
     * @return string               Formatted size
     */
    protected function formatSize($size, $formatType = true)
    {
        if ($formatType) {
            $size /= pow(1000, $this->sizeFilter);
        }
        if ($size != (integer)$size) {
            $size = number_format($size, 3, ".", " ");
        }
        return $size;
    }

    /**
     * Execute query
     *
     * @param string   $query                           Query to send
     * @param string   $secondary_parameters            Secondary parameters
     *
     * @return array                                    Results of query
     */
    public function executeQuery($query, $secondary_parameters = [])
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($secondary_parameters);
        $results = [];

        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result['sum'] = isset($result['sum']) ? $this->formatSize($result['sum']) : '0.000';
            $results[] = $result;
        }

        return $results;
    }
}
