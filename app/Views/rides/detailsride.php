<?php
// app/Views/rides/detailsride.php
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ride Details - AVENTONES</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos propios -->
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/rides.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/ridedetails.css') ?>"><!-- opcional -->

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* Asegura altura visible del mapa sin tocar tus CSS globales */
        #map {
            height: 380px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        /* Oculta los combos "puente" que usa apimaps.js */
        .ghost-select {
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }

        /* Ajusta la posición del bloque de botones bajo el mapa */
        .card.p-3 .mt-3.d-flex {
            margin-top: 10px !important;
            justify-content: center;
        }

        /* Header styling */
        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
        }

        .logo {
            max-height: 80px;
            margin-bottom: 1rem;
        }

        .title {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 0;
        }

        /* Menu styling */
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

        .left-menu a,
        .right-menu a {
            margin: 0 10px;
            color: #495057;
            text-decoration: none;
        }

        .left-menu a:hover,
        .right-menu a:hover {
            color: #007bff;
        }

        .search-bar {
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            width: 300px;
        }

        .user-btn {
            position: relative;
            cursor: pointer;
        }

        .user-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 0.5rem;
            min-width: 120px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .user-btn:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu form {
            margin: 0;
        }

        .logout-btn {
            background: none;
            border: none;
            color: #dc3545;
            width: 100%;
            text-align: left;
            padding: 0.25rem 0;
            cursor: pointer;
        }

        .logout-btn:hover {
            color: #c82333;
        }

        /* Grid layout */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }

            .search-bar {
                width: 200px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <img src="<?= base_url('assets/img/Icono.png') ?>" alt="Logo" class="logo">
        <h1 class="title">AVENTONES</h1>
    </div>

    <!-- Menú unificado -->
    <div class="menu-container">
        <div class="menu">
            <nav class="left-menu">
                <a href="<?= site_url('rides/search') ?>">Home</a>
                <?php if ($isLoggedIn && $role === 'driver'): ?>
                    <a href="<?= site_url('rides/my') ?>">My Rides</a>
                <?php endif; ?>
                <?php if ($isLoggedIn): ?>
                    <a href="<?= site_url('bookings') ?>">Bookings</a>
                <?php endif; ?>
            </nav>

            <div class="center-search">
                <input type="text" placeholder="Search..." class="search-bar">
            </div>

            <?php if ($isLoggedIn): ?>
                <div class="right-menu">
                    <div class="user-btn">
                        <img src="<?= base_url('assets/img/avatar.png') ?>" alt="User" class="user-icon">
                        <div class="dropdown-menu">
                            <form method="get" action="<?= site_url('logout') ?>">
                                <button type="submit" class="logout-btn">Logout</button>
                            </form>
                            <a href="<?= site_url('profile') ?>">Profile</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="right-menu">
                    <a href="<?= site_url('login') ?>">Login</a>
                    <a href="<?= site_url('register/passenger') ?>">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contenido -->
    <div class="container">
        <h2 class="mb-4"><?= esc($ride['name'] ?? 'Ride #' . $ride['id']) ?></h2>

        <div class="grid-2">
            <!-- Columna izquierda: datos -->
            <div class="card p-3">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Origen:</strong> <?= esc($ride['origin'] ?? '') ?></p>
                        <p><strong>Destino:</strong> <?= esc($ride['destination'] ?? '') ?></p>
                        <p><strong>Días:</strong> <?= esc($ride['days'] ?? '') ?></p>
                        <p><strong>Hora:</strong> <?= !empty($ride['time']) ? esc(substr($ride['time'], 0, 5)) : '' ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Conductor:</strong>
                            <?= esc(($ride['first_name'] ?? '') . ' ' . ($ride['last_name'] ?? '')) ?></p>
                        <p><strong>Vehículo:</strong>
                            <?= esc(($ride['make'] ?? '') . ' ' . ($ride['model'] ?? '') . ' ' . ($ride['year'] ?? '')) ?>
                        </p>
                        <p><strong>Placa:</strong> <?= esc($ride['plate'] ?? '') ?></p>
                        <p><strong>Color:</strong> <?= esc($ride['color'] ?? '') ?></p>
                        <p><strong>Asientos disponibles:</strong> <?= $ride['seats_total'] ?? 0 ?></p>
                        <p><strong>Tarifa por asiento:</strong>
                            ₡<?= !empty($ride['seat_price']) ? esc(number_format((float) $ride['seat_price'], 2)) : '0.00' ?>
                        </p>
                    </div>
                </div>

                <!-- Mostrar foto del vehículo si existe -->
                <h5 class="mt-4">Vehicle Photo</h5>

                <?php if (!empty($ride['vehicle_photo_url'])): ?>
                    <div class="text-center mb-3">
                        <img src="<?= esc($ride['vehicle_photo_url']) ?>" alt="Vehicle Photo" class="img-fluid rounded"
                            style="max-height: 200px; max-width: 300px;"
                            onerror="this.onerror=null;this.src='<?= base_url('assets/img/avatar.png') ?>';">
                    </div>
                <?php else: ?>
                    <div class="text-center mb-3">
                        <img src="<?= base_url('assets/img/avatar.png') ?>" alt="No Vehicle Photo" class="img-fluid rounded"
                            style="max-height: 200px; max-width: 300px; opacity: 0.5;">
                        <p class="text-muted mt-2">No vehicle photo available</p>
                    </div>
                <?php endif; ?>


            </div>

            <!-- Columna derecha: mapa -->
            <div class="card p-3">
                <h3 class="mb-3">Ruta</h3>

                <!-- "Puente" para apimaps.js: selects #from y #to con los valores del ride -->
                <select id="from" class="ghost-select" aria-hidden="true">
                    <option value="<?= esc($ride['origin'] ?? '') ?>" selected><?= esc($ride['origin'] ?? '') ?>
                    </option>
                </select>

                <select id="to" class="ghost-select" aria-hidden="true">
                    <option value="<?= esc($ride['destination'] ?? '') ?>" selected>
                        <?= esc($ride['destination'] ?? '') ?>
                    </option>
                </select>

                <!-- Contenedor del mapa (usado por apimaps.js) -->
                <small class="text-muted">Vista aproximada de la ruta entre origen y destino.</small>
                <div id="map"></div>

                <div class="mt-3 d-flex gap-2">
                    <a class="btn btn-outline-secondary" href="#"
                        onclick="event.preventDefault(); history.back();">Volver</a>
                    <?php if ($isLoggedIn && $role === 'driver'): ?>
                        <a class="btn btn-primary" href="<?= site_url('rides/edit/' . $ride['id']) ?>">Edit</a>
                    <?php endif; ?>

                    <?php if ($isLoggedIn && $role === 'passenger'): ?>
                        <!-- Botón para reservar (puedes añadir la funcionalidad después) -->
                        <a class="btn btn-success" href="<?= site_url('bookings/create/' . $ride['id']) ?>">Book Now</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet + apimaps -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="<?= base_url('assets/js/home/apimaps.js') ?>"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Initialize the map when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            // Trigger the map initialization from apimaps.js
            // This assumes apimaps.js has a function that watches for select changes
            setTimeout(function () {
                // Trigger change events to initialize the map
                const fromSelect = document.getElementById('from');
                const toSelect = document.getElementById('to');
                if (fromSelect && toSelect) {
                    fromSelect.dispatchEvent(new Event('change'));
                    toSelect.dispatchEvent(new Event('change'));
                }
            }, 500);
        });
    </script>
</body>

</html>