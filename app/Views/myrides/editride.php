<?php
// app/Views/myrides/editride.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ride - Aventones</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <style>
        .form-check {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 10px;
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

        .subtitle {
            color: #34495e;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #3498db;
        }
    </style>
</head>

<body class="auth">
    <div class="container mt-4">
        <div class="header">
            <img src="<?= base_url('assets/img/Icono.png') ?>" alt="Logo" class="logo">
            <h1 class="title">AVENTONES</h1>
        </div>

        <h2 class="subtitle">Edit Ride</h2>

        <?php if (session()->has('msg') && session('msg')): ?>
            <div class="alert alert-<?= strpos(session('msg'), 'updated') !== false ? 'success' : 'danger' ?> alert-dismissible fade show"
                role="alert">
                <?= esc(session('msg')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php foreach ($errors as $error): ?>
                    <div><?= esc($error) ?></div>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php
        // Preparar los días seleccionados
        $selectedDays = [];
        if (isset($ride['days']) && !empty($ride['days'])) {
            $selectedDays = explode(',', $ride['days']);
            $selectedDays = array_map('trim', $selectedDays);
        }
        ?>

        <form method="post" action="<?= site_url('rides/update/' . $ride['id']) ?>" class="card card-body gap-3">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Vehicle *</label>
                    <select name="vehicle_id" class="form-select" required>
                        <option value="">Select a vehicle</option>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?= $vehicle['id'] ?>" <?= (isset($ride['vehicle_id']) && $ride['vehicle_id'] == $vehicle['id']) ? 'selected' : '' ?>>
                                <?= esc($vehicle['plate'] . ' - ' . $vehicle['make'] . ' ' . $vehicle['model']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ride name *</label>
                    <input type="text" name="name" class="form-control" required maxlength="80"
                        value="<?= esc($ride['name'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Departure from *</label>
                    <input type="text" name="origin" class="form-control" required
                        value="<?= esc($ride['origin'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Arrive to *</label>
                    <input type="text" name="destination" class="form-control" required
                        value="<?= esc($ride['destination'] ?? '') ?>">
                </div>

                <div class="col-md-12">
                    <label class="form-label d-block">Days *</label>
                    <?php
                    $daysList = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    ?>

                    <?php foreach ($daysList as $day): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="days[]" value="<?= $day ?>"
                                id="day_<?= $day ?>" <?= in_array($day, $selectedDays) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="day_<?= $day ?>">
                                <?= $day ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Time *</label>
                    <?php
                    $timeValue = $ride['time'] ?? '';
                    if (!empty($timeValue) && strpos($timeValue, ':') !== false) {
                        $timeValue = substr($timeValue, 0, 5);
                    }
                    ?>
                    <input type="time" name="time" class="form-control" required value="<?= esc($timeValue) ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Seat price ($) *</label>
                    <input type="number" step="0.01" min="0" name="seat_price" class="form-control" required
                        value="<?= esc($ride['seat_price'] ?? '0.00') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Available seats *</label>
                    <input type="number" min="1" name="seats_total" class="form-control" required
                        value="<?= esc($ride['seats_total'] ?? '1') ?>">
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a class="btn btn-outline-secondary" href="<?= site_url('rides/my') ?>">Back to My Rides</a>
                <?php if (isset($ride['id'])): ?>
                    <a class="btn btn-secondary" href="<?= site_url('rides/details/' . $ride['id']) ?>">View Details</a>
                <?php endif; ?>
            </div>
        </form>

        <footer class="mt-4">
            <hr>
            <nav class="text-center">
                <a href="<?= site_url('rides/my') ?>">My Rides</a> |
                <a href="<?= site_url('rides/search') ?>">Search Rides</a> |
                <a href="<?= site_url('vehicles') ?>">My Vehicles</a> |
                <a href="<?= site_url('logout') ?>">Logout</a>
            </nav>
            <p class="text-center text-muted">&copy; <?= date('Y') ?> Aventones.com</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function (e) {
            const days = document.querySelectorAll('input[name="days[]"]:checked');
            if (days.length === 0) {
                e.preventDefault();
                alert('Please select at least one day.');
                return false;
            }

            const seats = document.querySelector('input[name="seats_total"]');
            if (parseInt(seats.value) < 1) {
                e.preventDefault();
                alert('Available seats must be at least 1.');
                seats.focus();
                return false;
            }

            const price = document.querySelector('input[name="seat_price"]');
            if (parseFloat(price.value) < 0) {
                e.preventDefault();
                alert('Price cannot be negative.');
                price.focus();
                return false;
            }
        });
    </script>
</body>

</html>