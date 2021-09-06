<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona.
 *
 * Bundle medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\medona;

/**
 * Interface for management of archival agreements
 *
 * @package RecordsMangement
 * @author  Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */
interface archivalAgreementInterface
{
    /**
     * List archival agreements
     *
     * @action medona/archivalAgreement/index
     */
    public function readIndex();

    /**
     * New empty archival profile with default values
     *
     * @action medona/archivalAgreement/newAgreement
     */
    public function readNewagreement();

    /**
     * Edit a archival profile
     *
     * @action medona/archivalAgreement/edit
     */
    public function read_archivalAgreementId_();

    /**
     * create a archival profile
     * @param medona/archivalAgreement $archivalAgreement The archival agreement object
     *
     * @action medona/archivalAgreement/create
     */
    public function create($archivalAgreement);

    /**
     * update a archival profile
     * @param medona/archivalAgreement $archivalAgreement The archival agreement object
     *
     * @action medona/archivalAgreement/update
     */
    public function update($archivalAgreement);

    /**
     * delete an archival profile
     *
     * @action medona/archivalAgreement/delete
     */
    public function delete_archivalAgreementId_();
}
