<?php
/** @var array $vehicles */
/** @var array|null $editingVehicle */
/** @var string $msg */
/** @var array $errors */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vehículos - Aventones</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vehicles.css') ?>">

    <style>
        .confirmation-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
        }

        .confirmation-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 420px;
            width: 90%;
            text-align: center;
        }
    </style>
</head>

<body class="auth">

    <?php if (!empty($deleteConfirm ?? null)): ?>
        <div class="confirmation-modal">
            <div class="confirmation-content">
                <h4>¿Estás seguro?</h4>
                <p>
                    ¿Deseas eliminar el vehículo con placa
                    <strong><?= esc($deleteConfirm['plate']) ?></strong>?
                </p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>

                <div class="mt-4 d-flex gap-2 justify-content-center">
                    <form method="post" action="<?= site_url('vehicles/delete/' . (int) $deleteConfirm['id']) ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-danger" type="submit">Sí, eliminar</button>
                    </form>
                    <a href="<?= site_url('vehicles') ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">

        <div class="header">
            <img src="<?= base_url('assets/img/Icono.png') ?>" alt="Logo" class="logo">
            <h1 class="title">AVENTONES</h1>
        </div>

        <h2 class="subtitle">Gestión de Vehículos</h2>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= esc($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <div><?= esc($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Tabla -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Mis Vehículos</h3>
            </div>

            <div class="card-body">
                <?php if (empty($vehicles)): ?>
                    <p class="text-muted">No tienes vehículos registrados.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Placa</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Año</th>
                                    <th>Color</th>
                                    <th>Asientos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($vehicles as $v): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($v['photo_path'])): ?>
                                                <img src="<?= base_url($v['photo_path']) ?>" alt="Vehículo" class="vehicle-photo"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                <span class="text-muted" style="display:none;">Sin foto</span>
                                            <?php else: ?>
                                                <span class="text-muted">Sin foto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($v['plate']) ?></td>
                                        <td><?= esc($v['make']) ?></td>
                                        <td><?= esc($v['model']) ?></td>
                                        <td><?= esc($v['year']) ?></td>
                                        <td><?= esc($v['color']) ?></td>
                                        <td><?= esc($v['seats_capacity']) ?></td>
                                        <td class="table-actions d-flex gap-2">
                                            <a class="btn btn-sm btn-warning"
                                                href="<?= site_url('vehicles/edit/' . (int) $v['id']) ?>">
                                                Editar
                                            </a>
                                            <a class="btn btn-sm btn-danger"
                                                href="<?= site_url('vehicles/confirm-delete/' . (int) $v['id']) ?>">
                                                Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form create/update -->
        <div class="form-section">
            <h3><?= !empty($editingVehicle) ? 'Editar Vehículo' : 'Agregar Nuevo Vehículo' ?></h3>

            <form method="post" enctype="multipart/form-data" class="row g-3" action="<?= !empty($editingVehicle)
                ? site_url('vehicles/update/' . (int) $editingVehicle['id'])
                : site_url('vehicles/store') ?>">

                <?= csrf_field() ?>

                <div class="col-md-6">
                    <label class="form-label">Placa *</label>
                    <input type="text" class="form-control" name="plate"
                        value="<?= esc($editingVehicle['plate'] ?? '') ?>" required maxlength="15">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Color *</label>
                    <input type="text" class="form-control" name="color"
                        value="<?= esc($editingVehicle['color'] ?? '') ?>" required maxlength="30">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Marca *</label>
                    <input type="text" class="form-control" name="make"
                        value="<?= esc($editingVehicle['make'] ?? '') ?>" required maxlength="50">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Modelo *</label>
                    <input type="text" class="form-control" name="model"
                        value="<?= esc($editingVehicle['model'] ?? '') ?>" required maxlength="50">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Año *</label>
                    <input type="number" class="form-control" name="year"
                        value="<?= esc($editingVehicle['year'] ?? '') ?>" min="1990" max="<?= date('Y') + 1 ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Capacidad de Asientos *</label>
                    <input type="number" class="form-control" name="seats_capacity"
                        value="<?= esc($editingVehicle['seats_capacity'] ?? '') ?>" min="1" max="20" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Fotografía del Vehículo</label>
                    <input type="file" class="form-control" name="photo" accept="image/*">

                    <?php if (!empty($editingVehicle) && !empty($editingVehicle['photo_path'])): ?>
                        <div class="mt-2">
                            <small>Foto actual:</small><br>
                            <img src="<?= base_url($editingVehicle['photo_path']) ?>" alt="Vehículo"
                                style="max-width: 200px; max-height: 150px;" class="mt-1"
                                onerror="this.style.display='none';">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= !empty($editingVehicle) ? 'Actualizar Vehículo' : 'Crear Vehículo' ?>
                    </button>

                    <?php if (!empty($editingVehicle)): ?>
                        <a href="<?= site_url('vehicles') ?>" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>

                    <a class="btn btn-outline-secondary" href="<?= site_url('rides/my') ?>">Back to My Rides</a>
                </div>
            </form>
        </div>

        <footer class="mt-4">
            <hr>
            <nav class="text-center">
                <a href="<?= site_url('rides/my') ?>">Mis Viajes</a> |
                <a href="<?= site_url('rides/search') ?>">Buscar Viajes</a>
            </nav>
            <p class="text-center text-muted">&copy; <?= date('Y') ?> Aventones.com</p>
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>