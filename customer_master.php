<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <title>Customer Master</title>
</head>
<body>
  <h2>Customer Master</h2>

  <!-- Add New Customer Form -->
  <form method="POST" action="">
    <label>Customer ID:</label>
    <input type="text" name="customer_id" required><br>
    
    <label>Customer Description:</label>
    <input type="text" name="customer_desc" required><br>

    <input type="submit" name="submit" value="Add Customer">
  </form>

  <hr>

  <!-- Display Customer Table -->
  <h3>Existing Customers</h3>
  <table border="1" cellpadding="5">
    <tr>
      <th>Customer ID</th>
      <th>Description</th>
    </tr>
    <?php
    $result = $conn->query("SELECT CUSTOMER_ID, CUSTOMER_DESC FROM customermaster");
    while ($row = $result->fetch_assoc()) {
      echo "<tr><td>{$row['CUSTOMER_ID']}</td><td>{$row['CUSTOMER_DESC']}</td></tr>";
    }
    ?>
  </table>

  <?php
  // Handle form submission
  if (isset($_POST['submit'])) {
    $id = $_POST['customer_id'];
    $desc = $_POST['customer_desc'];

    $stmt = $conn->prepare("INSERT INTO customermaster (CUSTOMER_ID, CUSTOMER_DESC, TMSTP_ENTERED, ID_USER_ENTERED) VALUES (?, ?, NOW(), 'admin')");
    $stmt->bind_param("ss", $id, $desc);
    if ($stmt->execute()) {
      echo "<p style='color:green;'>Customer added successfully. Refresh the page.</p>";
    } else {
      echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
  }
  ?>
</body>
</html>
