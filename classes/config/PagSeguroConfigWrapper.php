<?php
/**
 * 2007-2014 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 *Licensed under the Apache License, Version 2.0 (the "License");
 *you may not use this file except in compliance with the License.
 *You may obtain a copy of the License at
 *
 *http://www.apache.org/licenses/LICENSE-2.0
 *
 *Unless required by applicable law or agreed to in writing, software
 *distributed under the License is distributed on an "AS IS" BASIS,
 *WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *See the License for the specific language governing permissions and
 *limitations under the License.
 *
 * @author    PagSeguro Internet Ltda.
 * @copyright 2007-2014 PagSeguro Internet Ltda.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

class PagSeguroConfigWrapper {

    /**
     * @return array
     */
    public static function getConfig() {
        $PagSeguroConfig = array();

        $PagSeguroConfig = array_merge_recursive(
            self::getEnvironment(),
            self::getCredentials(),
            self::getApplicationEncoding(),
            self::getLogConfig()
        );

        return $PagSeguroConfig;
    }

    /**
     * @return mixed
     */
    private static function getEnvironment() {
        $PagSeguroConfig['environment'] = 'production';

        return $PagSeguroConfig;
    }

    /**
     * @return array
     */
    private static function getCredentials() {
        $PagSeguroConfig = array();
        $PagSeguroConfig['credentials'] = array();
        $PagSeguroConfig['credentials']['email'] = get_config('enrol_paymentpagseguro', 'email');
        $PagSeguroConfig['credentials']['token']['production'] = get_config('enrol_paymentpagseguro', 'token');
        $PagSeguroConfig['credentials']['token']['sandbox'] = getenv('PAGSEGURO_TOKEN_SANDBOX');
        $PagSeguroConfig['credentials']['appId']['production'] = getenv('PAGSEGURO_APP_ID_PRODUCTION');
        $PagSeguroConfig['credentials']['appId']['sandbox'] = getenv('PAGSEGURO_APP_ID_SANDBOX');
        $PagSeguroConfig['credentials']['appKey']['production'] = getenv('PAGSEGURO_APP_KEY_PRODUCTION');
        $PagSeguroConfig['credentials']['appKey']['sandbox'] = getenv('PAGSEGURO_APP_KEY_SANDBOX');

        return $PagSeguroConfig;
    }

    /**
     * @return mixed
     */
    private static function getApplicationEncoding() {
        $PagSeguroConfig['application'] = array();
        $PagSeguroConfig['application']['charset'] = 'UTF-8';

        return $PagSeguroConfig;
    }

    /**
     * @return mixed
     */
    private static function getLogConfig() {
        $PagSeguroConfig['log'] = array();
        $PagSeguroConfig['log']['active'] = false;
        $PagSeguroConfig['log']['fileLocation'] = '';

        return $PagSeguroConfig;
    }
}
