<?php

add_action('wp_ajax_filter_posts', 'handle_ajax_filter_posts');
add_action('wp_ajax_nopriv_filter_posts', 'handle_ajax_filter_posts');

function handle_ajax_filter_posts() {
    $args = [
        'post_type' => 'events',
        'posts_per_page' => -1,
        'tax_query' => [],
    ];

    // 🔍 Search query
    if (!empty($_GET['search'])) {
        $args['s'] = sanitize_text_field($_GET['search']);
    }
    
    // Filter by 'schedule' taxonomy
    if (!empty($_GET['schedule'])) {
        $schedule_terms = array_map('sanitize_text_field', (array) $_GET['schedule']);
        $args['tax_query'][] = [
            'taxonomy' => 'schedule',
            'field'    => 'slug',
            'terms'    => $schedule_terms,
        ];
    }

    // Filter by 'format' taxonomy
    if (!empty($_GET['format'])) {
        $format_terms = array_map('sanitize_text_field', (array) $_GET['format']);
        $args['tax_query'][] = [
            'taxonomy' => 'format',
            'field'    => 'slug',
            'terms'    => $format_terms,
        ];
    }

    // Only add relation if both are present
    if (count($args['tax_query']) > 1) {
        $args['tax_query']['relation'] = 'AND';
    }

    // Run the query
    $query = new WP_Query($args);
    $current_date = '';
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $event_date = get_the_date('Y-m-d');
            $date_timestamp = strtotime($event_date);
            $date_display = date_i18n('l d F Y', $date_timestamp);
           
            if ($current_date !== $event_date) {
                $current_date = $event_date;
                echo '<div class="list-date" id="event-' . esc_attr($event_date) . '" data-date="' . esc_attr($event_date) . '">';
                echo '<span>' . esc_html($date_display) . '</span>';
                echo '</div>';
            }
            $query->the_post();
            get_template_part('partials/event', 'card'); // Your event card/template
        }
    } else {
        echo '<p>No events found.</p>';
    }

    wp_reset_postdata();
    wp_die(); // Always terminate for AJAX
}
