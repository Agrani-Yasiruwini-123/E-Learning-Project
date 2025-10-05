<div class="container-fluid">

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <span class="d-none d-sm-inline-block text-muted">Platform Overview</span>
  </div>

  <!-- Stat Cards Row -->
  <div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="card-title text-uppercase text-muted mb-2">Total Users</h6>
              <span class="h2 mb-0 font-weight-bold"><?php echo $total_users; ?></span>
            </div>
            <div class="col-auto">
              <div class="icon-shape bg-primary text-white rounded-circle shadow">
                <i class="fas fa-users"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="card-title text-uppercase text-muted mb-2">Total Courses</h6>
              <span class="h2 mb-0 font-weight-bold"><?php echo $total_courses; ?></span>
            </div>
            <div class="col-auto">
              <div class="icon-shape bg-success text-white rounded-circle shadow">
                <i class="fas fa-book-open"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="card-title text-uppercase text-muted mb-2">Total Enrollments</h6>
              <span class="h2 mb-0 font-weight-bold"><?php echo $total_enrollments; ?></span>
            </div>
            <div class="col-auto">
              <div class="icon-shape bg-info text-white rounded-circle shadow">
                <i class="fas fa-clipboard-list"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="card-title text-uppercase text-muted mb-2">New Users (30d)</h6>
              <span class="h2 mb-0 font-weight-bold"><?php echo $new_users; ?></span>
              <span class="text-success ms-2"><i class="fas fa-arrow-up"></i></span>
            </div>
            <div class="col-auto">
              <div class="icon-shape bg-warning text-white rounded-circle shadow">
                <i class="fas fa-user-plus"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Content Row -->
  <div class="row">
    <!-- Quick Actions & Recent Registrations -->
    <div class="col-lg-7 mb-4">
      <!-- Quick Actions -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
        </div>
        <div class="card-body text-center">
          <a href="manage-instructors.php" class="btn btn-outline-primary btn-lg m-2">
            <i class="fas fa-user-plus me-2"></i>Add New Instructor
          </a>
        </div>
      </div>
      <!-- Recent Registrations -->
      <div class="card shadow">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Recent Registrations</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <tbody>
                <?php foreach ($recent_users as $user): ?>
                  <tr>
                    <td><i class="fas fa-user text-muted me-2"></i> <?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="text-end text-muted"><?php echo date("M j, Y", strtotime($user['registration_date'])); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Enrollments -->
    <div class="col-lg-5 mb-4">
      <div class="card shadow">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Recent Enrollments</h6>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <?php foreach ($recent_enrollments as $enrollment): ?>
              <li class="list-group-item">
                <div class="fw-bold"><?php echo htmlspecialchars($enrollment['username']); ?></div>
                <small class="text-muted">enrolled in "<?php echo htmlspecialchars($enrollment['course_title']); ?>"</small>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .icon-shape {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
  }

  .icon-shape i {
    font-size: 1.25rem;
  }

  .card .table {
    margin-bottom: 0;
  }
</style>

<?php
$conn->close();
require 'includes/footer.php';
?>