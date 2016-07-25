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
 * Web Service functions for steps.
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
use \local_usertours\tour as tourinstance;

/**
 * Web Service functions for steps.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tour extends external_api {
    /**
     * Fetch the tour configuration for the specified tour.
     *
     * @param   int     $tourid     The ID of the tour to fetch.
     * @param   int     $context    The Context ID of the current page.
     * @return  array               As described in fetch_tour_returns
     */
    public static function fetch_tour($tourid, $context) {
        global $PAGE;

        $context = \context_helper::instance_by_id($context);
        self::validate_context($context);

        $params = self::validate_parameters(self::fetch_tour_parameters(), [
                    'tourid'    => $tourid,
                    'context'   => $context->id,
                ]);

        $tour = tourinstance::instance($params['tourid']);
        if (!$tour->should_show_for_user()) {
            return [];
        }

        $touroutput = new \local_usertours\output\tour($tour);

        return [
            'tourConfig' => $touroutput->export_for_template($PAGE->get_renderer('core')),
        ];
    }

    /**
     * The parameters for fetch_tour.
     *
     * @return external_function_parameters
     */
    public static function fetch_tour_parameters() {
        return new external_function_parameters([
            'tourid' => new external_value(PARAM_INT, 'Tour ID'),
            'context' => new external_value(PARAM_INT, 'Context ID'),
        ]);
    }

    /**
     * The return configuration for fetch_tour.
     *
     * @return external_single_structure
     */
    public static function fetch_tour_returns() {
        return new external_single_structure([
            'tourConfig'    => new external_single_structure([
                'name'      => new external_value(PARAM_RAW, 'Tour ID'),
                'steps'     => new external_multiple_structure(
                    new external_single_structure([
                        'title'             => new external_value(PARAM_TEXT,
                                'Step Title'),
                        'content'           => new external_value(PARAM_RAW,
                                'Step Content'),
                        'element'           => new external_value(PARAM_TEXT,
                                'Step Target'),
                        'placement'         => new external_value(PARAM_TEXT,
                                'Step Placement'),
                        'delay'             => new external_value(PARAM_INT,
                                'Delay before showing the step (ms)', VALUE_OPTIONAL),
                        'backdrop'          => new external_value(PARAM_BOOL,
                                'Whether a backdrop should be used', VALUE_OPTIONAL),
                        'reflex'            => new external_value(PARAM_BOOL,
                                'Whether to move to the next step when the target element is clicked', VALUE_OPTIONAL),
                        'orphan'            => new external_value(PARAM_BOOL,
                                'Whether to display the step even if it could not be found', VALUE_OPTIONAL),
                    ])
                ),
            ])
        ]);
    }

    /**
     * Reset the specified tour for the current user.
     *
     * @param   string  $path       The path of the current page requesting the reset.
     * @param   int     $tourid     The ID of the tour.
     * @return  array               As described in reset_tour_returns
     */
    public static function reset_tour($path, $tourid) {
        global $PAGE;
        $PAGE->set_context(\context_system::instance());
        self::validate_context($PAGE->context);

        $tour = tourinstance::instance($tourid);
        $tour->request_user_reset();

        $result = [];

        if ($tourinstance = \local_usertours\manager::get_matching_tours(new \moodle_url($path))) {
            if ($tour->get_id() === $tourinstance->get_id()) {
                $result['startTour'] = $tour->get_id();
            }
        }

        return $result;
    }

    /**
     * The parameters for reset_tour.
     *
     * @return external_function_parameters
     */
    public static function reset_tour_parameters() {
        return new external_function_parameters([
            'path'      => new external_value(PARAM_URL, 'Current page location'),
            'tourid'    => new external_value(PARAM_INT, 'Tour ID'),
        ]);
    }

    /**
     * The return configuration for reset_tour.
     *
     * @return external_single_structure
     */
    public static function reset_tour_returns() {
        return new external_single_structure([
            'startTour'     => new external_value(PARAM_INT, 'Tour ID', VALUE_OPTIONAL),
        ]);
    }

    /**
     * Mark the specified tour as completed for the current user.
     *
     * @param   int     $tourid     The ID of the tour.
     * @return  array               As described in complete_tour_returns
     */
    public static function complete_tour($tourid) {
        global $PAGE;
        $PAGE->set_context(\context_system::instance());
        self::validate_context($PAGE->context);
        require_login();

        $tour = tourinstance::instance($tourid);
        $tour->mark_user_completed();

        return [];
    }

    /**
     * The parameters for complete_tour f.
     *
     * @return external_function_parameters
     */
    public static function complete_tour_parameters() {
        return new external_function_parameters([
            'tourid' => new external_value(PARAM_INT, 'Tour ID'),
        ]);
    }

    /**
     * The return configuration for complete_tour.
     *
     * @return external_single_structure
     */
    public static function complete_tour_returns() {
        return new external_single_structure([]);
    }
}
