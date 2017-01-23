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
 * Tour manager.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_usertours;

/**
 * Tour manager.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * @var ACTION_LISTTOURS      The action to get the list of tours.
     */
    const ACTION_LISTTOURS = 'listtours';

    /**
     * @var ACTION_NEWTOUR        The action to create a new tour.
     */
    const ACTION_NEWTOUR = 'newtour';

    /**
     * @var ACTION_EDITTOUR       The action to edit the tour.
     */
    const ACTION_EDITTOUR = 'edittour';

    /**
     * @var ACTION_MOVETOUR The action to move a tour up or down.
     */
    const ACTION_MOVETOUR = 'movetour';

    /**
     * @var ACTION_EXPORTTOUR     The action to export the tour.
     */
    const ACTION_EXPORTTOUR = 'exporttour';

    /**
     * @var ACTION_IMPORTTOUR     The action to import the tour.
     */
    const ACTION_IMPORTTOUR = 'importtour';

    /**
     * @var ACTION_DELETETOUR     The action to delete the tour.
     */
    const ACTION_DELETETOUR = 'deletetour';

    /**
     * @var ACTION_VIEWTOUR       The action to view the tour.
     */
    const ACTION_VIEWTOUR = 'viewtour';

    /**
     * @var ACTION_NEWSTEP The action to create a new step.
     */
    const ACTION_NEWSTEP = 'newstep';

    /**
     * @var ACTION_EDITSTEP The action to edit step configuration.
     */
    const ACTION_EDITSTEP = 'editstep';

    /**
     * @var ACTION_MOVESTEP The action to move a step up or down.
     */
    const ACTION_MOVESTEP = 'movestep';

    /**
     * @var ACTION_DELETESTEP The action to delete a step.
     */
    const ACTION_DELETESTEP = 'deletestep';

    /**
     * @var ACTION_VIEWSTEP The action to view a step.
     */
    const ACTION_VIEWSTEP = 'viewstep';

    /**
     * @var ACTION_HIDETOUR The action to hide a tour.
     */
    const ACTION_HIDETOUR = 'hidetour';

    /**
     * @var ACTION_SHOWTOUR The action to show a tour.
     */
    const ACTION_SHOWTOUR = 'showtour';

    /**
     * This is the entry point for this controller class.
     *
     * @param   string  $action     The action to perform.
     */
    public function execute($action) {
        admin_externalpage_setup('local_usertours/tours');
        // Add the main content.
        switch($action) {
            case self::ACTION_NEWTOUR:
            case self::ACTION_EDITTOUR:
                $this->edit_tour(optional_param('id', null, PARAM_INT));
                break;

            case self::ACTION_MOVETOUR:
                $this->move_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_EXPORTTOUR:
                $this->export_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_IMPORTTOUR:
                $this->import_tour();
                break;

            case self::ACTION_VIEWTOUR:
                $this->view_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_HIDETOUR:
                $this->hide_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_SHOWTOUR:
                $this->show_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_DELETETOUR:
                $this->delete_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_NEWSTEP:
            case self::ACTION_EDITSTEP:
                $this->edit_step(optional_param('id', null, PARAM_INT));
                break;

            case self::ACTION_MOVESTEP:
                $this->move_step(required_param('id', PARAM_INT));
                break;

            case self::ACTION_DELETESTEP:
                $this->delete_step(required_param('id', PARAM_INT));
                break;

            case self::ACTION_LISTTOURS:
            default:
                $this->print_tour_list();
                break;
        }
    }

    /**
     * Print out the page header.
     *
     * @param   string  $title     The title to display.
     */
    protected function header($title = null) {
        global $OUTPUT;

        // Print the page heading.
        echo $OUTPUT->header();

        if ($title === null) {
            $title = get_string('tours', 'local_usertours');
        }

        echo $OUTPUT->heading($title);
    }

    /**
     * Print out the page footer.
     *
     * @return void
     */
    protected function footer() {
        global $OUTPUT;

        echo $OUTPUT->footer();
    }

    /**
     * Print the the list of tours.
     */
    protected function print_tour_list() {
        global $PAGE, $OUTPUT;

        $this->header();
        echo \html_writer::span(get_string('tourlist_explanation', 'local_usertours'));
        $table = new tour_table();
        $tours = helper::get_tours();
        foreach ($tours as $tour) {
            $table->add_data_keyed($table->format_row($tour));
        }

        $table->finish_output();
        $actions = [
            (object) [
                'link'  => helper::get_edit_tour_link(),
                'linkproperties' => [],
                'img'   => 'b/tour-new',
                'title' => get_string('newtour', 'local_usertours'),
            ],
            (object) [
                'link'  => helper::get_import_tour_link(),
                'linkproperties' => [],
                'img'   => 'b/tour-import',
                'title' => get_string('importtour', 'local_usertours'),
            ],
            (object) [
                'link'  => new \moodle_url('https://moodle.net/mod/data/view.php', [
                        'id' => 17,
                    ]),
                'linkproperties' => [
                        'target' => '_blank',
                    ],
                'img'   => 'b/tour-shared',
                'title' => get_string('sharedtourslink', 'local_usertours'),
            ],
        ];

        echo \html_writer::start_tag('div', [
                'class' => 'tour-actions',
            ]);

        echo \html_writer::start_tag('ul');
            foreach ($actions as $config) {
                $action = \html_writer::start_tag('li');
                $linkproperties = $config->linkproperties;
                $linkproperties['href'] = $config->link;
                $action .= \html_writer::start_tag('a', $linkproperties);
                $action .= \html_writer::img(
                    $OUTPUT->pix_url($config->img, 'local_usertours'),
                    $config->title);
                $action .= \html_writer::div($config->title);
                $action .= \html_writer::end_tag('a');
                $action .= \html_writer::end_tag('li');
                echo $action;
            }
        echo \html_writer::end_tag('ul');
        echo \html_writer::end_tag('div');

        // JS for Tour management.
        $PAGE->requires->js_call_amd('local_usertours/managetours', 'setup');
        $this->footer();
    }

    /**
     * Return the edit tour link.
     *
     * @param   int         $id     The ID of the tour
     * @return string
     */
    protected function get_edit_tour_link($id = null) {
        $addlink = helper::get_edit_tour_link($id);
        return \html_writer::link($addlink, get_string('newtour', 'local_usertours'));
    }

    /**
     * Print the edit tour link.
     *
     * @param   int         $id     The ID of the tour
     */
    protected function print_edit_tour_link($id = null) {
        echo $this->get_edit_tour_link($id);
    }

    /**
     * Get the import tour link.
     *
     * @return string
     */
    protected function get_import_tour_link() {
        $importlink = helper::get_import_tour_link();
        return \html_writer::link($importlink, get_string('importtour', 'local_usertours'));
    }

    /**
     * Print the edit tour page.
     *
     * @param   int         $id     The ID of the tour
     */
    protected function edit_tour($id = null) {
        global $PAGE;
        if ($id) {
            $tour = tour::instance($id);
            $PAGE->navbar->add($tour->get_name(), $tour->get_edit_link());

        } else {
            $tour = new tour();
            $PAGE->navbar->add(get_string('newtour', 'local_usertours'), $tour->get_edit_link());
        }

        $form = new forms\edittour($tour);

        if ($form->is_cancelled()) {
            redirect(helper::get_list_tour_link());
        } else if ($data = $form->get_data()) {
            // Creating a new tour.
            $tour
                ->set_name($data->name)
                ->set_description($data->description)
                ->set_pathmatch($data->pathmatch)
                ->set_enabled(!empty($data->enabled))
                ;

            foreach (configuration::get_defaultable_keys() as $key) {
                $tour->set_config($key, $data->$key);
            }

            $tour->persist();

            redirect(helper::get_list_tour_link());
        } else {
            if (empty($tour)) {
                $this->header('newtour');
            } else {
                $this->header($tour->get_name());
                $form->set_data($tour->prepare_data_for_form());
            }

            $form->display();
            $this->footer();
        }
    }

    /**
     * Print the export tour page.
     *
     * @param   int         $id     The ID of the tour
     */
    protected function export_tour($id) {
        $tour = tour::instance($id);

        // Grab the full data record.
        $export = $tour->to_record();

        // Remove the id.
        unset($export->id);

        // Set the version.
        $export->version = get_config('local_usertours', 'version');

        // Step export.
        $export->steps = [];
        foreach ($tour->get_steps() as $step) {
            $record = $step->to_record();
            unset($record->id);
            unset($record->tourid);

            $export->steps[] = $record;
        }

        $exportstring = json_encode($export);

        $filename = 'tour_export_' . $tour->get_id() . '_' . time() . '.json';

        // Force download.
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: ' . gmdate('D, d M Y H:i:s', 0) . 'GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
        header('Content-disposition: attachment; filename=' . $filename);
        header('Content-length: ' . strlen($exportstring));
        header('Content-type: text/calendar; charset=utf-8');

        echo $exportstring;
        die;
    }

    /**
     * Handle tour import.
     */
    protected function import_tour() {
        global $PAGE;
        $PAGE->navbar->add(get_string('importtour', 'local_usertours'), helper::get_import_tour_link());

        $form = new forms\importtour();

        if ($form->is_cancelled()) {
            redirect(helper::get_list_tour_link());
        } else if ($form->get_data()) {
            // Importing a tour.
            $tourconfigraw = $form->get_file_content('tourconfig');
            $tour = self::import_tour_from_json($tourconfigraw);

            redirect($tour->get_view_link());
        } else {
            $this->header();
            $form->display();
            $this->footer();
        }
    }

    /**
     * Print the view tour page.
     *
     * @param   int         $tourid     The ID of the tour to display.
     */
    protected function view_tour($tourid) {
        global $PAGE;
        $tour = helper::get_tour($tourid);

        $PAGE->navbar->add($tour->get_name(), $tour->get_view_link());

        $this->header($tour->get_name());
        echo \html_writer::span(get_string('viewtour_info', 'local_usertours', [
                'tourname'  => $tour->get_name(),
                'path'      => $tour->get_pathmatch(),
            ]));

        $table = new step_table($tourid);
        foreach ($tour->get_steps() as $step) {
            $table->add_data_keyed($table->format_row($step));
        }

        $table->finish_output();
        $this->print_edit_step_link($tourid);

        // JS for Step management.
        $PAGE->requires->js_call_amd('local_usertours/managesteps', 'setup', array('tourid' => $tourid));

        $this->footer();
    }

    /**
     * Show the tour.
     *
     * @param   int         $tourid     The ID of the tour to display.
     */
    protected function show_tour($tourid) {
        $this->show_hide_tour($tourid, 1);
    }

    /**
     * Hide the tour.
     *
     * @param   int         $tourid     The ID of the tour to display.
     */
    protected function hide_tour($tourid) {
        $this->show_hide_tour($tourid, 0);
    }

    /**
     * Show or Hide the tour.
     *
     * @param   int         $tourid     The ID of the tour to display.
     * @param   int         $visibility The intended visibility.
     */
    protected function show_hide_tour($tourid, $visibility) {
        global $DB;

        require_sesskey();

        $tour = $DB->get_record('usertours_tours', array('id' => $tourid));
        $tour->enabled = $visibility;
        $DB->update_record('usertours_tours', $tour);

        redirect(helper::get_list_tour_link());
    }

    /**
     * Delete the tour.
     *
     * @param   int         $tourid     The ID of the tour to remove.
     */
    protected function delete_tour($tourid) {
        require_sesskey();

        $tour = tour::instance($tourid);
        $tour->remove();

        redirect(helper::get_list_tour_link());
    }

    /**
     * Get the first tour matching the current page URL.
     *
     * @return  tour
     */
    public static function get_current_tour($reset = false) {
        global $PAGE;

        static $tour = false;

        if ($tour === false || $reset) {
            $tour = self::get_matching_tours($PAGE->url);
        }

        return $tour;
    }

    /**
     * Get the first tour matching the specified URL.
     *
     * @param   moodle_url  $pageurl        The URL to match.
     * @return  tour
     */
    public static function get_matching_tours(\moodle_url $pageurl) {
        global $DB;

        // Do not show tours whilst upgrades are pending.
        if (moodle_needs_upgrading()) {
            return null;
        }

        $sql = <<<EOF
            SELECT * FROM {usertours_tours}
             WHERE enabled = 1
               AND ? LIKE pathmatch
          ORDER BY sortorder ASC
EOF;

        $localurl = $pageurl->out_as_local_url();
        $usertour = optional_param('usertour', '', PARAM_ALPHANUMEXT);
        $localurl .= '&amp;usertour='.$usertour;
        $tours = $DB->get_records_sql($sql, array(
            $localurl,
        ));

        foreach ($tours as $record) {
            $tour = tour::load_from_record($record);
            if ($tour->is_enabled()) {
                return $tour;
            }
        }

        return null;
    }

    /**
     * Import the provided tour JSON.
     *
     * @param   string      $json           The tour configuration.
     * @return  tour
     */
    public static function import_tour_from_json($json) {
        $tourconfig = json_decode($json);

        // We do not use this yet - we may do in the future.
        unset($tourconfig->version);

        $steps = $tourconfig->steps;
        unset($tourconfig->steps);

        $tourconfig->id = null;
        $tourconfig->sortorder = null;
        $tour = tour::load_from_record($tourconfig, true);
        $tour->persist(true);

        // Ensure that steps are orderered by their sortorder.
        \core_collator::asort_objects_by_property($steps, 'sortorder', \core_collator::SORT_NUMERIC);

        foreach ($steps as $stepconfig) {
            $stepconfig->id = null;
            $stepconfig->tourid = $tour->get_id();
            $step = step::load_from_record($stepconfig, true);
            $step->persist(true);
        }

        return $tour;
    }

    /**
     * Helper to fetch the renderer.
     *
     * @return  renderer
     */
    protected function get_renderer() {
        global $PAGE;
        return $PAGE->get_renderer('local_usertours');
    }

    /**
     * Print the edit step link.
     *
     * @param   int     $tourid     The ID of the tour.
     * @param   int     $stepid     The ID of the step.
     * @return  string
     */
    protected function print_edit_step_link($tourid, $stepid = null) {
        $addlink = helper::get_edit_step_link($tourid, $stepid);
        $attributes = [];
        if (empty($stepid)) {
            $attributes['class'] = 'createstep';
        }
        echo \html_writer::link($addlink, get_string('newstep', 'local_usertours'), $attributes);
    }

    /**
     * Display the edit step form for the specified step.
     *
     * @param   int     $id     The step to edit.
     */
    protected function edit_step($id) {
        global $PAGE;

        if (isset($id)) {
            $step = step::instance($id);
        } else {
            $step = new step();
            $step
                ->set_tourid(required_param('tourid', PARAM_INT))
                ->set_targettype(required_param('targettype', PARAM_INT))
                ;
        }

        $tour = $step->get_tour();
        $PAGE->navbar->add($tour->get_name(), $tour->get_view_link());
        if (isset($id)) {
            $PAGE->navbar->add($step->get_title(), $step->get_edit_link());
        } else {
            $PAGE->navbar->add(get_string('newstep', 'local_usertours'), $step->get_edit_link());
        }

        $form = new forms\editstep($step->get_edit_link(), $step);
        if ($form->is_cancelled()) {
            redirect($step->get_tour()->get_view_link());
        } else if ($data = $form->get_data()) {
            $step->handle_form_submission($form, $data);
            $step->get_tour()->reset_step_sortorder();
            redirect($step->get_tour()->get_view_link());
        } else {
            if (empty($id)) {
                $this->header(get_string('newstep', 'local_usertours'));
            } else {
                $this->header(get_string('editstep', 'local_usertours', $step->get_title()));
            }
            $form->set_data($step->prepare_data_for_form());

            $form->display();
            $this->footer();
        }
    }

    /**
     * Move a tour up or down.
     *
     * @param   int     $id     The tour to move.
     */
    protected function move_tour($id) {
        require_sesskey();

        $direction = required_param('direction', PARAM_INT);

        $tour = tour::instance($id);
        $currentsortorder   = $tour->get_sortorder();
        $targetsortorder    = $currentsortorder + $direction;

        $swapwith = helper::get_tour_from_sortorder($targetsortorder);

        // Set the sort order to something out of the way.
        $tour
            ->set_sortorder(-1)
            ->persist()
            ;

        // Swap the two sort orders.
        $swapwith
            ->set_sortorder($currentsortorder)
            ->persist()
            ;

        $tour->set_sortorder($targetsortorder)
            ->persist()
            ;

        redirect(helper::get_list_tour_link());
    }

    /**
     * Move a step up or down.
     *
     * @param   int     $id     The step to move.
     */
    protected function move_step($id) {
        require_sesskey();

        $direction = required_param('direction', PARAM_INT);

        $step = step::instance($id);
        $currentsortorder   = $step->get_sortorder();
        $targetsortorder    = $currentsortorder + $direction;

        $tour = $step->get_tour();
        $swapwith = helper::get_step_from_sortorder($tour->get_id(), $targetsortorder);

        // Set the sort order to something out of the way.
        $step
            ->set_sortorder(-1)
            ->persist()
            ;

        // Swap the two sort orders.
        $swapwith
            ->set_sortorder($currentsortorder)
            ->persist()
            ;

        $step->set_sortorder($targetsortorder)
            ->persist()
            ;

        // Reset the sort order.
        $tour->reset_step_sortorder();
        redirect($tour->get_view_link());
    }

    /**
     * Delete the step.
     *
     * @param   int         $stepid     The ID of the step to remove.
     */
    protected function delete_step($stepid) {
        require_sesskey();

        $step = step::instance($stepid);
        $tour = $step->get_tour();

        $step->remove();
        redirect($tour->get_view_link());
    }
}
