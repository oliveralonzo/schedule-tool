<?php
include 'course.php';
include 'schedule.php';

class Schedules {
	private $schedules;
	private $courses;
	private $amount;

	function __construct($courses, $maxCredits) {
		$this->amount = $amount;
		$this->courses = $courses;
		$this->schedules = [];
		$this->generateSchedules(0, new Schedule($maxCredits));
	}

	private function generateSchedules($current_index, $schedule) {
		$course = $this->courses[$current_index];
		if ($schedule->getCurrentCredits() + $course->getCredits() > $schedule->getMaxCredits()) {
			array_push($this->schedules, clone $schedule);
			return;
		} else {
			for ($i = $current_index; $i < count($this->courses)-1; $i++) {
				$schedule->addCourse($course);
				$this->generateSchedules(++$i, $schedule, $course);
				$schedule->removeCourse($course);
			}
		}
	}

	public function getSchedules() {
		return $this->schedules;
	}

	public function __toString() {
		return implode("\n\n", array_values($this->schedules));
	}
}

$file = file('options.txt');
$courses = [];
foreach ($file as $line) {
	list($crn, $course, $instructor, $title, $days, $time, $credits, $category) = explode(',', $line);
	array_push($courses, new Course($crn, $course, $instructor, $title, $days, $time, $credits, $category));
}
// echo join("<br>", $courses);
$schedules = new Schedules($courses, 3);
echo nl2br($schedules);
// echo "<br>".count($schedules->getSchedules());
?>
