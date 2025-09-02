<?php
// Allow pages to handle DB failures without crashing
if (!defined('ALLOW_DB_FAIL_SILENT')) {
  define('ALLOW_DB_FAIL_SILENT', true);
}

require_once __DIR__ . '/database.php';

$students = [];
$usingDummyRecords = false;

// Attempt to load from DB; on failure, show demo data
if (isset($pdo) && $pdo instanceof PDO) {
  try {
    $stmt = $pdo->query('SELECT id, full_name, email, department, matric_number, registration_date FROM student_records ORDER BY id ASC');
    $students = $stmt->fetchAll();
  } catch (Throwable $e) {
    $usingDummyRecords = true;
  }
} else {
  $usingDummyRecords = true;
}

if ($usingDummyRecords) {
  // Persist demo records per session and allow dummy deletion
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }

  if (!isset($_SESSION['demo_students'])) {
    $_SESSION['demo_students'] = [
      [
        'id' => 1,
        'full_name' => 'Gideon Ibanga',
        'email' => 'anotherbanga@gmail.com',
        'department' => 'Computer Science',
        'matric_number' => '23/CSC/149',
        'registration_date' => date('Y-m-d H:i:s', strtotime('-2 days')),
      ],
      [
        'id' => 2,
        'full_name' => 'Nelson Etim',
        'email' => 'nelly.young@gmail.com',
        'department' => 'Electrical Engineering',
        'matric_number' => '24/EEE/014',
        'registration_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
      ],
      [
        'id' => 3,
        'full_name' => 'Aminu Bello',
        'email' => 'aminu.bello@gmail.com',
        'department' => 'Mathematics',
        'matric_number' => '25/MTH/027',
        'registration_date' => date('Y-m-d H:i:s'),
      ],
      [
        'id' => 4,
        'full_name' => 'Stanley Emmanuel',
        'email' => 'stanley.emma2@gmail.com',
        'department' => 'Computer Science',
        'matric_number' => '22/CSC/015',
        'registration_date' => date('Y-m-d H:i:s', strtotime('-2 days')),
      ],[
        'id' => 5,
        'full_name' => 'Goodnews Anwana',
        'email' => 'g.anwana234@gmail.com',
        'department' => 'Computer Science',
        'matric_number' => '21/CSC/029',
        'registration_date' => date('Y-m-d H:i:s', strtotime('-2 days')),
      ],
    ];
  }

  // Handle dummy delete postback
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['demo_delete_id'])) {
    $deleteId = (int)$_POST['demo_delete_id'];
    $_SESSION['demo_students'] = array_values(array_filter(
      $_SESSION['demo_students'],
      function ($s) use ($deleteId) { return (int)$s['id'] !== $deleteId; }
    ));
    header('Location: view.php?status=success&message=' . urlencode('Record deleted'));
    exit;
  }

  // Use the session-stored demo list and order by id ASC (to match requested sorting)
  $students = $_SESSION['demo_students'];
  usort($students, function ($a, $b) { return (int)$a['id'] <=> (int)$b['id']; });
}
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
                      <?php if (!empty($usingDummyRecords)): ?>
                        <form action="view.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                          <input type="hidden" name="demo_delete_id" value="<?= (int)$row['id'] ?>" />
                          <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                      <?php else: ?>
                        <form action="delete.php" method="POST" onsubmit="return confirm('Delete this record?');">
                          <input type="hidden" name="id" value="<?= (int)$row['id'] ?>" />
                          <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                      <?php endif; ?>
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