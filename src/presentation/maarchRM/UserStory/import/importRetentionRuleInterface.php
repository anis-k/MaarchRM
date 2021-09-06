<?php

namespace presentation\maarchRM\UserStory\import;

interface importRetentionRuleInterface
{
    /**
     * Import a csv file with retention rules informations
     *
     * @param resource  $data       Data base64 encoded or not in proper format
     * @param boolean   $isReset    Reset tables or not
     *
     * @uses recordsManagement/retentionRule/createImport
     *
     */
    public function createImportRetentionrules($data, $isReset = false);
}
