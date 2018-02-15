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
 * Defines a list of known payment method codes.
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroPaymentMethodCode {

    private static $codeList = array(
        /**
         * VISA
         */
        'VISA_CREDIT_CARD' => 101,
        /**
         * MasterCard
         */
        'MASTERCARD_CREDIT_CARD' => 102,
        /**
         * American Express
         */
        'AMEX_CREDIT_CARD' => 103,
        /**
         * Diners
         */
        'DINERS_CREDIT_CARD' => 104,
        /**
         * Hipercard
         */
        'HIPERCARD_CREDIT_CARD' => 105,
        /**
         * Aura
         */
        'AURA_CREDIT_CARD' => 106,
        /**
         * Elo
         */
        'ELO_CREDIT_CARD' => 107,
        /**
         * PLENOCard
         */
        'PLENOCARD_CREDIT_CARD' => 108,
        /**
         * PersonalCard
         */
        'PERSONALCARD_CREDIT_CARD' => 109,
        /**
         * JCB
         */
        'JCB_CREDIT_CARD' => 110,
        /**
         * Discover
         */
        'DISCOVER_CREDIT_CARD' => 111,
        /**
         * BrasilCard
         */
        'BRASILCARD_CREDIT_CARD' => 112,
        /**
         * FORTBRASIL
         */
        'FORTBRASIL_CREDIT_CARD' => 113,
        /**
         * CARDBAN
         */
        'CARDBAN_CREDIT_CARD' => 114,
        /**
         * VALECARD
         */
        'VALECARD_CREDIT_CARD' => 115,
        /**
         * Cabal
         */
        'CABAL_CREDIT_CARD' => 116,
        /**
         * Mais!
         */
        'MAIS_CREDIT_CARD' => 117,
        /**
         * Avista
         */
        'AVISTA_CREDIT_CARD' => 118,
        /**
         * GranCard
         */
        'GRANDCARD_CREDIT_CARD' => 119,
        /**
         * Bradesco - boleto -  is a form of invoicing in Brazil
         */
        'BRADESCO_BOLETO' => 201,
        /**
         * Santander - boleto -  is a form of invoicing in Brazil
         */
        'SANTANDER_BOLETO' => 202,
        /**
         * Bradesco on-line transfer
         */
        'BRADESCO_ONLINE_TRANSFER' => 301,
        /**
         * Itau on-line transfer
         */
        'ITAU_ONLINE_TRANSFER' => 302,
        /**
         * Unibanco on-line transfer
         */
        'UNIBANCO_ONLINE_TRANSFER' => 303,
        /**
         * Banco do Brasil on-line transfer
         */
        'BANCO_BRASIL_ONLINE_TRANSFER' => 304,
        /**
         * Banco Real on-line transfer
         */
        'REAL_ONLINE_TRANSFER' => 305,
        /**
         * Banrisul on-line transfer
         */
        'BANRISUL_ONLINE_TRANSFER' => 306,
        /**
         * HSBC on-line transfer
         */
        'HSBC_ONLINE_TRANSFER' => 307,
        /**
         * PagSeguro account balance
         */
        'PS_BALANCE' => 401,
        /**
         * OiPaggo
         */
        'OI_PAGGO' => 501,
        /**
         * Banco do Brasil direct deposit
         */
        'BANCO_BRASIL_DIRECT_DEPOSIT' => 701
    );

    /**
     * Payment method code
     * Example: 101
     */
    private $value;

    public function __construct($value = null) {
        if ($value) {
            $this->setValue($value);
        }
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function setByType($type) {
        if (isset(self::$codeList[$type])) {
            $this->value = self::$codeList[$type];
        } else {
            throw new Exception("undefined index $type");
        }
    }

    /**
     * @return integer the payment method code value
     * Example: 101
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param $value
     * @return PagSeguroPaymentMethodCode the corresponding to the informed value
     */
    public function getTypeFromValue($value = null) {
        $value = ($value == null ? $this->value : $value);
        return array_search($value, self::$codeList);
    }
}
