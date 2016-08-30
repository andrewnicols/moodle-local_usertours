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
 * Upgrade code for tours.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the local_usertours plugins.
 *
 * @param int $oldversion The old version of the local_usertours module
 * @return bool
 */
function xmldb_local_usertours_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016052300) {
        // Define field sortorder to be added to usertours_tours.
        $table = new xmldb_table('usertours_tours');
        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'enabled');

        // Conditionally launch add field sortorder.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        \local_usertours\helper::reset_tour_sortorder();

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2016052300, 'local', 'usertours');
    }

    if ($oldversion < 2016052303) {
        // Rename field comment on table usertours_tours to description.
        $table = new xmldb_table('usertours_tours');
        $field = new xmldb_field('comment', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');

        // Launch rename field description.
        $dbman->rename_field($table, $field, 'description');

        // Usertours savepoint reached.
        upgrade_plugin_savepoint(true, 2016052303, 'local', 'usertours');
    }

    return true;
}
