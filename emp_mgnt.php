<style>
  .box {
    border: solid 1px black;
  }
</style>
<?php
require_once("config.php");
require_once("select-department.php");

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>


<?php
// Process previous request
if (isset($_POST['cmd']) && $_POST['cmd'] == 'del') {
  // Delete employee
  $emp_no = $_POST['emp_no'];
  $sql = "DELETE FROM employees WHERE emp_no = $emp_no";
  if (mysqli_query($conn, $sql)) {
    echo "Record deleted successfully";
  } else {
    echo "Error deleting record: " . mysqli_connect_error();
  }
}

// Process SAVE request - extract data and update the database
if (isset($_POST['cmd']) && $_POST['cmd'] == 'save') {
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $emp_no = $_POST['emp_no'];
  $gender = $_POST['gender'];
  $birth_date = $_POST['birth_date'];
  $hire_date = $_POST['hire_date'];
  $dept_no = $_POST['dept_no'];
  $salary = $_POST['salary'];

  // 1.Update employees table
  $sql = "UPDATE employees SET
            emp_no = '$emp_no',
            birth_date = '$birth_date',
            first_name = '$first_name',
            last_name = '$last_name',
            gender = '$gender',
            hire_date = '$hire_date'
            salary = '$salary'
            WHERE emp_no = '$emp_no'";

  if ($conn->query($sql) === TRUE) {
    echo "Success<br/>$sql<br/>";
  } else {
    echo "Error: " . $sql . "<br/>" . $conn->error;
  }

  // 2.Update dept_emp
  // consider whether the department data has changed
  // if NO, don't update anything
  // if YES, 
  // 1. terminate the current department to_date = today; 
  // 2. add new record (set from_date = today and to_date = '9999-01-01'
  $sql = "SELECT * FROM dept_emp WHERE emp_no = '$emp_no' AND dept_no = '$dept_no' ORDER BY to_date DESC";
  $sql = "SELECT * FROM salaries WHERE emp_no = '$emp_no' AND salary = '$salary' ORDER BY to_date DESC"; 
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    // he is still in the same department, no department change
  } else {
    // 1. terminate the current department to_date = today; 
    $sql = "UPDATE dept_emp SET to_date = NOW() WHERE emp_no = '$emp_no' AND dept_no = '$dept_no' AND to_date = '9999-01-01'";
    if ($conn->query($sql) === TRUE) {
      echo "Success<br/>$sql<br/>";
    } else {
      echo "Error: " . $sql . "<br/>" . $conn->error;
    }

    // 2. add new record (set from_date = today and to_date = '9999-01-01'
    $sql = "INSERT INTO dept_emp (emp_no,dept_no,from_date,to_date) VALUES
    ('$emp_no','$dept_no',NOW(),'9999-01-01')";
    if ($conn->query($sql) === TRUE) {
      echo "Success<br/>$sql<br/>";
    } else {
      echo "Error: " . $sql . "<br/>" . $conn->error;
    }
  }

  if (mysqli_num_rows($result) > 0) {
    // he is still in the same department, no department change
  } else {
    // 1. terminate the current department to_date = today; 
    $sql = "UPDATE salaries SET to_date = NOW() WHERE emp_no = '$emp_no' AND salary = '$salary' AND to_date = '9999-01-01'";
    if ($conn->query($sql) === TRUE) {
      echo "Success<br/>$sql<br/>";
    } else {
      echo "Error: " . $sql . "<br/>" . $conn->error;
    }

    // 2. add new record (set from_date = today and to_date = '9999-01-01'
    $sql = "INSERT INTO salaries (emp_no,salary,from_date,to_date) VALUES
    ('$emp_no','$salary',NOW(),'9999-01-01')";
    if ($conn->query($sql) === TRUE) {
      echo "Success<br/>$sql<br/>";
    } else {
      echo "Error: " . $sql . "<br/>" . $conn->error;
    }
  }
}
?>


<h3>Employee Management</h3>
<?php
// $sql = "SELECT * FROM employees LIMIT 10";
$sql = "
      select * from (employees e left join dept_emp de on e.emp_no = de.emp_no) join departments d on de.dept_no = d.dept_no
      ORDER BY de.to_date DESC
      limit 10;
      ";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
?>
  <table>
    <tbody>
      <?php
      // output data of each row
      while ($row = mysqli_fetch_assoc($result)) {
        $emp_no = $row['emp_no'];
        // echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"POST\" id=\"form{$emp_no}\">";
        // echo "<input type='hidden' name='cmd' value='del' />";
        // echo "<input type='hidden' name='emp_no' value='{$emp_no}'/>";
        // echo "<input type='button' onclick='confirmDelete(\"form{$emp_no}\",\"{$row['first_name']}\")' value='Delete' />";
        // echo " [{$emp_no}]:  - {$row['first_name']} {$row['last_name']}";
        // echo "</form>"; 
      ?>
        <tr>
          <td class="box"><?php echo $row['emp_no']; ?></td>
          <td class="box"><?php echo $row['first_name']; ?></td>
          <td class="box"><?php echo $row['last_name']; ?></td>
          <td class="box"><?php echo $row['dept_name']; ?></td>
          <td class="box"><?php echo $row['birth_date']; ?></td>
          <td class="box"><?php echo $row['hire_date']; ?></td>
          <td class="box"><?php echo $row['salary']; ?></td>
          <td class="box"><?php echo $row['gender']; ?></td>

          <td class="box">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="form<?php echo $row['emp_no']; ?>">
              <input type="hidden" name="emp_no" value="<?php echo $row['emp_no']; ?>" />
              <input type="hidden" name="cmd" value="del" />
            </form>
            <!-- <button type="submit" form="form<?php echo $row['emp_no']; ?>">
                <img src="img/icon-del.jpeg" width="20" />
              </button> -->
            <button onClick='confirmDelete("form<?php echo $row['emp_no']; ?>", "<?php echo $row['first_name']; ?>")'>
              <img src="img/icon-del.jpeg" width="20" />
            </button>
          </td>
          <td class="box">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="formUpdate<?php echo $row['emp_no']; ?>">
              <input type="hidden" name="emp_no" value="<?php echo $row['emp_no']; ?>" />
              <input type="hidden" name="cmd" value="update" />

              <button onClick=''>
                <img src="img/icon-edit.png" width="20" />
              </button>
            </form>
          </td>
        </tr>

      <?php
      }
      ?>
    </tbody>
  </table>

  <?php




  // Process UPDATE request - populate the data into the form
  if (isset($_POST['cmd']) && $_POST['cmd'] == 'update') {
    // Delete employee
    $emp_no = $_POST['emp_no'];
    $sql = "SELECT * FROM employees WHERE emp_no = $emp_no";
    // echo $sql;
    $result = mysqli_query($conn, $sql);
    // echo $result;
    $row = mysqli_fetch_assoc($result);
    // echo var_dump($row);

    // Get the current department of this employee
    $sql = "SELECT * FROM dept_emp WHERE emp_no = $emp_no ORDER BY to_date DESC";
    $sql = "SELECT * FROM salaries WHERE emp_no = $emp_no ORDER BY to_date DESC";
    $result2 = mysqli_query($conn, $sql);
    $row2 = mysqli_fetch_assoc($result2);

  }
  ?>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <input type="hidden" name="cmd" value="save" />
    <table>
      <tr>
        <th>Department <?php echo $row2['dept_no']; ?></th>
        <td>
          <?php echo select_department($conn, $row['dept_no']); ?>
        </td>
      </tr>
      <tr>
        <th>Emp#</th>
        <td><input type="text" name="emp_no" value="<?php echo $row['emp_no']; ?>"></td>
      </tr>
      <tr>
        <th>First Name</th>
        <td><input type="text" name="first_name" value="<?php echo $row['first_name']; ?>"></td>
      </tr>
      <tr>
        <th>Last Name</th>
        <td><input type="text" name="last_name" value="<?php echo $row['last_name']; ?>"></td>
      </tr>
      <tr>
        <th>Birth Date</th>
        <td><input type="date" name="birth_date" value="<?php echo $row['birth_date']; ?>"></td>
      </tr>
      <tr>
        <th>Hire Date</th>
        <td><input type="date" name="hire_date" value="<?php echo $row['hire_date']; ?>"></td>
      </tr>
      <tr>
        <th>Gender</th>
        <td>
          <input type="radio" name="gender" <?php echo ($row['gender'] == 'M') ? "checked" : ""; ?> value="M">Male<br />

          <input type="radio" name="gender" <?php echo ($row['gender'] == 'F') ? "checked" : ""; ?> value="F">Female
        </td>
      </tr>
    </table>
    <input type="submit" value="UPDATE" />
  </form>

<?php
}

mysqli_close($conn);
?>
<script>
  function confirmDelete(formId, empName) {
    // to type this `, hold ALT and then type 96
    if (confirm(`Are you sure to delete ${empName}?`)) {
      // go on an delete    
      console.log("DELETE")
      document.getElementById(formId).submit()
    }
  }
</script>