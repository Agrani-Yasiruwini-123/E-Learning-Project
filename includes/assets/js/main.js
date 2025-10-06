document.addEventListener("DOMContentLoaded", function () {
	const hamburgerMenu = document.getElementById("hamburger-menu");
	const navLinks = document.querySelector(".nav-links");

	if (hamburgerMenu) {
		hamburgerMenu.addEventListener("click", () => {
			navLinks.classList.toggle("active");
		});
	}
});
