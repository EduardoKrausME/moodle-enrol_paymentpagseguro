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

require_once("../../config.php");
require_once("{$CFG->dirroot}/enrol/paymentpagseguro/lib.php");

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

require_once("{$CFG->dirroot}/enrol/paymentpagseguro/classes/payment/request.php");

$request = new \enrol_paymentpagseguro\payment\request();
$request->create_request($course, $enrol);
