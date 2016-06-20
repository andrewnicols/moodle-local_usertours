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
 * Block target.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_usertours\target;
use local_usertours\step;

/**
 * Block target.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block extends base {
    /**
     * @inheritdoc
     */
    public function convert_to_css() {
        // The block has the following CSS class selector style:
        // .block-region .block_[name] .
        return sprintf('.block-region .block_%s', $this->step->get_targetvalue());
    }

    /**
     * @inheritdoc
     */
    public function get_displayname() {
        return get_string('block_named', 'local_usertours', $this->get_block_name());
    }

    /**
     * Get the translated name of the block.
     */
    protected function get_block_name() {
        return get_string('pluginname', self::get_frankenstyle($this->step->get_targetvalue()));
    }

    /**
     * Get the frankenstyle name of the block.
     *
     * @param   string  $block  The block name.
     * @return                  The frankenstyle block name.
     */
    protected static function get_frankenstyle($block) {
        return sprintf('block_%s', $block);
    }

    /**
     * @inheritdoc
     */
    public function add_config_to_form(\MoodleQuickForm $mform) {
        global $PAGE;

        $blocks = [];
        foreach ($PAGE->blocks->get_installed_blocks() as $block) {
            $blocks[$block->name] = get_string('pluginname', 'block_' . $block->name);
        }

        $mform->addElement('select', 'targetvalue', get_string('block', 'local_usertours'), $blocks);

        return $this;
    }
}
