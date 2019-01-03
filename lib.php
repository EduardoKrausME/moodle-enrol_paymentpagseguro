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
 * Plugin lib file.
 *
 * @package    enrol_paymentpagseguro
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_paymentpagseguro_plugin extends enrol_plugin {

    /**
     * @param array $instances
     * @return array
     */
    public function get_info_icons(array $instances) {
        return array(new pix_icon('icon', get_string('pluginname', 'enrol_paymentpagseguro'), 'enrol_paymentpagseguro'));
    }

    /**
     * Lists all protected user roles.
     * @return bool(true or false)
     */
    public function roles_protected() {
        return false;
    }

    /**
     * @param stdClass $instance of the plugin
     * @return bool(true or false)
     */
    public function allow_unenrol(stdClass $instance) {
        return true;
    }

    /**
     * @param stdClass $instance of the plugin
     * @return bool(true or false)
     */
    public function allow_manage(stdClass $instance) {
        return true;
    }

    /**
     * @param stdClass $instance of the plugin
     * @return bool(true or false)
     */
    public function show_enrolme_link(stdClass $instance) {
        return ($instance->status == ENROL_INSTANCE_ENABLED);
    }

    /**
     * @param navigation_node $instancesnode
     * @param stdClass $instance
     * @throws coding_exception
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'paymentpagseguro') {
            throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/paymentpagseguro:config', $context)) {
            $managelink = new moodle_url('/enrol/paymentpagseguro/edit.php',
                array('courseid' => $instance->courseid, 'id' => $instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * @param stdClass $instance
     * @return array
     * @throws coding_exception
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'paymentpagseguro') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/paymentpagseguro:config', $context)) {
            $editlink = new moodle_url("/enrol/paymentpagseguro/edit.php",
                array('courseid' => $instance->courseid, 'id' => $instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core',
                array('class' => 'iconsmall')));
        }

        return $icons;
    }

    /**
     * @param int $courseid
     * @return moodle_url|null
     * @throws coding_exception
     */
    public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/paymentpagseguro:config', $context)) {
            return null;
        }

        return new moodle_url('/enrol/paymentpagseguro/edit.php', array('courseid' => $courseid));
    }

    /**
     * @param stdClass $instance
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     */
    public function enrol_page_hook(stdClass $instance) {
        global $CFG, $USER, $OUTPUT, $DB;
        ob_start();

        if ($DB->record_exists('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
            return ob_get_clean();
        }

        if ($instance->enrolenddate != 0 && $instance->enrolenddate < time()) {
            return ob_get_clean();
        }

        $cost = (float)$instance->cost;

        if (abs($cost) < 0.1) {
            echo '<p>' . get_string('nocost', 'enrol_paymentpagseguro') . '</p>';
        } else {

            if (isguestuser()) {
                if (empty($CFG->loginhttps)) {
                    $wwwroot = $CFG->wwwroot;
                } else {
                    $wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
                }
                echo '<div class="mdl-align"><p>' . get_string('paymentrequired') . '</p>';
                echo '<p><a href="' . $wwwroot . '/login/">' . get_string('loginsite') . '</a></p>';
                echo '</div>';
            } else {
                $instancename = $this->get_instance_name($instance);
                $localisedcost = format_float($cost, 2, true);

                if ($instance->customint1 >= 3 && get_config('enrol_paymentpagseguro', 'subscriptions')) {
                    $charge = get_string('costmonthly', 'enrol_paymentpagseguro', $localisedcost);
                    $langbutton = get_string('signforpagseguro', 'enrol_paymentpagseguro');
                } else {
                    $charge = get_string('costunique', 'enrol_paymentpagseguro', $localisedcost);
                    $langbutton = get_string('payforpagseguro', 'enrol_paymentpagseguro');
                }

                echo "
                    <div align=\"center\">
                        <p>" . get_string('requestpayforpagseguro', 'enrol_paymentpagseguro') . "</p>
                        <p><strong>{$instancename}</strong></p>
                        <p><strong>{$charge}</strong></p>
                        <p><img width=\"300\" alt=\"pagseguro\"
                                src=\"{$CFG->wwwroot}/enrol/paymentpagseguro/pix/paymentpagseguro-logo.png\"/></p>
                        <p>&nbsp;</p>
                        <p><a href=\"{$CFG->wwwroot}/enrol/paymentpagseguro/pagar.php?id={$instance->courseid}\"
                              style=\"border-radius:15px;box-shadow:0 1px 3px #666666;color:#ffffff;font-size:20px;
                                      background:#4DB082;padding:10px 20px;text-decoration:none;\">{$langbutton}</a></p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                    </div>";
            }

        }

        return $OUTPUT->box(ob_get_clean());
    }

    /**
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $course
     * @param int $oldid
     * @throws coding_exception
     * @throws dml_exception
     */
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        global $DB;
        if ($instances = $DB->get_records('enrol', array('courseid' => $data->courseid, 'enrol' => 'manual'), 'id')) {
            $instance = reset($instances);
            $instanceid = $instance->id;
        } else {
            $instanceid = $this->add_instance($course, (array)$data);
        }
        $step->set_mapping('enrol', $oldid, $instanceid);
    }

    /**
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $instance
     * @param int $userid
     * @param int $oldinstancestatus
     * @throws coding_exception
     */
    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid, $oldinstancestatus) {
        $this->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, $data->status);
    }

    /**
     * @param course_enrolment_manager $manager
     * @param stdClass $ue
     * @return array
     * @throws coding_exception
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol($instance) && has_capability("enrol/paymentpagseguro:unenrol", $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url,
                array('class' => 'unenrollink', 'rel' => $ue->id));
        }
        if ($this->allow_manage($instance) && has_capability("enrol/paymentpagseguro:manage", $context)) {
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'), $url,
                array('class' => 'editenrollink', 'rel' => $ue->id));
        }
        return $actions;
    }

    /**
     * @param stdClass $instance
     * @return bool
     * @throws coding_exception
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/paymentpagseguro:config', $context);
    }

    /**
     * @param stdClass $instance
     * @return bool
     * @throws coding_exception
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/paymentpagseguro:config', $context);
    }
}
