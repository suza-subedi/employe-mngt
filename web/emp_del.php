<?php
require_once("config.php");

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['cmd']) && $_POST['cmd'] == 'del') {
  // Delete employee
  $emp_no = $_POST['emp_no'];
  $sql = "DELETE FROM employees WHERE emp_no = $emp_no";
  $result = mysqli_query($conn, $sql);

  if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
  } else {
    echo "Error deleting record: " . $conn->error;
  }
}
?>

<h3>Delete Employee</h3>
<?php
$sql = "SELECT * FROM employees LIMIT 10";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    $emp_no = $row['emp_no'];
    echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"POST\" id=\"form{$emp_no}\">";
    echo "<input type='hidden' name='cmd' value='del' />";
    echo "<input type='hidden' name='emp_no' value='{$emp_no}'/>";
    echo "<input type='button' onclick='confirmDelete(\"form{$emp_no}\",\"{$row['first_name']}\")' value='Delete' />";
    echo " [{$emp_no}]:  - {$row['first_name']} {$row['last_name']}";
    echo "</form>"; 
  }
}

mysqli_close($conn);

// TODO Show list of departments
?>
<script>
  function confirmDelete(formId, name){
    if (confirm(`Are you sure to delete ${name}?`)) {
      // alert(`Delete ${formId}`);
      document.getElementById(formId).submit();
    }
  }
</script>