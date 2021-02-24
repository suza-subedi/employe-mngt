<?php
include_once("config.php");
include_once("select-department.php");

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}


if (isset($_POST['cmd']) && $_POST['cmd'] == 'add') {
  // Add new employee
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $emp_no = $_POST['emp_no'];
  $gender = $_POST['gender'];
  $birth_date = $_POST['birth_date'];
  $hire_date = $_POST['hire_date'];
  $dept_no = $_POST['dept_no'];
  $sql = "INSERT INTO employees (emp_no,birth_date,first_name,last_name,gender,hire_date)
          VALUES ('$emp_no','$birth_date','$first_name','$last_name','$gender','$hire_date')";
  if ($conn->query($sql) === TRUE) {
    echo "New EMPLOYEE created successfully<br/>";
    $sql2 = "INSERT INTO dept_emp (emp_no,dept_no,from_date,to_date)
             VALUES ('$emp_no','$dept_no',CURDATE(),'9999-01-01')";
    if ($conn->query($sql2) === TRUE) {
      echo "EMPLOYEE has been set to DEPARTMENT successfully<br/>";
    } else {
      echo "Error: " . $sql2 . "<br>" . $conn->error;
    }
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
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
          echo "id: " . $row["emp_no"] . " - Name: " . $row["first_name"] . " " . $row["last_name"] . "<br>";
          $counter++;
        }
        echo "$counter results";
      } else {
        echo "0 results";
      }


      ?>
    </td>
    <td valign="top">
      <h3>New Employee</h3>
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
            <td><input type="text" name="emp_no"></td>
          </tr>
          <tr>
            <th>First Name</th>
            <td><input type="text" name="first_name"></td>
          </tr>
          <tr>
            <th>Last Name</th>
            <td><input type="text" name="last_name"></td>
          </tr>
          <tr>
            <th>Birth Date</th>
            <td><input type="date" name="birth_date"></td>
          </tr>
          <tr>
            <th>Hire Date</th>
            <td><input type="date" name="hire_date" value="<?php echo date("Y-m-d"); ?>"></td>
          </tr>
          <tr>
            <th>Gender</th>
            <td>
              <input type="radio" name="gender" value="M">Male<br />
              <input type="radio" name="gender" value="F">Female
            </td>
          </tr>
        </table>
        <input type="submit" value="Create" />
      </form>
    </td>
  </tr>
</table>

<?php
mysqli_close($conn);
?>