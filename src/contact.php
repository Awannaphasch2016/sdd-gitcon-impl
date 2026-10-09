<?php

declare(strict_types=1);

const CONTACT_GROUPS = [
    'General',
    'Registration',
    'Abstract Submission',
    'Sponsorship',
];

function contact_errors(): array
{
    return [
        'group' => 'Please select a group',
        'subject' => 'Please enter a subject',
        'message' => 'Please enter a message',
        'name' => 'Please enter your full name',
        'email' => 'Please enter a valid email',
        'phone' => 'Please enter a valid phone number',
    ];
}

function valid_email(string $email): bool
{
    $parts = explode('@', $email);
    if (count($parts) !== 2) {
        return false;
    }
    [$local, $domain] = $parts;
    if ($local === '' || $domain === '') {
        return false;
    }
    return str_contains($domain, '.');
}

function valid_phone(string $phone): bool
{
    return $phone === '' || preg_match('/^[0-9 +\-()]+$/', $phone) === 1;
}

function inspect_contact(array $post): array
{
    $raw = [
        'group' => (string) ($post['group'] ?? ''),
        'subject' => (string) ($post['subject'] ?? ''),
        'message' => (string) ($post['message'] ?? ''),
        'name' => (string) ($post['name'] ?? ''),
        'email' => (string) ($post['email'] ?? ''),
        'phone' => (string) ($post['phone'] ?? ''),
    ];
    $messages = contact_errors();
    $errors = [];
    if (!in_array($raw['group'], CONTACT_GROUPS, true)) {
        $errors['group'] = $messages['group'];
    }
    if (trim($raw['subject']) === '') {
        $errors['subject'] = $messages['subject'];
    }
    if (trim($raw['message']) === '') {
        $errors['message'] = $messages['message'];
    }
    if (trim($raw['name']) === '') {
        $errors['name'] = $messages['name'];
    }
    if (!valid_email(trim($raw['email']))) {
        $errors['email'] = $messages['email'];
    }
    $phone = trim($raw['phone']);
    if (!valid_phone($phone)) {
        $errors['phone'] = $messages['phone'];
    }

    return [
        'raw' => $raw,
        'errors' => $errors,
        'stored' => [
            'group' => $raw['group'],
            'subject' => trim($raw['subject']),
            'message' => trim($raw['message']),
            'name' => trim($raw['name']),
            'email' => trim($raw['email']),
            'phone' => $phone,
        ],
    ];
}

function submissions_path(): string
{
    return data_dir() . '/submissions.jsonl';
}

function store_contact(array $stored): array
{
    $record = [
        'group' => $stored['group'],
        'subject' => $stored['subject'],
        'message' => $stored['message'],
        'name' => $stored['name'],
        'email' => $stored['email'],
        'phone' => $stored['phone'],
        'submitted_at' => gmdate('Y-m-d\TH:i:s\Z'),
    ];
    $line = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($line === false) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Could not store the message\n";
        exit;
    }
    $handle = fopen(submissions_path(), 'ab');
    if ($handle === false) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Could not store the message\n";
        exit;
    }
    flock($handle, LOCK_EX);
    fwrite($handle, $line . "\n");
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
    publish_submissions(read_contacts());
    return $record;
}

function submissions_public_path(): string
{
    return dirname(__DIR__) . '/public/contact/submissions.json';
}

function publish_submissions(array $rows): void
{
    $json = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false || file_put_contents(submissions_public_path(), $json, LOCK_EX) === false) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Could not store the message\n";
        exit;
    }
}

function read_contacts(): array
{
    $path = submissions_path();
    if (!is_file($path)) {
        return [];
    }
    $handle = fopen($path, 'rb');
    if ($handle === false) {
        return [];
    }
    $rows = [];
    flock($handle, LOCK_SH);
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $row = json_decode($line, true);
        if (is_array($row)) {
            $rows[] = [
                'group' => (string) ($row['group'] ?? ''),
                'subject' => (string) ($row['subject'] ?? ''),
                'message' => (string) ($row['message'] ?? ''),
                'name' => (string) ($row['name'] ?? ''),
                'email' => (string) ($row['email'] ?? ''),
                'phone' => (string) ($row['phone'] ?? ''),
                'submitted_at' => (string) ($row['submitted_at'] ?? ''),
            ];
        }
    }
    flock($handle, LOCK_UN);
    fclose($handle);
    return $rows;
}
