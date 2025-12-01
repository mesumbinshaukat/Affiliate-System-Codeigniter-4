<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= esc($title ?? 'An Error Occurred') ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff7ed; color: #7c2d12; margin: 40px; }
        .error-container { max-width: 700px; margin: auto; }
        h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        pre { background: #fff; border: 1px solid #fed7aa; padding: 1rem; overflow-x: auto; }
        a { color: #2563eb; }
    </style>
</head>
<body>
<div class="error-container">
    <h1><?= esc($title ?? 'An Error Occurred') ?></h1>
    <p><?= esc($message ?? 'Something went wrong while processing your request.') ?></p>
    <?php if (CI_DEBUG && isset($exception)) : ?>
        <h2>Exception Trace</h2>
        <pre><?= esc($exception->getMessage()) ?>
<?= esc($exception->getTraceAsString()) ?></pre>
    <?php endif; ?>
    <p><a href="/">Return to homepage</a></p>
</div>
</body>
</html>
