<?php
require_once("config.php");
// Create connection
$conn = new mysqli($server, $username, $password, $db);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>

<h2>Department</h2>
<?php
// Department
$sql = "SELECT *
        FROM departments";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  $counter = 0;
  echo '<select name="dept">';
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<option id=\"{$row['dept_no']}\">{$row['dept_name']}</option>";
    $counter++;
  }
  echo '</select>';
} else {
  echo "0 results";
}
?>

<hr/>
<h2>Employees</h2>
<?php
// Employee with department
$sql = "SELECT e.*, d.* 
        FROM (employees e INNER JOIN dept_emp de ON e.emp_no = de.emp_no)
        INNER JOIN departments d ON de.dept_no = d.dept_no
        LIMIT 10";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  $counter = 0;
  while ($row = mysqli_fetch_assoc($result)) {
    echo "id: {$row['emp_no']}:  - {$row['first_name']} {$row['last_name']} [{$row['dept_name']}]<br>";
    $counter++;
  }
  echo "$counter results";
} else {
  echo "0 results";
}

mysqli_close($conn);
// TODO select by department name
?>