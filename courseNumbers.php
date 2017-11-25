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

/////////////////////////
$subjectCode = $_POST["subject_code"];

$query = "SELECT DISTINCT course_number, course_title FROM vw_class_info WHERE subject_code = '".$subjectCode."'";
// Would output:
// SELECT `title` FROM `table` WHERE `id` = '5'

if (!($result=mysqli_query($conn,$query))) {
  printf("Error: %s\n", mysqli_error($conn));
}

$courseNumbersArray = array();
while($row = $result->fetch_assoc()) {
  $courseNumbersArray[] = str_replace('"',"",$row['course_number']) . " && " . $row['course_title'];
}

$conn->close();

asort($courseNumbersArray);
foreach ($courseNumbersArray as $i => $num) {
  $values = explode(" && ", $num);
  echo '<option value="'.$values[1].' - '.$subjectCode.$values[0].'">'.$values[0].'</option>';
}

}

connectAndPullTitles();

 ?>
