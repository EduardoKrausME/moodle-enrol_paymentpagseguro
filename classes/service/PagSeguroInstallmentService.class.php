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
 * Encapsulates web service calls regarding PagSeguro installment requests
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroInstallmentService {
    /**
     *
     */
    const SERVICE_NAME = 'installmentService';
    /**
     * @var
     */
    private static $connectionData;

    /**
     * @param PagSeguroConnectionData $connectionData
     * @param $amount
     * @param $cardBrand
     * @param $maxInstallmentNoInterest
     * @return string
     */
    private static function buildInstallmentURL(
        PagSeguroConnectionData $connectionData,
        $amount,
        $cardBrand = null,
        $maxInstallmentNoInterest = null
    ) {
        $url = $connectionData->getWebserviceUrl() . $connectionData->getResource('url');
        $stringBuilder = "&amount=" . $amount;
        $stringBuilder .= ($cardBrand == null) ? "" : "&cardBrand=" . $cardBrand;
        $stringBuilder .= ($maxInstallmentNoInterest == null) ? "" :
            "&maxInstallmentNoInterest=" . $maxInstallmentNoInterest;
        return "{$url}/?" . $connectionData->getCredentialsUrlQuery() . $stringBuilder;
    }

    /**
     * Get from webservice installments for direct payment.
     * @param PagSeguroCredentials $credentials
     * @param $amount
     * @param $cardBrand
     * @param $maxInstallmentNoInterest
     * @return bool|PagSeguroInstallment
     * @throws Exception
     * @throws PagSeguroServiceException
     */
    public static function getInstallments(
        PagSeguroCredentials $credentials,
        $amount,
        $cardBrand = null,
        $maxInstallmentNoInterest = null
    ) {
        $amount = PagSeguroHelper::decimalFormat($amount);
        LogPagSeguro::info(
            "PagSeguroInstallmentService.getInstallments(" . $amount . ") - begin"
        );
        self::$connectionData = new PagSeguroConnectionData($credentials, self::SERVICE_NAME);

        try {
            $connection = new PagSeguroHttpConnection();
            $connection->get(
                self::buildInstallmentURL(self::$connectionData, $amount, $cardBrand, $maxInstallmentNoInterest),
                self::$connectionData->getServiceTimeout(),
                self::$connectionData->getCharset()
            );

            $httpStatus = new PagSeguroHttpStatus($connection->getStatus());

            switch ($httpStatus->getType()) {
                case 'OK':
                    $installments = PagSeguroInstallmentParser::readInstallments($connection->getResponse());
                    LogPagSeguro::info(
                        "PagSeguroInstallmentService.getInstallments() - end "
                    );
                    break;
                case 'BAD_REQUEST':
                    $errors = PagSeguroInstallmentParser::readErrors($connection->getResponse());
                    $e = new PagSeguroServiceException($httpStatus, $errors);
                    LogPagSeguro::error(
                        "PagSeguroInstallmentService.getInstallments() - error " .
                        $e->getOneLineMessage()
                    );
                    throw $e;
                    break;
                default:
                    $e = new PagSeguroServiceException($httpStatus);
                    LogPagSeguro::error(
                        "PagSeguroInstallmentService.getInstallments() - error " .
                        $e->getOneLineMessage()
                    );
                    throw $e;
                    break;

            }
            return (isset($installments) ? $installments : false);

        } catch (PagSeguroServiceException $e) {
            throw $e;
        } catch (Exception $e) {
            LogPagSeguro::error("Exception: " . $e->getMessage());
            throw $e;
        }
    }
}
