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


/**
 * Class notification
 * @package enrol_paymentpagseguro\payment
 */
class notification {

    /**
     * @param $notification
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function proccess($notificationtype) {
        global $CFG;

        require_once("$CFG->dirroot/enrol/paymentpagseguro/vendor/pagseguro/PagSeguroLibrary.php");

        if ($notificationtype == 'transaction') {
            $this->proccess_transaction();
        }

        if ($notificationtype == 'preApproval') {
            $this->proccess_preapproval();
        }
    }

    /**
     * Processes single payments.
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function proccess_transaction() {

        global $DB;

        $notificationcode = optional_param('notificationCode', false, PARAM_RAW);

        if (!$notificationcode) {
            die(get_string('errorapi', 'enrol_paymentpagseguro'));
        }

        try {
            $response = \PagSeguroNotificationService::checkTransaction(
                \PagSeguroConfig::getAccountCredentials(),
                $notificationcode
            );
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        $paymentpagseguroid = str_replace("REF", "", $response->getReference());

        $paymentpagseguro = $DB->get_record('enrol_paymentpagseguro', array('id' => $paymentpagseguroid));
        if ($paymentpagseguro) {

            $plugininstance = $DB->get_record("enrol", array("id" => $paymentpagseguro->instanceid, "status" => 0));

            if ($response->getStatus()->getValue() == 3) {
                $this->add_enrollment($plugininstance, $paymentpagseguro);
            } else if ($response->getStatus()->getValue() == 6) {
                $this->remove_enrollment($plugininstance, $paymentpagseguro);
            }
        }
    }

    /**
     * Process payments for monthly payments.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function proccess_preapproval() {

        global $DB;

        $notificationcode = optional_param('notificationCode', false, PARAM_RAW);

        if (!$notificationcode) {
            die(get_string('errorapi', 'enrol_paymentpagseguro'));
        }

        try {
            $response = \PagSeguroNotificationService::checkPreApproval(
                \PagSeguroConfig::getAccountCredentials(),
                $notificationcode
            );
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        $paymentpagseguroid = str_replace("REF", "", $response->getReference());

        $paymentpagseguro = $DB->get_record('enrol_paymentpagseguro', array('id' => $paymentpagseguroid));
        if ($paymentpagseguro) {

            $plugininstance = $DB->get_record("enrol", array("id" => $paymentpagseguro->instanceid, "status" => 0));

            /*
             * Status:
             * 1 - Awaiting payment.
             * 2 - Under review.
             * 3 - Pay.
             * 4 - Available.
             * 5 - In dispute.
             * 6 - Returned.
             * 7 - Canceled.
             * Actions:
             * 3 - releases registration.
             * 6 - remove the license plate.
             */
            if ($response->getStatus()->getValue() == 3) {
                // Expira na quantidade de meses de mensalidade.
                $plugininstance->enrolperiod = strtotime("+{$plugininstance->customint1} Month");

                $this->add_enrollment($plugininstance, $paymentpagseguro);
            } else if ($response->getStatus()->getValue() == 6) {
                $this->remove_enrollment($plugininstance, $paymentpagseguro);
            }
        }
    }

    /**
     * Add enrol_user
     *
     * @param $plugininstance
     * @param $paymentpagseguro
     *
     * @throws \coding_exception
     */
    private function add_enrollment($plugininstance, $paymentpagseguro) {
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
     * Remove a enrol_user
     *
     * @param $plugininstance
     * @param $paymentpagseguro
     *
     * @throws \coding_exception
     */
    private function remove_enrollment($plugininstance, $paymentpagseguro) {
        if ($plugininstance->customint2) {
            $plugin = enrol_get_plugin('paymentpagseguro');
            $plugin->enrol_user($plugininstance, $paymentpagseguro->userid,
                $plugininstance->roleid, time(), 0, ENROL_USER_SUSPENDED);
        }
    }

}