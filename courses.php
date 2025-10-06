<<<<<<< HEAD
=======
<?php
$pageTitle = "Our Courses";
require 'includes/header.php';
require 'config/database.php';


$search_term = $_GET['search'] ?? '';
$sql = "SELECT c.*, u.username as instructor_name 
        FROM courses c 
        JOIN users u ON c.instructor_id = u.user_id";

if (!empty($search_term)) {
    $sql .= " WHERE c.course_title LIKE ? OR c.course_description LIKE ?";
}
?>

>>>>>>> fad82c75e07657c92ddbab09be6b2f6a24fa8000
<!-- Section: Page Header -->
<section class="page-header text-center py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold">Explore Our Courses</h1>
        <p class="lead text-muted">Find the perfect course to help you achieve your goals.</p>
    </div>
</section>

<!-- Section: Courses Grid & Filters -->
<section class="py-5">
    <div class="container">
        <!-- Filter Bar -->
        <div class="filter-bar card p-3 mb-4 shadow-sm">
            <form action="courses.php" method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-9 col-md-12">
                        <input type="text" name="search" class="form-control" placeholder="Search for courses..." value="<?php echo htmlspecialchars($search_term); ?>">
                    </div>
                    <div class="col-lg-3 col-md-12">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Courses Grid -->
        <div class="row">
            <?php
            $stmt = $conn->prepare($sql);
            if (!empty($search_term)) {
                $search_param = "%{$search_term}%";
                $stmt->bind_param("ss", $search_param, $search_param);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='col-lg-4 col-md-6 mb-4 d-flex align-items-stretch'>";
                    echo "<div class='card h-100 shadow-sm border-0 course-card'>";
                    echo "<div class='course-card-img-container'>";
                    echo "<img src='" . htmlspecialchars($row['course_thumbnail']) . "' class='card-img-top' alt='" . htmlspecialchars($row['course_title']) . "'>";
                    echo "<span class='badge bg-primary course-category-badge'>" . htmlspecialchars($row['category']) . "</span>";
                    echo "</div>";
                    echo "<div class='card-body d-flex flex-column'>";
                    echo "<h5 class='card-title'>" . htmlspecialchars($row['course_title']) . "</h5>";
                    echo "<p class='card-text text-muted flex-grow-1'>" . htmlspecialchars(substr($row['course_description'], 0, 100)) . "...</p>";
                    echo "<p class='small text-muted mb-0'>By " . htmlspecialchars($row['instructor_name']) . "</p>";
                    echo "</div>";
                    echo "<div class='card-footer bg-white border-0 text-center py-3'>";
                    echo "<a href='course-overview.php?id=" . $row['course_id'] . "' class='btn btn-primary'>View Details</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-center text-muted'>No courses found matching your criteria.</p>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>