<?php

require_once 'session-manager.php';

function updateQuizScore($enrollment_id, $quiz_score)
{

    require 'config/database.php';

    $sql = "UPDATE enrollments SET quiz_score = ? WHERE enrollment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quiz_score, $enrollment_id);

    $success = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $success;
}

function require_login()
{

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function is_active($page_name)
{
    if (basename($_SERVER['SCRIPT_NAME']) == $page_name) {
        if ($page_name === "register.php") {
            return 'text-white active';
        }
        return 'active';
    }
    return '';
}
