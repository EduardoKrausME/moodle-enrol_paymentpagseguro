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
 * Represents document
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroDocument {

    private static $availableDocumentList = array(
        1 => 'CPF',
        2 => 'CNPJ'
    );

    /**
     * The type of document
     * @var string
     */
    private $type;

    /**
     * The value of document
     * @var string
     */
    private $value;

    public function __construct(array $data = null) {
        if ($data) {
            if (isset($data['type']) && isset($data['value'])) {
                $this->setType($data['type']);
                $this->setValue(PagSeguroHelper::getOnlyNumbers($data['value']));
            }
        }
    }

    /**
     * Get document type
     * @return String
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set document type
     * @param String $type
     */
    public function setType($type) {
        $this->type = strtoupper($type);
    }

    /**
     * Get document value
     * @return String
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Set document value
     * @param String $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Check if document type is available for PagSeguro
     * @param string $documentType
     * @return array|boolean
     */
    public static function isDocumentTypeAvailable($documentType) {
        return (array_search(strtoupper($documentType), self::$availableDocumentList));
    }
}
