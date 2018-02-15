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
 * Defines a list of known transaction cancellation source.
 * This class is not an enum to enable the introduction of new cancellation source.
 * without breaking this version of the library.
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroTransactionCancellationSource {

    /**
     * @var array
     */
    private static $sourceList = array(
        'PAGSEGURO' => "INTERNAL",
        'FINANCEIRA' => "EXTERNAL"
    );

    /**
     * the value of the transaction cancellation source
     * Example: EXTERNAL
     */
    private $value;

    /**
     * @param null $value
     */
    public function __construct($value = null) {
        if ($value) {
            $this->value = $value;
        }
    }

    /**
     * @param $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * @param $type
     * @throws Exception
     */
    public function setByType($type) {
        if (isset(self::$sourceList[$type])) {
            $this->value = self::$sourceList[$type];
        } else {
            throw new Exception("undefined index $type");
        }
    }

    /**
     * @return string the status value.
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param value
     * @return String the transaction cancellation source corresponding to the informed source value
     */
    public function getTypeFromValue($value = null) {
        $value = ($value == null ? $this->value : $value);
        return array_search($this->value, self::$sourceList);
    }

    /**
     * Get status list
     * @return array
     */
    public static function getSourceList() {
        return self::$sourceList;
    }
}
