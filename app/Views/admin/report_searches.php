<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>

<body>
    <div class="container mt-4">

        <a href="<?= site_url('admin') ?>" class="btn btn-secondary mb-3">
            ← Back to Administration
        </a>

        <h2>Reporte de Búsquedas</h2>

        <?php if (!empty($error)): ?>
            <p style="color:red;"><?= esc($error) ?></p>
        <?php endif; ?>

        <form method="post" action="<?= site_url('admin/reports/searches') ?>" class="mb-4">
            <label>Desde</label>
            <input type="date" name="from" value="<?= esc($from ?? '') ?>" required>

            <label>Hasta</label>
            <input type="date" name="to" value="<?= esc($to ?? '') ?>" required>

            <button type="submit" class="btn btn-primary ms-2">Generar</button>
        </form>

        <?php if (!empty($rows)): ?>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Lugar de Salida</th>
                        <th>Lugar de Llegada</th>
                        <th>Cantidad de Resultados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= esc($r['fecha']) ?></td>
                            <td><?= esc($r['usuario'] ?? 'Desconocido') ?></td>
                            <td><?= esc($r['salida']) ?></td>
                            <td><?= esc($r['llegada']) ?></td>
                            <td><?= esc($r['resultados']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (!empty($from) && !empty($to)): ?>
            <p>No hay búsquedas en ese rango.</p>
        <?php endif; ?>

    </div>
</body>


</html>