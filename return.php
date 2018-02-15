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
require(dirname(__FILE__) . '/classes/PagSeguroLibrary.php');

admin_externalpage_setup('returpaymentpagseguro');

$notification = optional_param('notificationType', false, PARAM_RAW);

if ($notification) {
    if ($notification == 'transaction') {
        proccess_transaction();
    } else if ($notification == 'preApproval') {
        proccess_preapproval();
    }
}

die('API de retorno do PagSeguro!');

/**
 * Processa os pagaments por mensalidades.
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function proccess_preapproval() {

    global $DB;

    $notificationcode = optional_param('notificationCode', false, PARAM_RAW);

    if (!$notificationcode) {
        die('API de retorno do PagSeguro!');
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
         * 1 - Aguardando pagamento: o comprador iniciou a transação, mas até o momento o PagSeguro não recebeu nenhuma informação sobre o pagamento.
         * 2 - Em análise: o comprador optou por pagar com um cartão de crédito e o PagSeguro está analisando o risco da transação.
         * 3 - Paga: a transação foi paga pelo comprador e o PagSeguro já recebeu uma confirmação da instituição financeira responsável pelo processamento.
         * 4 - Disponível: a transação foi paga e chegou ao final de seu prazo de liberação sem ter sido retornada e sem que haja nenhuma disputa aberta.
         * 5 - Em disputa: o comprador, dentro do prazo de liberação da transação, abriu uma disputa.
         * 6 - Devolvida: o valor da transação foi devolvido para o comprador.
         * 7 - Cancelada: a transação foi cancelada sem ter sido finalizada.
         *
         * 3 - libera a matrícula
         * 6 - remove a matrícula
         */
        if ($response->getStatus()->getValue() == 3) {
            add_matricula($plugininstance, $paymentpagseguro);
        } else if ($response->getStatus()->getValue() == 6) {
            remove_matricula($plugininstance, $paymentpagseguro);
        }
    }
}

/**
 * Processa os pagamento únicos.
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function proccess_transaction() {

    global $DB;

    $notificationcode = optional_param('notificationCode', false, PARAM_RAW);

    if (!$notificationcode) {
        die('API de retorno do PagSeguro!');
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
            add_matricula($plugininstance, $paymentpagseguro);
        } else if ($response->getStatus()->getValue() == 6) {
            remove_matricula($plugininstance, $paymentpagseguro);
        }
    }
}

/**
 * Add a Matrícula
 *
 * @param $plugininstance
 * @param $paymentpagseguro
 * @throws coding_exception
 */
function add_matricula($plugininstance, $paymentpagseguro) {

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
function remove_matricula($plugininstance, $paymentpagseguro) {
    if ($plugininstance->customint2) {
        $plugin = enrol_get_plugin('paymentpagseguro');
        $plugin->enrol_user($plugininstance, $paymentpagseguro->userid,
            $plugininstance->roleid, time(), 0, ENROL_USER_SUSPENDED);
    }
}