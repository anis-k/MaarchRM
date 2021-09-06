<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\adminArchive;

/**
 * Interface for management of archival agreements
 *
 * @package Medona
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface archivalAgreementInterface
{
    /**
     * List archival agreements
     *
     * @uses medona/archivalAgreement/readIndex
     * @return medona/archivalAgreement/index
     */
    public function readMedonaArchivalagreements();

    /**
     * New empty archival profile with default values
     *
     * @uses medona/archivalAgreement/readNewagreement
     * @return medona/archivalAgreement/edit
     */
    public function readMedonaArchivalagreementNewagreement();

    /**
     * Edit a archival profile
     *
     * @uses medona/archivalAgreement/read_archivalAgreementId_
     * @return medona/archivalAgreement/edit
     */
    public function readMedonaArchivalagreementEdit_archivalAgreementId_($archivalAgreementId);

    /**
     * create a archival profile
     * @param medona/archivalAgreement $archivalAgreement The archival profile object
     *
     * @uses medona/archivalAgreement/create
     * @return medona/archivalAgreement/create
     */
    public function createMedonaArchivalagreement($archivalAgreement);

    /**
     * update a archival profile
     * @param medona/archivalAgreement $archivalAgreement The archival profile object
     *
     * @uses medona/archivalAgreement/update
     * @return medona/archivalAgreement/update
     */
    public function updateMedonaArchivalagreement($archivalAgreement);

    /**
     * delete an archival profile
     *
     * @uses medona/archivalAgreement/delete_archivalAgreementId_
     * @return medona/archivalAgreement/delete
     */
    public function deleteMedonaArchivalagreement_archivalAgreementId_($archivalAgreementId);
}
