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
 * Tour table class.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_usertours;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * Tour table class.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tour_table extends \flexible_table {
    /**
     * Construct the tour table.
     */
    public function __construct() {
        parent::__construct('tours');

        $baseurl = new \moodle_url('/tool/usertours/configure.php');
        $this->define_baseurl($baseurl);

        // Column definition.
        $this->define_columns(array(
            'name',
            'description',
            'appliesto',
            'enabled',
            'actions',
        ));

        $this->define_headers(array(
            get_string('name', 'local_usertours'),
            get_string('description', 'local_usertours'),
            get_string('appliesto', 'local_usertours'),
            get_string('enabled', 'local_usertours'),
            get_string('actions', 'local_usertours'),
        ));

        $this->set_attribute('class', 'admintable generaltable');
        $this->setup();

        $this->tourcount = helper::count_tours();
    }

    /**
     * Format the current row's name column.
     *
     * @param   tour    $tour       The tour for this row.
     * @return  string
     */
    protected function col_name(tour $tour) {
        global $OUTPUT;
        return $OUTPUT->render(helper::render_tourname_inplace_editable($tour));
    }

    /**
     * Format the current row's description column.
     *
     * @param   tour    $tour       The tour for this row.
     * @return  string
     */
    protected function col_description(tour $tour) {
        global $OUTPUT;
        return $OUTPUT->render(helper::render_tourdescription_inplace_editable($tour));
    }

    /**
     * Format the current row's appliesto column.
     *
     * @param   tour    $tour       The tour for this row.
     * @return  string
     */
    protected function col_appliesto(tour $tour) {
        return $tour->get_pathmatch();
    }

    /**
     * Format the current row's enabled column.
     *
     * @param   tour    $tour       The tour for this row.
     * @return  string
     */
    protected function col_enabled(tour $tour) {
        // Tour visibility toggle.
        if ($tour->is_enabled()) {
            return helper::format_icon_link(
                    helper::get_show_hide_tour_link($tour->get_id(), 0),
                    't/hide',
                    get_string('disable')
                );
        } else {
            return helper::format_icon_link(
                    helper::get_show_hide_tour_link($tour->get_id(), 1),
                    't/show',
                    get_string('enable')
                );
        }
    }

    /**
     * Format the current row's actions column.
     *
     * @param   tour    $tour       The tour for this row.
     * @return  string
     */
    protected function col_actions(tour $tour) {
        $actions = [];

        if ($tour->is_first_tour()) {
            $actions[] = helper::get_filler_icon();
        } else {
            $actions[] = helper::format_icon_link($tour->get_moveup_link(), 't/up',
                    get_string('movetourup', 'local_usertours'));
        }

        if ($tour->is_last_tour($this->tourcount)) {
            $actions[] = helper::get_filler_icon();
        } else {
            $actions[] = helper::format_icon_link($tour->get_movedown_link(), 't/down',
                    get_string('movetourdown', 'local_usertours'));
        }

        $actions[] = helper::format_icon_link($tour->get_view_link(), 't/viewdetails', get_string('view'));
        $actions[] = helper::format_icon_link($tour->get_edit_link(), 't/edit', get_string('edit'));
        $actions[] = helper::format_icon_link($tour->get_export_link(), 't/export',
                get_string('exporttour', 'local_usertours'), 'local_usertours');
        $actions[] = helper::format_icon_link($tour->get_delete_link(), 't/delete', get_string('delete'), null, [
                'data-action'   => 'delete',
                'data-id'       => $tour->get_id(),
            ]);

        return implode('&nbsp;', $actions);
    }
}
