document.addEventListener("DOMContentLoaded", function () {
	const addContentButton = document.getElementById("add-content");
	const courseContentSection = document.getElementById(
		"course-content-section"
	);

	const attachEventListeners = (contentItem) => {
		const contentTypeSelect = contentItem.querySelector(".content-type-select");
		contentTypeSelect.addEventListener("change", function () {
			const contentType = this.value;
			contentItem.querySelector(".video-url").style.display =
				contentType === "video" ? "block" : "none";
			contentItem.querySelector(".pdf-upload").style.display =
				contentType === "pdf" ? "block" : "none";
			contentItem.querySelector(".quiz-section").style.display =
				contentType === "quiz" ? "block" : "none";
		});
	};

	document.querySelectorAll(".course-content-item").forEach((item) => {
		attachEventListeners(item);

		item
			.querySelector(".content-type-select")
			.dispatchEvent(new Event("change"));
	});

	if (addContentButton) {
		addContentButton.addEventListener("click", function () {
			const contentIndex = courseContentSection.getElementsByClassName(
				"course-content-item"
			).length;
			const newContentItem = document.createElement("div");
			newContentItem.classList.add(
				"course-content-item",
				"card",
				"p-3",
				"mb-3"
			);
			newContentItem.innerHTML = `
              <hr>
              <h5 class="mb-3">New Content Section</h5>
              <div class="mb-3">
                  <label class="form-label">Content Type</label>
                  <select class="form-select content-type-select" name="content_type[]" required>
                      <option value="video" selected>Video</option>
                      <option value="pdf">PDF</option>
                      <option value="quiz">Quiz</option>
                  </select>
              </div>
              <div class="mb-3 video-url">
                  <label class="form-label">Video URL (YouTube)</label>
                  <input type="url" class="form-control" name="content_url[]" placeholder="https:
              </div>
              <div class="mb-3 pdf-upload" style="display: none;">
                  <label class="form-label">PDF File</label>
                  <input type="file" class="form-control" name="pdf_file[]" accept=".pdf">
              </div>
              <div class="mb-3 quiz-section" style="display: none;">
                  <label class="form-label">Quiz Title</label>
                  <input type="text" class="form-control" name="quiz_title[]">
                  <div class="mt-3">
                      <h6>Quiz Questions</h6>
                      <div class="quiz-questions-container">
                         <!-- Questions will be added here -->
                      </div>
                       <button type="button" class="btn btn-sm btn-outline-secondary mt-2 add-question">Add Question</button>
                  </div>
              </div>
              <button type="button" class="btn btn-danger btn-sm align-self-end remove-content">Remove</button>
          `;
			courseContentSection.appendChild(newContentItem);
			attachEventListeners(newContentItem);

			newContentItem
				.querySelector(".content-type-select")
				.dispatchEvent(new Event("change"));
		});
	}

	courseContentSection.addEventListener("click", function (event) {
		if (event.target.classList.contains("remove-content")) {
			event.target.closest(".course-content-item").remove();
		}
		if (event.target.classList.contains("add-question")) {
			const questionsContainer = event.target.previousElementSibling;
			const questionIndex =
				questionsContainer.getElementsByClassName("quiz-question").length;
			const newQuestion = document.createElement("div");
			newQuestion.classList.add(
				"quiz-question",
				"border",
				"p-2",
				"rounded",
				"mb-2"
			);
			newQuestion.innerHTML = `
              <input type="text" class="form-control mb-1" name="question_text[${
								courseContentSection.getElementsByClassName(
									"course-content-item"
								).length - 1
							}][]" placeholder="Question Text" required>
              <input type="text" class="form-control mb-1" name="option_a[${
								courseContentSection.getElementsByClassName(
									"course-content-item"
								).length - 1
							}][]" placeholder="Option A" required>
              <input type="text" class="form-control mb-1" name="option_b[${
								courseContentSection.getElementsByClassName(
									"course-content-item"
								).length - 1
							}][]" placeholder="Option B" required>
              <input type="text" class="form-control mb-1" name="option_c[${
								courseContentSection.getElementsByClassName(
									"course-content-item"
								).length - 1
							}][]" placeholder="Option C" required>
              <input type="text" class="form-control mb-1" name="option_d[${
								courseContentSection.getElementsByClassName(
									"course-content-item"
								).length - 1
							}][]" placeholder="Option D" required>
              <select class="form-select" name="correct_option[${
								courseContentSection.getElementsByClassName(
									"course-content-item"
								).length - 1
							}][]" required>
                  <option value="a">Correct is A</option>
                  <option value="b">Correct is B</option>
                  <option value="c">Correct is C</option>
                  <option value="d">Correct is D</option>
              </select>
          `;
			questionsContainer.appendChild(newQuestion);
		}
	});
});
