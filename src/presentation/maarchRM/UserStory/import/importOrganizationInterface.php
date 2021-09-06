<?php

namespace presentation\maarchRM\UserStory\import;

interface importOrganizationInterface
{
    /**
     * Import a csv file with organization informations
     *
     * @param resource  $data      Data base64 encoded or not in proper format
     * @param boolean   $isReset   Reset tables or not
     *
     * @uses organization/organization/createImport
     *
     */
    public function createImportOrganizations($data, $isReset = false);
}
