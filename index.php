<?php
include 'course.php';
include 'schedule.php';

class Schedules {
	private $schedules;
	private $coursesByTitle;
	private $courseTitles;
	private $amount;
	private $maxCredits;

	function __construct($coursesByTitle, $maxCredits) {
		$this->amount = $amount;
		$this->coursesByTitle = $coursesByTitle;
		$this->courseTitles = array_keys($coursesByTitle);
		$this->maxCredits = $maxCredits;
		$this->schedules = [];
		$this->generateSchedules();
	}

	private function generateSchedules() {
		$numTitles = count($this->courseTitles);
		for ($i = 0; $i<$numTitles; $i++) {
			//echo "restart: " . $i ."<br><br><br><br>";
			$this->generateSchedulesHelper($i, new Schedule($this->maxCredits));
		}
	}

	private function generateSchedulesHelper($currentIndex, $schedule) {
		//echo "count: ". count($schedule) . "<br>";
		$numTitles = count($this->courseTitles);
		if ($currentIndex <= $numTitles) {
			for ($i = $currentIndex; $i<$numTitles; $i++) {
				$currentTitle = $this->courseTitles[$i];
				foreach($this->coursesByTitle[$currentTitle] as $course) {
					$schedule->addCourse($course);
					//echo nl2br($schedule). "<br><br>";
					if ($schedule->full()) {
						//echo "yes" ."<br><br><br><br>";
						array_push($this->schedules, clone $schedule);
						$schedule->removeCourse($course);
					}
					$this->generateSchedulesHelper(++$currentIndex, $schedule);
					$schedule->removeCourse($course);
				}
			}
		}
	}

	public function getSchedules() {
		return array_unique($this->schedules);
	}

	public function __toString() {
		return implode("\n\n", array_values($this->getSchedules()));
	}
}

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

// foreach($coursesByTitle as $title => $courses) {
//   echo $title . "<br>";
//   foreach($courses as $course) {
// 	  echo $course . "<br>";
//   }
//   echo "<br>";
// }

// echo join("<br>", $courses);
$schedules = new Schedules($coursesByTitle, 18);
echo nl2br($schedules);
echo "<br>".count($schedules->getSchedules());

?>
