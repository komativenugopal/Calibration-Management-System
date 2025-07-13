<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

$message = "";

if (isset($_POST['submit'])) {
    // Trim inputs to remove extra spaces
    $instrument_id = trim($_POST['instrument_id']);
    $customer_id = trim($_POST['customer_id']);
    $identification_no = trim($_POST['identification_no']);
    $description = trim($_POST['description']);
    $calibration_range = trim($_POST['calibration_range']);
    $actual_range = trim($_POST['actual_range']);
    $make = trim($_POST['make']);
    $periodicity = trim($_POST['periodicity']);
    $least_count = trim($_POST['least_count']);
    $ref_std = trim($_POST['ref_std']);

    $stmt = $conn->prepare("INSERT INTO instrumentmaster (
        INSTRUMENT_ID, CUSTOMER_ID, IDENTIFICATION_NO, DESCRIPTION,
        CALIBRATION_RANGE, ACTUAL_RANGE_CALIBRATED, MAKE, PERIODICITY,
        LEAST_COUNT, REF_STD, TMSTP_ENTERED, ID_USER_ENTERED
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'admin')");

    $stmt->bind_param(
        "ssssssssss",
        $instrument_id,
        $customer_id,
        $identification_no,
        $description,
        $calibration_range,
        $actual_range,
        $make,
        $periodicity,
        $least_count,
        $ref_std
    );

    if ($stmt->execute()) {
        // Success message
        $message = "<p style='color:green;'>Instrument added successfully.</p>";
        // Clear POST data to avoid duplicate submission on refresh
        $_POST = array();
    } else {
        $message = "<p style='color:red;'>Error: " . htmlspecialchars($conn->error) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Instrument Master</title>
</head>
<body>
  <h2>Instrument Master</h2>

  <!-- Display message -->
  <?php echo $message; ?>

  <!-- Add New Instrument -->
  <form method="POST" action="">
    <label>Instrument ID:</label>
    <input type="text" name="instrument_id" value="<?php echo isset($_POST['instrument_id']) ? htmlspecialchars($_POST['instrument_id']) : ''; ?>" required><br><br>

    <label>Customer ID:</label>
    <select name="customer_id" required>
      <option value="">-- Select Customer --</option>
      <?php
      $customers = $conn->query("SELECT CUSTOMER_ID, CUSTOMER_DESC FROM customermaster");
      while ($c = $customers->fetch_assoc()) {
        $selected = (isset($_POST['customer_id']) && $_POST['customer_id'] == $c['CUSTOMER_ID']) ? 'selected' : '';
        echo "<option value='{$c['CUSTOMER_ID']}' $selected>{$c['CUSTOMER_ID']} - {$c['CUSTOMER_DESC']}</option>";
      }
      ?>
    </select><br><br>

    <label>Identification No:</label>
    <input type="text" name="identification_no" value="<?php echo isset($_POST['identification_no']) ? htmlspecialchars($_POST['identification_no']) : ''; ?>" required><br><br>

    <label>Description:</label>
    <input type="text" name="description" value="<?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?>"><br><br>

    <label>Calibration Range:</label>
    <input type="text" name="calibration_range" value="<?php echo isset($_POST['calibration_range']) ? htmlspecialchars($_POST['calibration_range']) : ''; ?>"><br><br>

    <label>Actual Range Calibrated:</label>
    <input type="text" name="actual_range" value="<?php echo isset($_POST['actual_range']) ? htmlspecialchars($_POST['actual_range']) : ''; ?>"><br><br>

    <label>Make:</label>
    <input type="text" name="make" value="<?php echo isset($_POST['make']) ? htmlspecialchars($_POST['make']) : ''; ?>"><br><br>

    <label>Periodicity:</label>
    <input type="text" name="periodicity" value="<?php echo isset($_POST['periodicity']) ? htmlspecialchars($_POST['periodicity']) : ''; ?>"><br><br>

    <label>Least Count:</label>
    <input type="text" name="least_count" value="<?php echo isset($_POST['least_count']) ? htmlspecialchars($_POST['least_count']) : ''; ?>"><br><br>

    <label>Reference Std:</label>
    <input type="text" name="ref_std" value="<?php echo isset($_POST['ref_std']) ? htmlspecialchars($_POST['ref_std']) : ''; ?>"><br><br>

    <input type="submit" name="submit" value="Add Instrument">
  </form>

  <hr>

  <h3>Existing Instruments</h3>
  <table border="1" cellpadding="5" cellspacing="0">
    <tr>
      <th>Instrument ID</th>
      <th>Customer ID</th>
      <th>Identification No</th>
      <th>Make</th>
      <th>Description</th>
    </tr>
    <?php
    $result = $conn->query("SELECT INSTRUMENT_ID, CUSTOMER_ID, IDENTIFICATION_NO, MAKE, DESCRIPTION FROM instrumentmaster ORDER BY INSTRUMENT_ID ASC");
    while ($row = $result->fetch_assoc()) {
      echo "<tr>
        <td>" . htmlspecialchars($row['INSTRUMENT_ID']) . "</td>
        <td>" . htmlspecialchars($row['CUSTOMER_ID']) . "</td>
        <td>" . htmlspecialchars($row['IDENTIFICATION_NO']) . "</td>
        <td>" . htmlspecialchars($row['MAKE']) . "</td>
        <td>" . htmlspecialchars($row['DESCRIPTION']) . "</td>
      </tr>";
    }
    ?>
  </table>
</body>
</html>
