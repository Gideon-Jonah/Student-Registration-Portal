<?php
require_once __DIR__ . '/database.php';

// Fetch all records
$stmt = $pdo->query('SELECT id, full_name, email, department, matric_number, registration_date FROM student_records ORDER BY registration_date DESC');
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Records</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header class="site-header">
    <h1>Student Records</h1>
    <nav>
      <a class="nav-link" href="index.html">Register</a>
    </nav>
  </header>

  <main class="container">
    <section class="card">
      <h2 class="card-title">Registered Students</h2>

      <?php if (isset($_GET['status'], $_GET['message'])): ?>
        <div class="alert <?= $_GET['status'] === 'success' ? 'alert-success' : 'alert-error' ?>">
          <?= htmlspecialchars($_GET['message']) ?>
        </div>
      <?php endif; ?>

      <?php if (empty($students)): ?>
        <div class="alert alert-error">No records found.</div>
      <?php else: ?>
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Matric Number</th>
                <th>Registered</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($students as $row): ?>
                <tr>
                  <td><?= (int)$row['id'] ?></td>
                  <td><?= htmlspecialchars($row['full_name']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['department']) ?></td>
                  <td><?= htmlspecialchars($row['matric_number']) ?></td>
                  <td><?= htmlspecialchars($row['registration_date']) ?></td>
                  <td>
                    <div class="actions">
                      <form action="delete.php" method="POST" onsubmit="return confirm('Delete this record?');">
                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>" />
                        <button class="btn btn-danger" type="submit">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <footer class="site-footer">
    <small>&copy; <?= date('Y'); ?> Student Registration System</small>
  </footer>
</body>
</html>