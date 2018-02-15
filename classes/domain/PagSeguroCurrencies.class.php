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
 * Class tha represents available currencies on PagSeguro
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroCurrencies {

    /**
     * List of available currencies on PagSeguro Transactions
     * <code>
     *      key = currency name
     *      value = currency iso code 3
     * </code>
     * @var array
     */
    private static $currencies = array(
        'REAL' => 'BRL'
    );

    /**
     * Check if currency is available by informed iso code for PagSeguro transactions
     * @param string $currency_iso_code
     * @return boolean
     */
    public static function checkCurrencyAvailabilityByIsoCode($currency_iso_code) {
        $available = false;
        if (array_search(strtoupper($currency_iso_code), self::$currencies)) {
            $available = true;
        }
        return $available;
    }

    /**
     * Check if currency is available by informed currency name for PagSeguro transactions
     * @param string $name
     * @return boolean
     */
    public static function checkCurrencyAvailabilityByName($name) {
        $available = false;
        if (array_key_exists(strtoupper($name), self::$currencies)) {
            $available = true;
        }
        return $available;
    }

    /**
     * Return currencies list
     * @return array
     */
    public static function getCurrenciesList() {
        return self::$currencies;
    }

    /**
     * Return iso code by currency name
     * Default return BRL (Brazilian Real) iso code
     * @param string $name - the currency name
     * @return string
     */
    public static function getIsoCodeByName($name) {
        $name = strtoupper($name);
        return (isset(self::$currencies[$name])) ? self::$currencies[$name] : self::$currencies['REAL'];
    }

    /**
     * Return currency name by iso code
     * @param string $iso_code
     * @return string
     */
    public static function getCurrencyNameByIsoCode($iso_code) {
        return array_search(strtoupper($iso_code), self::getCurrenciesList());
    }
}
