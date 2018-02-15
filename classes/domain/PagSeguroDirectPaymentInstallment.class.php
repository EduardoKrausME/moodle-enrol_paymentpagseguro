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
 * Direct Payment Installment information
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroDirectPaymentInstallment {
    /**
     * Installment quantity
     */
    private $quantity;
    /**
     * Installment value
     */
    private $value;

    /**
     * No interest insallment qty.
     */
    private $noInterestInstallmentQuantity;

    /**
     * Initializes a new instance of the PagSeguroDirectPaymentInstallment class
     * @param array $data
     */
    public function __construct(array $data = null) {
        if ($data) {
            if (isset($data['quantity'])) {
                $this->setQuantity($data['quantity']);
            }
            if (isset($data['value'])) {
                $this->setValue($data['value']);
            }
            if (isset($data['noInterestInstallmentQuantity'])) {
                $this->setNoInterestInstallmentQuantity($data['noInterestInstallmentQuantity']);
            }
        }
    }

    /**
     * Set installment quantity
     * @param $quantity int
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    /**
     * @return int installment quantity
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * Set installment value
     * @param $value float
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * @return float installment value
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getNoInterestInstallmentQuantity() {
        return $this->noInterestInstallmentQuantity;
    }

    /**
     * @param mixed $noInterestInstallmentQuantity
     */
    public function setNoInterestInstallmentQuantity($noInterestInstallmentQuantity) {
        $this->noInterestInstallmentQuantity = $noInterestInstallmentQuantity;
    }

    /**
     * Sets the installment value and quantity
     * @param $quantity int
     * @param $value float
     */
    public function setInstallment($quantity, $value = null, $noInterestInstallmentQuantity = null) {
        $param = $quantity;
        if (isset($param) && is_array($param) || is_object($param)) {
            $this->setQuantity($param['quantity']);
            $this->setValue($param['value']);
            $this->setNoInterestInstallmentQuantity($param["noInterestInstallmentQuantity"]);
        } else {
            $this->setQuantity($quantity);
            $this->setValue($value);
            $this->setNoInterestInstallmentQuantity($noInterestInstallmentQuantity);
        }
    }
}
