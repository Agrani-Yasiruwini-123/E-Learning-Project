<div class="row">
  <!-- Add Category Form Card -->
  <div class="col-lg-4 mb-4">
    <div class="card shadow">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle me-2"></i>Add New Category</h6>
      </div>
      <div class="card-body">
        <form action="manage-categories.php" method="POST">
          <div class="mb-3">
            <label for="category_name" class="form-label">Category Name</label>
            <input type="text" class="form-control" id="category_name" name="category_name" required>
          </div>
          <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Current Categories List Card -->
  <div class="col-lg-8">
    <div class="card shadow">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ul me-2"></i>Current Categories</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="dataTable">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($categories)): ?>
                <tr>
                  <td colspan="3" class="text-center">No categories found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($categories as $category): ?>
                  <tr>
                    <td><?php echo $category['category_id']; ?></td>
                    <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-warning edit-btn"
                        data-bs-toggle="modal" data-bs-target="#editModal"
                        data-id="<?php echo $category['category_id']; ?>"
                        data-name="<?php echo htmlspecialchars($category['category_name']); ?>">
                        <i class="fas fa-edit"></i> Edit
                      </button>
                      <button class="btn btn-sm btn-danger delete-btn"
                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                        data-id="<?php echo $category['category_id']; ?>"
                        data-name="<?php echo htmlspecialchars($category['category_name']); ?>">
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
</div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="manage-categories.php" method="POST">
        <div class="modal-body">
          <input type="hidden" id="edit-category-id" name="category_id_to_edit">
          <div class="mb-3">
            <label for="edit-category-name" class="form-label">Category Name</label>
            <input type="text" class="form-control" id="edit-category-name" name="category_name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="edit_category" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to remove the category <strong id="delete-category-name"></strong>? This could affect existing courses.
      </div>
      <div class="modal-footer">
        <form action="manage-categories.php" method="POST">
          <input type="hidden" id="delete-category-id" name="category_id_to_delete">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_category" class="btn btn-danger">Yes, Remove Category</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript to populate modals -->

<?php
$conn->close();
require 'includes/footer.php';
?>