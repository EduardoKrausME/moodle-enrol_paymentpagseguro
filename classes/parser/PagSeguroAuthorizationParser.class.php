<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * 2007-2014 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    PagSeguro Internet Ltda.
 * @copyright 2007-2014 PagSeguro Internet Ltda.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * Class PagSeguroAuthorizationParser
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroAuthorizationParser extends PagSeguroServiceParser {

    /**
     * @param $authorization PagSeguroAuthorizationRequest
     * @param $credentials PagSeguroAuthorizationCredentials
     * @return mixed
     */
    public static function getData($authorization) {

        $data = null;

        if ($authorization->getReference() != null) {
            $data['reference'] = $authorization->getReference();
        }

        if ($authorization->getRedirectURL() != null) {
            $data['redirectURL'] = $authorization->getRedirectURL();
        }

        if ($authorization->getNotificationURL() != null) {
            $data['notificationURL'] = $authorization->getNotificationURL();
        }

        if ($authorization->getPermissions()->getPermissions() != null) {
            $data['permissions'] = implode(',', $authorization->getPermissions()->getPermissions());
        }

        if (count($authorization->getParameter()->getItems()) > 0) {
            foreach ($authorization->getParameter()->getItems() as $item) {
                if ($item instanceof PagSeguroParameterItem) {
                    if (!PagSeguroHelper::isEmpty($item->getKey()) && !PagSeguroHelper::isEmpty($item->getValue())) {
                        if (!PagSeguroHelper::isEmpty($item->getGroup())) {
                            $data[$item->getKey() . '' . $item->getGroup()] = $item->getValue();
                        } else {
                            $data[$item->getKey()] = $item->getValue();
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param $str_xml
     * @return PagSeguroAuthorization
     */
    public static function readAuthorization($str_xml) {

        $parser = new PagSeguroXmlParser($str_xml);

        return self::buildAuthorization(
            new PagSeguroAuthorization(),
            $parser->getResult('authorization')
        );
    }

    /**
     * @param $str_xml
     * @return PagSeguroAuthorization
     */
    public static function readSearchResult($str_xml) {

        $parser = new PagSeguroXmlParser($str_xml);

        $authorization = new PagSeguroAuthorizationSearchResult();

        $data = $parser->getResult('authorizationSearchResult');

        if (isset($data["date"])) {
            $authorization->setDate($data['date']);
        }


        if (isset($data['authorizations']) && is_array($data['authorizations'])) {
            if (isset($data['authorizations']['authorization'])
                && $data["resultsInThisPage"] > 1) {
                $i = 0;
                foreach ($data['authorizations']['authorization'] as $key => $value) {
                    $newAuthorization = new PagSeguroAuthorization();
                    $nAuthorization[$i++] = self::buildAuthorization($newAuthorization, $value);
                }
                $authorization->setAuthorizations($nAuthorization);

            } else {
                $newAuthorization = new PagSeguroAuthorization();
                $authorization->setAuthorizations(
                    self::buildAuthorization(
                        $newAuthorization,
                        $data['authorizations']['authorization']
                    )
                );
            }

        }

        if (isset($data["resultsInThisPage"])) {
            $authorization->setResultsInThisPage($data['resultsInThisPage']);
        }

        if (isset($data["totalPages"])) {
            $authorization->setTotalPages($data['totalPages']);
        }

        if (isset($data["currentPage"])) {
            $authorization->setCurrentPage($data["currentPage"]);
        }

        return $authorization;
    }

    /**
     * @param PagSeguroAuthorization $authorization
     * @param $data
     */
    private static function buildAuthorization(PagSeguroAuthorization $authorization, $data) {

        if (isset($data["code"])) {
            $authorization->setCode($data['code']);
        }

        if (isset($data["creationDate"])) {
            $authorization->setCreationDate($data['creationDate']);
        }

        if (isset($data["reference"])) {
            $authorization->setReference($data['reference']);
        }

        if (isset($data["account"]) and isset($data["account"]['publicKey'])) {
            $authorization->setAccount(new PagSeguroAuthorizationAccount($data["account"]['publicKey']));
        }

        if (isset($data["permissions"])) {
            if (isset($data["permissions"]["permission"])) {
                foreach ($data["permissions"]["permission"] as $permission) {
                    $permissions[] = new PagSeguroAuthorizationPermission(
                        $permission['code'],
                        $permission['status'],
                        $permission['lastUpdate']
                    );
                }
            }
            $permissions = new PagSeguroAuthorizationPermissions($permissions);
            $authorization->setPermissions($permissions);

            return $authorization;
        }
    }

    /**
     * @param $str_xml
     * @return PagSeguroParserData Success
     */
    public static function readSuccessXml($str_xml) {
        $parser = new PagSeguroXmlParser($str_xml);

        $data = $parser->getResult('authorizationRequest');
        $authorizationParserData = new PagSeguroParserData();
        $authorizationParserData->setCode($data['code']);
        $authorizationParserData->setRegistrationDate($data['date']);
        return $authorizationParserData;
    }

    /**
     * @param $error Authorization error
     * @return object()
     */
    private static function readError($error) {
        $err = new stdClass();
        $err->message = key($error);
        $err->status = true;

        return $err;
    }
}
