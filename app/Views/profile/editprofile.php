<?php
// app/Views/profile/edit.php
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - AVENTONES</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/profile.css') ?>">

    <style>
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #dee2e6;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            max-height: 80px;
            margin-bottom: 1rem;
        }

        .title {
            color: #2c3e50;
            font-weight: bold;
        }

        .menu-container {
            background: #f8f9fa;
            padding: 0.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #dee2e6;
        }

        .menu {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1rem;
        }

        .left-menu a {
            margin: 0 10px;
            color: #495057;
            text-decoration: none;
        }

        .left-menu a:hover {
            color: #007bff;
        }

        .field {
            margin-bottom: 1rem;
        }

        .field label {
            display: block;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .card {
            padding: 1.5rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            background: white;
        }

        .footer {
            text-align: center;
            margin-top: 3rem;
            padding: 1rem;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <img src="<?= base_url('assets/img/Icono.png') ?>" alt="Logo" class="logo">
        <h1 class="title">AVENTONES</h1>
    </div>

    <!-- Menu -->
    <div class="menu-container">
        <div class="menu">
            <nav class="left-menu">
                <a href="<?= site_url('rides/search') ?>">Home</a>
                <a href="<?= site_url('rides/search') ?>">Search</a>
                <?php if (session()->get('user_role') === 'passenger'): ?>
                    <a href="<?= site_url('bookings') ?>">Bookings</a>
                <?php elseif (session()->get('user_role') === 'driver'): ?>
                    <a href="<?= site_url('rides/my') ?>">My Rides</a>
                <?php endif; ?>
            </nav>
            <div class="right-menu">
                <form method="GET" action="<?= site_url('logout') ?>">
                    <button class="btn btn-sm btn-danger" type="submit" name="logout" value="true" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container">

        <div class="grid-2">
            <!-- Perfil -->
            <form class="card" method="POST" action="<?= site_url('profile/update') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <h2 class="mb-4">Datos del perfil</h2>

                <div style="display:flex; gap:16px; align-items:center; margin-bottom: 20px;">
                    <img id="avatarPreview" class="avatar"
                        src="<?= base_url($user['photo_path'] ?? 'assets/img/avatar.png') ?>"
                        onerror="this.onerror=null;this.src='<?= base_url('assets/img/avatar.png') ?>';" alt="Avatar">
                    <div>
                        <div class="field">
                            <label for="photo">Foto de perfil</label>
                            <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp"
                                class="form-control">
                        </div>
                        <small class="text-muted">Formatos: JPG, PNG o WEBP. Máximo 5MB</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="first_name">Nombre *</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required
                            value="<?= esc($user['first_name'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label for="last_name">Apellido *</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required
                            value="<?= esc($user['last_name'] ?? '') ?>">
                    </div>

                    <div class="field">
                        <label for="national_id">Cédula</label>
                        <input type="text" id="national_id" name="national_id" class="form-control"
                            value="<?= esc($user['national_id'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label for="birth_date">Fecha de nacimiento</label>
                        <input type="date" id="birth_date" name="birth_date" class="form-control"
                            value="<?= esc($birth_date ?? '') ?>">
                    </div>

                    <div class="field">
                        <label for="email">Correo electrónico *</label>
                        <input type="email" id="email" name="email" class="form-control" required
                            value="<?= esc($user['email'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label for="phone">Teléfono</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                            value="<?= esc($user['phone'] ?? '') ?>">
                    </div>

                    <div class="field full actions">
                        <button type="reset" class="btn btn-secondary">Restablecer</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </div>
            </form>

            <!-- Contraseña -->
            <form class="card" method="POST" action="<?= site_url('profile/update-password') ?>">
                <?= csrf_field() ?>

                <h2 class="mb-4">Actualizar contraseña</h2>

                <div class="field">
                    <label for="current_password">Contraseña actual *</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>
                <div class="field">
                    <label for="new_password">Nueva contraseña *</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required
                        minlength="8">
                    <small class="text-muted">Mínimo 8 caracteres</small>
                </div>
                <div class="field">
                    <label for="confirm_password">Confirmar nueva contraseña *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                <div class="actions">
                    <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>© <?= date('Y') ?> AVENTONES</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview inmediata de foto nueva
        document.getElementById('photo')?.addEventListener('change', (e) => {
            const [file] = e.target.files || [];
            if (!file) return;
            const url = URL.createObjectURL(file);
            document.getElementById('avatarPreview').src = url;
        });

        // Validación de contraseña
        document.querySelector('form[action*="update-password"]')?.addEventListener('submit', function (e) {
            const newPass = document.getElementById('new_password');
            const confirmPass = document.getElementById('confirm_password');

            if (newPass.value.length < 8) {
                e.preventDefault();
                alert('La nueva contraseña debe tener al menos 8 caracteres.');
                newPass.focus();
                return false;
            }

            if (newPass.value !== confirmPass.value) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
                confirmPass.focus();
                return false;
            }
        });
    </script>
</body>

</html>