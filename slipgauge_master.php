<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <title>Slip Gauge Master</title>
</head>
<body>
  <h2>Slip Gauge Master</h2>

  <!-- Add New Slip Gauge -->
  <form method="POST" action="">
    <label>Slip Gauge ID:</label>
    <input type="text" name="slip_gauge_id" required><br>

    <label>Certificate No:</label>
    <input type="text" name="certificate_no" required><br>

    <label>Certificate Date:</label>
    <input type="date" name="certificate_date" required><br>

    <label>Serial No:</label>
    <input type="text" name="serial_no" required><br>

    <label>Whether Obsolete (Y/N):</label>
    <input type="text" name="obsolete" maxlength="1" required><br>

    <input type="submit" name="submit" value="Add Slip Gauge">
  </form>

  <hr>

  <!-- View Existing Slip Gauges -->
  <h3>Existing Slip Gauges</h3>
  <table border="1" cellpadding="5">
    <tr>
      <th>Slip Gauge ID</th>
      <th>Cert No</th>
      <th>Cert Date</th>
      <th>Serial No</th>
      <th>Obsolete?</th>
    </tr>
    <?php
    $result = $conn->query("SELECT * FROM slipgaugemaster ORDER BY SLIP_GAUGE_ID DESC");
    while ($row = $result->fetch_assoc()) {
      echo "<tr>
        <td>{$row['SLIP_GAUGE_ID']}</td>
        <td>{$row['CERTIFICATE_NO']}</td>
        <td>{$row['CERTIFICATE_DATE']}</td>
        <td>{$row['SERIAL_NO']}</td>
        <td>{$row['WHETHER_OBSOLETE']}</td>
      </tr>";
    }
    ?>
  </table>

  <?php
  if (isset($_POST['submit'])) {
    $stmt = $conn->prepare("INSERT INTO slipgaugemaster (
      SLIP_GAUGE_ID, CERTIFICATE_NO, CERTIFICATE_DATE,
      SERIAL_NO, WHETHER_OBSOLETE, TMSTP_ENTERED, ID_USER_ENTERED
    ) VALUES (?, ?, ?, ?, ?, NOW(), 'admin')");

    $stmt->bind_param(
      "sssss",
      $_POST['slip_gauge_id'],
      $_POST['certificate_no'],
      $_POST['certificate_date'],
      $_POST['serial_no'],
      $_POST['obsolete']
    );

    if ($stmt->execute()) {
      echo "<p style='color:green;'>Slip Gauge added successfully. Refresh the page.</p>";
    } else {
      echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
  }
  ?>
</body>
</html>
