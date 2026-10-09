<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$path = request_path();
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if (str_starts_with($path, '/assets/')) {
    $assetRoot = realpath(__DIR__ . '/assets');
    $candidate = realpath(__DIR__ . $path);
    if (
        $assetRoot !== false
        && $candidate !== false
        && is_file($candidate)
        && str_starts_with($candidate, $assetRoot . DIRECTORY_SEPARATOR)
    ) {
        return false;
    }
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Not found\n";
    return;
}

$routes = [
    '/' => ['GET'],
    '/program' => ['GET'],
    '/registration-fee' => ['GET'],
    '/contact' => ['GET', 'POST'],
    '/contact/submissions.json' => ['GET'],
];

if (!isset($routes[$path])) {
    if ($method === 'GET') {
        render_not_found();
    } else {
        render_method_not_allowed();
    }
    return;
}

if (!in_array($method, $routes[$path], true)) {
    render_method_not_allowed();
    return;
}

if ($path === '/') {
    render_home();
    return;
}
if ($path === '/program') {
    render_program();
    return;
}
if ($path === '/registration-fee') {
    render_fees();
    return;
}
if ($path === '/contact') {
    handle_contact_request($method);
    return;
}
if ($path === '/contact/submissions.json') {
    render_submissions();
}
