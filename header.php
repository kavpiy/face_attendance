<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">

  <style>
    .navbar {
      padding: 0.5rem 1rem;
      background-color: #212529 !important;
    }

    .list-group-item {
      background-color: #212529;
      color: #fff;
      border: none;
      font-weight: 500;
    }

    .list-group-item:hover {
      background-color: #343a40;
    }

    body {
      padding-top: 60px; /* Ensure content does not go under navbar */
    }
  </style>
</head>
<body>

<div class="d-flex" id="wrapper">

  <!-- Sidebar -->
<!-- Sidebar -->
<div class="bg-dark min-vh-100 pt-4" id="sidebar-wrapper">
  <div class="list-group list-group-flush p-2">
    <a href="index.php" class="pb-3 list-group-item list-group-item-action text-white bg-dark border-0">
      🏠 Home
    </a>
    <a href="login.php" class=" list-group-item list-group-item-action text-white bg-dark border-0 d-block d-sm-none">
      🔐 Login
    </a>
  </div>
</div>


  <!-- Page Content -->
  <div id="page-content-wrapper" class="flex-grow-1">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
      <div class="container-fluid">
        <!-- Sidebar toggle and brand -->
        <div class="d-flex align-items-center">
          <button class="btn btn-sm btn-outline-light me-2" id="menu-toggle">
            <i class="fas fa-bars"></i>
          </button>
          <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="fas fa-graduation-cap me-2"></i>
            <span>Attendance System</span>
          </a>
        </div>

        <!-- Right side message -->
        <div class="ms-auto text-white d-none d-md-block">
          You are not logged in. (<a href="login.php" class="text-decoration-none text-info">Log in</a>)
        </div>
      </div>
    </nav>


<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

