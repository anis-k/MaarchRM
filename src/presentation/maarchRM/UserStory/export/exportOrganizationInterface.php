<?php

namespace presentation\maarchRM\UserStory\export;

interface exportOrganizationInterface
{
    /**
     * Get organizations infos
     *
     * @uses organization/organization/readExport
     *
     * @return importExport/Export/listCsv
     */
    public function readExportOrganizations($limit = null, $ref = null);
}
