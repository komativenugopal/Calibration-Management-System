<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <title>Calibration Details</title>
</head>
<body>

<h2>Calibration Details Entry</h2>

<!-- 🔽 Calibration ID Dropdown -->
<form method="GET" action="">
  <label>Select Calibration ID:</label>
  <select name="calibration_id" onchange="this.form.submit()" required>
    <option value="">-- Select Calibration --</option>
    <?php
    $result = $conn->query("SELECT CALIBRATION_ID FROM calibrationmaster ORDER BY CALIBRATION_DATE DESC");
    while ($row = $result->fetch_assoc()) {
      $selected = ($_GET['calibration_id'] ?? '') == $row['CALIBRATION_ID'] ? 'selected' : '';
      echo "<option value='{$row['CALIBRATION_ID']}' $selected>{$row['CALIBRATION_ID']}</option>";
    }
    ?>
  </select>
</form>

<hr>

  <title>Calibration Data Entry</title>
  <style>
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
      padding: 6px;
      text-align: center;
    }
    th {
      background-color: #d9e7ff;
    }
    input[type="text"] {
      width: 80px;
    }
  </style>
</head>
<body>
  <h2>Calibration Data Entry</h2>

  <?php
  $resultsTable = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Save calibration header if needed
    // You can extend this section to save meta fields like instrument_id, date, etc.

    $nominal_sizes = $_POST['nominal_size'];
    $measured_1 = $_POST['measured_1'];
    $measured_2 = $_POST['measured_2'];
    $measured_3 = $_POST['measured_3'];

    $resultsTable .= "<h3>Calibration Results</h3>
    <table>
      <tr>
        <th>Nominal Size</th>
        <th>Actual Measured 1</th>
        <th>Actual Measured 2</th>
        <th>Actual Measured 3</th>
        <th>Error in Slip Gauge</th>
      </tr>";

    for ($i = 0; $i < count($nominal_sizes); $i++) {
      $nom = floatval($nominal_sizes[$i]);
      $m1 = floatval($measured_1[$i]);
      $m2 = floatval($measured_2[$i]);
      $m3 = floatval($measured_3[$i]);

      if ($nom == 0 && $m1 == 0 && $m2 == 0 && $m3 == 0) continue; // skip empty rows

      $avg = ($m1 + $m2 + $m3) / 3;
      $error = round($avg - $nom, 4);

      $resultsTable .= "<tr>
        <td>{$nom}</td>
        <td>{$m1}</td>
        <td>{$m2}</td>
        <td>{$m3}</td>
        <td>{$error}</td>
      </tr>";

      // Optionally insert into DB if needed
    }

    $resultsTable .= "</table>";
  }
  ?>

  <!-- Form to enter multiple calibration entries -->
  <form method="POST" action="">
    <table>
      <tr>
        <th>Nominal Size</th>
        <th>Actual Measured 1</th>
        <th>Actual Measured 2</th>
        <th>Actual Measured 3</th>
      </tr>

      <?php for ($i = 0; $i < 1; $i++): ?>
      <tr>
        <td><input type="text" name="nominal_size[]" /></td>
        <td><input type="text" name="measured_1[]" /></td>
        <td><input type="text" name="measured_2[]" /></td>
        <td><input type="text" name="measured_3[]" /></td>
      </tr>
      <?php endfor; ?>
    </table><br>

    <input type="submit" value="Calculate & Save">
  </form>

  <!-- Show calculated error table -->
  <?php echo $resultsTable; ?>

</body>
</html>
