<?php
/* app/Views/bookings/bookings.php */
/** @var array $bookings */
/** @var string $role */
/** @var string $msg */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Bookings - AVENTONES</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bookings.css') ?>">
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets/img/Icono.png') ?>" alt="Logo" class="logo">
        <h1 class="title">AVENTONES</h1>
    </div>

    <div class="menu-container">
        <div class="menu">
            <nav class="left-menu">
                <a href="<?= site_url('rides/search') ?>">Home</a>
                <?= $role === 'driver' ? '<a href="' . site_url('rides/my') . '">Rides</a>' : '' ?>
                <a class="active"
                    href="<?= site_url('bookings') ?>"><?= $role === 'driver' ? 'Bookings' : 'My Bookings' ?></a>
            </nav>

            <div class="center-search">
                <input type="text" placeholder="Search..." class="search-bar">
            </div>

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
        </div>
    </div>

    <div class="container">

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info"><?= esc($msg) ?></div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ride</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Días</th>
                        <th>Hora</th>
                        <th>Vehículo</th>
                        <th>Status</th>
                        <th>Asientos</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><?= esc($b['ride_name']) ?></td>
                                <td><?= esc($b['origin']) ?></td>
                                <td><?= esc($b['destination']) ?></td>
                                <td><?= esc($b['days']) ?></td>
                                <td><?= esc($b['time']) ?></td>
                                <td><?= esc($b['vehicle']) ?></td>
                                <td><span class="badge <?= esc($b['status_class']) ?>"><?= esc($b['status_label']) ?></span>
                                </td>
                                <td><?= (int) $b['available_seats'] ?> / <?= (int) $b['seats_total'] ?></td>

                                <td class="text-end">
                                    <?php if (!empty($b['actions'])): ?>
                                        <?php foreach ($b['actions'] as $a): ?>
                                            <form method="post" action="<?= site_url('bookings/action') ?>" class="inline"
                                                onsubmit="return confirm('<?= esc($a['confirm'] ?? 'Confirmar?') ?>')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="<?= esc($a['post_action']) ?>">
                                                <?php foreach (($a['fields'] ?? []) as $k => $v): ?>
                                                    <input type="hidden" name="<?= esc($k) ?>" value="<?= esc((string) $v) ?>">
                                                <?php endforeach; ?>
                                                <button type="submit" class="<?= esc($a['class']) ?>"><?= esc($a['label']) ?></button>
                                            </form>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline" disabled>Sin acciones</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No tienes reservas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

</body>

</html>