<?php
require_once __DIR__ . '/../../includes/session-manager.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'instructor') {
  header("Location: ../login.php");
  exit();
}
