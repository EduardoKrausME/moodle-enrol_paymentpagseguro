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
 * Represent available payment method groups.
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroPaymentMethodGroups {

    private static $availableGroupList = array(
        'CREDIT_CARD' => 'Payment with Credit Card',
        'BOLETO' => 'Payment with Boleto',
        'EFT' => 'Payment with Online Debit',
        'BALANCE' => 'Payment with PagSeguro Balance',
        'DEPOSIT' => 'Payment with Deposit'
    );

    /**
     * Get available payment method groups list for payment method config use in PagSeguro transactions
     * @return array
     */
    public static function getAvailableGroupList() {
        return self::$availableGroupList;
    }

    /**
     * Check if payment method groups is available for PagSeguro
     * @param string $key
     * @return boolean
     */
    public static function isKeyAvailable($key) {
        $key = strtoupper($key);
        return (isset(self::$availableGroupList[$key]));
    }

    /**
     * Gets group description by key
     * @param string $key
     * @return string
     */
    public static function getDescriptionByKey($key) {
        $key = strtoupper($key);
        if (isset(self::$availableGroupList[$key])) {
            return self::$availableGroupList[$key];
        } else {
            return false;
        }
    }

    /**
     * Gets group type by description
     * @param string $description
     * @return string
     */
    public static function getGroupByDescription($description) {
        return array_search(strtolower($description), array_map('strtolower', self::$availableGroupList));
    }
}
