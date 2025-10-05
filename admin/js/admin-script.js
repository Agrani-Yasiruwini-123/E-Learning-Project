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
