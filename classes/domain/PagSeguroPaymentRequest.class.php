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
 * Represents a payment request
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroPaymentRequest extends PagSeguroRequest {

    /**
     * @var
     */
    private $preApproval;

    /**
     * Class constructor to make sure the library was initialized.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Sets recurrence for this payment request
     * @param mixed $preApproval
     */
    public function setPreApproval($preApproval) {
        $this->preApproval = $preApproval;
    }

    /**
     * @return mixed
     */
    public function getPreApproval() {
        return $this->preApproval;
    }

    /**
     * Calls the PagSeguro web service and register this request for payment
     *
     * @param PagSeguroCredentials $credentials , lighbox
     * @return String The URL to where the user needs to be redirected to in order to complete the payment process or
     * the CODE when use lightbox
     */
    public function register(PagSeguroCredentials $credentials, $onlyCheckoutCode = false) {
        return PagSeguroPaymentService::checkoutRequest($credentials, $this, $onlyCheckoutCode);
    }

    /**
     * Verify if the adress of NotificationURL or RedirectURL is for tests and return empty
     * @param type $url
     * @return type
     */
    public function verifyURLTest($url) {
        $adress = array(
            '127.0.0.1',
            '::1'
        );

        $urlReturn = null;
        foreach ($adress as $item) {
            $find = strpos($url, $item);

            if ($find) {
                $urlReturn = '';
                break;
            } else {
                $urlReturn = $url;
            }
        }

        return $urlReturn;
    }
}
