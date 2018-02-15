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
 * CreditCard Holder information
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroCreditCardHolder {

    /**
     * Credit card holder name
     */
    private $name;

    /**
     * Credit card holder cpf
     */
    private $documents;

    /**
     * Credit card holder birth date
     */
    private $birthDate;

    /**
     * Credit card holder phone
     */
    private $phone;

    /**
     * Initializes a new instance of the PagSeguroCreditCardHolder class
     * @param array $data
     */
    public function __construct(array $data = null) {

        if ($data) {
            if (isset($data['name'])) {
                $this->setName($data['name']);
            }
            if (isset($data['documents']) && is_array($data['documents'])) {
                $this->setDocuments($data['documents']);
            } else if (isset($data['documents']) && $data['documents'] instanceof PagSeguroDocument) {
                $this->documents = $data['documents'];
            }
            if (isset($data['birthDate'])) {
                $this->setBirthDate($data['birthDate']);
            }
            if (isset($data['phone']) && $data['phone'] instanceof PagSeguroPhone) {
                $this->setPhone($data['phone']);
            } else if (isset($data['areaCode']) && isset($data['number'])) {
                $this->setPhone($data['areaCode'], $data['number']);
            }
        }
    }

    /**
     * Set the credit card holder name
     * @param $name string
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string the credit card holder name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set PagSeguro documents
     * @param array $documents
     * @see PagSeguroDocument
     */
    public function setDocuments(array $documents) {
        if ($documents instanceof PagSeguroDocument) {
            $this->documents = $documents;
        } else {
            if (is_array($documents)) {
                $this->addDocument($documents['type'], $documents['value']);
            }
        }
    }

    /**
     * Add a document for Holder object
     * @param String $type
     * @param String $value
     */
    public function addDocument($type, $value) {
        if ($type && $value) {
            if (count($this->documents) == 0) {
                $data = array(
                    'type' => $type,
                    'value' => $value
                );
                $document = new PagSeguroDocument($data);
                $this->documents = $document;
            }
        }
    }

    /**
     * Get Holder documents
     * @return array PagSeguroDocument List of PagSeguroDocument
     * @see PagSeguroDocument
     */
    public function getDocuments() {
        return $this->documents;
    }

    /**
     * Set the credit card holder birth date
     * @param $birthDate date
     */
    public function setBirthDate($birthDate) {
        $this->birthDate = $birthDate;
    }

    /**
     * @return date the credit card holder birth date
     */
    public function getBirthDate() {
        return $this->birthDate;
    }

    /**
     * Sets the holder phone
     * @param String $areaCode
     * @param String $number
     */
    public function setPhone($areaCode, $number = null) {
        $param = $areaCode;
        if ($param instanceof PagSeguroPhone) {
            $this->phone = $param;
        } else if ($number) {
            $phone = new PagSeguroPhone($areaCode, $number);
            $this->phone = $phone;
        }
    }

    /**
     * @return PagSeguroPhone the holder phone
     * @see PagSeguroPhone
     */
    public function getPhone() {
        return $this->phone;
    }
}
