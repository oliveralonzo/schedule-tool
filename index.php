<?php
include 'schedule-tool.php';

function formatTime($time) {
	if (strlen($time) == 5) {
		$time = "0".$time;
	}
	return substr($time, 0, 2) . ":" . substr($time, 2, 2);
}

$file = file('options.txt');
$coursesByTitle = [];
foreach ($file as $line) {
	list($crn, $course, $instructor, $title, $days, $time, $credits, $category) = explode(',', $line);
	if (!array_key_exists($title, $coursesByTitle)) {
		$coursesByTitle[$title] = [];
	}
	array_push($coursesByTitle[$title], new Course($crn, $course, $instructor, $title, $days, $time, $credits, $category));
}

$schedules = new Schedules($coursesByTitle, 18);
echo nl2br($schedules);
echo "<br>".count($schedules->getSchedules());

?>
