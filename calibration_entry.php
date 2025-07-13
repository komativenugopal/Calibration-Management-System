<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <title>Calibration Entry</title>
</head>
<body>
  <h2>Calibration Entry</h2>

  <!-- Calibration Form -->
  <form method="POST">
    <label>Calibration ID:</label>
    <input type="text" name="calibration_id" required><br>

    <label>Instrument ID:</label>
    <select name="instrument_id" required>
      <option value="">-- Select Instrument --</option>
      <?php
      $instruments = $conn->query("SELECT INSTRUMENT_ID FROM instrumentmaster ORDER BY INSTRUMENT_ID");
      while ($row = $instruments->fetch_assoc()) {
        echo "<option value='{$row['INSTRUMENT_ID']}'>{$row['INSTRUMENT_ID']}</option>";
      }
      ?>
    </select><br>

    <label>Slip Gauge ID:</label>
    <select name="slip_gauge_id" required>
      <option value="">-- Select Slip Gauge --</option>
      <?php
      $slips = $conn->query("SELECT SLIP_GAUGE_ID FROM slipgaugemaster ORDER BY SLIP_GAUGE_ID");
      while ($row = $slips->fetch_assoc()) {
        echo "<option value='{$row['SLIP_GAUGE_ID']}'>{$row['SLIP_GAUGE_ID']}</option>";
      }
      ?>
    </select><br>

    <label>Reference No:</label>
    <input type="text" name="reference_no"><br>

    <label>Reference Date:</label>
    <input type="date" name="reference_date"><br>

    <label>Calibration Date:</label>
    <input type="date" name="calibration_date" required><br>

    <label>Temperature (°C):</label>
    <input type="number" name="temp" required><br>

    <label>Humidity (%):</label>
    <input type="number" name="humidity" required><br>

    <input type="submit" name="submit" value="Save Calibration">
  </form>

  <hr>

  <!-- View Existing Entries -->
  <h3>Existing Calibration Sessions</h3>
  <table border="1" cellpadding="5">
    <tr>
      <th>Calibration ID</th>
      <th>Instrument</th>
      <th>Slip Gauge</th>
      <th>Calib. Date</th>
      <th>Temp</th>
      <th>Humidity</th>
    </tr>
    <?php
    $entries = $conn->query("SELECT * FROM calibrationmaster ORDER BY CALIBRATION_DATE DESC");
    while ($row = $entries->fetch_assoc()) {
      echo "<tr>
        <td>{$row['CALIBRATION_ID']}</td>
        <td>{$row['INSTRUMENT_ID']}</td>
        <td>{$row['SLIP_GAUGE_ID']}</td>
        <td>{$row['CALIBRATION_DATE']}</td>
        <td>{$row['TEMP']}</td>
        <td>{$row['HUMIDITY']}</td>
      </tr>";
    }
    ?>
  </table>

  <?php
  if (isset($_POST['submit'])) {
    $stmt = $conn->prepare("INSERT INTO calibrationmaster (
      CALIBRATION_ID, INSTRUMENT_ID, SLIP_GAUGE_ID,
      REFERENCE_NO, REFERENCE_DATE, CALIBRATION_DATE,
      TEMP, HUMIDITY, TMSTP_ENTERED, ID_USER_ENTERED
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'admin')");

    $stmt->bind_param(
      "ssssssii",
      $_POST['calibration_id'],
      $_POST['instrument_id'],
      $_POST['slip_gauge_id'],
      $_POST['reference_no'],
      $_POST['reference_date'],
      $_POST['calibration_date'],
      $_POST['temp'],
      $_POST['humidity']
    );

    if ($stmt->execute()) {
      echo "<p style='color:green;'>Calibration saved successfully. Refresh to view.</p>";
    } else {
      echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
  }
  ?>
</body>
</html>
