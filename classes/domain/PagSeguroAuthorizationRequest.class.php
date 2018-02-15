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
 * Represents a authorization request
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroAuthorizationRequest {

    /**
     * Reference code
     * Optional. You can use the reference code to store an identifier so you can
     * associate the PagSeguro transaction to a transaction in your system.
     */
    private $reference;

    /**
     * Uri to where the PagSeguro payment page should redirect the user after the payment information is processed.
     * Typically this is a confirmation page on your web site.
     * @var String
     */
    private $redirectURL;

    /**
     * Determines for which url PagSeguro will send the order related notifications codes.
     * Optional. Any change happens in the transaction status, a new notification request will be send
     * to this url. You can use that for update the related order.
     */
    private $notificationURL;

    /**
     * Permission List
     */
    private $permissions;

    /**
     * Extra parameters that user can add to a PagSeguro authorization request
     *
     * Optional
     * @var PagSeguroParameter
     */
    private $parameter;

    /**
     * Class constructor to make sure the library was initialized.
     */
    public function __construct() {
        PagSeguroLibrary::init();
    }

    /**
     * Sets reference for PagSeguro authorization requests
     * @param string $reference
     */
    public function setReference($reference) {
        $this->reference = $reference;
    }

    /**
     * @return string of $redirectURL
     */
    public function getReference() {
        return $this->reference;
    }

    /**
     * Sets redirect URL for PagSeguro authorization requests
     * @param string $redirectURL
     */
    public function setRedirectURL($redirectURL) {
        $this->redirectURL = $redirectURL;
    }

    /**
     * @return string of $redirectURL
     */
    public function getRedirectURL() {
        return $this->redirectURL;
    }

    /**
     * Sets notificationURL for PagSeguro authorization requests
     * @param string $notificationURL
     */
    public function setNotificationURL($notificationURL) {
        $this->permissions = $notificationURL;
    }

    /**
     * @return string of notificationURL
     */
    public function getNotificationURL() {
        return $this->notificationURL;
    }

    /**
     * @return array of permissions
     */
    public function getPermissions() {
        return $this->permissions;
    }

    /**
     * Sets permissions for PagSeguro authorization requests
     * @param array $permissions
     */
    public function setPermissions(array $permissions) {
        $this->permissions = new PagSeguroAuthorizationPermissions($permissions);
    }

    /**
     * Sets parameter for PagSeguro authorization requests
     *
     * @param PagSeguroParameter $parameter
     */
    public function setParameter($parameter) {
        $this->parameter = $parameter;
    }

    /**
     * Gets parameter for PagSeguro authorization requests
     *
     * @return PagSeguroParameter
     */
    public function getParameter() {
        if ($this->parameter == null) {
            $this->parameter = new PagSeguroParameter();
        }
        return $this->parameter;
    }

    /**
     * add a parameter for PagSeguro authorization request
     *
     * @param PagSeguroParameterItem $parameterName key
     * @param PagSeguroParameterItem $parameterValue value
     */
    public function addParameter($parameterName, $parameterValue) {
        $this->getParameter()->addItem(new PagSeguroParameterItem($parameterName, $parameterValue));
    }

    /**
     * add a parameter for PagSeguro authorization request
     *
     * @param PagSeguroParameterItem $parameterName key
     * @param PagSeguroParameterItem $parameterValue value
     * @param PagSeguroParameterItem $parameterIndex group
     */
    public function addIndexedParameter($parameterName, $parameterValue, $parameterIndex) {
        $this->getParameter()->addItem(new PagSeguroParameterItem($parameterName, $parameterValue, $parameterIndex));
    }

    /**
     * Calls the PagSeguro web service and register this request for authorization
     * @param PagSeguroCredentials $credentials
     * @param bool $onlyAuthorizationCode
     * @return PagSeguroAuthorizationService Data
     */
    public function register(PagSeguroCredentials $credentials, $onlyAuthorizationCode = false) {
        return PagSeguroAuthorizationService::createAuthorizationRequest($credentials, $this, $onlyAuthorizationCode);
    }

    /**
     * @return String a string that represents the current object
     */
    public function toString() {

        $request = array();
        $request['Reference'] = $this->reference;

        return "PagSeguroAuthorizationRequest: " . implode(' - ', $request);
    }

    /**
     * Verify if the adress of NotificationURL or RedirectURL is for tests and return empty
     * @param type $url
     * @return type
     */
    public function verifyURLTest($url) {
        $address = array('127.0.0.1', '::1');

        foreach ($address as $item) {
            $find = strpos($url, $item);

            if ($find) {
                return false;
            } else {
                return $url;
            }
        }
    }
}
