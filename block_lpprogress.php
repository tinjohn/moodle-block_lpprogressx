<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Block lpprogress is defined here.
 *
 * @package     block_lpprogress
 * @copyright   2021 Daniel Neis Araujo <daniel@adapta.online>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * lpprogress block.
 *
 * @package    block_lpprogress
 * @copyright  2021 Daniel Neis Araujo <daniel@adapta.online>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_lpprogress extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_lpprogress');
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {
        global $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        if (!get_config('core_competency', 'enabled')) {
            return $this->content;
        }

        try {
            $plans = core_competency\api::list_user_plans($USER->id);
        } catch (required_capability_exception $e) {
            $plans = [];
        }

        $renderer = $this->page->get_renderer('block_lpprogress');
        $renderable = new \block_lpprogress\output\lpprogress($plans);

        $this->content = (object) [
            'text' => $renderer->render($renderable),
            'footer' => ''
        ];

        return $this->content;
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediatly after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_lpprogress');
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * Sets the applicable formats for the block.
     *
     * @return string[] Array of pages and permissions.
     */
    public function applicable_formats() {
        return array(
            'all' => true,
        );
    }
}
