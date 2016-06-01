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
 * A step designed to be orphaned.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_usertours\target;
use local_usertours\step;

/**
 * A step designed to be orphaned.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unattached extends base {
    /**
     * @var     array       $forcedsettings The settings forced by this type.
     */
    protected $forcedsettings = [
            'placement'     => 'top',
            'orphan'        => true,
            'reflex'        => false,
        ];

    /**
     * @inheritdoc
     */
    public function convert_to_css() {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function get_displayname() {
        return get_string('target_unattached', 'local_usertours');
    }

    /**
     * @inheritdoc
     */
    public function add_config_to_form(\MoodleQuickForm $mform) {
        // There is no relevant value here.
        $mform->addElement('hidden', 'targetvalue', '');
        $mform->setType('targetvalue', PARAM_TEXT);

        return $this;
    }
}
