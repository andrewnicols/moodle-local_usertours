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
 * The local_usertours step_shown event.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_usertours\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The local_usertours step_shown event.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int       tourid:     The id of the tour
 *      - string    pageurl:    The URL of the page viewing the tour
 * }
 */
class step_shown extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'usertours_steps';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_step_shown', 'local_usertours');
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['tourid'])) {
            throw new \coding_exception('The \'tourid\' value must be set in other.');
        }

        if (!isset($this->other['stepindex'])) {
            throw new \coding_exception('The \'stepindex\' value must be set in other.');
        }

        if (!isset($this->other['pageurl'])) {
            throw new \coding_exception('The \'pageurl\' value must be set in other.');
        }
    }

    public static function get_other_mapping() {
        return [
            'pageurl'   => \core\event\base::NOT_MAPPED,
            'tourid'    => [
                'db'        => 'usertours_tours',
                'restore'   => \core\event\base::NOT_MAPPED,
            ],
            'stepindex' => \core\event\base::NOT_MAPPED,
        ];
    }

    public static function get_objectid_mapping() {
        return [
            'db'        => 'usertours_steps',
            'restore'   => \core\event\base::NOT_MAPPED,
        ];
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '{$this->userid}' has viewed the tour with id " .
            "'{$this->other['tourid']}' at step index " .
            "'{$this->other['stepindex']}' (id '{$this->objectid}') on the page with URL " .
            "'{$this->other['pageurl']}'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return \local_usertours\helper::get_edit_step_link($this->other['tourid'], $this->objectid);
    }
}
