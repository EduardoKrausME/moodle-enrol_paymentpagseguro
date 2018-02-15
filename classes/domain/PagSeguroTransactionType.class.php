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
 * Defines a list of known transaction types.
 * This class is not an enum to enable the introduction of new shipping types
 * without breaking this version of the library.
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroTransactionType {

    private static $typeList = array(
        'PAYMENT' => 1,
        'TRANSFER' => 2,
        'FUND_ADDITION' => 3,
        'WITHDRAW' => 4,
        'CHARGE' => 5,
        'DONATION' => 6,
        'BONUS' => 7,
        'BONUS_REPASS' => 8,
        'OPERATIONAL' => 9,
        'POLITICAL_DONATION' => 10
    );

    private $value;

    public function __construct($value = null) {
        if ($value) {
            $this->value = $value;
        }
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function setByType($type) {
        if (isset(self::$typeList[$type])) {
            $this->value = self::$typeList[$type];
        } else {
            throw new Exception("undefined index $type");
        }
    }

    public function getValue() {
        return $this->value;
    }

    /**
     * @param integer|string $value
     * @return string|integer the transaction type corresponding to the informed type value value
     */
    public function getTypeFromValue($value = null) {
        $value = ($value == null ? $this->value : $value);
        return array_search($value, self::$typeList);
    }
}
