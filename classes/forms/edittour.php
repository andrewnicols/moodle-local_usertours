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
 * Form for editing tours.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_usertours\forms;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing tours.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edittour extends \moodleform {
    /**
     * @var local_usertours\tour $tour
     */
    protected $tour;

    /**
     * Create the edit tour form.
     *
     * @param   tour        $tour       The tour being editted.
     */
    public function __construct(\local_usertours\tour $tour) {
        $this->tour = $tour;

        parent::__construct($tour->get_edit_link());
    }

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        // ID of existing tour.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Name of the tour.
        $mform->addElement('text', 'name', get_string('name', 'local_usertours'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        // Admin-only descriptions.
        $mform->addElement('textarea', 'description', get_string('description', 'local_usertours'));
        $mform->setType('description', PARAM_RAW);

        // Application.
        $mform->addElement('text', 'pathmatch', get_string('pathmatch', 'local_usertours'));
        $mform->setType('pathmatch', PARAM_RAW);
        $mform->addHelpButton('pathmatch', 'pathmatch', 'local_usertours');

        $mform->addElement('checkbox', 'enabled', get_string('enabled', 'local_usertours'));

        // Configuration.
        $this->tour->add_config_to_form($mform);

        $this->add_action_buttons();
    }
}
