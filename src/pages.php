<?php

declare(strict_types=1);

function render_home(): void
{
    http_response_code(200);
    layout_start('Home - GIT 2025 Responsible Gem & Jewelry Supply Chain', 'GIT 2025');
    echo '<p class="lede">Responsible Gem & Jewelry Supply Chain</p>';
    echo '<p>8 - 9 September 2025</p>';
    echo '<p>Bangkok, Thailand</p>';
    echo '<p>The Gem and Jewelry Institute of Thailand (public organization) or GIT has proudly hosted the Gem and Jewelry Conferences since 2006. Each event has been met with the warmest reception and overwhelming success. The past conferences have not only served as academic platforms but also as networking forums for peers, academia, and industry experts.</p>';
    echo '<p>This year, the 8th International Gem and Jewelry Conference (GIT 2025) will welcome you to Bangkok with the theme \'Responsible Gem & Jewelry Supply Chain\'. The theme was chosen with the main purpose of bringing together experts, academics, and industry professionals to exchange knowledge and ideas on the development of the gem industry, the use of environmentally friendly technology, and the creation of a sustainable supply chain.</p>';
    echo '<p>The conference will take place in Bangkok, Thailand, from September 8 - 9, 2025. Attendees will also have the opportunity to participate in the 72nd Bangkok Gem and Jewelry Fair, held concurrently.</p>';
    echo '<p class="actions"><a href="/registration-fee">Registration Fee</a> <a href="/program">Program</a></p>';
    layout_end();
}

function render_program(): void
{
    http_response_code(200);
    $program = program_records();
    layout_start('Program - GIT 2025', 'Program');
    $days = $program['days'] ?? [];
    if (is_array($days)) {
        foreach ($days as $day) {
            if (!is_array($day)) {
                continue;
            }
            echo '<section>';
            echo '<h2>' . e((string) ($day['title'] ?? '')) . '</h2>';
            echo '<table>';
            echo '<thead><tr><th>Time</th><th>Title</th><th>Speaker</th><th>Affiliation</th><th>Room</th></tr></thead>';
            echo '<tbody>';
            $sessions = $day['sessions'] ?? [];
            if (is_array($sessions)) {
                foreach ($sessions as $session) {
                    if (!is_array($session)) {
                        continue;
                    }
                    echo '<tr>';
                    foreach (['time', 'title', 'speaker', 'affiliation', 'room'] as $field) {
                        echo '<td>' . e((string) ($session[$field] ?? '')) . '</td>';
                    }
                    echo '</tr>';
                }
            }
            echo '</tbody></table>';
            echo '</section>';
        }
    }
    layout_end();
}

function render_fees(): void
{
    http_response_code(200);
    $fees = fee_records();
    layout_start('Registration Fee - GIT 2025', 'Registration Fee');
    $columns = $fees['columns'] ?? [];
    $tables = $fees['tables'] ?? [];
    if (is_array($tables)) {
        foreach ($tables as $table) {
            if (!is_array($table)) {
                continue;
            }
            echo '<section>';
            echo '<h2>' . e((string) ($table['title'] ?? '')) . '</h2>';
            echo '<table>';
            echo '<thead><tr>';
            if (is_array($columns)) {
                foreach ($columns as $column) {
                    echo '<th>' . e((string) $column) . '</th>';
                }
            }
            echo '</tr></thead><tbody>';
            $rows = $table['rows'] ?? [];
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    echo '<tr>';
                    foreach ($row as $cell) {
                        echo '<td>' . e((string) $cell) . '</td>';
                    }
                    echo '</tr>';
                }
            }
            echo '</tbody></table>';
            echo '</section>';
        }
    }
    echo '<p>All fees include conference materials, lunches and coffee breaks on both days.</p>';
    layout_end();
}

function render_contact_form(?array $raw = null, array $errors = []): void
{
    http_response_code(200);
    layout_start('Contact - GIT 2025', 'Contact');
    organiser_details();
    $values = $raw ?? [
        'group' => 'General',
        'subject' => '',
        'message' => '',
        'name' => '',
        'email' => '',
        'phone' => '',
    ];
    $posted = $raw !== null;
    echo '<form method="post" action="/contact">';
    echo '<label for="group">Group</label>';
    echo '<select id="group" name="group">';
    foreach (CONTACT_GROUPS as $option) {
        $selected = '';
        if ($posted) {
            if ($values['group'] === $option) {
                $selected = ' selected';
            }
        } elseif ($option === 'General') {
            $selected = ' selected';
        }
        echo '<option value="' . e_attr($option) . '"' . $selected . '>' . e($option) . '</option>';
    }
    echo '</select>';
    echo field_error($errors, 'group');
    echo '<label for="subject">Subject</label>';
    echo '<input id="subject" name="subject" type="text" value="' . e_attr((string) $values['subject']) . '">';
    echo field_error($errors, 'subject');
    echo '<label for="message">Message</label>';
    echo '<textarea id="message" name="message">' . e((string) $values['message']) . '</textarea>';
    echo field_error($errors, 'message');
    echo '<label for="name">Full Name</label>';
    echo '<input id="name" name="name" type="text" value="' . e_attr((string) $values['name']) . '">';
    echo field_error($errors, 'name');
    echo '<label for="email">Email</label>';
    echo '<input id="email" name="email" type="email" value="' . e_attr((string) $values['email']) . '">';
    echo field_error($errors, 'email');
    echo '<label for="phone">Phone Number</label>';
    echo '<input id="phone" name="phone" type="text" value="' . e_attr((string) $values['phone']) . '">';
    echo field_error($errors, 'phone');
    echo '<button type="submit">Send</button>';
    echo '</form>';
    layout_end();
}

function render_contact_success(string $subject): void
{
    http_response_code(200);
    layout_start('Contact - GIT 2025', 'Contact');
    organiser_details();
    echo '<div data-confirmation>Thank you, your message has been sent. ' . e($subject) . '</div>';
    layout_end();
}

function handle_contact_request(string $method): void
{
    if ($method === 'GET') {
        render_contact_form();
        return;
    }
    if ($method === 'POST') {
        $result = inspect_contact($_POST);
        if ($result['errors'] !== []) {
            render_contact_form($result['raw'], $result['errors']);
            return;
        }
        $saved = store_contact($result['stored']);
        render_contact_success($saved['subject']);
        return;
    }
    render_method_not_allowed();
}

function render_submissions(): void
{
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(read_contacts(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function render_not_found(): void
{
    http_response_code(404);
    layout_start('Page not found - GIT 2025', 'Page not found');
    echo '<p><a href="/">Back to Home</a></p>';
    layout_end();
}

function render_method_not_allowed(): void
{
    http_response_code(405);
    layout_start('Method not allowed - GIT 2025', 'Method not allowed');
    layout_end();
}

function field_error(array $errors, string $name): string
{
    if (!isset($errors[$name])) {
        return '';
    }
    return '<p data-error="' . e_attr($name) . '">' . e((string) $errors[$name]) . '</p>';
}
