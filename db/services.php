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
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
    'local_usertours_get_targettypes' => array(
        'classname'       => 'local_usertours\external\target',
        'methodname'      => 'get_target_types',
        'description'     => 'Fetch all target types',
        'type'            => 'read',
        'capabilities'    => 'moodle/site:config',
        'ajax'            => true,
    ),

    'local_usertours_set_target' => array(
        'classname'       => 'local_usertours\external\target',
        'methodname'      => 'set_target',
        'description'     => 'Set the target value and fetch the next step',
        'type'            => 'read',
        'capabilities'    => 'moodle/site:config',
        'ajax'            => true,
    ),

    'local_usertours_fetch_tour' => array(
        'classname'       => 'local_usertours\external\tour',
        'methodname'      => 'fetch_tour',
        'description'     => 'Fetch the specified tour',
        'type'            => 'read',
        'capabilities'    => '',
        'ajax'            => true,
    ),

    'local_usertours_complete_tour' => array(
        'classname'       => 'local_usertours\external\tour',
        'methodname'      => 'complete_tour',
        'description'     => 'Mark the specified tour as completed for the current user',
        'type'            => 'write',
        'capabilities'    => '',
        'ajax'            => true,
    ),
    'local_usertours_reset_tour' => array(
        'classname'       => 'local_usertours\external\tour',
        'methodname'      => 'reset_tour',
        'description'     => 'Remove the specified tour',
        'type'            => 'write',
        'capabilities'    => '',
        'ajax'            => true,
    ),
);
