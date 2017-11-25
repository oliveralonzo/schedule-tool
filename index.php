<?php
include 'schedule-tool.php';

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

$numCredits = intval($_POST["credits"]);

$schedules = new Schedules($coursesByTitle, $numCredits);
echo nl2br($schedules);
echo "<br>".count($schedules->getSchedules());

?>
