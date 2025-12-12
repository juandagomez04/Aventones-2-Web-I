<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuarios - Aventones</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- CSS usando base_url -->
  <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
</head>

<body class="auth">
  <div class="container">
    <div class="header">
      <img src="<?= base_url('assets/img/Icono.png') ?>" alt="Logo" class="logo">
      <h1 class="title">AVENTONES</h1>
    </div>

    <h2 class="subtitle">User Registration</h2>

    <!-- Registration Form -->
    <form class="form" action="<?= site_url('register/passenger') ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="register_type" value="passenger">

      <!-- Campos que van al execute() -->
      <div class="row">
        <div class="field">
          <label for="fname">First Name</label>
          <input type="text" id="fname" name="fname" required>
        </div>
        <div class="field">
          <label for="lname">Last Name</label>
          <input type="text" id="lname" name="lname" required>
        </div>
      </div>

      <div class="field">
        <label for="cedula">National ID (Cédula)</label>
        <input type="text" id="cedula" name="cedula" maxlength="12" required>
      </div>

      <div class="field">
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" required>
      </div>

      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="row">
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div class="field">
          <label for="repeat">Repeat Password</label>
          <input type="password" id="repeat" name="repeat" required>
        </div>
      </div>

      <div class="field">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" required>
      </div>

      <div class="field">
        <label for="photo">Photo</label>
        <input type="file" id="photo" name="photo" accept="image/*">
      </div>

      <!-- Links -->
      <div class="links">
        <p class="login-link">
          Already a user?
          <a href="<?= site_url('login') ?>">Login here</a>
        </p>
        <p class="register-driver-link">
          Register as driver?
          <a href="<?= site_url('register/driver') ?>">Click here</a>
        </p>
      </div>

      <button type="submit" class="submit-btn">Sign up</button>
    </form>

  </div>
  <footer>
    <hr>
    <nav>
      <a href="<?= site_url('rides/search') ?>">Home</a> |
      <a href="<?= site_url('rides/my') ?>">Rides</a> |
      <a href="<?= site_url('bookings') ?>">Bookings</a> |
      <a href="<?= site_url('profile') ?>">Settings</a> |
      <a href="<?= site_url('login') ?>">Login</a> |
      <a href="<?= site_url('register/passenger') ?>">Register</a>
    </nav>
    <p>&copy; Aventones.com</p>
  </footer>
</body>

</html>