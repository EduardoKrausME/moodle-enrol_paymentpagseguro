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
 * Plugin return file.
 *
 * @package    enrol_paymentpagseguro
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require("../../config.php");
require(dirname(__FILE__) . '/vendor/pagseguro/PagSeguroLibrary.php');

admin_externalpage_setup('returpaymentpagseguro');

$notification = optional_param('notificationType', false, PARAM_RAW);

if ($notification) {
    if ($notification == 'transaction') {
        proccess_transaction();
    } else if ($notification == 'preApproval') {
        proccess_preapproval();
    }
}

die(get_string('errorapi', 'enrol_paymentpagseguro'));

/**
 * Process payments for monthly payments.
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function proccess_preapproval() {

    global $DB;

    $notificationcode = optional_param('notificationCode', false, PARAM_RAW);

    if (!$notificationcode) {
        die(get_string('errorapi', 'enrol_paymentpagseguro'));
    }

    try {
        $response = PagSeguroNotificationService::checkPreApproval(
            PagSeguroConfig::getAccountCredentials(),
            $notificationcode
        );
    } catch (Exception $e) {
        die($e->getMessage());
    }

    $paymentpagseguroid = str_replace("REF", "", $response->getReference());

    $paymentpagseguro = $DB->get_record('enrol_paymentpagseguro', array('id' => $paymentpagseguroid));
    if ($paymentpagseguro) {

        $plugininstance = $DB->get_record("enrol", array("id" => $paymentpagseguro->instanceid, "status" => 0));
        $plugininstance->enrolperiod = strtotime("+{$plugininstance->customint1} Month"); // Expira na quantidade de meses

         /*
          * Status:
          * 1 - Awaiting payment: the buyer started the transaction, but so far PagSeguro has not received any payment information.
          * 2 - Under review: the buyer has chosen to pay with a credit card and PagSeguro is analyzing the risk of the transaction.
          * 3 - Pay: the transaction was paid by the buyer and PagSeguro has already received a confirmation from the financial institution responsible for the processing.
          * 4 - Available: The transaction has been paid and has reached the end of its release period without being returned and without any open dispute.
          * 5 - In dispute: the buyer, within the term of release of the transaction, opened a dispute.
          * 6 - Returned: The transaction amount was returned to the buyer.
          * 7 - Canceled: the transaction was canceled without being finalized.
          *
          * 3 - releases registration.
          * 6 - remove the license plate.
          */
        if ($response->getStatus()->getValue() == 3) {
            add_enrollment($plugininstance, $paymentpagseguro);
        } else if ($response->getStatus()->getValue() == 6) {
            remove_enrollment($plugininstance, $paymentpagseguro);
        }
    }
}

/**
 * Processes single payments.
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function proccess_transaction() {

    global $DB;

    $notificationcode = optional_param('notificationCode', false, PARAM_RAW);

    if (!$notificationcode) {
        die(get_string('errorapi', 'enrol_paymentpagseguro'));
    }

    try {
        $response = PagSeguroNotificationService::checkTransaction(
            PagSeguroConfig::getAccountCredentials(),
            $notificationcode
        );
    } catch (Exception $e) {
        die($e->getMessage());
    }

    $paymentpagseguroid = str_replace("REF", "", $response->getReference());

    $paymentpagseguro = $DB->get_record('enrol_paymentpagseguro', array('id' => $paymentpagseguroid));
    if ($paymentpagseguro) {

        $plugininstance = $DB->get_record("enrol", array("id" => $paymentpagseguro->instanceid, "status" => 0));

        if ($response->getStatus()->getValue() == 3) {
            add_enrollment($plugininstance, $paymentpagseguro);
        } else if ($response->getStatus()->getValue() == 6) {
            remove_enrollment($plugininstance, $paymentpagseguro);
        }
    }
}

/**
 * Add to Cart
 *
 * @param $plugininstance
 * @param $paymentpagseguro
 * @throws coding_exception
 */
function add_enrollment($plugininstance, $paymentpagseguro) {

    if ($plugininstance->enrolperiod) {
        $timeend = time() + $plugininstance->enrolperiod;
    } else {
        $timeend = 0;
    }

    $plugin = enrol_get_plugin('paymentpagseguro');
    $plugin->enrol_user($plugininstance, $paymentpagseguro->userid,
        $plugininstance->roleid, time(), $timeend, ENROL_USER_ACTIVE);
}

/**
 * Remove a Matrícula
 *
 * @param $plugininstance
 * @param $paymentpagseguro
 * @throws coding_exception
 */
function remove_enrollment($plugininstance, $paymentpagseguro) {
    if ($plugininstance->customint2) {
        $plugin = enrol_get_plugin('paymentpagseguro');
        $plugin->enrol_user($plugininstance, $paymentpagseguro->userid,
            $plugininstance->roleid, time(), 0, ENROL_USER_SUSPENDED);
    }
}