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
 * Tests for manager.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Tests for step.
 *
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager_testcase extends advanced_testcase {

    /**
     * @var moodle_database
     */
    protected $DB;

    /**
     * Setup to store the DB reference.
     */
    public function setUp() {
        global $DB;

        $this->DB = $DB;
    }

    /**
     * Tear down to restore the original DB reference.
     */
    public function tearDown() {
        global $DB;

        $DB = $this->DB;
    }

    /**
     * Helper to mock the database.
     *
     * @return moodle_database
     */
    public function mock_database() {
        global $DB;

        $DB = $this->getMockBuilder('moodle_database')
            ->getMock()
            ;

        return $DB;
    }

    /**
     * Data provider to ensure that all modification actions require the session key.
     *
     * @return array
     */
    public function sesskey_required_provider() {
        $tourid = rand(1, 100);
        $stepid = rand(1, 100);

        return [
                'Tour removal' => [
                        'delete_tour',
                        [$tourid],
                    ],
                'Step removal' => [
                        'delete_step',
                        [$stepid],
                    ],
                'Tour visibility' => [
                        'show_hide_tour',
                        [$tourid, true],
                    ],
                'Move step' => [
                        'move_step',
                        [$stepid],
                    ],
            ];
    }

    /**
     * Ensure that all modification actions require the session key.
     *
     * @dataProvider sesskey_required_provider
     */
    public function test_sesskey_required($function, $arguments) {
        $manager = new \local_usertours\manager();

        $rc = new \ReflectionClass('\local_usertours\manager');
        $rcm = $rc->getMethod($function);
        $rcm->setAccessible(true);

        $this->setExpectedException('moodle_exception', 'A required parameter (sesskey) was missing');
        $rcm->invokeArgs($manager, $arguments);
    }

    public function get_matching_tours_provider() {
        global $CFG;

        $alltours = [
            [
                    'pathmatch'     => '/my/%',
                    'enabled'       => false,
                    'name'          => 'Failure',
                    'description'   => '',
                    'configdata'    => '',
                ],
            [
                    'pathmatch'     => '/my/%',
                    'enabled'       => true,
                    'name'          => 'My tour enabled',
                    'description'   => '',
                    'configdata'    => '',
                ],
            [
                    'pathmatch'     => '/my/%',
                    'enabled'       => false,
                    'name'          => 'Failure',
                    'description'   => '',
                    'configdata'    => '',
                ],
            [
                    'pathmatch'     => '/course/?id=%foo=bar',
                    'enabled'       => false,
                    'name'          => 'Failure',
                    'description'   => '',
                    'configdata'    => '',
                ],
            [
                    'pathmatch'     => '/course/?id=%foo=bar',
                    'enabled'       => true,
                    'name'          => 'course tour with additional params enabled',
                    'description'   => '',
                    'configdata'    => '',
                ],
            [
                    'pathmatch'     => '/course/?id=%foo=bar',
                    'enabled'       => false,
                    'name'          => 'Failure',
                    'description'   => '',
                    'configdata'    => '',
                ],
            [
                    'pathmatch'     => '/course/?id=%',
                    'enabled'       => false,
                    'name'          => 'Failure',
                    'description'   => '',
                    'configdata'    => '',
                ],
            [
                    'pathmatch'     => '/course/?id=%',
                    'enabled'       => true,
                    'name'          => 'course tour enabled',
                    'description'   => '',
                    'configdata'    => '',
                ],
            [
                    'pathmatch'     => '/course/?id=%',
                    'enabled'       => false,
                    'name'          => 'Failure',
                    'description'   => '',
                    'configdata'    => '',
                ],
        ];

        return [
                'No matches found' => [
                        $alltours,
                        $CFG->wwwroot . '/some/invalid/value',
                        null,
                    ],
                'Never return a disabled tour' => [
                        $alltours,
                        $CFG->wwwroot . '/my/index.php',
                        'My tour enabled',
                    ],
                'My not course' => [
                        $alltours,
                        $CFG->wwwroot . '/my/index.php',
                        'My tour enabled',
                    ],
                'My with params' => [
                        $alltours,
                        $CFG->wwwroot . '/my/index.php?id=42',
                        'My tour enabled',
                    ],
                'Course with params' => [
                        $alltours,
                        $CFG->wwwroot . '/course/?id=42',
                        'course tour enabled',
                    ],
                'Course with params and trailing content' => [
                        $alltours,
                        $CFG->wwwroot . '/course/?id=42&foo=bar',
                        'course tour with additional params enabled',
                    ],
            ];
    }

    /**
     * @dataProvider get_matching_tours_provider
     */
    public function test_get_matching_tours($alltours, $url, $expected) {
        $this->resetAfterTest();

        foreach ($alltours as $tourconfig) {
            $tourconfig = (object) $tourconfig;
            $tourconfig->id = null;
            $tour = \local_usertours\tour::load_from_record($tourconfig, true);
            $tour->persist(true);
        }

        $match = \local_usertours\manager::get_matching_tours(new moodle_url($url));
        if ($expected === null) {
            $this->assertNull($match);
        } else {
            $this->assertEquals($expected, $match->get_name());
        }
    }

    /**
     * @dataProvider get_matching_tours_provider
     */
    public function test_get_matching_tours_requires_upgrade($alltours, $url, $expected) {
        $this->resetAfterTest();

        global $CFG;
        unset($CFG->version);

        foreach ($alltours as $tourconfig) {
            $tourconfig = (object) $tourconfig;
            $tourconfig->id = null;
            $tour = \local_usertours\tour::load_from_record($tourconfig, true);
            $tour->persist(true);
        }

        $this->assertNull(\local_usertours\manager::get_matching_tours(new moodle_url($url)));
    }

}
