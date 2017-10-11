<?php

class Schedule {
	private $courses;
	private $maxCredits;
	private $credits;

	function __construct($maxCredits) {
		$this->courses = [];
		$this->maxCredits = $maxCredits;
		$this->credits = 0;
	}

	public function getCourses() {
		return $this->courses;
	}

	public function addCourse($course) {
		if (!$this->courseIsContained($course) and $this->getCurrentCredits() + $course->getCredits() <= $this->maxCredits) {
			$this->courses[] = $course;
			$this->credits += $course->getCredits();
			return true;
		} else {
			return false;
		}
	}

	private function courseIsContained($course) {
		foreach ($this->courses as $current) {
			if ($current->equals($course)) {
				return true;
			}
		}
		return false;
	}

	public function getCurrentCredits() {
		return $this->credits;
	}

	public function __toString() {
		return implode("\n", $this->courses);
	}

	public function equals($other) {
		return strcmp($this->__toString(), $other->__toString()) == 0;
	}
}

?>
