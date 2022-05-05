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
 * External Web Service.
 *
 * @package    local_quiz_dates
 * @copyright  2021 Marc Leconte
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . "/externallib.php");

/**
 * Definition of web services.
 *
 * @package    local_quiz_dates
 * @copyright  2021 Marc Leconte
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_getquizdates_external extends external_api {

    /**
     * Define the description of the web service parameters.
     * @return the description of the web service parameters.
     */
    public static function get_quiz_dates_parameters() {
        $newbegtxt = 'Identifiant du quiz';
        return new external_function_parameters(array(
            'quizid' => new external_value(PARAM_INT, 'identifiant du quiz', VALUE_DEFAULT, 1),
            ));
    }

    /**
     * The web service method.
     * @param string $quizid The id of quiz.
     * @return result of process, format json true or false with reason.
     */
    public static function get_quiz_dates($quizid) {
        self::validate_parameters (self::get_quiz_dates_parameters (), array ('quizid' => $courseid));

        // Processing.
        $ret = self::getquizdates($quizid);

        return json_encode($ret);
    }

    /**
     * Define return values for get_quiz_dates methode.
     * @return the description of the returned values.
     */
    public static function get_quiz_dates_returns() {
        $returntxt = "chaine json exposant les dates de début et de fin du quiz";
        return new external_value ( PARAM_TEXT, $returntxt);
    }

    /**
     * Get quiz timeopen and timeclose.
     * @param String $quizid The quiz id.
     * @return returns a json structure with the result of the execution.
     */
    public static function getquizdates($quizid) {
        global $DB, $CFG;

        $quiz = $DB->get_record('quiz', array('id' => $quizid));
        $ret = new stdClass();

        if (!isset($quiz->id)) {
            $ret->result = "false";
            $ret->raison = "aucun quiz ne correspond a id [" . $quizid. "]";
            return $ret;
        }
        if ($quiz->timeopen == 0) {
            $ret->result = "undefined";
            $ret->timeopen = 0;
            $ret->timeclose = $quiz->timeclose;
        } else {
            $ret->result = "true";
            $ret->timeopen = $quiz->timeopen;
            $ret->timeclose = $quiz->timeclose;
        }
        return $ret;
    }
}