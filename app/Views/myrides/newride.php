<?php
/** @var array $vehicles */
/** @var array $errors */
/** @var array $old */
/** @var string $msg */
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>New Ride - Aventones</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/rides.css') ?>">
</head>

<body>
    <div class="container mt-4">
        <h2 class="subtitle">New Ride</h2>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info"><?= esc($msg) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <div><?= esc($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('rides/store') ?>" class="card card-body gap-3">
            <?= csrf_field() ?>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Vehicle</label>
                    <select name="vehicle_id" class="form-select" required>
                        <option value="">Select vehicle</option>
                        <?php foreach ($vehicles as $v): ?>
                            <option value="<?= $v['id'] ?>" <?= ($old['vehicle_id'] ?? '') == $v['id'] ? 'selected' : '' ?>>
                                <?= esc($v['plate'] . ' - ' . $v['make'] . ' ' . $v['model']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ride name</label>
                    <input name="name" class="form-control" value="<?= esc($old['name'] ?? '') ?>" required
                        maxlength="80">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Departure from</label>
                    <input name="origin" class="form-control" value="<?= esc($old['origin'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Arrive to</label>
                    <input name="destination" class="form-control" value="<?= esc($old['destination'] ?? '') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label d-block">Days</label>
                    <?php
                    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    $oldDays = $old['days'] ?? [];
                    foreach ($days as $d):
                        ?>
                        <label class="me-3">
                            <input type="checkbox" name="days[]" value="<?= $d ?>" <?= in_array($d, $oldDays) ? 'checked' : '' ?>>
                            <?= $d ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Time</label>
                    <input type="time" name="time" class="form-control" value="<?= esc($old['time'] ?? '') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Seat price</label>
                    <input type="number" step="0.01" min="0" name="seat_price" class="form-control"
                        value="<?= esc($old['seat_price'] ?? '') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Seats</label>
                    <input type="number" min="1" name="seats_total" class="form-control"
                        value="<?= esc($old['seats_total'] ?? '') ?>" required>
                </div>

            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Create</button>
                <a class="btn btn-outline-secondary" href="<?= site_url('rides/my') ?>">
                    Back to My Rides
                </a>
            </div>
        </form>
    </div>
</body>

</html>