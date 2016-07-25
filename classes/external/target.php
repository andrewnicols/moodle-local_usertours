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
 * Web Service functions for targets.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_usertours\external;

use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;

/**
 * Web Service functions for targets.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class target extends external_api {

    /**
     * Get the list of available target types.
     *
     * @return  array               As described in get_target_types_returns.
     */
    public static function get_target_types() {
        global $PAGE;

        $PAGE->set_context(\context_system::instance());
        self::validate_context($PAGE->context);

        $types = \local_usertours\target::get_target_types();
        $result = [];
        foreach ($types as $value => $type) {
            $result[] = (object) [
                'type'      => $type,
                'title'     => get_string('target_' . $type, 'local_usertours'),
            ];
        }

        return $result;
    }

    /**
     * The parameters for get_target_types.
     *
     * @return external_function_parameters
     */
    public static function get_target_types_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * The return configuration for get_target_types_parameters.
     *
     * @return external_single_structure
     */
    public static function get_target_types_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'type'      => new external_value(PARAM_ALPHA, 'Type of the target'),
                'title'     => new external_value(PARAM_TEXT, 'Title of the target'),
            ])
        );
    }

    /**
     * Set the selected target type and get the next page link.
     *
     * @param   int     $tourid     The ID of the tour.
     * @param   string  $typename   The target type.
     * @return  array               As described in get_target_types_returns.
     */
    public static function set_target($tourid, $typename) {
        global $PAGE;

        $params = self::validate_parameters(self::set_target_parameters(), [
                    'tourid'        => $tourid,
                    'targettype'    => $typename,
                ]);

        $PAGE->set_context(\context_system::instance());
        self::validate_context($PAGE->context);

        $targettype = \local_usertours\target::get_target_constant($typename);
        return [
            'redirectTo'    => \local_usertours\helper::get_new_step_link($tourid, $targettype)->out(false),
        ];
    }

    /**
     * The parameters for get_target_types.
     *
     * @return external_function_parameters
     */
    public static function set_target_parameters() {
        return new external_function_parameters([
            'tourid'        => new external_value(PARAM_INT, 'Tour ID'),
            'targettype'    => new external_value(PARAM_ALPHANUMEXT, 'Type of Target'),
        ]);
    }

    /**
     * The return configuration for set_target.
     *
     * @return external_single_structure
     */
    public static function set_target_returns() {
        return new external_single_structure([
            'redirectTo'    => new external_value(PARAM_URL, 'URL to redirect to'),
        ]);
    }
}
