<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scout Management</title>
  <?php
  $is_localhost = isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
  if ($is_localhost) {
  ?>
    <link href="https://localhost/centennial/shared/assets/styles.css" rel="stylesheet" />
  <?php
  } else { ?>
    <link href="https://shared.centennialdistrict.co/assets/styles.css" rel="stylesheet" />
  <?php } ?>
</head>

<body>
  <div class="container mt-5">
    <h1 class="text-center">Life Scout</h1>
    <form method="post" class="mt-4">
      <div class="row mb-3">
        <div class="col-md-4">
          <label for="ScoutName" class="form-label">Choose a Scout:</label>
          <select class="form-select" id="ScoutName" name="ScoutName" required>
            <?php while ($rowScouts = $result_ByScout->fetch_assoc()): ?>
              <option value="<?= htmlspecialchars($rowScouts['Scoutid']) ?>">
                <?= htmlspecialchars($rowScouts['LastName'] . ', ' . $rowScouts['FirstName']) ?>
              </option>
            <?php endwhile; ?>
            <option value="-1">Add New Scout</option>
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" name="SubmitScout" value="Select Scout" class="btn btn-primary">Select Scout</button>
        </div>
      </div>
    </form>

    <?php if (isset($rowScout)): ?>
      <?php include 'ScoutForm.php'; ?>
    <?php endif; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>