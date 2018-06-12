<?php

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
 * Installments information
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroInstallments {
    /**
     * @var $installments
     */
    private $installments;

    /**
     * Initializes a new instance of the PagSeguroInstallments class
     * @param null|array $installments
     * @throws string Exception
     */
    public function __construct($installments = null) {
        if (isset($installments)) {
            $this->setInstallments($installments);
        }
    }

    /**
     * @return array of $installments
     */
    public function getInstallments() {
        return $this->installments;
    }

    /**
     * @param array $installments
     */
    public function setInstallments($installments) {
        if (isset($installments)) {
            $this->installments = $installments;
        }
    }

}