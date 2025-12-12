<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Search Rides - AVENTONES</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets/img/Icono.png') ?>" alt="Logo" class="logo">
        <h1 class="title">AVENTONES</h1>
    </div>

    <div class="menu-container">
        <div class="menu">
            <nav class="left-menu">
                <a class="active" href="<?= site_url('rides/search') ?>">Home</a>

                <?php if ($isLoggedIn): ?>
                    <?php if ($role === 'driver'): ?>
                        <a href="<?= site_url('rides/my') ?>">My Rides</a>
                        <a href="<?= site_url('bookings') ?>">Bookings</a>
                    <?php elseif ($role === 'passenger'): ?>
                        <a href="<?= site_url('bookings') ?>">My Bookings</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <div class="center-search">
                <input type="text" placeholder="Search..." class="search-bar">
            </div>

            <div class="right-menu">
                <?php if ($isLoggedIn): ?>
                    <div class="user-btn">
                        <img src="<?= base_url('assets/img/avatar.png') ?>" alt="User" class="user-icon">
                        <div class="dropdown-menu">
                            <form method="get" action="<?= site_url('logout') ?>">
                                <button type="submit" class="logout-btn">Logout</button>
                            </form>
                            <a href="<?= site_url('profile') ?>">Profile</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-links">
                        <a href="<?= site_url('login') ?>" class="btn btn-outline">Login</a>
                        <a href="<?= site_url('register/passenger') ?>" class="btn btn-primary">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">

        <form method="get" action="<?= site_url('rides/search') ?>" class="filters-form">

            <label for="origin">Origin</label>
            <select id="origin" name="origin">
                <option value="">-- Select origin --</option>
                <?php foreach ($origins as $o): ?>
                    <option value="<?= esc($o) ?>" <?= ($origin === $o) ? 'selected' : '' ?>>
                        <?= esc($o) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="destination">Destination</label>
            <select id="destination" name="destination">
                <option value="">-- Select destination --</option>
                <?php foreach ($destForSelected as $d): ?>
                    <option value="<?= esc($d) ?>" <?= ($destination === $d) ? 'selected' : '' ?>>
                        <?= esc($d) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="sort">Sort</label>
            <select id="sort" name="sort">
                <option value="time" <?= ($sort === 'time') ? 'selected' : '' ?>>Time</option>
                <option value="origin" <?= ($sort === 'origin') ? 'selected' : '' ?>>Origin</option>
                <option value="destination" <?= ($sort === 'destination') ? 'selected' : '' ?>>Destination</option>
            </select>

            <select name="dir">
                <option value="asc" <?= ($dir === 'asc') ? 'selected' : '' ?>>ASC</option>
                <option value="desc" <?= ($dir === 'desc') ? 'selected' : '' ?>>DESC</option>
            </select>

            <button type="submit">Search</button>

        </form>


        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Días</th>
                        <th>Hora</th>
                        <th>Vehículo</th>
                        <th>Asientos</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rides)): ?>
                        <?php foreach ($rides as $r):
                            $rideId = (int) ($r['id'] ?? 0);
                            $veh = trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '') . ' ' . ($r['year'] ?? ''));
                            $avail = (int) ($r['available_seats'] ?? 0);
                            $already = (bool) ($r['already_booked'] ?? false);
                            ?>
                            <tr>
                                <td><?= esc($r['name'] ?? '') ?></td>
                                <td><?= esc($r['origin'] ?? '') ?></td>
                                <td><?= esc($r['destination'] ?? '') ?></td>
                                <td><?= esc($r['days'] ?? '') ?></td>
                                <td><?= esc(substr((string) ($r['time'] ?? ''), 0, 5)) ?></td>
                                <td><?= esc($veh) ?></td>
                                <td><?= $avail ?> / <?= (int) ($r['seats_total'] ?? 0) ?></td>
                                <td class="text-end">

                                    <a href="<?= site_url('rides/details/' . $rideId) ?>" class="btn btn-sm btn-info">Ver
                                        Detalles</a>

                                    <?php if (!$isLoggedIn): ?>
                                        <a href="<?= site_url('login') ?>" class="btn btn-sm btn-primary">Login para reservar</a>
                                    <?php elseif ($role === 'driver'): ?>
                                        <span class="text-muted">Solo para pasajeros</span>
                                    <?php elseif ($role === 'passenger'): ?>
                                        <?php if ($avail <= 0): ?>
                                            <span class="text-muted">Sin asientos</span>
                                        <?php elseif ($already): ?>
                                            <span class="text-muted">Ya solicitado</span>
                                        <?php else: ?>
                                            <form method="post" action="<?= site_url('bookings/action') ?>" class="inline">
                                                <input type="hidden" name="action" value="create">
                                                <input type="hidden" name="ride_id" value="<?= $rideId ?>">
                                                <button class="btn btn-sm btn-success">Solicitar reserva</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No se encontraron viajes.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <footer class="container">
        <hr>
        <nav>
            <a href="<?= site_url('rides/search') ?>">Home</a> |
            <?php if ($isLoggedIn): ?>
                <?php if ($role === 'driver'): ?>
                    <a href="<?= site_url('rides/my') ?>">My Rides</a> |
                <?php elseif ($role === 'passenger'): ?>
                    <a href="<?= site_url('bookings') ?>">My Bookings</a> |
                <?php endif; ?>
                <a href="<?= site_url('profile') ?>">Settings</a> |
            <?php endif; ?>
            <a href="<?= site_url('login') ?>">Login</a> |
            <a href="<?= site_url('register/passenger') ?>">Register</a>
        </nav>
        <p>&copy; Aventones.com</p>
    </footer>

    <script>
        const map = <?= $mapJson ?>;
        const originSel = document.getElementById('origin');
        const destSel = document.getElementById('destination');

        function rebuildDestinations(selectedOrigin, selectedDestination = "") {
            const list = map[selectedOrigin] || [];
            destSel.innerHTML = '<option value="">-- Select destination --</option>';

            list.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d;
                opt.textContent = d;
                if (d === selectedDestination) opt.selected = true;
                destSel.appendChild(opt);
            });
        }

        originSel.addEventListener('change', () => {
            rebuildDestinations(originSel.value, "");
        });

        window.addEventListener('DOMContentLoaded', () => {
            rebuildDestinations("<?= addslashes($origin) ?>", "<?= addslashes($destination) ?>");
        });
    </script>
</body>

</html>