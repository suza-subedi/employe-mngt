<style>
  .box {
  }

  th, td {
  padding: 15px;
  text-align: left;
  border-bottom: 1px solid #ddd;

}

.green{
  background-color: #4CAF50;
  color: white;
}

table {
  border-collapse: collapse;
}

h2 {
  text-align: center;
}

.center {
  margin-left: auto;
  margin-right: auto;
}

.left {
  margin-left: 178;
  border: 1px solid black;
  border-bottom: 1px solid black;
  background: rgba(0, 128, 0, 0.4) /* Green background with 30% opacity */
}

h3 {
  margin-left: 178;
}

.btn{
  margin-left: 178;
}

input[type=text] {
  background-color: white;
  padding: 10px 16px;
  
}

input[type=date] {
  background-color: white;
  padding: 10px 16px;
  
}

input[type=text]:focus {
  background-color: lightblue;
  padding: 10px 16px;
  border-radius: 4px;
}

select {
  width: 100%;
  padding: 16px 20px;
  border: none;
  border-radius: 4px;
  background-color: white;
}

 input[type=submit] {
  background-color: #4CAF50;
  border: none;
  color: white;
  padding: 16px 32px;
  text-decoration: none;
  margin: 4px 2px;
  cursor: pointer;
}

tr:hover {background-color: #f5f5f5;}
</style>
<?php
require_once("config.php");
require_once("select-department.php");
require_once("select-title.php");


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>


<?php
function getDatetimeNow() {
    $tz_object = new DateTimeZone('Asia/Bangkok');
    //date_default_timezone_set('Brazil/East');

    $datetime = new DateTime();
    $datetime->setTimezone($tz_object);
    return strval($datetime->format('Y\-m\-d'));
}
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
  $emp_no = $_POST['emp_number'];
  if($emp_no == ''){
    echo "<p> Invalid emp no </p>";
  }else{
    echo "<p> Employee  :$emp_no </p>";
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $hire_date = $_POST['hire_date'];
    $dept_no = $_POST['dept_no'];
    $titles = $_POST['titles'];
    $salary = $_POST['salary'];

    // 1.Update employees table
    $sql = "UPDATE employees SET
              emp_no = '$emp_no',
              dept_no = '$dept_no',
              birth_date = '$birth_date',
              first_name = '$first_name',
              last_name = '$last_name',
              gender = '$gender',
              hire_date = '$hire_date'
              title = '$title'
              salary = '$salary'
              WHERE emp_no = '$emp_no' " ;

    if ($conn->query($sql) === TRUE) {
      echo "Success<br/>$sql<br/>";
    } else {
      // echo "Error: " . $sql . "<br/>" . $conn->error;
    }
    // 2.Update dept_emp
    // consider whether the department data has changed
    // if NO, don't update anything
    // if YES, 
    // 1. terminate the current department to_date = today; 
    // 2. add new record (set from_date = today and to_date = '9999-01-01'
    $sql = "SELECT DISTINCT * FROM dept_emp WHERE emp_no = '$emp_no' AND dept_no = '$dept_no' ORDER BY to_date DESC";
    $result1 = mysqli_query($conn, $sql);

    $sql = "SELECT DISTINCT * FROM salaries WHERE emp_no = '$emp_no' AND salary = '$salary' ORDER BY to_date DESC";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result1) > 0) {
      echo "Invalid request: Already has the title" . "<br/>";

    } else {
      $Now = getDatetimeNow();
      // 1. terminate the current department to_date = today; 
      $sql = "UPDATE titles SET to_date = $Now WHERE emp_no = '$emp_no'" ;
      if ($conn->query($sql) === TRUE) {
        echo "Successfully expiered title <br/>$sql<br/>";
      } else {
        echo "Error: " . $sql . "<br/>" . $conn->error;
      }
      
      //2. add new record (set from_date = today and to_date = '9999-01-01';
      $sql = "INSERT INTO titles (emp_no,from_date,to_date, salary, title) VALUES ('$emp_no','$Now','9999-01-01', '$salary', '$title') ";
      if ($conn->query($sql) === TRUE) {
        echo "Success<br/>$sql<br/>";
      } else {
        //echo "Dupilicate request: Already added the title" . "<br/>";
      }
    }
  }
}
?>



<h2>Employee Management</h2>
</br>


<?php
// $sql = "SELECT * FROM employees LIMIT 10";
$sql = "
      select distinct * from (((employees e left join dept_emp de on e.emp_no = de.emp_no) join departments d on de.dept_no = d.dept_no) join salaries s on e.emp_no = s.emp_no) join titles t on e.emp_no = t.emp_no 
      ORDER BY de.to_date DESC
      limit 10;
      ";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
?>
  <table class = "center">
    <thead>
    <th class = "green"> ID </th>
    <th class = "green"> First Name </th>
    <th class = "green"> Last Name </th>
    <th class = "green"> Department </th>
    <th class = "green"> Birth Date </th>
    <th class = "green"> Hire Data </th>
    <th class = "green"> Salary </th>
    <th class = "green"> Title </th>
    <th class = "green"> Gender </th>
    <th class = "green"> Delete </th>
    <th class = "green"> Update </th>

    </thead>
    
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
          <td class="box"><?php echo $row['title']; ?></td>
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
              <img src="images/icon-del.png" width="20" />
            </button>
          </td>
          <td class="box">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="formUpdate<?php echo $row['emp_no']; ?>">
              <input type="hidden" name="emp_no" value="<?php echo $row['emp_no']; ?>" />
              <input type="hidden" name="cmd" value="update" />

              <button onClick=''>
                <img src="images/icon-edit.png" width="20" />
              </button>
            </form>
          </td>
        </tr>

      <?php
      }
      ?>
    </tbody>
  </table>
  </br>
</br>
</br>

  <?php


  // Process UPDATE request - populate the data into the form
  if (isset($_POST['cmd']) && $_POST['cmd'] == 'update') {
    // Delete employee
    $emp_no = $_POST['emp_no'];
    $sql = "SELECT * FROM employees WHERE emp_no = $emp_no";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    // Get the current department of this employee
    $sql = "SELECT * FROM titles WHERE emp_no = $emp_no ORDER BY to_date DESC";
    $result2 = mysqli_query($conn, $sql);
    $row2 = mysqli_fetch_assoc($result2);
  }

 

  
  ?>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <input type="hidden" name="cmd" value="save" />
    <input type="hidden" name="emp_number" value= <?php echo $row['emp_no']; ?> />

    <h3> Employee Update Form </h3>
    </br>
  
    <table class = "left">
      <tr>
        <th>Department <?php echo $row2['dept_no']; ?></th>
        <td>
          <?php echo select_department($conn, $row['dept_no']); ?>
        </td>
      </tr>
      <tr>
        <th>Emp#</th>
        <td><input type="text" name="emp_no" value=" <?php echo $row['emp_no']; ?>"></td>
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
        <th>Title <?php echo $row2['dept_no']; ?></th>
        <td>
          <?php echo select_title($conn, $row['emp_no']); ?>
        </td>
      </tr>
      <tr>
        <th>Salary</th>
        <td><input type="text" name="salary" value="<?php echo $row['salary']; ?>"></td>
      </tr>
      <tr>
        <th>Gender</th>
        <td>
          <input type="radio" name="gender" <?php echo ($row['gender'] == 'M') ? "checked" : ""; ?> value="M">Male<br />

          <input type="radio" name="gender" <?php echo ($row['gender'] == 'F') ? "checked" : ""; ?> value="F">Female
        </td>
      </tr>
    </table>
    <p class = "btn"> <input type="submit" value="UPDATE" /></p>
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