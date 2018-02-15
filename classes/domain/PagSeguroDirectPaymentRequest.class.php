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
 * Represents a direct payment request
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroDirectPaymentRequest extends PagSeguroRequest {

    /**
     * Sender hash
     */
    private $senderHash;

    /**
     * Receiver e-mail
     */
    private $receiverEmail;

    /**
     * Billing information associated with this credit card
     */
    private $billing;

    /**
     * Payment mode for this payment request
     */
    private $paymentMode;

    /**
     * Payment method for this payment request
     */
    private $paymentMethod;

    /**
     * Credit Card information associated with this payment request
     */
    private $creditCard;

    /**
     * Bank name information associated with this payment request for online debit
     */
    private $onlineDebit;

    /**
     * Class constructor to make sure the library was initialized.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @return PagSeguroPaymentRequest
     */
    public function getThis() {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSenderHash() {
        return $this->senderHash;
    }

    /**
     * Sets the sender hash
     * @param string $senderHash
     */
    public function setSenderHash($senderHash) {
        $this->senderHash = $senderHash;
    }

    /**
     * @return string the receiverEmail
     */
    public function getReceiverEmail() {
        return $this->receiverEmail;
    }

    /**
     * Sets the receiver email
     * @param string $receiverEmail
     */
    public function setReceiverEmail($receiverEmail) {
        $this->receiverEmail = $receiverEmail;
    }

    /**
     * @return String payment mode for this payment request
     */
    public function getPaymentMode() {
        return $this->paymentMode;
    }

    /**
     * Sets payment mode for this payment request
     * @param string|object $mode
     */
    public function setPaymentMode($mode) {

        try {
            if ($mode instanceof PagSeguroPaymentMode) {
                $this->paymentMode = $mode;
            } else {
                $this->paymentMode = new PagSeguroPaymentMode($mode);
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * @return PagSeguroPaymentMethod payment method for this payment request
     */
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }

    /**
     * Sets payment method for this payment request
     * @param string|object $method
     */
    public function setPaymentMethod($method) {
        try {
            if ($method instanceof PagSeguroDirectPaymentMethods) {
                $this->paymentMethod = $method;
            } else {
                $this->paymentMethod = new PagSeguroDirectPaymentMethods($method);
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Sets the billing address for this payment request
     * @param String $postalCode
     * @param String $street
     * @param String $number
     * @param String $complement
     * @param String $district
     * @param String $city
     * @param String $state
     * @param String $country
     */
    public function setBillingAdress(
        $postalCode,
        $street = null,
        $number = null,
        $complement = null,
        $district = null,
        $city = null,
        $state = null,
        $country = null
    ) {

        $param = $postalCode;
        $this->billing = new PagSeguroBilling();
        if (isset($param) and is_array($param)) {
            $this->billing->setAddress(new PagSeguroAddress($param));
        } else if ($param instanceof PagSeguroAddress) {
            $this->billing->setAddress($param);
        } else {
            $billindAdress = array(
                'postalCode' => $postalCode,
                'street' => $street,
                'number' => $number,
                'complement' => $complement,
                'district' => $district,
                'city' => $city,
                'state' => $state,
                'country' => $country
            );

            $this->billing->setAddress(new PagSeguroAddress($billindAdress));
        }
    }

    /**
     * @return PagSeguroBilling the billing information for this payment request
     * @see PagSeguroBilling
     */
    public function getBillingAdress() {
        return $this->billing;
    }

    /**
     * Sets the info for credit card for this payment request
     * @param array|object $params
     */
    public function setCreditCard($params = null) {

        if ($params instanceof PagSeguroCreditCardCheckout) {
            $this->creditCard = $params;
        } else if (isset($params) && is_array($params)) {
            $this->creditCard = new PagSeguroCreditCardCheckout();
            if (isset($params['token'])) {
                $this->creditCard->setToken($params['token']);
            }
            if (isset($params['installment']) && $params['installment'] instanceof PagSeguroInstallment) {
                $this->creditCard->setInstallment($params['installment']);
            }
            if (isset($params['holder']) && $params['holder'] instanceof PagSeguroCreditCardHolder) {
                $this->creditCard->setHolder($params['holder']);
            }
            if (isset($params['billing']) && $params['billing'] instanceof PagSeguroBilling) {
                $this->creditCard->setBilling($params['billing']);
            }
        }
    }

    /**
     * @return PagSeguroCreditCard the credit card info
     * @see PagSeguroCreditCard
     */
    public function getCreditCard() {
        return $this->creditCard;
    }

    /**
     * @return string the bank name of this payment request for online debit
     */
    public function getOnlineDebit() {
        return $this->onlineDebit;
    }

    /**
     * Sets the bank name of this payment request for online debit
     * @param string|object $bankName
     */
    public function setOnlineDebit($bankName) {

        if ($bankName instanceof PagSeguroOnlineDebitCheckout) {
            $this->onlineDebit = $bankName;
        } else if (is_array($bankName)) {
            $this->onlineDebit = new PagSeguroOnlineDebitCheckout($bankName);
        } else {
            $this->onlineDebit = new PagSeguroOnlineDebitCheckout(
                array(
                    "bankName" => $bankName
                )
            );
        }
    }

    /**
     * Calls the PagSeguro web service and register this request for payment
     *
     * @param PagSeguroCredentials $credentials
     * @return String The URL to where the user needs to be redirected to in order to complete the payment process
     */
    public function register(PagSeguroCredentials $credentials) {
        return PagSeguroDirectPaymentService::checkoutRequest($credentials, $this);
    }
}
