<?php
include_once("config.php");
include_once("select-department.php");

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

?>
<h1>Employees</h1>
<?php
//Employees
$sql = "SELECT * FROM employees LIMIT 10";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  $counter = 0;
  while($row = mysqli_fetch_assoc($result)) {
    echo "id: " . $row["emp_no"]. " - Name: " . $row["first_name"]. " " . $row["last_name"]. "<br>";
    $counter++;
  }
  echo "$counter results";
} else {
  echo "0 results";
}

?>
<hr/>
<h1>Departments</h1>
<?php

//Departments
$sql = "SELECT * FROM departments LIMIT 10";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  $counter = 0;
  while($row = mysqli_fetch_assoc($result)) {
    echo "id: " . $row["dept_no"]. " - Name: " . $row["dept_name"].    "<br>";
    $counter++;
  }
  echo "$counter results";
} else {
  echo "0 results";
} 
?>

<hr/>

<h1>Salaries</h1>
<?php

//Saleries
$sql = "SELECT * FROM salaries LIMIT 10";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  $counter = 0;
  while($row = mysqli_fetch_assoc($result)) {
    echo "id: " . $row["emp_no"]. " - Salary: " . $row["salary"]. " " .  "<br>";
    $counter++;
  }
  echo "$counter results";
} else {
  echo "0 results";
} 

?>

<hr/>

<h1>Title</h1>
<?php

//Title
$sql = "SELECT * FROM titles LIMIT 10";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  $counter = 0;
  while($row = mysqli_fetch_assoc($result)) {
    echo "id: " . $row["emp_no"].  " - Title: " . $row["title"]. " " .  "<br>";
    $counter++;
  }
  echo "$counter results";
} else {
  echo "0 results";
}


$first_name = "";
$last_name = "";
$emp_no = "";
$gender = "";
$birth_date = "";
$hire_date = "";
$dept_no = "";
$salary = "";
$title_no ="";


if (isset($_POST['cmd']) && $_POST['cmd'] == 'update') {
  $emp_no = $_POST['emp_no'];
  $sql = "SELECT * FROM employees WHERE emp_no = '{$emp_no}'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    // output data of each row
    $counter = 0;
    $row = mysqli_fetch_assoc($result);
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $emp_no = $row['emp_no'];
    $gender = $row['gender'];
    $birth_date = $row['birth_date'];
    $hire_date = $row['hire_date'];
    $salary = $row['salary'];
    $title = $row['title'];
    // $dept_no = $row['dept_no'];
  }

}

$sql = "SELECT * FROM employees LIMIT 10";
$result = mysqli_query($conn, $sql);
?>


<table>
  <tr>
    <td>
      <?php
      if (mysqli_num_rows($result) > 0) {
        // output data of each row
        $counter = 0;
        while ($row = mysqli_fetch_assoc($result)) {
          $emp_no = $row['emp_no'];
          echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"POST\" id=\"form{$emp_no}\">";
          echo "<input type='hidden' name='cmd' value='update' />";
          echo "<input type='hidden' name='emp_no' value='{$emp_no}'/>";
          echo "<input type='submit' value='Update' />";
          echo " [{$emp_no}]:  - {$row['first_name']} {$row['last_name']}  {$row['salary']} {$row['title']}";
          echo "</form>"; 
          $counter++;
        }
        echo "$counter results";
      } else {
        echo "0 results";
      }


      ?>
    </td>
    <td valign="top">
      <h3>Update Employee</h3>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" name="cmd" value="add" />
        <table>
          <tr>
            <th>Department</th>
            <td>
              <?php echo select_department($conn); ?>
            </td>
          </tr>

          <tr>
            <th>Emp#</th>
            <td><input type="text" name="emp_no" value="<?php echo $emp_no; ?>"></td>
          </tr>
          <tr>
            <th>First Name</th>
            <td><input type="text" name="first_name" value="<?php echo $first_name; ?>"></td>
          </tr>
          <tr>
            <th>Last Name</th>
            <td><input type="text" name="last_name" value="<?php echo $last_name; ?>"></td>
          </tr>
          <tr>
            <th>Birth Date</th>
            <td><input type="date" name="birth_date"  value="<?php echo $birth_date; ?>"></td>
          </tr>
          <tr>
            <th>Hire Date</th>
            <td><input type="date" name="hire_date" value="<?php echo $hire_date; ?>"></td>
          </tr>
          <tr>
            <th>Title</th>
            <td>
              <?php echo select_department($conn); ?>
            </td>
          </tr>
          <th>Salary</th>
            <td><input type="text" name="Salary" value="<?php echo $salary; ?>"></td>
          </tr>
          <tr>
            <th>Gender</th>
            <td>
              <input type="radio" name="gender" value="M">Male<br />
              <input type="radio" name="gender" value="F">Female
            </td>
          </tr>
          
        </table>
        <input type="submit" value="Update" />
      </form>
    </td>
  </tr>
</table>

<?php
mysqli_close($conn);

// TODO Show list of departments
?>