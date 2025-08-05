<div class="main-filter">
    <form action="#">
        <input type="reset" class="filter-button" />
        <div class="single-select">
            <div class="dropdown">
                <div class="dropdown-toggle">
                    <span class="label">Partner</span>
                    <span class="icon">▼</span>
                </div>
                <div class="dropdown-menu">
                    <input type="text" placeholder="Search..." class="search-box" />
                    <div class="clear-btn">Clear all</div>

                    <?php
                    // Query all 'partner' posts
                    $partners = new WP_Query([
                        'post_type' => 'partner',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC',
                    ]);

                    if ($partners->have_posts()) :
                        while ($partners->have_posts()) : $partners->the_post();
                            $partner_name = get_the_title();
                    ?>
                            <div class="dropdown-item">
                                <input
                                    type="checkbox"
                                    class="item-checkbox"
                                    name="partner[]"
                                    value="<?php echo esc_attr($partner_name); ?>" />
                                <span><?php echo esc_html($partner_name); ?></span>
                            </div>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <div class="dropdown-item">No partners found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="single-select">
            <div class="dropdown">
                <div class="dropdown-toggle">
                    <span class="label">Speaker</span>
                    <span class="icon">▼</span>
                </div>
                <div class="dropdown-menu">
                    <input type="text" placeholder="Search..." class="search-box" />
                    <div class="clear-btn">Clear all</div>

                    <?php
                    // Query all 'partner' posts
                    $partners = new WP_Query([
                        'post_type' => 'speakers',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC',
                    ]);

                    if ($partners->have_posts()) :
                        while ($partners->have_posts()) : $partners->the_post();
                            $partner_name = get_the_title();
                    ?>
                            <div class="dropdown-item">
                                <input
                                    type="checkbox"
                                    class="item-checkbox"
                                    name="speaker[]"
                                    value="<?php echo esc_attr($partner_name); ?>" />
                                <span><?php echo esc_html($partner_name); ?></span>
                            </div>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <div class="dropdown-item">No partners found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="single-select">
            <div class="dropdown">
                <div class="dropdown-toggle">
                    <span class="label">Theme</span>
                    <span class="icon">▼</span>
                </div>
                <div class="dropdown-menu">
                    <input
                        type="text"
                        placeholder="Search..."
                        class="search-box" />
                    <div class="clear-btn">Clear all</div>

                    <?php
                    $event_theme_terms = get_terms([
                        'taxonomy' => 'event_theme',
                        'hide_empty' => false,
                    ]);

                    if (!empty($event_theme_terms) && !is_wp_error($event_theme_terms)) {
                        foreach ($event_theme_terms as $term) {
                            $term_name = esc_html($term->name);
                            $term_slug = esc_attr($term->slug);
                    ?>
                            <div class="dropdown-item">
                                <input
                                    type="checkbox"
                                    name="event_theme[]"
                                    class="item-checkbox"
                                    value="<?php echo $term_slug; ?>" />
                                <span><?php echo $term_name; ?></span>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<div class="dropdown-item">No event themes found.</div>';
                    }
                    ?>
                </div>

            </div>
        </div>
        <div class="single-select">
            <div class="dropdown">
                <div class="dropdown-toggle">
                    <span class="label">Region</span>
                    <span class="icon">▼</span>
                </div>
                <div class="dropdown-menu">
                    <input
                        type="text"
                        placeholder="Search..."
                        class="search-box" />
                    <div class="clear-btn">Clear all</div>
                    <?php
                    // Fetch all terms of the 'region' taxonomy (associated with 'events' post type)
                    $regions = get_terms(array(
                        'taxonomy' => 'region',  // taxonomy slug (usually lowercase)
                        'hide_empty' => false,   // show even if no posts assigned
                    ));
                    ?>
                    <?php if (!empty($regions) && !is_wp_error($regions)) : ?>
                        <?php foreach ($regions as $region) : ?>
                            <div class="dropdown-item">
                                <input
                                    type="checkbox"
                                    name="region[]"
                                    class="item-checkbox"
                                    value="<?php echo esc_attr($region->slug); ?>"
                                    id="region-<?php echo esc_attr($region->term_id); ?>" />
                                <span for="region-<?php echo esc_attr($region->term_id); ?>">
                                    <?php echo esc_html($region->name); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>No regions found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="input-search">
            <input type="text" name="search" placeholder="Enter a keyword" />
        </div>
    </form>
</div>
<div class="sub-filter">
    <form action="#">
        <?php
        // Get all terms from the 'schedule' taxonomy, including empty terms
        $schedule_terms = get_terms([
            'taxonomy' => 'schedule',
            'hide_empty' => false,
            'orderby' => 'id',
        ]);

        if (!empty($schedule_terms) && !is_wp_error($schedule_terms)) {
            foreach ($schedule_terms as $term) {
                // Use term slug or ID as value & id for inputs
                $term_id = esc_attr($term->term_id);
                $term_name = esc_html($term->name);
                $term_slug = esc_attr($term->slug);
        ?>
                <label for="schedule-<?php echo $term_id; ?>">
                    <input type="checkbox" name="schedule[]" id="schedule-<?php echo $term_id; ?>" value="<?php echo $term_slug; ?>" />
                    <?php echo $term_name; ?>
                </label>
        <?php
            }
        } else {
            echo "No schedule terms found.";
        }
        ?>

        <?php
        $excluded_id = 13;

        $format_terms = get_terms([
            'taxonomy' => 'format',
            'hide_empty' => false,
            'exclude' => [$excluded_id],
        ]);

        if (!empty($format_terms) && !is_wp_error($format_terms)) {
            foreach ($format_terms as $term) {
                $term_id = esc_attr($term->term_id);
                $term_name = esc_html($term->name);
                $term_slug = esc_attr($term->slug);
        ?>
                <label for="format-<?php echo $term_id; ?>">
                    <input type="checkbox" name="format[]" id="format-<?php echo $term_id; ?>" value="<?php echo $term_slug; ?>" />
                    <?php echo $term_name; ?>
                </label>
        <?php
            }
        } else {
            echo "No format terms found.";
        }
        ?>

    </form>
</div>