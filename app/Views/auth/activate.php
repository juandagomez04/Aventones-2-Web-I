<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Activation</title>
</head>
<body>

<?php if (!empty($success) && $success === true): ?>
    <h2>✅ Account activated successfully</h2>
    <p>You can now log in.</p>
    <a href="<?= site_url('login') ?>">Go to Login</a>
<?php else: ?>
    <h2>❌ Activation failed</h2>
    <p><?= esc($message ?? 'Invalid or expired token.') ?></p>
    <a href="<?= site_url('login') ?>">Go to Login</a>
<?php endif; ?>

</body>
</html>
