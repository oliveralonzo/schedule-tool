<?php
include 'schedule-tool.php';

function formatTime($time) {
	if (strlen($time) == 5) {
		$time = "0".$time;
	}
	return substr($time, 0, 2) . ":" . substr($time, 2, 2);
}

$coursesByTitle = [];

// Uncomment the following line for testing
//$sections = file('options.txt');
foreach ($sections as $line) {
	list($crn, $course, $instructor, $title, $days, $time, $credits) = explode(',', $line);
	if (!array_key_exists($title, $coursesByTitle)) {
		$coursesByTitle[$title] = [];
	}
	array_push($coursesByTitle[$title], new Course($crn, $course, $instructor, $title, $days, $time, $credits));
}

$schedules = new Schedules($coursesByTitle, 5);
echo nl2br($schedules);
echo "<br>".count($schedules->getSchedules());

?>
