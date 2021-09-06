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
namespace bundle\recordsManagement\Generator;

class Sequence implements \bundle\recordsManagement\Controller\archiverArchiveIdGeneratorInterface
{
    /**
     * Sdo Factory for management of archive persistance
     * @var dependency/sdo/Factory
     */
    protected $sdoFactory;
    protected $sequenceConfiguration;

    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->sequenceConfiguration = \laabs::configuration('recordsManagement')['archiveIdGenerator']['archiverArchiveIdRules'];
    }
    /**
     * Generate archiverArchiveId
     *
     * @param  recordsManagement/archive $archive
     *
     * @return recordsManagement/archive $archive
     */
    public function generate($archive)
    {
        if (is_null($this->sequenceConfiguration) || empty($this->sequenceConfiguration)) {
            return;
        }

        if (!empty($archive->archiverArchiveId) && !$this->sequenceConfiguration['isAutomaticallyForced']) {
            return;
        }

        if (isset($archive->parentArchiveId) && !empty($archive->parentArchiveId) && $this->sequenceConfiguration['isInheritedFromParent']) {
            $parentArchive = $this->sdoFactory->read('recordsManagement/archive', $archive->parentArchiveId);
            $parentArchiveId = (string) $parentArchive->archiveId;
            $directChildrenCount = $this->sdoFactory->count('recordsManagement/archive', "parentArchiveId='$parentArchiveId'");
            $separator = $this->sequenceConfiguration['contentSuffix']['separator'];
            $paddingLength = $this->sequenceConfiguration['contentSuffix']['length'];
            $paddingString = $this->sequenceConfiguration['contentSuffix']['value'];
            $archive->archiverArchiveId = $parentArchive->archiverArchiveId . $separator . str_pad($directChildrenCount + 1, $paddingLength, $paddingString, STR_PAD_LEFT);
        } else {
            $archive->archiverArchiveId = $this->resolveSequenceFormat($this->sequenceConfiguration['format']);
        }
    }

    protected function getNewSequenceId($sequenceName)
    {
        $sequenceId = null;

        $query = <<<EOT
            SELECT NEXTVAL('"recordsManagement"."$sequenceName"');
EOT;
        $stmt = $this->sdoFactory->das->pdo->prepare($query);
        $stmt->execute();
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $sequenceId = $result['nextval'];
        }

        return $sequenceId;
    }

    protected function resolveSequenceFormat($pattern = null)
    {
        if (is_null($pattern)) {
            return;
        }

        if (preg_match_all("/\<[^\>]+\>/", $pattern, $variables)) {
            foreach ($variables[0] as $variable) {
                // retrieve parts between <> and switch on strings before ( character
                // followings strok functions call retrieves parts of remaining string before )
                $token = substr($variable, 1, -1);
                switch (strtok($token, '(')) {
                    case 'date':
                        $pattern = str_replace($variable, date(strtok(')')), $pattern);
                        break;
                    case 'sequence':
                        $sequenceParameters = explode(',', strtok(')'));
                        $sequenceName = trim($sequenceParameters[0]);
                        $paddingString = trim($sequenceParameters[1]);
                        $paddingLength = trim($sequenceParameters[2]);
                        $pattern = str_replace(
                            $variable,
                            str_pad(
                                $this->getNewSequenceId($sequenceName),
                                $paddingLength,
                                $paddingString,
                                STR_PAD_LEFT
                            ),
                            $pattern
                        );
                        break;
                }
            }
        }

        return $pattern;
    }
}
