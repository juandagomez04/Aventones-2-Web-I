<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Rides - AVENTONES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/rides.css') ?>">

    <style>
        table.table-rides {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 15px;
            margin: 0;
            background: transparent;
            box-shadow: none;
            border-radius: 0;
            overflow: visible;
            font-size: 14px;
        }

        table.table-rides thead {
            background: #f8f9fa;
            font-weight: 600;
        }

        table.table-rides th {
            padding: 20px 25px;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #dee2e6;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            background: #f8f9fa;
        }

        table.table-rides td {
            padding: 28px 25px;
            background: #ffffff;
            border: none;
            font-size: 14px;
            color: #495057;
            vertical-align: middle;
            line-height: 1.5;
        }

        table.table-rides tbody tr {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: #ffffff;
        }

        table.table-rides tbody tr:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        table.table-rides td.actions {
            display: flex;
            flex-direction: row;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            padding-right: 6px;
        }

        table.table-rides td.actions form {
            display: inline-block;
            margin: 0;
        }

        table.table-rides td.actions .btn,
        table.table-rides td.actions a.btn,
        table.table-rides td.actions form .btn,
        table.table-rides td.actions button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 80px;
            height: 30px;
            padding: 6px 12px;
            line-height: 1;
            font-size: 12px;
            font-weight: 500;
            border-radius: 4px;
            background: #007bff;
            border: 1px solid #007bff;
            color: #fff;
            text-decoration: none;
            box-shadow: 0 1px 4px rgba(13, 110, 253, 0.2);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        table.table-rides td.actions .btn:hover,
        table.table-rides td.actions a.btn:hover,
        table.table-rides td.actions form .btn:hover,
        table.table-rides td.actions button:hover {
            background: #0056b3;
            border-color: #0056b3;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.25);
            transform: translateY(-1px);
        }

        table.table-rides .text-muted {
            color: #6c757d;
            text-align: center;
            padding: 50px;
            font-style: italic;
            font-size: 16px;
            border-bottom: none;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <img src="<?= base_url('assets/img/Icono.png') ?>" alt="Logo" class="logo">
        <h1 class="title">AVENTONES</h1>
    </div>

    <!-- Top menu -->
    <div class="menu-container">
        <div class="menu">
            <nav class="left-menu">
                <a href="<?= site_url('rides/search') ?>">Home</a>
                <a class="active" href="<?= site_url('rides/my') ?>">Rides</a>
                <a href="<?= site_url('bookings') ?>">Bookings</a>
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

    <hr>

    <main>
        <div class="container my-3">
            <h2 class="subtitle">My rides</h2>

            <?php if (!empty($msg)): ?>
                <div class="alert alert-info"><?= esc($msg) ?></div>
            <?php endif; ?>

            <div class="mb-3 d-flex gap-2">
                <a href="<?= site_url('vehicles') ?>" class="btn btn-outline-secondary">Manage Vehicles</a>
                <a href="<?= site_url('rides/new') ?>" class="btn btn-primary">New Ride</a>
            </div>

            <div class="table-rides-container">
                <table class="table-rides">
                    <thead>
                        <tr>
                            <th>Ride</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Time</th>
                            <th>Seats</th>
                            <th>Price</th>
                            <th>Car</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($rides)): ?>
                            <?php foreach ($rides as $r): ?>
                                <?php
                                $rideId = (int) ($r['id'] ?? 0);
                                $veh = trim(($r['plate'] ?? '') . ' ' . ($r['make'] ?? '') . ' ' . ($r['model'] ?? ''));
                                ?>
                                <tr>
                                    <td><?= esc($r['name'] ?? '') ?></td>
                                    <td><?= esc($r['origin'] ?? '') ?></td>
                                    <td><?= esc($r['destination'] ?? '') ?></td>
                                    <td><?= esc($r['days'] ?? '') ?></td>
                                    <td><?= esc(substr((string) ($r['time'] ?? ''), 0, 5)) ?></td>
                                    <td><?= (int) ($r['seats_total'] ?? 0) ?></td>
                                    <td>₡<?= number_format((float) ($r['seat_price'] ?? 0), 2) ?></td>
                                    <td><?= esc($veh) ?></td>

                                    <td class="actions">
                                        <a class="btn" href="<?= site_url('rides/details/' . $rideId) ?>">Details</a>
                                        <a class="btn" href="<?= site_url('rides/edit/' . $rideId) ?>">Edit</a>

                                        <form method="post" action="<?= site_url('rides/action') ?>"
                                            onsubmit="return confirm('Delete this ride?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="ride_id" value="<?= $rideId ?>">
                                            <button type="submit" class="btn">Delete</button>
                                        </form>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-muted">You have no rides yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="container">
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