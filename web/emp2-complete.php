<?php
require_once("config.php");
$dept_default = "d009";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>

<h2>Department</h2>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
<?php
// Department
$dept = (empty($_GET['dept']))?$dept_default:$_GET['dept'];

$sql = "SELECT *
        FROM departments";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  $counter = 0;
  echo '<select name="dept">';
  while ($row = mysqli_fetch_assoc($result)) {
    $selected = ($row['dept_no'] == $dept) ? "selected" : "";
    echo "<option value=\"{$row['dept_no']}\" $selected>{$row['dept_name']}</option>";
    $counter++;
  }
  echo '</select>';
} else {
  echo "0 results";
}
?>
<input type="submit" value="Search"/>
</form>
<hr/>
<h2>Employee</h2>
<?php


// Employee with department
$sql = "SELECT e.*, d.* 
        FROM (employees e INNER JOIN dept_emp de ON e.emp_no = de.emp_no)
        INNER JOIN departments d ON de.dept_no = d.dept_no
        WHERE d.dept_no = '$dept'
        LIMIT 10";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  $counter = 0;
  while ($row = mysqli_fetch_assoc($result)) {
    echo "[{$row['emp_no']}]:  - {$row['first_name']} {$row['last_name']} [{$row['dept_name']}]<br>";
    $counter++;
  }
  echo "$counter results";
} else {
  echo "0 results";
}

mysqli_close($conn);
// TODO select by department name
?>