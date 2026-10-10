<?php

declare(strict_types=1);

require __DIR__ . '/records.php';
require __DIR__ . '/contact.php';
require __DIR__ . '/pages.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function e_attr(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function data_dir(): string
{
    return dirname(__DIR__) . '/data';
}

function request_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        $path = '/';
    }
    $decoded = rawurldecode($path);
    if ($decoded !== '/' && str_ends_with($decoded, '/')) {
        $decoded = rtrim($decoded, '/');
        if ($decoded === '') {
            $decoded = '/';
        }
    }
    return $decoded;
}

function layout_start(string $title, string $heading): void
{
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . $title . '</title>';
    echo '<link rel="stylesheet" href="/assets/site.css">';
    echo '</head>';
    echo '<body>';
    echo '<header class="site-header">';
    echo '<a class="brand" href="/">GIT 2025</a>';
    echo '<nav>';
    echo '<a href="/">Home</a>';
    echo '<a href="/program">Program</a>';
    echo '<a href="/registration-fee">Registration Fee</a>';
    echo '<a href="/contact">Contact</a>';
    echo '</nav>';
    echo '</header>';
    echo '<main>';
    echo '<h1>' . e($heading) . '</h1>';
}

function layout_end(): void
{
    echo '</main>';
    echo '<footer class="site-footer">';
    echo '<p>The Gem and Jewelry Institute of Thailand (Public Organization)</p>';
    echo '<p>140, 140/1-3, 140/5 ITF - Tower Building, 1st - 4th and 6th Floor, Silom Road, Suriyawong, Bangrak, Bangkok 10500, Thailand</p>';
    echo '<p>(+66) 2634 4999 ext. 451-457</p>';
    echo '<p>(+66) 2634 4970</p>';
    echo '<p><a href="mailto:gitconference@git.or.th">gitconference@git.or.th</a></p>';
    echo '<p>Copyright © 2025. All rights reserved. The Gem and Jewelry Institute of Thailand (Public Organization).</p>';
    echo '</footer>';
    echo '</body>';
    echo '</html>';
}

function organiser_details(): void
{
    echo '<p>The Gem and Jewelry Institute of Thailand (Public Organization)</p>';
    echo '<p>140, 140/1-3, 140/5 ITF - Tower Building, 1st - 4th and 6th Floor, Silom Road, Suriyawong, Bangrak, Bangkok 10500, Thailand</p>';
    echo '<p>(+66) 2634 4999 ext. 451-457</p>';
    echo '<p>(+66) 2634 4970</p>';
    echo '<p><a href="mailto:gitconference@git.or.th">gitconference@git.or.th</a></p>';
}
