<?php
/**
* File that takes in all the course sections and builds an array
* 	indexing them by title
* @author Josh Nichols & Oliver Alonzo
* @version 1.0
*/
error_reporting(0); // stops annoying notice

include 'schedule-tool.php';

$coursesByTitle = [];

/**
* Goes through all of $sections.
*		the $sections variable is coming from courseDB.php, where index.php is called
*		for scope 
*/
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

// Add message if time limit is reached
set_time_limit(10);
$schedules = new Schedules($coursesByTitle, $numCredits, $blocks);

// Output for testing
// echo nl2br($schedules);
// echo "<br><br>".count($schedules->getSchedules()) ."<br><br>";

//Output for production
echo $schedules;


?>
