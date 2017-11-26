<?php
error_reporting(0); // stops annoying notice

include 'schedule-tool.php';

$coursesByTitle = [];

// Uncomment the following line for testing
//$sections = file('options.txt');
foreach ($sections as $line) {
	list($crn, $course, $instructor, $title, $times, $credits) = explode(',', $line);
	if (!array_key_exists($title, $coursesByTitle)) {
		$coursesByTitle[$title] = [];
	}
	$current = $coursesByTitle[$title][$crn];
	if (empty($current)) {
			$coursesByTitle[$title][$crn] = new Course($crn, $course, $instructor, $title, $times, $credits);
	} else {
			$current->addTimes($times);
			$coursesByTitle[$title][$crn] = $current;
	}
}

$numCredits = intval($_POST["credits"]);

$blocks = $_POST["blocks"];
if (!empty($blocks)) {
	$blocks = explode(" && ", $blocks);
} else {
	$blocks = [];
}

$schedules = new Schedules($coursesByTitle, $numCredits, $blocks);
echo $schedules;


?>
