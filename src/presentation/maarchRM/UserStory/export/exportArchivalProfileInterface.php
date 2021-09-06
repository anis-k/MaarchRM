<?php

namespace presentation\maarchRM\UserStory\export;

interface exportArchivalProfileInterface
{
    /**
     * Get archival profiles infos
     *
     * @uses recordsManagement/archivalProfile/readExport
     *
     * @return importExport/Export/listCsv
     */
    public function readExportArchivalprofiles($limit = null, $ref = null);
}
