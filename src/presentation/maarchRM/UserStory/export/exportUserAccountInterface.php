<?php

namespace presentation\maarchRM\UserStory\export;

interface exportUserAccountInterface
{
    /**
     * Get user account infos
     *
     * @uses auth/userAccount/readExport
     *
     * @return importExport/Export/listCsv
     *
     */
    public function readExportUseraccounts($limit = null, $ref = null);
}
