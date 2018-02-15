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
 * Encapsulates web service calls regarding PagSeguro payment requests
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroDirectPaymentService {

    /**
     * @var $connectionData
     */
    private static $connectionData;

    /**
     *
     */
    const SERVICE_NAME = 'directPaymentService';

    /**
     * @param PagSeguroConnectionData $connectionData
     * @return string
     */
    private static function buildCheckoutRequestUrl(PagSeguroConnectionData $connectionData) {
        return $connectionData->getServiceUrl() . '/?' . $connectionData->getCredentialsUrlQuery();
    }

    /**
     * @param PagSeguroConnectionData $connectionData
     * @param $code
     * @return string
     */
    private static function buildReturnUrl(PagSeguroConnectionData $connectionData, $code) {
        return $connectionData->getServiceUrl() . '/' . $code . '/?' . $connectionData->getCredentialsUrlQuery();
    }

    /**
     * checkoutRequest is the actual implementation of the Register method
     * This separation serves as test hook to validate the Uri
     * against the code returned by the service
     * @param PagSeguroCredentials $credentials
     * @param PagSeguroDirectrequest $request
     * @return bool|string
     * @throws Exception|PagSeguroServiceException
     * @throws Exception
     */
    public static function checkoutRequest(
        PagSeguroCredentials $credentials,
        PagSeguroDirectPaymentRequest $request
    ) {

        LogPagSeguro::info("PagSeguroDirectPaymentService.Register(" . $request->toString() . ") - begin");

        $connectionData = new PagSeguroConnectionData($credentials, self::SERVICE_NAME);

        try {
            $connection = new PagSeguroHttpConnection();
            $connection->post(
                self::buildCheckoutRequestUrl($connectionData),
                PagSeguroDirectPaymentParser::getData($request),
                $connectionData->getServiceTimeout(),
                $connectionData->getCharset()
            );

            return self::getResult($connection, $request);

        } catch (PagSeguroServiceException $error) {
            throw $error;
        } catch (Exception $error) {
            LogPagSeguro::error("Exception: " . $error->getMessage());
            throw $error;
        }
    }

    /**
     * @param $connection
     * @param PagSeguroDirectPaymentRequest $request
     * @return null|PagSeguroParserData
     * @throws PagSeguroServiceException
     */
    private static function getResult($connection, PagSeguroDirectPaymentRequest $request) {
        $httpStatus = new PagSeguroHttpStatus($connection->getStatus());

        switch ($httpStatus->getType()) {
            case 'OK':
                $paymentReturn = PagSeguroTransactionParser::readTransaction($connection->getResponse());

                LogPagSeguro::info(
                    "PagSeguroDirectPaymentService.Register(" . $request->toString() . ") - end {1}" .
                    $paymentReturn->getCode()
                );
                break;
            case 'BAD_REQUEST':
                $errors = PagSeguroTransactionParser::readErrors($connection->getResponse());
                $error = new PagSeguroServiceException($httpStatus, $errors);
                LogPagSeguro::error(
                    "PagSeguroDirectPaymentService.Register(" . $request->toString() . ") - error " .
                    $error->getOneLineMessage()
                );
                throw $error;
                break;
            default:
                $error = new PagSeguroServiceException($httpStatus);
                LogPagSeguro::error(
                    "PagSeguroDirectPaymentService.Register(" . $request->toString() . ") - error " .
                    $error->getOneLineMessage()
                );
                throw $error;
                break;
        }
        return (isset($paymentReturn) ? $paymentReturn : false);
    }
}
