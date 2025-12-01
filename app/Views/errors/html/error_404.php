<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= esc($heading ?? 'Page Not Found') ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #0f172a; margin: 40px; }
        .error-container { max-width: 600px; margin: auto; text-align: center; }
        h1 { font-size: 2rem; }
        p { line-height: 1.5; }
        a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
<div class="error-container">
    <h1><?= esc($heading ?? 'Page Not Found') ?></h1>
    <p><?= esc($message ?? 'Sorry, the page you are looking for could not be found.') ?></p>
    <p><a href="/">Go back to homepage</a></p>
</div>
</body>
</html>
