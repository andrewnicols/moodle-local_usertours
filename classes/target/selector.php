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
 * Selector target.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_usertours\target;
use local_usertours\step;

/**
 * Selector target.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class selector extends base {
    /**
     * @inheritdoc
     */
    public function convert_to_css() {
        return $this->step->get_targetvalue();
    }

    /**
     * @inheritdoc
     */
    public function get_displayname() {
        return get_string('selectordisplayname', 'local_usertours', $this->step->get_targetvalue());
    }

    /**
     * @inheritdoc
     */
    public function get_default_title() {
        return get_string('selector_defaulttitle', 'local_usertours');
    }

    /**
     * @inheritdoc
     */
    public function get_default_content() {
        return get_string('selector_defaultcontent', 'local_usertours');
    }

    /**
     * @inheritdoc
     */
    public function add_config_to_form(\MoodleQuickForm $mform) {
        $mform->addElement('text', 'targetvalue', get_string('cssselector', 'local_usertours'));
        $mform->setType('targetvalue', PARAM_RAW);
        $mform->addHelpButton('targetvalue', 'target_selector_targetvalue', 'local_usertours');

        return $this;
    }
}
