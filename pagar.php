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
 * @package    enrol_paymentpagseguro
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require("../../config.php");
require_once("$CFG->dirroot/enrol/paymentpagseguro/lib.php");

$id = required_param('id', PARAM_INT);

if (!$course = $DB->get_record("course", array("id" => $id))) {
    redirect($CFG->wwwroot);
}

$enrol = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'paymentpagseguro'));

if (!$enrol) {
    print_error('Nenhuma matrícula localizada!');
}

$context = context_course::instance($course->id, MUST_EXIST);
$PAGE->set_context($context);

require_login();

$fullname = format_string($course->fullname, true, array('context' => $context));

require dirname(__FILE__) . '/classes/PagSeguroLibrary.php';

$cost = format_float($enrol->cost, 2, false);
$costlocaled = format_float($enrol->cost, 2, true);

if ($cost < 0.1) {
    print_error('Valor é muito baixo!');
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

    $paymentRequest = new PagSeguroPaymentRequest();
    $paymentRequest->addItem($course->id, $fullname, 1, format_float($enrol->cost, 2, false));
    $paymentRequest->setSender(fullname($USER), $USER->email);
    $paymentRequest->setCurrency("BRL");
    $paymentRequest->setReference("REF{$paymentpagseguro->id}");
    $paymentRequest->setRedirectUrl($CFG->wwwroot);
    $paymentRequest->addParameter("notificationURL", "{$CFG->wwwroot}/enrol/paymentpagseguro/return.php");

    try {
        $credentials = PagSeguroConfig::getAccountCredentials();
        $checkoutUrl = $paymentRequest->register($credentials);


    } catch (Exception $e) {
        print_error($e->getMessage());
    }

    $paymentpagseguro->transactionid = $checkoutUrl;
    $paymentpagseguro->timeupdated = time();
    $DB->update_record("enrol_paymentpagseguro", $paymentpagseguro);

    redirect($checkoutUrl, get_string('paymentinstant'));

} else {

    $paymentPreApproval = new PagSeguroPreApprovalRequest();
    $paymentPreApproval->addItem($course->id, $fullname, 1, $cost);
    $paymentPreApproval->setPreApprovalName("Todo dia " . date('d') . " será cobrado o valor de R\${$costlocaled} referente ao curso {$fullname}");

    $paymentPreApproval->setSender(fullname($USER), $USER->email);
    $paymentPreApproval->setPreApprovalPeriod('Monthly');
    $paymentPreApproval->setPreApprovalAmountPerPayment(format_float($enrol->cost, 2, false));

    $paymentPreApproval->setCurrency("BRL");
    $paymentPreApproval->setReference("REF{$paymentpagseguro->id}");
    $paymentPreApproval->setRedirectUrl($CFG->wwwroot);
    $paymentPreApproval->addParameter("notificationURL", "{$CFG->wwwroot}/enrol/paymentpagseguro/return.php");

    try {
        $credentials = PagSeguroConfig::getAccountCredentials();
        $result = $paymentPreApproval->register($credentials);
    } catch (Exception $e) {
        print_error($e->getMessage());
    }

    $paymentpagseguro->transactionid = $result['checkoutUrl'];
    $paymentpagseguro->timeupdated = time();
    $DB->update_record("enrol_paymentpagseguro", $paymentpagseguro);

    redirect($result['checkoutUrl'], get_string('paymentinstant'));
}



