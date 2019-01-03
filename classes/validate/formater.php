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

namespace enrol_paymentpagseguro\validate;

defined('MOODLE_INTERNAL') || die();


/**
 * Class formater
 * @package enrol_paymentpagseguro\validate
 */
class formater {
    /**
     * @param $number
     * @return float
     */
    public static function preco_to_float($number) {
        $number = str_replace(",", ".", $number);

        return floatval("0{$number}");
    }

    /**
     * @param $number
     * @return string
     */
    public static function number_formater($number) {
        $number = self::preco_to_float($number);
        return number_format($number, 2, ',', '.');
    }

    /**
     * @param $number
     * @return string
     */
    public static function number_by_punt($number) {
        $number = self::preco_to_float($number);
        return number_format($number, 2, '.', '');
    }

    /**
     * @param $number
     * @return null|string|string[]
     */
    public static function only_number($number) {
        return preg_replace('/[^0-9]/', '', $number);
    }
}