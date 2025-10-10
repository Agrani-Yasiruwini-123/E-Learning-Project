<!-- Collapsible Add Instructor Form -->
<div class="accordion mb-4" id="addInstructorAccordion">
  <div class="accordion-item card shadow">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
        <i class="fas fa-plus-circle me-2"></i> Add New Instructor
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#addInstructorAccordion">
      <div class="accordion-body">
        <form action="manage-instructors.php" method="POST">
          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="password" class="form-label">Set Initial Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
          </div>
          <button type="submit" name="add_instructor" class="btn btn-primary">Add Instructor</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Current Instructors List Card -->
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Current Instructors</h6>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="dataTable">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Date Registered</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($instructors)): ?>
            <tr>
              <td colspan="5" class="text-center">No instructors found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($instructors as $instructor): ?>
              <tr>
                <td><?php echo $instructor['user_id']; ?></td>
                <td><?php echo htmlspecialchars($instructor['username']); ?></td>
                <td><?php echo htmlspecialchars($instructor['email']); ?></td>
                <td><?php echo date("M j, Y", strtotime($instructor['registration_date'])); ?></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-warning edit-btn"
                    data-bs-toggle="modal" data-bs-target="#editModal"
                    data-id="<?php echo $instructor['user_id']; ?>"
                    data-username="<?php echo htmlspecialchars($instructor['username']); ?>"
                    data-email="<?php echo htmlspecialchars($instructor['email']); ?>">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                  <button class="btn btn-sm btn-danger delete-btn"
                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                    data-id="<?php echo $instructor['user_id']; ?>"
                    data-username="<?php echo htmlspecialchars($instructor['username']); ?>">
                    <i class="fas fa-trash"></i> Remove
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>

<!-- Edit Instructor Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Instructor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="manage-instructors.php" method="POST">
        <div class="modal-body">
          <input type="hidden" id="edit-user-id" name="user_id_to_edit">
          <div class="mb-3">
            <label for="edit-username" class="form-label">Username</label>
            <input type="text" class="form-control" id="edit-username" name="username" required>
          </div>
          <div class="mb-3">
            <label for="edit-email" class="form-label">Email</label>
            <input type="email" class="form-control" id="edit-email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="edit-password" class="form-label">New Password (Optional)</label>
            <input type="password" class="form-control" id="edit-password" name="password">
            <small class="form-text text-muted">Leave blank to keep the current password.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="edit_instructor" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to remove the instructor <strong id="delete-username"></strong>? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <form action="manage-instructors.php" method="POST">
          <input type="hidden" id="delete-user-id" name="user_id_to_delete">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_instructor" class="btn btn-danger">Yes, Remove Instructor</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$conn->close();
require 'includes/footer.php';
?>