<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <title>Nominal Size Entry</title>
</head>
<body>
  <h2>Nominal Size Entry</h2>

  <!-- Instrument Selection -->
  <form method="GET">
    <label>Select Instrument ID:</label>
    <select name="instrument_id" onchange="this.form.submit()">
      <option value="">-- Select Instrument --</option>
      <?php
      $instruments = $conn->query("SELECT INSTRUMENT_ID FROM instrumentmaster");
      while ($row = $instruments->fetch_assoc()) {
        $selected = ($_GET['instrument_id'] ?? '') == $row['INSTRUMENT_ID'] ? 'selected' : '';
        echo "<option value='{$row['INSTRUMENT_ID']}' $selected>{$row['INSTRUMENT_ID']}</option>";
      }
      ?>
    </select>
  </form>

  <hr>

  <?php
  if (!empty($_GET['instrument_id'])):
    $instrument_id = $_GET['instrument_id'];
    $info = $conn->query("SELECT * FROM instrumentmaster WHERE INSTRUMENT_ID = '$instrument_id'")->fetch_assoc();
  ?>

  <h3>Instrument Info</h3>
  <p><b>Customer ID:</b> <?= $info['CUSTOMER_ID'] ?> &nbsp;&nbsp;
     <b>Make:</b> <?= $info['MAKE'] ?> &nbsp;&nbsp;
     <b>Description:</b> <?= $info['DESCRIPTION'] ?> &nbsp;&nbsp;
     <b>Least Count:</b> <?= $info['LEAST_COUNT'] ?></p>

  <!-- Add Nominal Size -->
  <form method="POST" action="">
    <input type="hidden" name="instrument_id" value="<?= $instrument_id ?>">
    <label>Add Nominal Size (mm):</label>
    <input type="number" name="nominal_size" step="0.001" required>
    <input type="submit" name="add_size" value="Add Size">
  </form>

  <br>

  <!-- Existing Sizes -->
  <h3>Nominal Sizes for <?= $instrument_id ?></h3>
  <table border="1" cellpadding="5">
    <tr><th>Nominal Size</th><th>Action</th></tr>
    <?php
    $sizes = $conn->query("SELECT NOMINAL_SIZE FROM nominalsizes WHERE INSTRUMENT_ID = '$instrument_id' ORDER BY NOMINAL_SIZE");
    while ($row = $sizes->fetch_assoc()) {
      echo "<tr>
              <td>{$row['NOMINAL_SIZE']}</td>
              <td>
                <form method='POST' style='display:inline;'>
                  <input type='hidden' name='delete_size' value='{$row['NOMINAL_SIZE']}'>
                  <input type='hidden' name='instrument_id' value='$instrument_id'>
                  <input type='submit' value='X delete'>
                </form>
              </td>
            </tr>";
    }
    ?>
  </table>

  <?php endif; ?>

  <?php
  // Insert nominal size
  if (isset($_POST['add_size'])) {
    $id = $_POST['instrument_id'];
    $size = $_POST['nominal_size'];
    $stmt = $conn->prepare("INSERT INTO nominalsizes (INSTRUMENT_ID, NOMINAL_SIZE, TMSTP_ENTERED, ID_USER_ENTERED) VALUES (?, ?, NOW(), 'admin')");
    $stmt->bind_param("sd", $id, $size);
    if ($stmt->execute()) {
      header("Location: nominal_size_entry.php?instrument_id=" . $id);
      exit();
    } else {
      echo "<p style='color:red;'>Error adding size: " . $conn->error . "</p>";
    }
  }

  // Delete nominal size
  if (isset($_POST['delete_size'])) {
    $id = $_POST['instrument_id'];
    $size = $_POST['delete_size'];
    $conn->query("DELETE FROM nominalsizes WHERE INSTRUMENT_ID = '$id' AND NOMINAL_SIZE = $size");
    header("Location: nominal_size_entry.php?instrument_id=" . $id);
    exit();
  }
  ?>
</body>
</html>
