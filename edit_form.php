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
 * Block Game configuration form definition
 *
 * @package     block_lpprogressx
 * @copyright   2023 Tina John <johnt.22.tijo@gmail.com> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/game/lib.php');

/**
 *  Block Game config form definition class
 *
 * @package    block_lpprogressx
 * @copyright  2023 Tina John <johnt.22.tijo@gmailcom>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_lpprogressx_edit_form extends block_edit_form {

    /**
     * Block Game form definition
     *
     * @param mixed $mform
     * @return void
     */
    protected function specific_definition($mform) {
        global $COURSE, $OUTPUT;

        // Start block specific section in config form.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

            if (has_capability('moodle/site:config', context_system::instance())) {
                // Game block instance alternate title.
                $mform->addElement('text', 'config_title', get_string('config_title', 'block_lpprogressx'));
                $mform->setDefault('config_title', '');
                $mform->setType('config_title', PARAM_MULTILANG);
                $mform->addHelpButton('config_title', 'config_title', 'block_lpprogressx');

                // in sidebar 
                $mform->addElement('selectyesno', 'config_insidebar', get_string('insidebar', 'block_lpprogressx'));
                $mform->setDefault('config_insidebar', 1);
                $mform->addHelpButton('insidebar_help', 'config_insidebar', 'block_lpprogressx');

            }
    }

}
