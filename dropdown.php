<?php

function connectAndPullTitles() {
$servername = "RaDLabpeoplecounter.creighton.edu";
$username = "jen94317";
$password = "SVOOyr80h7m3sSsT";
$mainDatabase= "for_capstone";
// Create connection
$conn = new mysqli($servername, $username, $password, $mainDatabase);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    echo "Connected fail";
}
$sql="SHOW DATABASES";


// Notice the subtraction from $current_id
$query = "SELECT DISTINCT course_title, subject_code, course_number, credit_hrs FROM vw_class_info";
// Would output:
// SELECT `title` FROM `table` WHERE `id` = '5'

if (!($result=mysqli_query($conn,$query))) {
  printf("Error: %s\n", mysqli_error($conn));
}

$courseTitleArray = array();
$subjectCodeArray = array();
while($row = $result->fetch_assoc()) {
    $courseTitleArray[] = str_replace('"',"",$row['course_title']) . " && " . $row['subject_code'] . $row['course_number'] . " && " . $row['credit_hrs'];
    $subjectCode = $row['subject_code'];
    if (!in_array($subjectCode,$subjectCodeArray)) {
        array_push($subjectCodeArray, $subjectCode);
    }
}

$conn->close();

asort($courseTitleArray);
asort($subjectCodeArray);
$resultDrop = "";

//echo '<form id="classSelector" action="index.html">';
//print_r($courseTitleArray);
//print_r($subjectCodeArray);
echo '<div class="titleDropdown">';
echo '<select id="titles" name="credits" onchange="" class="ui search selection dropdown">';
foreach ($courseTitleArray as $i => $title) {
  if ($i == 0){
    continue;
  }

  $values = explode(" && ", $title);

  echo '<option title="'.$values[0].'" course="'.$values[1].'" credits="'.$values[2].'">'.$values[0].'</option>';
}
echo '</select>';
echo '<input id="submitByTitle" type="button" name="add" value="Add" class="ui button">';
echo '</div>';
//echo "</form>";
////////////////////////
echo '<div class="codeDropdown">';
echo '<select id="codes" name="credits" onchange="" class="ui search selection dropdown">';
foreach ($subjectCodeArray as $i => $code) {
  if ($i == 0){
    continue;
  }
  echo '<option value="'.$code.'">'.$code.'</option>';
}
echo '</select>';

echo '<select id="courseNumbers" name="credits" onchange="" class="ui search selection dropdown">';
echo '</select>';

echo '<input id="submitByCode" type="button" name="add" value="Add" class="ui button">';
echo '</div>';

} //close connectAndPullTitles

connectAndPullTitles();

?>
