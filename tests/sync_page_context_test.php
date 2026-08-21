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
 * Login-enrol sync leaves $PAGE->context as the request set it.
 *
 * @package    enrol_autoenrol
 * @copyright  2026 NSW Department of Education
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_autoenrol;

/**
 * Login-enrol sync and $PAGE->context.
 *
 * @covers \enrol_autoenrol_plugin::sync_user_enrolments
 */
final class sync_page_context_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        set_config('enrol_plugins_enabled', 'autoenrol');
        set_config('loginenrol', 1, 'enrol_autoenrol');
    }

    /**
     * Add a login-enrol instance. $newenrols is customint4 (0 = closed, 1 = open).
     *
     * @param stdClass $course The course
     * @param int $newenrols Enable new enrols
     * @return void
     */
    private function add_login_instance(\stdClass $course, int $newenrols): void {
        global $DB;

        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
        enrol_get_plugin('autoenrol')->add_instance($course, [
            'status' => ENROL_INSTANCE_ENABLED,
            'roleid' => $studentrole->id,
            'customint1' => 1,
            'customint4' => $newenrols,
        ]);
    }

    /**
     * Closed instance: no enrolment, $PAGE->context unchanged.
     */
    public function test_login_sync_leaves_page_context_when_enrol_refused(): void {
        global $PAGE, $USER;

        $target = $this->getDataGenerator()->create_course();
        $sentinel = $this->getDataGenerator()->create_course();
        $this->add_login_instance($target, 0);

        $PAGE = new \moodle_page();
        $PAGE->set_context(\context_course::instance($sentinel->id));
        $this->setUser($this->getDataGenerator()->create_user());
        enrol_check_plugins($USER);

        $this->assertFalse(is_enrolled(\context_course::instance($target->id), $USER));
        $this->assertEquals(\context_course::instance($sentinel->id)->id, $PAGE->context->id);
    }

    /**
     * Open instance: user is enrolled, $PAGE->context unchanged.
     */
    public function test_login_sync_enrols_user_and_leaves_page_context(): void {
        global $PAGE, $USER;

        $target = $this->getDataGenerator()->create_course();
        $sentinel = $this->getDataGenerator()->create_course();
        $this->add_login_instance($target, 1);

        $PAGE = new \moodle_page();
        $PAGE->set_context(\context_course::instance($sentinel->id));
        $this->setUser($this->getDataGenerator()->create_user());
        enrol_check_plugins($USER);

        $this->assertTrue(is_enrolled(\context_course::instance($target->id), $USER));
        $this->assertEquals(\context_course::instance($sentinel->id)->id, $PAGE->context->id);
    }
}
