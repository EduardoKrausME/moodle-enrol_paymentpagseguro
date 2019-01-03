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
 * Provider Class.
 *
 * @package   enrol_paymentpagseguro
 * @copyright 2018 Eduardo Kraus  {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_paymentpagseguro\payment;

defined('MOODLE_INTERNAL') || die();

use enrol_paymentpagseguro\validate\formater;

/**
 * Class request
 * @package enrol_paymentpagseguro\payment
 */
class request {
    /**
     * @param $course
     * @param $enrol
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \Exception
     */
    public function create_request($course, $enrol) {
        global $DB, $CFG, $USER;

        require_once("{$CFG->dirroot}/enrol/paymentpagseguro/classes/validate/formater.php");
        require_once("$CFG->dirroot/enrol/paymentpagseguro/vendor/pagseguro/PagSeguroLibrary.php");

        if (formater::preco_to_float($enrol->cost) < 1) {
            print_error(get_string('errorlowvalue', 'enrol_paymentpagseguro'));
        }

        $paymentpagseguro = new \stdClass();
        $paymentpagseguro->courseid = $course->id;
        $paymentpagseguro->userid = $USER->id;
        $paymentpagseguro->instanceid = $enrol->id;
        $paymentpagseguro->amount = $enrol->cost;
        $paymentpagseguro->payment_status = 'started';
        $paymentpagseguro->transactionid = '';
        $paymentpagseguro->timeupdated = time();
        $paymentpagseguro->id = $DB->insert_record("enrol_paymentpagseguro", $paymentpagseguro);

        if ($enrol->customint1 >= 3 && get_config('enrol_paymentpagseguro', 'subscriptions')) {
            $this->pagar_mensal($course, $enrol, $paymentpagseguro);
        } else {
            $this->pagar_unico($course, $enrol, $paymentpagseguro);
        }
    }

    /**
     * @param $course
     * @param $enrol
     * @param $paymentpagseguro
     *
     * @throws \Exception
     */
    private function pagar_unico($course, $enrol, $paymentpagseguro) {
        global $CFG, $DB, $USER;

        $request = new \PagSeguroPaymentRequest();
        $request->addItem(
            $course->id,
            $course->fullname,
            1,
            formater::number_by_punt($enrol->cost));
        $request->setSender(fullname($USER), $USER->email);

        $request->setCurrency("BRL");
        $request->setReference("REF{$paymentpagseguro->id}");
        $request->setRedirectUrl($CFG->wwwroot);
        $request->addParameter("notificationURL", "{$CFG->wwwroot}/enrol/paymentpagseguro/return.php");

        try {
            $credentials = \PagSeguroConfig::getAccountCredentials();
            $checkouturl = $request->register($credentials);

            $paymentpagseguro->transactionid = $checkouturl;
            $paymentpagseguro->timeupdated = time();
            $DB->update_record("enrol_paymentpagseguro", $paymentpagseguro);

            redirect($checkouturl, get_string('paymentinstant'));

        } catch (\Exception $e) {
            echo "<div class=\"alert alert-warning\">
                      <i class=\"fa fa-exclamation-circle\"></i>
                      {$e->getMessage()}
                  </div>";

            $paymentpagseguro->transactionid = "error";
            $paymentpagseguro->timeupdated = time();
            $DB->update_record("enrol_paymentpagseguro", $paymentpagseguro);
        }
    }

    /**
     * @param $course
     * @param $enrol
     * @param $paymentpagseguro
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function pagar_mensal($course, $enrol, $paymentpagseguro) {
        global $CFG, $DB, $USER;

        $preapproval = new \PagSeguroPreApprovalRequest();
        $preapproval->addItem(
            $course->id,
            $course->fullname,
            1,
            formater::number_by_punt($enrol->cost));

        $preapproval->setPreApprovalName(
            get_string('paytext', 'enrol_paymentpagseguro',
                array('date' => date('d'),
                    'costlocaled' => format_float($enrol->cost, 2),
                    'fullname' => $course->fullname
                ))
        );

        $preapproval->setSender(fullname($USER), $USER->email);
        $preapproval->setPreApprovalPeriod('Monthly');
        $preapproval->setPreApprovalAmountPerPayment(formater::number_by_punt($enrol->cost));

        $preapproval->setCurrency("BRL");
        $preapproval->setReference("REF{$paymentpagseguro->id}");
        $preapproval->setRedirectUrl($CFG->wwwroot);
        $preapproval->addParameter("notificationURL", "{$CFG->wwwroot}/enrol/paymentpagseguro/return.php");

        try {
            $credentials = \PagSeguroConfig::getAccountCredentials();
            $result = $preapproval->register($credentials);

            $paymentpagseguro->transactionid = $result['checkoutUrl'];
            $paymentpagseguro->timeupdated = time();
            $DB->update_record("enrol_paymentpagseguro", $paymentpagseguro);

            redirect($result['checkoutUrl'], get_string('paymentinstant'));

        } catch (\Exception $e) {
            echo "<div class=\"alert alert-warning\">
                      <i class=\"fa fa-exclamation-circle\"></i>
                      {$e->getMessage()}
                  </div>";

            $paymentpagseguro->transactionid = "error";
            $paymentpagseguro->timeupdated = time();
            $DB->update_record("enrol_paymentpagseguro", $paymentpagseguro);
        }
    }
}