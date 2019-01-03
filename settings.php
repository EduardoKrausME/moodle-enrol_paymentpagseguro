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
 * Plugin Setting file.
 *
 * @package    enrol_paymentpagseguro
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('enrol_paymentpagseguro_settings', '',
        get_string('pluginname_desc', 'enrol_paymentpagseguro')));

    $settings->add(new admin_setting_configtext('enrol_paymentpagseguro/email',
        get_string('email', 'enrol_paymentpagseguro'),
        get_string('email_desc', 'enrol_paymentpagseguro')
        , '', PARAM_RAW));

    $settings->add(new admin_setting_configtext('enrol_paymentpagseguro/token',
        get_string('token', 'enrol_paymentpagseguro'),
        get_string('token_desc', 'enrol_paymentpagseguro'),
        '', PARAM_RAW));

    $settings->add(new admin_setting_configcheckbox('enrol_paymentpagseguro/subscriptions',
        get_string('subscriptions', 'enrol_paymentpagseguro'),
        get_string('subscriptions_desc', 'enrol_paymentpagseguro')
        , 0));

    $options = array(
        ENROL_EXT_REMOVED_KEEP => get_string('extremovedkeep', 'enrol'),
        ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'),
        ENROL_EXT_REMOVED_UNENROL => get_string('extremovedunenrol', 'enrol'),
    );
    $settings->add(new admin_setting_configselect('enrol_paymentpagseguro/expiredaction',
        get_string('expiredaction', 'enrol_paymentpagseguro'),
        get_string('expiredaction_help', 'enrol_paymentpagseguro'), ENROL_EXT_REMOVED_SUSPENDNOROLES, $options));
}
