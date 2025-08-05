<?php
get_header();
/**
 * Template Name: Calender
 */
?>

<div class="page-wrapper">
    <div class="page-header">
        <h1>DIAC Calender </h1>
    </div>

    <div class="filter-top">
        <h5 class="filter-title">FILTER EVENTS -</h5>
        <button class="filter-button rounded">See on a map</button>
        <a class="filter-button rounded" href="">Download Full Calender</a>
    </div>

    <div class="filter-container">
        <?php require_once get_template_directory() . '/partials/calendar-filters.php'; ?>
    </div>

    <div class="listing with-filter">
        <?php
        $args = [
            'post_type'      => 'events',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'ASC',
        ];

        $events = new WP_Query($args);

        if ($events->have_posts()):

            echo '<div class="timeline"><ul>';

            $today = new DateTime();
            $index = 0;
            $dates_output = [];

            while ($events->have_posts()) : $events->the_post();

                $event_date = get_the_date('Y-m-d');

                if (in_array($event_date, $dates_output)) {
                    continue;
                }

                $dates_output[] = $event_date;

                $date_obj = DateTime::createFromFormat('Y-m-d', $event_date);
                if (!$date_obj) continue;

                $day_name = $date_obj->format('l');
                $day_num  = $date_obj->format('d');

                $classes = [$index];

                if ($event_date === $today->format('Y-m-d')) {
                    $classes[] = 'today'; // Optional for styling today
                }

                echo '<li data-date="' . esc_attr($event_date) . '" class="' . implode(' ', $classes) . '">';
                echo esc_html($day_name) . ' <b>' . esc_html($day_num) . '</b>';
                echo '</li>';

                $index++;

            endwhile;

            echo '</ul></div>';
            wp_reset_postdata();
        else:
            echo '<p>No events found.</p>';
        endif;
        ?>

        <div class="list-events">
            <?php
            $args = [
                'post_type'      => 'events',
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ];

            $events = new WP_Query($args);
            $current_date = '';

            if ($events->have_posts()) : ?>

                <?php while ($events->have_posts()) : $events->the_post();

                    $select_partners = get_field('select_partners');
                    $host_speakers   = get_field('host_speakers');
                    $guest_speakers  = get_field('guest_speakers');

                    $event_date = get_the_date('Y-m-d');
                    $date_timestamp = strtotime($event_date);
                    $date_display = date_i18n('l d F Y', $date_timestamp);
                    $day_short = date_i18n('D', $date_timestamp);
                    $day_num = date_i18n('d', $date_timestamp);
                    $month = date_i18n('F', $date_timestamp);
                    $year = date_i18n('Y', $date_timestamp);

                    if ($current_date !== $event_date) {
                        $current_date = $event_date;
                        echo '<div class="list-date" id="event-' . esc_attr($event_date) . '" data-date="' . esc_attr($event_date) . '">';
                        echo '<span>' . esc_html($date_display) . '</span>';
                        echo '</div>';
                    }
                ?>

                    <div class="single-event" style="margin-bottom: 10px;">
                        <div class="inside" data-id="<?php echo get_the_ID(); ?>">
                            <div class="event-date">
                                <span class="day"><?php echo esc_html($day_short); ?>. <b><?php echo esc_html($day_num); ?></b> <?php echo esc_html($month); ?></span>
                                <span class="year"><?php echo esc_html($year); ?></span>
                            </div>

                            <div class="event-details">
                                <a href="<?php the_permalink(); ?>" class="event-title">
                                    <?php the_title(); ?>
                                </a>
                                <?php
                                if (!function_exists('output_related_partners')) {
                                    function output_related_partners($items)
                                    {
                                        if ($items) {
                                            $total = count($items);
                                            foreach ($items as $index => $item) {
                                                echo '<a href="' . esc_url(get_permalink($item)) . '" class="partner">' . esc_html(get_the_title($item)) . '</a>';
                                                if ($index < $total - 1) {
                                                    echo ', ';
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>

                                <?php
                                if (!empty($select_partners) || !empty($host_speakers) || !empty($guest_speakers)) :
                                    // Merge all arrays, skipping empty ones
                                    $all_partners = array_merge(
                                        !empty($select_partners) ? $select_partners : [],
                                        !empty($host_speakers) ? $host_speakers : [],
                                        !empty($guest_speakers) ? $guest_speakers : []
                                    );
                                ?>
                                    <div class="event-partners">
                                        <?php output_related_partners($all_partners); ?>
                                    </div>
                                <?php endif; ?>


                                <div class="event-themes">
                                    <?php
                                    $themes = get_the_terms(get_the_ID(), 'event_theme');
                                    if ($themes && !is_wp_error($themes)) {
                                        foreach ($themes as $theme) {
                                            echo '<span class="theme">#' . esc_html($theme->name) . '</span>';
                                        }
                                    }
                                    $regions = get_the_terms(get_the_ID(), 'region');
                                    if ($regions && !is_wp_error($regions)) {
                                        foreach ($regions as $region) {
                                            echo '<span class="region">#' . esc_html($region->name) . '</span>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="event-metas">
                                <div class="format">
                                    <?php
                                    $formats = get_the_terms(get_the_ID(), 'format');

                                    if ($formats && !is_wp_error($formats)) {
                                        $total_formats = count($formats);
                                        $index = 0;

                                        foreach ($formats as $format) {
                                            $index++;

                                            // Start SVG block
                                            if ($format->slug === 'online') {
                                    ?>
                                                <!-- Online SVG -->
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="92" height="26" viewBox="0 0 92 26">
                                                    <defs>
                                                        <clipPath>
                                                            <rect id="Rectangle_54" data-name="Rectangle 54" width="19.161" height="18.272" fill="#373737"></rect>
                                                        </clipPath>
                                                    </defs>
                                                    <g id="Groupe_10567" data-name="Groupe 10567" transform="translate(-196 -3020)">
                                                        <g id="Composant_62_1" data-name="Composant 62 – 1" transform="translate(209.92 3023.863)">
                                                            <g id="Groupe_72" data-name="Groupe 72">
                                                                <path id="Tracé_63" data-name="Tracé 63" d="M153.009,227.895a.626.626,0,0,0-1.178,0l-2.6,7.329a.616.616,0,0,0,.08.572.632.632,0,0,0,.5.264h5.2a.632.632,0,0,0,.5-.264.622.622,0,0,0,.08-.572ZM150,235.353l2.421-6.906,2.436,6.867Z" transform="translate(-142.839 -217.786)" fill="#373737"></path>
                                                                <path id="Tracé_64" data-name="Tracé 64" d="M9.578,0A9.537,9.537,0,0,0,4.954,17.9a.4.4,0,0,0,.184.045.368.368,0,0,0,.323-.189.373.373,0,0,0-.144-.5,8.8,8.8,0,1,1,8.527,0,.373.373,0,1,0,.363.651A9.537,9.537,0,0,0,9.578.01Z" transform="translate(0 0)" fill="#373737"></path>
                                                                <path id="Tracé_65" data-name="Tracé 65" d="M99.384,99.749a.388.388,0,0,0,.2-.06,5.733,5.733,0,1,0-6.23,0,.373.373,0,0,0,.5-.109.383.383,0,0,0-.109-.5,4.972,4.972,0,1,1,5.415,0,.373.373,0,0,0,.224.666Z" transform="translate(-86.872 -85.345)" fill="#373737"></path>
                                                                <path id="Tracé_66" data-name="Tracé 66" d="M153.88,153.466a.378.378,0,0,0,.269.114.393.393,0,0,0,.259-.1,3.232,3.232,0,1,0-4.43,0,.373.373,0,0,0,.527-.527,2.486,2.486,0,1,1,3.406,0,.373.373,0,0,0-.03.512Z" transform="translate(-142.612 -141.593)" fill="#373737"></path>
                                                            </g>
                                                        </g>
                                                        <path id="Rectangle_196" data-name="Rectangle 196" d="M13,1A12,12,0,0,0,4.515,21.485,11.921,11.921,0,0,0,13,25H79A12,12,0,0,0,87.485,4.515,11.921,11.921,0,0,0,79,1H13m0-1H79a13,13,0,0,1,0,26H13A13,13,0,0,1,13,0Z" transform="translate(196 3020)" fill="#373737"></path>
                                                        <text id="Online" transform="translate(235 3037)" fill="#373737" font-size="11" font-family="Roboto-Medium, Roboto" font-weight="500">
                                                            <tspan x="0" y="0"><?php echo esc_html(strtoupper($format->name)); ?></tspan>
                                                        </text>
                                                    </g>
                                                </svg>
                                            <?php
                                            } elseif ($format->slug === 'hybrid') {
                                            ?>
                                                <!-- Hybrid SVG -->
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="84" height="26" viewBox="0 0 84 26">
                                                    <defs>
                                                        <clipPath>
                                                            <rect id="Rectangle_168" data-name="Rectangle 168" width="12.259" height="14.886" fill="none"></rect>
                                                        </clipPath>
                                                        <clipPath>
                                                            <rect id="Rectangle_169" data-name="Rectangle 169" width="15.113" height="18.707" fill="none"></rect>
                                                        </clipPath>
                                                    </defs>
                                                    <g id="Groupe_9769" data-name="Groupe 9769" transform="translate(-1450 -961)">
                                                        <g id="Groupe_80" data-name="Groupe 80" transform="translate(1450 961)">
                                                            <path id="Rectangle_9" data-name="Rectangle 9" d="M13,1A12,12,0,0,0,4.515,21.485,11.921,11.921,0,0,0,13,25H71A12,12,0,0,0,79.485,4.515,11.921,11.921,0,0,0,71,1H13m0-1H71a13,13,0,0,1,0,26H13A13,13,0,0,1,13,0Z" fill="#373737"></path>
                                                            <text id="Hybrid" transform="translate(31 17)" fill="#373737" font-size="11" font-family="Roboto-Medium, Roboto" font-weight="500">
                                                                <tspan x="0" y="0"><?php echo esc_html(strtoupper($format->name)); ?></tspan>
                                                            </text>
                                                        </g>
                                                        <g id="Groupe_9454" data-name="Groupe 9454" transform="translate(1463.016 964.646)">
                                                            <g id="Groupe_9451" data-name="Groupe 9451" transform="translate(0 3.821)">
                                                                <g id="Groupe_9450" data-name="Groupe 9450">
                                                                    <path id="Tracé_27208" data-name="Tracé 27208" d="M10.333,6.038a6.226,6.226,0,0,0-8.552,0A5.669,5.669,0,0,0,1.323,13.8L5.7,19.078a.466.466,0,0,0,.656.061.451.451,0,0,0,.061-.061L10.8,13.8a5.669,5.669,0,0,0-.464-7.764M2.426,6.71a5.3,5.3,0,0,1,7.262,0,4.737,4.737,0,0,1,.386,6.5L6.057,18.051,2.04,13.207a4.737,4.737,0,0,1,.386-6.5ZM4.659,9.928a1.4,1.4,0,1,1,1.4,1.4,1.4,1.4,0,0,1-1.4-1.4M6.06,7.6A2.329,2.329,0,1,0,8.389,9.928,2.329,2.329,0,0,0,6.06,7.6" transform="translate(0 -4.361)" fill="#272727" fill-rule="evenodd"></path>
                                                                </g>
                                                            </g>
                                                            <g id="Groupe_9453" data-name="Groupe 9453">
                                                                <g id="Groupe_9452" data-name="Groupe 9452">
                                                                    <path id="Tracé_27209" data-name="Tracé 27209" d="M6.776.155A.438.438,0,0,0,6.445.7a.469.469,0,0,0,.549.349,6.9,6.9,0,0,1,8.021,5.08.468.468,0,0,0,.549.349.428.428,0,0,0,.335-.529A7.843,7.843,0,0,0,6.776.155Z" transform="translate(-0.8 0)" fill="#272727"></path>
                                                                    <path id="Tracé_27210" data-name="Tracé 27210" d="M7.366,2.247a.427.427,0,0,0-.334.529.469.469,0,0,0,.55.349A5.222,5.222,0,0,1,13.29,6.766a.469.469,0,0,0,.55.348.427.427,0,0,0,.334-.529A6.144,6.144,0,0,0,7.366,2.247Z" transform="translate(-0.873 -0.267)" fill="#272727"></path>
                                                                </g>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                            <?php
                                            } elseif ($format->slug === 'in-person') {
                                            ?>
                                                <!-- In-person SVG -->
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="101" height="26" viewBox="0 0 101 26">
                                                    <defs>
                                                        <clipPath>
                                                            <rect id="Rectangle_193" data-name="Rectangle 193" width="14" height="17" transform="translate(0 0)" fill="#373737"></rect>
                                                        </clipPath>
                                                    </defs>
                                                    <g id="Groupe_10566" data-name="Groupe 10566" transform="translate(-87 -3020)">
                                                        <path id="Rectangle_192" data-name="Rectangle 192" d="M13,1A12,12,0,0,0,4.515,21.485,11.921,11.921,0,0,0,13,25H88A12,12,0,0,0,96.485,4.515,11.921,11.921,0,0,0,88,1H13m0-1H88a13,13,0,0,1,0,26H13A13,13,0,0,1,13,0Z" transform="translate(87 3020)" fill="#373737"></path>
                                                        <text id="in_person" data-name="in person" transform="translate(120 3037)" fill="#373737" font-size="11" font-family="Roboto-Medium, Roboto" font-weight="500">
                                                            <tspan x="0" y="0"><?php echo esc_html(strtoupper($format->name)); ?></tspan>
                                                        </text>
                                                        <g id="Groupe_9529" data-name="Groupe 9529" transform="translate(99 3024.766)">
                                                            <g id="Groupe_9528" data-name="Groupe 9528" transform="translate(0 0.234)">
                                                                <path id="Tracé_27246" data-name="Tracé 27246" d="M11.8,1.942a7.111,7.111,0,0,0-9.766,0,6.474,6.474,0,0,0-.523,8.867l5,6.025a.532.532,0,0,0,.819,0l5-6.025A6.474,6.474,0,0,0,11.8,1.942m-9.03.768a6.047,6.047,0,0,1,8.293,0,5.41,5.41,0,0,1,.441,7.42L6.917,15.661,2.33,10.13a5.41,5.41,0,0,1,.441-7.42Zm2.55,3.675a1.6,1.6,0,1,1,1.6,1.6,1.6,1.6,0,0,1-1.6-1.6m1.6-2.66a2.66,2.66,0,1,0,2.66,2.66,2.66,2.66,0,0,0-2.66-2.66" transform="translate(0 -0.027)" fill="#373737" fill-rule="evenodd"></path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>

                                    <?php
                                            }

                                            // Add comma unless it's the last item
                                            // if ($index < $total_formats) {
                                            //     echo ', ';
                                            // }
                                        }
                                    }
                                    ?>
                                </div>


                            </div>

                            <?php
                            $status = get_field('status'); // Get your custom status field value

                            ?>

                            <div class="event-ressources">
                                <?php if ($status === 'live') : ?>
                                    <span class="media-available">Media available</span>
                                    <div class="medias">
                                        <span class="media-document">
                                        </span>
                                        <span class="media-photos">
                                            <span data-tooltip="Photos">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="31" height="24" viewBox="0 0 31 24">
                                                    <path d="M2.341,0A2.351,2.351,0,0,0,0,2.341V17.724a2.351,2.351,0,0,0,2.341,2.341h18.11a5.353,5.353,0,1,0,6.97-6.97V2.341A2.351,2.351,0,0,0,25.081,0Zm0,1.338h22.74a.987.987,0,0,1,1,1V12.76a5.317,5.317,0,0,0-.669-.052c-.032,0-.062.01-.094.01L21.266,9.5a.69.69,0,0,0-.847.021L15.78,13.45,9.855,6.908a.7.7,0,0,0-.93-.052L1.338,13.565V2.341a.987.987,0,0,1,1-1M16.386,4.013a2.341,2.341,0,1,0,2.341,2.341,2.351,2.351,0,0,0-2.341-2.341m0,1.338a1,1,0,1,1-1,1,.993.993,0,0,1,1-1M9.311,8.3l5.915,6.531a.694.694,0,0,0,.92.052l4.723-3.992,2.707,2.153a5.236,5.236,0,0,0-3.459,5.685H2.341a.987.987,0,0,1-1-1V15.351Zm16.1,5.747A4.013,4.013,0,1,1,21.4,18.058a4,4,0,0,1,4.013-4.013m0,1a.669.669,0,0,0-.669.669V18.79l-.533-.533a.665.665,0,1,0-.94.94c.544.547,1.115,1.145,1.662,1.651a.636.636,0,0,0,.961,0c.557-.5,1.106-1.116,1.662-1.651a.655.655,0,0,0,0-.94.662.662,0,0,0-.94,0l-.533.533V15.717a.669.669,0,0,0-.669-.669" transform="translate(0 0.215)" fill="#255377"></path>
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="media-replay">
                                        </span>
                                    </div>

                                <?php elseif ($status === 'close') : ?>
                                    <span class="closed">close</span>
                                <?php else : ?>
                                    <!-- Optional: default or no status -->
                                    <span class="media-document"></span>
                                    <span class="media-photos"></span>
                                    <span class="media-replay"></span>
                                <?php endif; ?>

                            </div>

                        </div>
                    </div>

                <?php endwhile; ?>

            <?php
            else :
                echo '<p>No upcoming events found.</p>';
            endif;
            wp_reset_postdata();
            ?>
        </div>

    </div>
</div>

<?php get_footer(); ?>