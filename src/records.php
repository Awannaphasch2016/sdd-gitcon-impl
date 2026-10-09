<?php

declare(strict_types=1);

function read_fixture(string $filename): array
{
    $path = data_dir() . '/' . $filename;
    $raw = file_get_contents($path);
    if ($raw === false) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Records unavailable\n";
        exit;
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Records unavailable\n";
        exit;
    }
    return $data;
}

function program_records(): array
{
    return read_fixture('program.json');
}

function fee_records(): array
{
    return read_fixture('fees.json');
}
