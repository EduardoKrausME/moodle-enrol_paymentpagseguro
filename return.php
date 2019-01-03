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

require_once("../../config.php");
require_once("{$CFG->libdir}/adminlib.php");

$pageurl = new moodle_url('/enrol/paymentpagseguro/return.php');
$PAGE->set_url($pageurl);

$notificationtype = optional_param('notificationType', false, PARAM_RAW);

if ($notificationtype) {
    require_once("{$CFG->dirroot}/enrol/paymentpagseguro/classes/payment/notification.php");

    $notification = new \enrol_paymentpagseguro\payment\notification();
    $notification->proccess($notificationtype);
}

die(get_string('errorapi', 'enrol_paymentpagseguro'));
