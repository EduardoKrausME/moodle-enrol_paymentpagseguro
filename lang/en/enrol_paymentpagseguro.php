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
 * Plugin lang file.
 *
 * @package    enrol_paymentpagseguro
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Subscription with payment by Pagseguro';
$string['pluginname_desc'] = 'Free course for payment via Pagseguro!';

$string['paymentpagseguro:config'] = 'Configure the enrollment method of the pagseguro';
$string['paymentpagseguro:manage'] = 'Manage subscribed users';
$string['paymentpagseguro:unenrol'] = 'Disenroll course users';
$string['paymentpagseguro:unenrolself'] = 'Remove me from course';
$string['unenrolselfconfirm'] = 'Do you really want to uninstall from the course "{$ a}"?';

$string['urlretorno'] = 'The return URL is <code>{$a}</code>!';

$string['email'] = 'PagSeguro Email';
$string['email_desc'] = 'PagSeguro Email';

$string['token'] = 'Secure Pagen Token';
$string['token_desc'] = 'Secure Pagen Token';

$string['subscriptions'] = 'Enable signatures on the form';
$string['subscriptions_desc'] = 'If checked, the subscription enables the signatures option.';

$string['status'] = 'Enable signatures?';

$string['expiredaction'] = 'Action upon expiration of enrollment';
$string['expiredaction_help'] = 'What action do you take when the registration expires?';

$string['cost'] = 'Price charged every month';
$string['cost_help'] = 'Amount charged in each month in the monthly payment!';
$string['costerror'] = 'The subscription price must be number';

$string['cost2'] = 'Price for registration';
$string['cost2_help'] = 'Value the user must pay to access the course!';

$string['months'] = 'Number of months';
$string['months_help'] = 'If set to 0 (ZERO) the payment is unique. If you set more than 3 it will be monthly!';
$string['monthserror'] = 'Month must be an integer between 0 and 24!';
$string['monthsmaxerror'] = 'Maximum 24 months';

$string['faulback'] = 'Disable in default?';
$string['faulback_help'] = 'If payment of tuition fails, tuition must be disabled ?!';

$string['enrolperiod'] = 'Duration of enrollment';
$string['enrolperiod_help'] = 'The length of time the subscription is valid, starting at the time the user is enrolled. If disabled, the duration of registration shall be unlimited.';

$string['enrolstartdate'] = 'Start of entries';
$string['enrolstartdate_help'] = 'If enabled, users can only be enrolled as of this date.';

$string['enrolenddate'] = 'Application Deadline';
$string['enrolenddate_help'] = 'If enabled, users can only subscribe by this date.';

$string['defaultrole'] = 'Assign Paper';
$string['defaultrole_help'] = 'Select the role that should be assigned to users during registrations paid via Pagseguro';