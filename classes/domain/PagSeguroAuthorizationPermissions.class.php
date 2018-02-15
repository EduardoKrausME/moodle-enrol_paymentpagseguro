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
 * Authorization permissions information
 */

defined('MOODLE_INTERNAL') || die();

class PagSeguroAuthorizationPermissions {

    /**
     * Enum: Permission list
     */
    private $permissionList = array(
        "CREATE_CHECKOUTS",
        "RECEIVE_TRANSACTION_NOTIFICATIONS",
        "SEARCH_TRANSACTIONS",
        "MANAGE_PAYMENT_PRE_APPROVALS",
        "DIRECT_PAYMENT",
        "REFUND_TRANSACTIONS",
        "CANCEL_TRANSACTIONS"
    );

    /**
     * @var $permissions
     */
    private $permissions;

    /**
     * Initializes a new instance of the PagSeguroAuthorizationPermissions class
     * @param null|array $permissions
     * @throws string Exception
     */
    public function __construct($permissions = null) {
        if (isset($permissions)) {
            $this->setPermissions($permissions);
        }
    }

    /**
     * @return array of permissions
     */
    public function getPermissions() {
        return $this->permissions;
    }

    /**
     * @param array $permissions
     */
    public function setPermissions($permissions) {
        if (isset($permissions)) {
            $this->permissions = $permissions;
        }
    }
}
