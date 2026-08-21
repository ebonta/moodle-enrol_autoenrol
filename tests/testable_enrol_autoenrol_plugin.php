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
 * Autoenrol enrolment testable class.
 *
 * @package    enrol_autoenrol
 * @copyright  2026 LightMoon Projects
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_autoenrol;

use context_course;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/enrol/autoenrol/lib.php');

/**
 * Testable autoenrol plugin wrapper.
 */
class testable_enrol_autoenrol_plugin extends \enrol_autoenrol_plugin {
    /** @var int */
    public $welcomecallcount = 0;

    /**
     * Preserve the production enrolment plugin name.
     *
     * @return string
     */
    public function get_name() {
        return 'autoenrol';
    }

    /**
     * Expose the protected welcome sender for tests.
     *
     * @param stdClass $instance
     * @param stdClass $user
     * @return void
     */
    public function send_welcome_message(stdClass $instance, stdClass $user): void {
        $this->email_welcome_message($instance, $user);
    }

    /**
     * Count calls while preserving the production implementation.
     *
     * @param stdClass $instance
     * @param stdClass $user
     * @return void
     */
    protected function email_welcome_message($instance, $user) {
        $this->welcomecallcount++;
        parent::email_welcome_message($instance, $user);
    }
}
