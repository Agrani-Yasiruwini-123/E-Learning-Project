<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Edit Course</h1>
  <a href="dashboard.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
  <?php if ($feedback_message) : ?>
    <div class="alert <?php echo $feedback_class; ?> alert-dismissible fade show" role="alert"><?php echo $feedback_message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <form action="course-edit.php?id=<?php echo $course_id; ?>" method="POST" enctype="multipart/form-data">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Course Details</h6>
      </div>
      <div class="card-body">
        <div class="mb-3"><label class="form-label">Course Title</label><input type="text" class="form-control" name="course_title" value="<?php echo htmlspecialchars($course['course_title']); ?>" required></div>
        <div class="mb-3"><label class="form-label">Course Description</label><textarea class="form-control" name="course_description" rows="4" required><?php echo htmlspecialchars($course['course_description']); ?></textarea></div>
        <div class="row">
          <div class="col-md-6 mb-3"><label class="form-label">Category</label><select class="form-select" name="category" required><?php foreach ($categories as $cat): ?><option value="<?php echo htmlspecialchars($cat['category_name']); ?>" <?php if ($course['category'] == $cat['category_name']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option><?php endforeach; ?></select></div>
          <div class="col-md-6 mb-3"><label class="form-label">Change Course Thumbnail</label><input type="file" class="form-control" name="course_thumbnail" accept="image/*">
            <div class="mt-2"><small>Current:</small><img src="../<?php echo htmlspecialchars($course['course_thumbnail']); ?>" class="img-thumbnail" width="100"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Course Content (Lessons)</h6>
      </div>
      <div class="card-body" id="course-content-section">
        <?php foreach ($curriculum as $index => $item): ?>
          <div class="course-content-item card p-3 mb-3">
            <div class="d-flex justify-content-end"><button type="button" class="btn-close remove-content-btn"></button></div>
            <div class="mb-3"><label class="form-label">Lesson Type</label><select class="form-select content-type-select" name="content_type[]">
                <option value="video" <?php if ($item['content_type'] == 'video') echo 'selected'; ?>>Video</option>
                <option value="quiz" <?php if ($item['content_type'] == 'quiz') echo 'selected'; ?>>Quiz</option>
              </select></div>
            <div class="mb-3"><label class="form-label">Lesson Description</label><textarea class="form-control" name="content_description[]" rows="2"><?php echo htmlspecialchars($item['content_description']); ?></textarea></div>
            <div class="video-fields" style="<?php if ($item['content_type'] != 'video') echo 'display: none;'; ?>">
              <div class="mb-3"><label class="form-label">Video Title</label><input type="text" class="form-control" name="video_title[]" value="<?php echo htmlspecialchars($item['content_title']); ?>"></div>
              <!-- *** PHP FIX HERE: Only output URL for videos *** -->
              <div class="mb-3"><label class="form-label">YouTube URL</label><input type="url" class="form-control" name="content_url[]" value="<?php echo ($item['content_type'] == 'video') ? htmlspecialchars($item['content_url']) : ''; ?>"></div>
            </div>
            <div class="quiz-fields" style="<?php if ($item['content_type'] != 'quiz') echo 'display: none;'; ?>">
              <div class="mb-3"><label class="form-label">Quiz Title</label><input type="text" class="form-control" name="quiz_title[]" value="<?php echo htmlspecialchars($item['content_title']); ?>"></div>
              <div class="quiz-questions-container border p-3 rounded">
                <h6>Questions</h6>
                <?php if ($item['content_type'] == 'quiz' && isset($quizzes_data[$item['content_url']])): ?>
                  <?php foreach ($quizzes_data[$item['content_url']] as $q_index => $question): ?>
                    <div class="quiz-question border-bottom pb-2 mb-2">
                      <div class="d-flex justify-content-end"><button type="button" class="btn-close btn-sm remove-question-btn"></button></div>
                      <div class="mb-2"><input type="text" class="form-control" name="question_text[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($question['question_text']); ?>" required></div>
                      <div class="input-group mb-1"><span class="input-group-text">A</span><input type="text" class="form-control" name="option_a[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($question['option_a']); ?>" required></div>
                      <div class="input-group mb-1"><span class="input-group-text">B</span><input type="text" class="form-control" name="option_b[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($question['option_b']); ?>" required></div>
                      <div class="input-group mb-1"><span class="input-group-text">C</span><input type="text" class="form-control" name="option_c[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($question['option_c']); ?>" required></div>
                      <div class="input-group mb-1"><span class="input-group-text">D</span><input type="text" class="form-control" name="option_d[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($question['option_d']); ?>" required></div>
                      <select class="form-select form-select-sm" name="correct_option[<?php echo $index; ?>][]" required>
                        <option value="a" <?php if ($question['correct_option'] == 'a') echo 'selected'; ?>>A is Correct</option>
                        <option value="b" <?php if ($question['correct_option'] == 'b') echo 'selected'; ?>>B is Correct</option>
                        <option value="c" <?php if ($question['correct_option'] == 'c') echo 'selected'; ?>>C is Correct</option>
                        <option value="d" <?php if ($question['correct_option'] == 'd') echo 'selected'; ?>>D is Correct</option>
                      </select>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary mt-2 add-question-btn">Add Question</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="card-footer"><button type="button" class="btn btn-secondary" id="add-content-btn"><i class="fas fa-plus"></i> Add Lesson</button></div>
    </div>
    <div class="alert alert-warning"><strong>Note:</strong> Saving changes will overwrite all existing lessons with the content defined above.</div>
    <button type="submit" class="btn btn-primary btn-lg">Save All Changes</button>
  </form>
</div>


<?php
$conn->close();
require 'includes/footer.php';
?>