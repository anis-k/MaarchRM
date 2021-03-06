<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\organization\Controller;

/**
 * Control of the organization types
 *
 * @package Organization
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class servicePosition extends abstractPosition
{
    /**
     * Get service postions list
     *
     * @return organization/servicePosition[] The list of positions
     */
    protected function listPositions()
    {
        $accountToken = \laabs::getToken('AUTH');

        if (empty($accountToken)) {
            return array();
        }

        $positions =  $this->sdoFactory->find('organization/servicePosition', "serviceAccountId = '".$accountToken->accountId."'");

        \laabs::setToken("ORGANIZATION", $positions[0], \laabs::configuration("auth")['securityPolicy']['sessionTimeout']);

        return $positions;
    }

    /**
     * Get service position
     * @param string $serviceAccountId The service account identifier
     *
     * @return bool The result of the operation
     */
    public function getPosition($serviceAccountId)
    {
        $servicePosition = $this->sdoFactory->find("organization/servicePosition", "serviceAccountId = '$serviceAccountId'");

        if (!count($servicePosition)) {
            return;
        }

        $servicePosition = $servicePosition[0];

        $serviceAccountController = \laabs::newController("auth/serviceAccount");
        $serviceAccount = $serviceAccountController->read($serviceAccountId);
        $servicePosition->accountName = $serviceAccount->accountName;
        
        $organization = $this->sdoFactory->read("organization/organization", $servicePosition->orgId);
        $servicePosition->organization = $organization;


        return $servicePosition;
    }

    /**
     * Get my all positions
     *
     * @return organization/servicePosition[] The list of my position's
     */
    public function getMyPositions()
    {
        return $this->listPositions();
    }
}
