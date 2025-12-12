<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión - Aventones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>

<body class="auth">

    <div class="login-box">
        <div class="header">
            <img src="<?= base_url('assets/img/Icono.png') ?>" alt="Logo" class="logo">
            <h1 class="title">AVENTONES</h1>
        </div>

        <div class="login-form">

            <!-- Mostrar mensaje de error -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?= esc($error_message) ?>
                </div>
            <?php endif; ?>

            <!-- Mensaje informativo -->
            <?php if (!empty($info_message)): ?>
                <div class="alert alert-info">
                    <?= esc($info_message) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('passwordless/send-link') ?>">
                <label for="pwless_email">Send Passwordless with email</label>
                <input type="email" id="pwless_email" name="pwless_email" required>
                <div style="margin-bottom: 1rem;"></div>
                <button type="submit" class="submit-btn login-btn">Send link</button>
            </form>


            <div class="separator">Or</div>

            <!-- FORMULARIO REAL DEL LOGIN -->
            <form method="POST" action="<?= site_url('login') ?>">

                <div class="field">
                    <label for="username">Email</label>
                    <input type="email" id="username" name="username" required value="<?= old('username') ?>">
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <p class="register-link">
                    Not a user?
                    <a href="<?= site_url('register/passenger') ?>">Register as Passenger</a> |
                    <a href="<?= site_url('register/driver') ?>">Register as Driver</a>
                </p>

                <button type="submit" class="submit-btn login-btn">Login</button>
            </form>
        </div>
    </div>

    <footer>
        <hr>
        <nav>
            <a href="<?= site_url('rides/search') ?>">Home</a> |
            <a href="<?= site_url('rides/search') ?>">Rides</a> |
            <a href="<?= site_url('reservations/my') ?>">Bookings</a> |
            <a href="<?= site_url('profile/config') ?>">Settings</a> |
            <a href="<?= site_url('login') ?>">Login</a> |
            <a href="<?= site_url('register/passenger') ?>">Register</a>
        </nav>

        <p>&copy; Aventones.com</p>
    </footer>

</body>

</html>