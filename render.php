<?php
date_default_timezone_set('Europe/London');

// Replace them with the exact course names from http://cs.ox.ac.uk/teaching/MSCinCS/
$courses = array('Machine Learning', 'Computer Security', 'Probabilistic Model Checking');

// Replace them with the days and times of your classes and practicals from minerva
$tutorials['Class']['Computer Security'] = array('day' => 'Tuesday', 'time' => '12');
$tutorials['Class']['Machine Learning'] = array('day' => 'Wednesday', 'time' => '12');
$tutorials['Class']['Probabilistic Model Checking'] = array('day' => 'Wednesday', 'time' => '15');

$tutorials['Practical']['Computer Security'] = array('day' => 'Friday', 'time' => '11');
$tutorials['Practical']['Machine Learning'] = array('day' => 'Tuesday', 'time' => '14');
$tutorials['Practical']['Probabilistic Model Checking'] = array('day' => 'Thursday', 'time' => '09');

require_once "iCalcreator.class.php";

$configAll = array("unique_id" => "Timetable-All");
$vcalendarAll = new vcalendar($configAll);
$vcalendarAll -> setProperty("X-WR-CALNAME", 'MSc Timetable');
$vcalendarAll -> setProperty("X-WR-TIMEZONE", 'Europe/London');

// Get the 3 feeds from the dept
$config['Lecture'] = array("unique_id" => "Timetable-Lecture", "url" => "http://www.cs.ox.ac.uk/feeds/Timetable-Lecture.ics");
$config['Class'] = array("unique_id" => "Timetable-Class", "url" => "http://www.cs.ox.ac.uk/feeds/Timetable-Class.ics");
$config['Practical'] = array("unique_id" => "Timetable-Practical", "url" => "http://www.cs.ox.ac.uk/feeds/Timetable-Practical.ics");

foreach ($config as $type => $v) {
	$vcalendar = new vcalendar($v);
	$vcalendar -> parse();

	while ($vevent = $vcalendar -> getComponent("vevent")) {
		$uid = $vevent -> getProperty("UID");
		$dtstart = $vevent -> getProperty("dtstart");
		$summary = $vevent -> getProperty("SUMMARY");

		if (in_array($summary, $courses)) {
			$dweek = date("l", strtotime($dtstart['year'] . "-" . $dtstart['month'] . "-" . $dtstart['day']));
			if ($type == 'Lecture' || ($type != 'Lecture' && $tutorials[$type][$summary]['day'] == $dweek && $tutorials[$type][$summary]['time'] == $dtstart['hour'])) {
				$vevent -> setProperty("SUMMARY", $summary . " (" . $type . ")");
				$vcalendarAll -> setComponent($vevent);
			}
		}
	}
}

$vcalendarAll -> sort();
$vcalendarAll -> returnCalendar();

// while ($vevent = $vcalendarAll -> getComponent("vevent")) {
// $dtstart = $vevent -> getProperty("dtstart");
// $summary = $vevent -> getProperty("SUMMARY");
// $type = $vevent -> getProperty("CATEGORIES");
// $dweek = date("l", strtotime($dtstart['year'] . "-" . $dtstart['month'] . "-" . $dtstart['day']));
// echo $summary . " " . $type . " " . $dweek . " " . $dtstart['hour'] . "\n";
// }
?>