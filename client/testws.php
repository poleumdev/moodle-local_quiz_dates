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
 * Test of the web service with the form settings.
 *
 * @package    local_quiz_dates
 * @copyright  2021 Marc Leconte
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// Main configuration importation (instanciate the $CFG global variable).
require_once(dirname(__FILE__) . '/../../../config.php');
// Define class curl.
require_once(dirname(__FILE__) . '/../../../lib/filelib.php');
// SETUP serveur.
$token = optional_param('token', 'none', PARAM_ALPHANUM);
$domainname = optional_param('serveur', 'none', PARAM_RAW);

// FUNCTION NAME.
$functionname = 'get_quiz_dates';

// PARAMETERS.
$quizid = optional_param('quizid', -1, PARAM_INT);

// XML-RPC CALL.
if ($token != 'none') {
    $serverurl = $domainname . '/webservice/xmlrpc/server.php'. '?wstoken=' . $token;
    $curl = new curl;
    $post = xmlrpc_encode_request($functionname, array($quizid));
    $resp = xmlrpc_decode($curl->post($serverurl, $post));
}

$toolpath = dirname(__FILE__);
$context = context_system::instance();
$PAGE->set_context($context);
require_login();
$urlact = new moodle_url($toolpath . '/testws.php', []);
$PAGE->set_url($urlact);
$PAGE->set_title("TEST");

echo $OUTPUT->header();

if ($token != 'none') {
    echo('Test de l\'obtention des date du quiz id = '. $quizid .'<br><div id="resultat"></div>');

    if (!is_array($resp)) {
        echo ("<script type='text/javascript'>
            function output(inp) {
                var element = document.getElementById('resultat');
                element.appendChild(document.createElement('pre')).innerHTML = inp;
            }
            var obj = " . $resp . ";
            var str = JSON.stringify(obj, undefined, 4);
            output(str);
            </script>");
    }
} else {
    echo ("<form method='post'>");
    echo ("TOKEN : <input type='text' name='token' size='50'/><br/>");
    echo ("SERVEUR : <input type='text' name='serveur' value=".$CFG->wwwroot."/><br/>");
    echo ("QUIZID : <input type='text' name='quizid' value='128'/><br/>");

    echo ("<input type='submit' value='Tester'/><br/>");
    echo ("</form>");
}
echo $OUTPUT->footer();