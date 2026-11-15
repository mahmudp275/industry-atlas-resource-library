<?php
/**
 * Plugin Name: Industry Atlas - Resource Library
 * Description: Standalone, shortcode-powered Resource Library with a WordPress admin dashboard, downloadable files, category filters and search.
 * Version: 2.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Industry Atlas Group
 * Text Domain: industry-atlas-resource-library
 */

if (!defined('ABSPATH')) {
    exit;
}

final class IAGRL_Resource_Library {
    const VERSION = '2.0.0';
    const POST_TYPE = 'iagrl_resource';
    const TAXONOMY = 'iagrl_topic';

    public function __construct() {
        add_action('init', array($this, 'register_content'));
        add_action('add_meta_boxes', array($this, 'register_meta_box'));
        add_action('save_post_' . self::POST_TYPE, array($this, 'save_resource'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend'));
        add_action('admin_menu', array($this, 'register_help_page'));
        add_filter('manage_' . self::POST_TYPE . '_posts_columns', array($this, 'resource_columns'));
        add_action('manage_' . self::POST_TYPE . '_posts_custom_column', array($this, 'render_resource_column'), 10, 2);
        add_shortcode('iag_resource_library', array($this, 'shortcode'));
    }

    public function register_content() {
        register_post_type(self::POST_TYPE, array(
            'labels' => array(
                'name' => 'Resource Library',
                'singular_name' => 'Resource',
                'menu_name' => 'Resource Library',
                'all_items' => 'All Resources',
                'add_new' => 'Add Resource',
                'add_new_item' => 'Add Downloadable Resource',
                'edit_item' => 'Edit Resource',
                'new_item' => 'New Resource',
                'search_items' => 'Search Resources',
            ),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'exclude_from_search' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-media-document',
            'menu_position' => 25,
            'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions'),
            'capability_type' => 'post',
        ));

        register_taxonomy(self::TAXONOMY, array(self::POST_TYPE), array(
            'labels' => array(
                'name' => 'Resource Categories',
                'singular_name' => 'Resource Category',
                'add_new_item' => 'Add Resource Category',
            ),
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite' => false,
        ));
    }

    public static function activate() {
        $instance = new self();
        $instance->register_content();
        $topics = array('Manufacturing Visibility', 'Supplier Discovery', 'Certification & Compliance');
        foreach ($topics as $topic) {
            if (!term_exists($topic, self::TAXONOMY)) {
                wp_insert_term($topic, self::TAXONOMY);
            }
        }

        // Only create the demonstration resource once, even if plugin is reactivated.
        $existing = get_posts(array(
            'post_type' => self::POST_TYPE,
            'post_status' => array('publish', 'draft', 'private', 'pending', 'trash'),
            'posts_per_page' => 1,
            'fields' => 'ids',
            'meta_key' => '_iagrl_seeded',
            'meta_value' => 'yes',
            'suppress_filters' => true,
        ));
        if (!$existing) {
            $post_id = wp_insert_post(array(
                'post_type' => self::POST_TYPE,
                'post_status' => 'publish',
                'post_title' => 'Manufacturing Visibility Checklist (Sample)',
                'post_excerpt' => 'A sample downloadable checklist demonstrating how resource files, categories, and download buttons work. Replace or remove before launch.',
                'post_content' => 'This is demonstration content supplied with the Industry Atlas Resource Library plugin. Edit or delete this sample resource in the WordPress dashboard.',
            ));
            if (!is_wp_error($post_id) && $post_id) {
                wp_set_object_terms($post_id, 'Manufacturing Visibility', self::TAXONOMY);
                update_post_meta($post_id, '_iagrl_url', plugin_dir_url(__FILE__) . 'assets/sample-manufacturing-visibility-checklist.pdf');
                update_post_meta($post_id, '_iagrl_is_sample', 'yes');
                update_post_meta($post_id, '_iagrl_seeded', 'yes');
            }
        }
    }

    public function register_meta_box() {
        add_meta_box('iagrl_file_box', 'Downloadable File', array($this, 'render_meta_box'), self::POST_TYPE, 'normal', 'high');
    }

    public function render_meta_box($post) {
        wp_nonce_field('iagrl_save_resource', 'iagrl_resource_nonce');
        $media_id = absint(get_post_meta($post->ID, '_iagrl_media_id', true));
        $manual_url = get_post_meta($post->ID, '_iagrl_url', true);
        $sample = get_post_meta($post->ID, '_iagrl_is_sample', true);
        $filename = $media_id ? basename((string) get_attached_file($media_id)) : '';
        ?>
        <p><strong>Select a file from WordPress Media Library</strong> (PDF, DOCX, XLSX, PPTX or other file types allowed by WordPress).</p>
        <input type="hidden" name="iagrl_media_id" id="iagrl_media_id" value="<?php echo esc_attr($media_id); ?>">
        <p><button type="button" class="button button-secondary" id="iagrl_select_media">Choose / Upload File</button>
        <button type="button" class="button" id="iagrl_clear_media">Remove Selected File</button></p>
        <p id="iagrl_selected_file"><em><?php echo $filename ? esc_html($filename) : 'No Media Library file selected.'; ?></em></p>
        <hr>
        <p><label for="iagrl_url"><strong>Or provide a direct file URL</strong> (used only when no Media Library file is selected)</label></p>
        <input type="url" class="widefat" name="iagrl_url" id="iagrl_url" placeholder="https://example.com/file.pdf" value="<?php echo esc_attr($manual_url); ?>">
        <p class="description">The link must be publicly accessible. PDF downloads and file links will appear on the Resource Library card. WordPress Media Library is recommended.</p>
        <p><label><input type="checkbox" name="iagrl_is_sample" value="yes" <?php checked($sample, 'yes'); ?>> Show a <strong>Sample</strong> badge on this resource</label></p>
        <p class="description">Tip: Add a short summary in the Excerpt field, assign a Resource Category in the sidebar, and click Publish / Update.</p>
        <?php
    }

    public function save_resource($post_id) {
        if (!isset($_POST['iagrl_resource_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['iagrl_resource_nonce'])), 'iagrl_save_resource')) {
            return;
        }
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post_id) || !current_user_can('edit_post', $post_id)) {
            return;
        }
        $media_id = isset($_POST['iagrl_media_id']) ? absint(wp_unslash($_POST['iagrl_media_id'])) : 0;
        if ($media_id && get_post_type($media_id) !== 'attachment') {
            $media_id = 0;
        }
        update_post_meta($post_id, '_iagrl_media_id', $media_id);
        $url = isset($_POST['iagrl_url']) ? esc_url_raw(wp_unslash($_POST['iagrl_url']), array('http', 'https')) : '';
        update_post_meta($post_id, '_iagrl_url', $url);
        update_post_meta($post_id, '_iagrl_is_sample', isset($_POST['iagrl_is_sample']) && $_POST['iagrl_is_sample'] === 'yes' ? 'yes' : 'no');
    }

    public function enqueue_admin() {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === self::POST_TYPE && in_array($screen->base, array('post', 'post-new'), true)) {
            wp_enqueue_media();
            wp_enqueue_script('iagrl-admin', plugin_dir_url(__FILE__) . 'assets/admin.js', array('jquery'), self::VERSION, true);
        }
    }

    public function enqueue_frontend() {
        // Scoped selectors only. Safe alongside Elementor and existing IAG pages.
        wp_enqueue_style('iagrl-styles', plugin_dir_url(__FILE__) . 'assets/resource-library.css', array(), self::VERSION);
        wp_enqueue_script('iagrl-filters', plugin_dir_url(__FILE__) . 'assets/resource-library.js', array(), self::VERSION, true);
    }

    public function register_help_page() {
        add_submenu_page('edit.php?post_type=' . self::POST_TYPE, 'Resource Library Guide', 'Shortcode & Help', 'edit_posts', 'iagrl-help', array($this, 'help_page'));
    }

    public function help_page() {
        echo '<div class="wrap"><h1>Industry Atlas - Resource Library</h1>';
        echo '<p>Add the following shortcode to an Elementor <strong>Shortcode</strong> widget or any WordPress page:</p>';
        echo '<p><code style="font-size:18px;padding:10px 14px;display:inline-block">[iag_resource_library]</code></p>';
        echo '<h2>Adding or editing a resource</h2><ol><li>Go to <strong>Resource Library → Add Resource</strong>.</li>';
        echo '<li>Add the resource title and a brief Excerpt.</li><li>Choose a <strong>Resource Category</strong>.</li>';
        echo '<li>In <strong>Downloadable File</strong>, upload/select a file or enter a direct file URL.</li>';
        echo '<li>Publish the resource. The page updates automatically.</li></ol>';
        echo '<p>The plugin provides one real, downloadable sample PDF on activation. Edit or delete it when adding your actual resources.</p>';
        echo '<p><strong>Note:</strong> This plugin creates only the Resource Library section. It does not automatically create pages, change your site menu, or modify your existing News & Resources plugin.</p></div>';
    }

    public function resource_columns($columns) {
        $columns['iagrl_file'] = 'Download File';
        return $columns;
    }

    public function render_resource_column($column, $post_id) {
        if ($column === 'iagrl_file') {
            $url = $this->file_url($post_id);
            echo $url ? '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">View file</a>' : '<span style="color:#a00">No file</span>';
        }
    }

    private function file_url($post_id) {
        $attachment_id = absint(get_post_meta($post_id, '_iagrl_media_id', true));
        if ($attachment_id) {
            $url = wp_get_attachment_url($attachment_id);
            if ($url) {
                return $url;
            }
        }
        return get_post_meta($post_id, '_iagrl_url', true);
    }

    private function file_type($url) {
        $path = wp_parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
        return in_array($ext, array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'txt', 'csv'), true) ? strtoupper($ext) : 'FILE';
    }

    private function svg($name) {
        $icons = array(
            'download' => '<path d="M12 3v12m-4-4 4 4 4-4M5 17v4h14v-4"/>',
            'search' => '<circle cx="10.9" cy="10.9" r="7"/><path d="m16 16 5 5"/>',
            'file' => '<path d="M6 2h8l5 5v15H6zM14 2v6h5M9 13h6M9 17h6"/>',
            'arrow' => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
            'folder' => '<path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v10H3z"/>',
        );
        return '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $icons[$name] . '</svg>';
    }

    /**
     * Returns a complete component, including a self-contained stylesheet.
     * Elementor can render shortcodes inside an AJAX preview where wp_head styles
     * are not reprinted. Keeping the section stylesheet next to its markup
     * prevents an unstyled / "broken" preview in that context.
     */
    public function shortcode($attributes = array()) {
        $attrs = shortcode_atts(array('title' => 'Resource Library', 'limit' => '6'), $attributes, 'iag_resource_library');
        $page_size = min(24, max(1, absint($attrs['limit'])));
        $resource_query = new WP_Query(array(
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 200,
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
        ));
        $topics = get_terms(array('taxonomy' => self::TAXONOMY, 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
        if (is_wp_error($topics)) $topics = array();
        $total = (int) $resource_query->post_count;
        $section_id = wp_unique_id('iagrl-');
        $stylesheet_path = plugin_dir_path(__FILE__) . 'assets/resource-library.css';
        // The shortcode must look correct even in Elementor's live editor / iframe.
        $inline_css = is_readable($stylesheet_path) ? file_get_contents($stylesheet_path) : '';
        ob_start();
        if ($inline_css) : ?>
        <style class="iagrl-embedded-css"><?php echo $inline_css; // Bundled static stylesheet, not user input. ?></style>
        <?php endif; ?>
        <section class="iagrl" id="<?php echo esc_attr($section_id); ?>" data-iagrl-section data-iagrl-limit="<?php echo esc_attr($page_size); ?>" aria-label="<?php echo esc_attr($attrs['title']); ?>">
            <div class="iagrl-hero">
                <div class="iagrl-hero-inner">
                    <div class="iagrl-hero-copy">
                        <div class="iagrl-overline"><span class="iagrl-dot"></span> INDUSTRY ATLAS GROUP <span class="iagrl-overline-sep">/</span> KNOWLEDGE CENTER</div>
                        <h2><?php echo esc_html($attrs['title']); ?></h2>
                        <p>Practical guides, checklists and resources to help manufacturing businesses get found, get certified and get connected.</p>
                        <div class="iagrl-hero-points">
                            <span><?php echo $this->svg('file'); ?> Industry insights</span>
                            <span><?php echo $this->svg('download'); ?> Free downloads</span>
                            <span><?php echo $this->svg('folder'); ?> Easy to explore</span>
                        </div>
                    </div>
                    <div class="iagrl-hero-art" aria-hidden="true">
                        <span class="iagrl-art-ring iagrl-art-ring-one"></span>
                        <span class="iagrl-art-ring iagrl-art-ring-two"></span>
                        <span class="iagrl-art-back"></span>
                        <div class="iagrl-art-front">
                            <div class="iagrl-art-brand"><span></span><span></span><span></span></div>
                            <div class="iagrl-art-icon"><?php echo $this->svg('file'); ?></div>
                            <span class="iagrl-art-line"></span><span class="iagrl-art-line short"></span>
                            <span class="iagrl-art-tag">RESOURCE GUIDE</span>
                        </div>
                        <span class="iagrl-art-download"><?php echo $this->svg('download'); ?></span>
                    </div>
                </div>
            </div>
            <div class="iagrl-content">
                <div class="iagrl-container">
                    <div class="iagrl-section-intro">
                        <div><span class="iagrl-kicker">BROWSE THE LIBRARY</span><h3>Explore our resources</h3><p>Find practical materials tailored to your business needs.</p></div>
                        <div class="iagrl-resource-count"><span class="iagrl-count-number"><?php echo esc_html(number_format_i18n($total)); ?></span><span><?php echo $total === 1 ? 'Available resource' : 'Available resources'; ?></span></div>
                    </div>
                    <div class="iagrl-toolbar">
                        <div class="iagrl-filters" role="group" aria-label="Filter resource categories">
                            <button type="button" class="iagrl-chip is-active" data-iagrl-filter="all" aria-pressed="true">All Resources</button>
                            <?php foreach ($topics as $topic) : ?>
                                <button type="button" class="iagrl-chip" data-iagrl-filter="<?php echo esc_attr($topic->slug); ?>" aria-pressed="false"><?php echo esc_html($topic->name); ?></button>
                            <?php endforeach; ?>
                        </div>
                        <label class="iagrl-search">
                            <?php echo $this->svg('search'); ?>
                            <span class="iagrl-visually-hidden">Search resources</span>
                            <input type="search" autocomplete="off" placeholder="Search resources..." data-iagrl-search>
                        </label>
                    </div>
                    <div class="iagrl-list-head"><span><span class="iagrl-list-dot"></span> DOWNLOADABLE RESOURCES</span><span data-iagrl-results aria-live="polite"><?php echo esc_html(number_format_i18n($total)); ?> <?php echo $total === 1 ? 'result' : 'results'; ?></span></div>
                    <div class="iagrl-grid<?php echo $total === 1 ? ' is-single' : ''; ?>" data-iagrl-grid>
                        <?php if ($resource_query->have_posts()) : ?>
                            <?php while ($resource_query->have_posts()) : $resource_query->the_post();
                                $resource_id = get_the_ID();
                                $url = $this->file_url($resource_id);
                                $type = $url ? $this->file_type($url) : 'FILE';
                                $terms = get_the_terms($resource_id, self::TAXONOMY);
                                $cats = ($terms && !is_wp_error($terms)) ? array_values($terms) : array();
                                $slugs = wp_list_pluck($cats, 'slug');
                                $cat_label = $cats ? $cats[0]->name : 'Industry Resources';
                                $desc = get_the_excerpt();
                                if (!$desc) $desc = wp_trim_words(wp_strip_all_tags(get_the_content()), 26);
                                $sample = get_post_meta($resource_id, '_iagrl_is_sample', true) === 'yes';
                            ?>
                            <article class="iagrl-card" data-iagrl-card data-iagrl-categories="<?php echo esc_attr(implode(' ', $slugs)); ?>" data-iagrl-text="<?php echo esc_attr(strtolower(get_the_title() . ' ' . $desc . ' ' . implode(' ', wp_list_pluck($cats, 'name')))); ?>">
                                <div class="iagrl-card-media">
                                    <div class="iagrl-card-medialines"><i></i><i></i><i></i></div>
                                    <div class="iagrl-card-file">
                                        <?php echo $this->svg('file'); ?>
                                        <span><?php echo esc_html($type); ?></span>
                                    </div>
                                    <span class="iagrl-media-caption">INDUSTRY ATLAS GROUP</span>
                                </div>
                                <div class="iagrl-card-body">
                                    <div class="iagrl-card-labels"><span class="iagrl-category"><?php echo esc_html($cat_label); ?></span><?php if ($sample) : ?><span class="iagrl-sample">SAMPLE RESOURCE</span><?php endif; ?></div>
                                    <h4><?php echo esc_html(get_the_title()); ?></h4>
                                    <p class="iagrl-description"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($desc), 27)); ?></p>
                                    <div class="iagrl-card-bottom">
                                        <span class="iagrl-meta"><?php echo $this->svg('file'); ?> <?php echo esc_html($type); ?> <span class="iagrl-meta-sep"></span> Updated <?php echo esc_html(get_the_modified_date('M j, Y')); ?></span>
                                        <?php if ($url) : ?>
                                            <a class="iagrl-download" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" download aria-label="Download <?php echo esc_attr(get_the_title()); ?>"><?php echo $this->svg('download'); ?> Download Resource <?php echo $this->svg('arrow'); ?></a>
                                        <?php else : ?>
                                            <span class="iagrl-download is-unavailable">File not available</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                            <?php endwhile; wp_reset_postdata(); ?>
                        <?php endif; ?>
                    </div>
                    <div class="iagrl-empty" data-iagrl-empty<?php if ($total) : ?> hidden<?php endif; ?>>
                        <div class="iagrl-empty-icon"><?php echo $this->svg('search'); ?></div>
                        <h4><?php echo $total ? 'No matching resources found' : 'Resources are coming soon'; ?></h4>
                        <p><?php echo $total ? 'Try a different search term or choose another category.' : 'New downloadable materials will appear here when they are published.'; ?></p>
                        <?php if ($total) : ?><button type="button" data-iagrl-reset>Clear filters</button><?php endif; ?>
                    </div>
                    <div class="iagrl-actions"><button type="button" class="iagrl-load-more" data-iagrl-more hidden>Load More Resources <?php echo $this->svg('arrow'); ?></button></div>
                    <div class="iagrl-footer-note"><?php echo $this->svg('folder'); ?> Resources are provided by Industry Atlas Group to support the manufacturing community.</div>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}

register_activation_hook(__FILE__, array('IAGRL_Resource_Library', 'activate'));
new IAGRL_Resource_Library();
