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

	public function full() {
		return $this->credits == $this->maxCredits;
	}

	public function addCourse($course) {
		if ($this->courseIsContained($course) or $this->getCurrentCredits() + $course->getCredits() > $this->maxCredits) {
			return false;
		} else {
			$this->courses[] = $course;
			$this->credits += $course->getCredits();
			return true;
		}
	}

	public function removeCourse($course) {
		if(($key = array_search($course, $this->courses)) != false) {
    		unset($this->courses[$key]);
			$this->credits -= $course->getCredits();
			return true;
		}
		return false;
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

	public function getMaxCredits() {
		return $this->maxCredits;
	}

	public function __toString() {
		return implode("\n", $this->courses);
	}

	public function equals($other) {
		return strcmp($this->__toString(), $other->__toString()) == 0;
	}
}

?>
