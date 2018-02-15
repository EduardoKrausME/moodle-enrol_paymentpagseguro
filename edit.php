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
 * Plugin edit file.
 *
 * @package    enrol_paymentpagseguro
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('edit_form.php');

$courseid = required_param('courseid', PARAM_INT);
$instanceid = optional_param('id', 0, PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('enrol/paymentpagseguro:config', $context);

$PAGE->set_url('/enrol/paymentpagseguro/edit.php', array('courseid' => $course->id, 'id' => $instanceid));
$PAGE->set_pagelayout('admin');

$return = new moodle_url('/enrol/instances.php', array('id' => $course->id));
if (!enrol_is_enabled('paymentpagseguro')) {
    redirect($return);
}

$plugin = enrol_get_plugin('paymentpagseguro');

if ($instanceid) {
    $instance = $DB->get_record('enrol',
        array('courseid' => $course->id, 'enrol' => 'paymentpagseguro', 'id' => $instanceid),
        '*', MUST_EXIST);
    $instance->cost = format_float($instance->cost, 2, true);
} else {
    require_capability('moodle/course:enrolconfig', $context);
    // No instance yet, we have to add new instance.
    navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id' => $course->id)));
    $instance = new stdClass();
    $instance->id = null;
    $instance->courseid = $course->id;
}

$mform = new enrol_paymentpagseguro_edit_form(null, array($instance, $plugin, $context));

if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {

    $instance->status = $data->status;
    $instance->name = $data->name;
    $instance->cost = unformat_float($data->cost);
    $instance->roleid = $data->roleid;
    $instance->enrolstartdate = $data->enrolstartdate;
    $instance->enrolenddate = $data->enrolenddate;
    $instance->timemodified = time();
    $instance->customint1 = $data->customint1;
    $instance->customint2 = $data->customint2;

    if ($instance->id) {
        $reset = ($instance->status != $data->status);

        $plugin->update_instance($instance, $instance);

        if ($reset) {
            $context->mark_dirty();
        }

    } else {
        $fields = json_decode(json_encode($instance), true);
        $plugin->add_instance($course, $fields);
    }

    redirect($return);
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'enrol_paymentpagseguro'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'enrol_paymentpagseguro'));
$mform->display();
echo $OUTPUT->footer();
