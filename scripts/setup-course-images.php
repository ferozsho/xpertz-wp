<?php
/**
 * Script to download free stock images and set as course featured images.
 * Run this script inside the WordPress container:
 *   docker exec -w /var/www/html xpertz_wp_app php /var/www/html/wp-content/scripts/setup-course-images.php
 */

// Bootstrap WordPress
define('WP_USE_THEMES', false);
require_once __DIR__ . '/../wp-load.php';

// Ensure we have media handling functions
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

// Courses to update
$courses = [
    61  => ['title' => 'LearnPress Course 1', 'query' => 'online+learning+education'],
    99  => ['title' => 'LearnPress Course 2', 'query' => 'student+studying+library'],
    140 => ['title' => 'LearnPress Course 3', 'query' => 'classroom+teaching'],
    172 => ['title' => 'LearnPress Course 4', 'query' => 'graduation+ceremony'],
    206 => ['title' => 'LearnPress Course 5', 'query' => 'laptop+learning+technology'],
    246 => ['title' => 'LearnPress Course 6', 'query' => 'books+knowledge'],
    278 => ['title' => 'LearnPress Course 7', 'query' => 'science+lab+research'],
    311 => ['title' => 'LearnPress Course 8', 'query' => 'presentation+workshop'],
    346 => ['title' => 'LearnPress Course 9', 'query' => 'writing+notes+study'],
    382 => ['title' => 'LearnPress Course 10', 'query' => 'global+education+world'],
];

// Target size
$target_w = 1200;
$target_h = 800;

$success = 0;
$failed = 0;

foreach ($courses as $course_id => $info) {
    echo "---\n";
    echo "Processing: {$info['title']} (ID: $course_id)\n";

    // Try to download from picsum.photos (free, no API key needed)
    $seed = sanitize_title($info['title']);
    $image_url = "https://picsum.photos/seed/{$seed}/{$target_w}/{$target_h}";

    echo "  Downloading: $image_url\n";

    $image_data = @file_get_contents($image_url, false, stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36\r\n",
            'timeout' => 30,
        ],
    ]));

    if ($image_data === false) {
        echo "  FAILED to download image\n";
        $failed++;
        continue;
    }

    // Save to temp file
    $tmp_path = wp_tempnam('course-image-' . $course_id . '.jpg');
    file_put_contents($tmp_path, $image_data);

    // Upload to WordPress media library
    $file_array = [
        'name'     => 'course-' . $course_id . '.jpg',
        'tmp_name' => $tmp_path,
    ];

    $attachment_id = media_handle_sideload($file_array, 0, $info['title']);

    if (is_wp_error($attachment_id)) {
        echo "  FAILED to upload to media library: " . $attachment_id->get_error_message() . "\n";
        @unlink($tmp_path);
        $failed++;
        continue;
    }

    echo "  Uploaded as attachment ID: $attachment_id\n";

    // Set as course featured image
    $result = set_post_thumbnail($course_id, $attachment_id);

    if ($result) {
        echo "  ✓ Featured image set successfully!\n";
        $success++;
    } else {
        echo "  ✗ Failed to set featured image\n";
        $failed++;
    }

    @unlink($tmp_path);
}

echo "\n========== SUMMARY ==========\n";
echo "Success: $success / " . count($courses) . "\n";
echo "Failed: $failed / " . count($courses) . "\n";
echo "=============================\n";
