<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src/bootstrap.php';

handle_contact_request(strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')));
