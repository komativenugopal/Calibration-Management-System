<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <title>Slip Gauge Error Entry</title>
</head>
<body>
  <h2>Slip Gauge Error Entry</h2>

  <!-- Select Slip Gauge -->
  <form method="GET">
    <label>Select Slip Gauge ID:</label>
    <select name="slip_gauge_id" onchange="this.form.submit()">
      <option value="">-- Select Slip Gauge --</option>
      <?php
      $slips = $conn->query("SELECT SLIP_GAUGE_ID FROM slipgaugemaster ORDER BY SLIP_GAUGE_ID");
      while ($s = $slips->fetch_assoc()) {
        $selected = ($_GET['slip_gauge_id'] ?? '') == $s['SLIP_GAUGE_ID'] ? 'selected' : '';
        echo "<option value='{$s['SLIP_GAUGE_ID']}' $selected>{$s['SLIP_GAUGE_ID']}</option>";
      }
      ?>
    </select>
  </form>

  <hr>

  <?php
  if (!empty($_GET['slip_gauge_id'])):
    $slip_id = $_GET['slip_gauge_id'];
    $info = $conn->query("SELECT * FROM slipgaugemaster WHERE SLIP_GAUGE_ID = '$slip_id'")->fetch_assoc();
  ?>

  <h3>Slip Gauge Info</h3>
  <p>
    <b>Serial No:</b> <?= $info['SERIAL_NO'] ?> &nbsp;
    <b>Certificate No:</b> <?= $info['CERTIFICATE_NO'] ?> &nbsp;
    <b>Certificate Date:</b> <?= $info['CERTIFICATE_DATE'] ?>
  </p>

  <!-- Add New Entry -->
  <form method="POST">
    <input type="hidden" name="slip_gauge_id" value="<?= $slip_id ?>">
    <label>Nominal Size (mm):</label>
    <input type="number" step="0.001" name="nominal_size" required>
    
    <label>Error in Slip (mm):</label>
    <input type="number" step="0.001" name="error" required>

    <input type="submit" name="add" value="Add Entry">
  </form>

  <br>

  <!-- Display Existing Records -->
  <h3>Error Records for <?= $slip_id ?></h3>
  <table border="1" cellpadding="5">
    <tr><th>Nominal Size</th><th>Error</th><th>Action</th></tr>
    <?php
    $rows = $conn->query("SELECT NOMINAL_SIZE, ERROR_IN_SLIP_GAUGE FROM slipgaugedetails WHERE SLIP_GAUGE_ID = '$slip_id' ORDER BY NOMINAL_SIZE");
    while ($row = $rows->fetch_assoc()) {
      echo "<tr>
              <td>{$row['NOMINAL_SIZE']}</td>
              <td>{$row['ERROR_IN_SLIP_GAUGE']}</td>
              <td>
                <form method='POST' style='display:inline'>
                  <input type='hidden' name='delete' value='{$row['NOMINAL_SIZE']}'>
                  <input type='hidden' name='slip_gauge_id' value='$slip_id'>
                  <input type='submit' value='Delete'>
                </form>
              </td>
            </tr>";
    }
    ?>
  </table>

  <?php endif; ?>

  <?php
  // Insert new record
  if (isset($_POST['add'])) {
    $id = $_POST['slip_gauge_id'];
    $size = $_POST['nominal_size'];
    $error = $_POST['error'];

    $stmt = $conn->prepare("INSERT INTO slipgaugedetails (SLIP_GAUGE_ID, NOMINAL_SIZE, ERROR_IN_SLIP_GAUGE, TMSTP_ENTERED, ID_USER_ENTERED) VALUES (?, ?, ?, NOW(), 'admin')");
    $stmt->bind_param("sdd", $id, $size, $error);

    if ($stmt->execute()) {
      header("Location: slipgauge_details.php?slip_gauge_id=$id");
      exit();
    } else {
      echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
  }

  // Delete record
  if (isset($_POST['delete'])) {
    $id = $_POST['slip_gauge_id'];
    $size = $_POST['delete'];

    $conn->query("DELETE FROM slipgaugedetails WHERE SLIP_GAUGE_ID = '$id' AND NOMINAL_SIZE = $size");
    header("Location: slipgauge_details.php?slip_gauge_id=$id");
    exit();
  }
  ?>
</body>
</html>
