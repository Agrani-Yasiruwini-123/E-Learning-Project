<?php


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle ?? 'Admin Panel'); ?> | EDUMA</title>
  <!-- Bootstrap CSS -->
  <link href="https:
  <link rel=" stylesheet" href="https:
  <!-- Admin-specific CSS -->
  <link rel=" stylesheet" href="css/admin-style.css">
</head>

<body>
  <div class="admin-wrapper">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">