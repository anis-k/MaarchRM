<?php

namespace presentation\maarchRM\UserStory\import;

interface importArchivalProfileInterface
{
    /**
     * Import a csv file with archival profile informations
     *
     * @param resource  $data       Data base64 encoded or not in proper format
     * @param boolean   $isReset    Reset tables or not
     *
     * @uses recordsManagement/archivalProfile/createImport
     *
     */
    public function createImportArchivalprofiles($data, $isReset = false);
}
