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
 * Payment method
 *
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroPaymentMethod {

    /**
     * Payment method type
     */
    private $type;

    /**
     * Payment method code
     */
    private $code;

    /**
     * Initializes a new instance of the PaymentMethod class
     *
     * @param PagSeguroPaymentMethodType $type
     * @param PagSeguroPaymentMethodCode $code
     */
    public function __construct($type = null, $code = null) {
        if ($type) {
            $this->setType($type);
        }
        if ($code) {
            $this->setCode($code);
        }
    }

    /**
     * @return the payment method type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the payment method type
     * @param PagSeguroPaymentMethodType $type
     */
    public function setType($type) {
        if ($type instanceof PagSeguroPaymentMethodType) {
            $this->type = $type;
        } else {
            $this->type = new PagSeguroPaymentMethodType($type);
        }
    }

    /**
     * @return the code
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Sets the payment method code
     * @param PagSeguroPaymentMethodCode $code
     */
    public function setCode($code) {
        if ($code instanceof PagSeguroPaymentMethodCode) {
            $this->code = $code;
        } else {
            $this->code = new PagSeguroPaymentMethodCode($code);
        }
    }
}
