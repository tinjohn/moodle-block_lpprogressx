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
 * Class containing data for lpprogress block.
 *
 * @package     block_lpprogress
 * @copyright   2021 Daniel Neis Araujo <daniel@adapta.online>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_lpprogress\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use core_competency\api;
use core_competency\plan;
use core_competency\external\performance_helper;
use core_competency\external\competency_exporter;
use core_competency\external\plan_exporter;

/**
 * Class containing data for timeline block.
 *
 * @copyright  Daniel Neis Araujo <daniel@adapta.online>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lpprogress implements renderable, templatable {

    /**
     * lpprogress constructor.
     *
     * @param array $plans The list of learning plans
     */
    public function __construct(array $plans) {
        $this->plans = $plans;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $plans = [];
        foreach ($this->plans as $plan) {
            if (($plan->get('status') == plan::STATUS_ACTIVE) || ($plan->get('status') == plan::STATUS_COMPLETE)) {
                $plans[] = $plan;
            }
        }
        $helper = new performance_helper();
        $activeplans = array();
        foreach ($plans as $plan) {
            $planexporter = new plan_exporter($plan, array('template' => $plan->get_template()));
            $currentplan = $planexporter->export($output);

            if ($currentplan->iscompleted) {
                $ucproperty = 'usercompetencyplan';
                $ucexporter = 'core_competency\\external\\user_competency_plan_exporter';
            } else {
                $ucproperty = 'usercompetency';
                $ucexporter = 'core_competency\\external\\user_competency_exporter';
            }
            $pclist = api::list_plan_competencies($plan);
            foreach ($pclist as $pc) {
                $comp = $pc->competency;
                $usercomp = $pc->$ucproperty;

                $compcontext = $helper->get_context_from_competency($comp);
                $framework = $helper->get_framework_from_competency($comp);
                $scale = $helper->get_scale_from_competency($comp);

                // Prepare the data.
                $record = new \stdClass();
                $exporter = new competency_exporter($comp, array('context' => $compcontext));
                $record->competency = $exporter->export($output);

                $exporter = new $ucexporter($usercomp, array('scale' => $scale));
                $record->$ucproperty = $exporter->export($output);

                if ($currentplan->iscompleted) {
                    $record->isproficient = $record->usercompetencyplan->proficiency;
                } else {
                    $record->isproficient = $record->usercompetency->proficiency;
                }
                $currentplan->competencies[] = $record;
            }

            $activeplans[] = $currentplan;
        }
        return ['plans' => $activeplans];
    }
}
