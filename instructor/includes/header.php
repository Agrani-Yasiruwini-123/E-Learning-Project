<?php
require_once 'auth-check.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle ?? 'Instructor Panel'); ?> | EDUMA</title>
  <link href="https:
  <link rel=" stylesheet" href="https:
  <!-- Use the same styles as the admin panel for a consistent backend theme -->
  <link rel=" stylesheet" href="../admin/css/admin-style.css">
</head>

<body>
  <div class="admin-wrapper">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">