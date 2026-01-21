<?php
/**
 * Plugin Name: Investalo Internal News Feed
 * Description: Sammelt automatisch Wirtschafts-News via RSS (Reuters/FAZ) und stellt sie per Shortcode bereit.
 * Version: 1.1
 * Author: Chris | Investalo Akademie
 */

if (!defined('ABSPATH')) exit;

/* ---------------------------------------------------------
   1. UI-Design (Fintech Style)
------------------------------------------------------------ */
add_action('wp_head', function () {
    ?>
    <style>
        .investalo-news { margin: 24px 0; max-width: 760px; position: relative; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .investalo-news::before { content: ""; position: absolute; left: -10px; top: 2px; bottom: 2px; width: 2px; background: linear-gradient(180deg, rgba(20,184,166,0), rgba(20,184,166,0.6), rgba(20,184,166,0)); }
        .investalo-news-header h3 { font-size: 14px; font-weight: 600; margin-bottom: 12px; color: #334155; letter-spacing: 0.08em; text-transform: uppercase; }
        .investalo-news-item { padding: 10px 0; border-bottom: 1px solid #eef2f7; transition: all 0.2s; }
        .investalo-news-item:last-child { border-bottom: none; }
        .investalo-news-item h4 { font-size: 14px; font-weight: 400; line-height: 1.4; margin: 0; color: #020617; display: inline; transition: color 0.15s ease; }
        .investalo-news-item a { text-decoration: none; }
        .investalo-news-item a:hover h4 { color: #14b8a6; }
        .investalo-news-meta { display: inline; font-size: 11px; color: #94a3b8; margin-left: 8px; white-space: nowrap; }
        .investalo-news-meta span:first-child { font-weight: 500; color: #64748b; }
        @media (max-width: 768px) { .investalo-news { margin: 18px 0; } }
    </style>
    <?php
});

/* ---------------------------------------------------------
   2. Logik: News-Aggregation & Cron
------------------------------------------------------------ */
function investalo_feed_sources() {
    return [
        'https://feeds.reuters.com/reuters/businessNews',
        'https://www.faz.net/rss/aktuell/wirtschaft/'
    ];
}

function investalo_collect_news() {
    if (!function_exists('fetch_feed')) {
        include_once ABSPATH . WPINC . '/feed.php';
    }

    $feeds = investalo_feed_sources();
    $news = [];

    foreach ($feeds as $feed_url) {
        $rss = fetch_feed($feed_url);
        if (!is_wp_error($rss)) {
            foreach ($rss->get_items(0, 5) as $item) {
                $news[] = [
                    'title'  => wp_strip_all_tags($item->get_title()),
                    'link'   => esc_url_raw($item->get_link()),
                    'source' => wp_strip_all_tags($item->get_feed()->get_title()),
                    'date'   => $item->get_date('U')
                ];
            }
        }
    }

    usort($news, fn($a, $b) => $b['date'] - $a['date']);
    set_transient('investalo_internal_news', array_slice($news, 0, 10), 3600);
}

// Cron Job Aktivierung
add_action('investalo_news_cron', 'investalo_collect_news');
if (!wp_next_scheduled('investalo_news_cron')) {
    wp_schedule_event(time(), 'hourly', 'investalo_news_cron');
}

/* ---------------------------------------------------------
   3. Shortcode: [investalo_news]
------------------------------------------------------------ */
function investalo_news_shortcode() {
    $news = get_transient('investalo_internal_news');

    if (!$news) {
        investalo_collect_news();
        $news = get_transient('investalo_internal_news');
    }

    if (empty($news)) return '<p>Derzeit keine Nachrichten verfügbar.</p>';

    ob_start(); ?>
    <section class="investalo-news">
        <div class="investalo-news-header"><h3>Finanz- & Wirtschaftsnachrichten</h3></div>
        <?php foreach ($news as $item): ?>
            <article class="investalo-news-item">
                <a href="<?= esc_url($item['link']); ?>" target="_blank" rel="noopener">
                    <h4><?= esc_html($item['title']); ?></h4>
                </a>
                <div class="investalo-news-meta">
                    <span><?= esc_html($item['source']); ?></span>
                    <span><?= date('d.m.Y H:i', $item['date']); ?></span>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
    <?php return ob_get_clean();
}
add_shortcode('investalo_news', 'investalo_news_shortcode');
