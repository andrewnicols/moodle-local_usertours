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
 * Tour Step Renderable.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_usertours\output;
use local_usertours\step as stepsource;

/**
 * Tour Step Renderable.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class step implements \renderable {

    /**
     * @var The step instance.
     */
    protected $step;

    /**
     * The step output.
     *
     * @param   step            $step       The step being output.
     */
    public function __construct(stepsource $step) {
        $this->step = $step;
    }

    /**
     * Export the step configuration.
     *
     * @param   renderer_base   $output     The renderer.
     * @return  object
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;
        $step = $this->step;
        $title = external_format_text(
                    static::get_string_from_input($step->get_title()),
                    FORMAT_HTML,
                    $PAGE->context->id,
                    'local_usertours',
                    null,
                    null,
                    [
                        'filter' => true,
                    ]
                )[0];
        $result =  (object) [
            'stepid'    => $step->get_id(),
            'title'     => external_format_string($title, $PAGE->context->id),
            'content'   => external_format_text(
                    static::get_string_from_input($step->get_content()),
                    FORMAT_HTML,
                    $PAGE->context->id,
                    'local_usertours',
                    null,
                    null,
                    [
                        'filter' => true,
                    ]
                )[0],
            'element'   => $step->get_target()->convert_to_css(),
        ];

        $result->content = str_replace("\n", "<br>\n", $result->content);

        foreach ($step->get_config_keys() as $key) {
            $result->$key = $step->get_config($key);
        }

        return $result;
    }

    /**
     * Attempt to fetch any matching langstring if the string is in the
     * format identifier,component.
     *
     * @param   string  $string
     * @return  string
     */
    protected static function get_string_from_input($string) {
        $string = trim($string);

        if (preg_match('|^([a-zA-Z][a-zA-Z0-9\.:/_-]*),([a-zA-Z][a-zA-Z0-9\.:/_-]*)$|', $string, $matches)) {
            if ($matches[2] === 'moodle') {
                $matches[2] = 'core';
            }

            if (get_string_manager()->string_exists($matches[1], $matches[2])) {
                $string = get_string($matches[1], $matches[2]);
            }
        }

        return $string;
    }
}
