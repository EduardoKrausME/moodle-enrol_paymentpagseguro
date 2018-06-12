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
 * Plugin pagar file.
 *
 * @package    enrol_paymentpagseguro
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require("../../config.php");
require("$CFG->dirroot/enrol/paymentpagseguro/lib.php");

$id = required_param('id', PARAM_INT);

if (!$course = $DB->get_record("course", array("id" => $id))) {
    redirect($CFG->wwwroot);
}

$enrol = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'paymentpagseguro'));

if (!$enrol) {
    print_error(get_string('errornoenrolment', 'enrol_paymentpagseguro'));
}

$context = context_course::instance($course->id, MUST_EXIST);
$PAGE->set_context($context);

require_login();

$fullname = format_string($course->fullname, true, array('context' => $context));

require(dirname(__FILE__) . '/vendor/pagseguro/PagSeguroLibrary.php');

$cost = format_float($enrol->cost, 2, false);
$costlocaled = format_float($enrol->cost, 2, true);

if ($cost < 0.1) {
    print_error(get_string('errorlowvalue', 'enrol_paymentpagseguro'));
}

$paymentpagseguro = new stdClass();
$paymentpagseguro->courseid = $id;
$paymentpagseguro->userid = $USER->id;
$paymentpagseguro->instanceid = $enrol->id;
$paymentpagseguro->amount = $cost;
$paymentpagseguro->payment_status = 'started';
$paymentpagseguro->transactionid = '';
$paymentpagseguro->timeupdated = time();
$paymentpagseguro->id = $DB->insert_record("enrol_paymentpagseguro", $paymentpagseguro);

if ($enrol->customint1 < 3) {

    $paymentrequest = new PagSeguroPaymentRequest();
    $paymentrequest->addItem($course->id, $fullname, 1, format_float($enrol->cost, 2, false));
    $paymentrequest->setSender(fullname($USER), $USER->email);
    $paymentrequest->setCurrency("BRL");
    $paymentrequest->setReference("REF{$paymentpagseguro->id}");
    $paymentrequest->setRedirectUrl($CFG->wwwroot);
    $paymentrequest->addParameter("notificationURL", "{$CFG->wwwroot}/enrol/paymentpagseguro/return.php");

    try {
        $credentials = PagSeguroConfig::getAccountCredentials();
        $checkouturl = $paymentrequest->register($credentials);

    } catch (Exception $e) {
        print_error($e->getMessage());
    }

    $paymentpagseguro->transactionid = $checkouturl;
    $paymentpagseguro->timeupdated = time();
    $DB->update_record("enrol_paymentpagseguro", $paymentpagseguro);

    redirect($checkouturl, get_string('paymentinstant'));

} else {

    $paymentpreapproval = new PagSeguroPreApprovalRequest();
    $paymentpreapproval->addItem($course->id, $fullname, 1, $cost);
    $paymentpreapproval->setPreApprovalName(
        get_string('paytext', 'enrol_paymentpagseguro',
            array('date' => date('d'),
                'costlocaled' => $costlocaled,
                'fullname' => $fullname
            ))
    );

    $paymentpreapproval->setSender(fullname($USER), $USER->email);
    $paymentpreapproval->setPreApprovalPeriod('Monthly');
    $paymentpreapproval->setPreApprovalAmountPerPayment(format_float($enrol->cost, 2, false));

    $paymentpreapproval->setCurrency("BRL");
    $paymentpreapproval->setReference("REF{$paymentpagseguro->id}");
    $paymentpreapproval->setRedirectUrl($CFG->wwwroot);
    $paymentpreapproval->addParameter("notificationURL", "{$CFG->wwwroot}/enrol/paymentpagseguro/return.php");

    try {
        $credentials = PagSeguroConfig::getAccountCredentials();
        $result = $paymentpreapproval->register($credentials);
    } catch (Exception $e) {
        print_error($e->getMessage());
    }

    $paymentpagseguro->transactionid = $result['checkoutUrl'];
    $paymentpagseguro->timeupdated = time();
    $DB->update_record("enrol_paymentpagseguro", $paymentpagseguro);

    redirect($result['checkoutUrl'], get_string('paymentinstant'));
}



