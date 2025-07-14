<?php

/**
 * Plugin Name: EchBay Shopee Link Simple
 * Plugin URI: https://echbay.com
 * Description: Plugin đơn giản để nhúng tiêu đề sản phẩm vào trang chi tiết sản phẩm WooCommerce
 * Version: 1.0.1
 * Author: EchBay
 * Author URI: https://echbay.com
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add Shopee quick buy button to single product page
add_action('woocommerce_after_add_to_cart_button', 'echbay_shopee_link_button', 10);
function echbay_shopee_link_button()
{
    // kiểm tra function get_field() có tồn tại không
    if (!function_exists('get_field')) {
        return;
    }

    // Lấy đường dẫn Shopee từ custom field
    $shopee_url = get_field('shopee_link', get_the_ID());
    if (!empty($shopee_url)) {
        echo '<a href="' . $shopee_url . '" target="_blank" class="button echbay-shopee_link" rel="nofollow">Mua ngay tại Shopee</a>';
    }
}

// GitHub Auto-Update Feature
class EchBayShopeeLink_GitHub_Updater
{
    private $plugin_slug;
    private $plugin_file;
    private $github_user;
    private $github_repo;
    private $version;

    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = plugin_basename($plugin_file);
        $this->github_user = 'itvn9online'; // Thay bằng GitHub username của bạn
        $this->github_repo = 'echbay-shopee-link'; // Thay bằng repository name
        $this->version = $this->get_current_version(); // Lấy version từ file VERSION

        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 10, 3);
    }

    public function check_for_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $remote_version = $this->get_remote_version();

        if (version_compare($this->version, $remote_version, '<')) {
            $transient->response[$this->plugin_slug] = (object) array(
                'slug' => $this->plugin_slug,
                'new_version' => $remote_version,
                'url' => "https://github.com/{$this->github_user}/{$this->github_repo}",
                'package' => "https://github.com/{$this->github_user}/{$this->github_repo}/archive/refs/heads/main.zip"
            );
        }

        return $transient;
    }

    public function plugin_info($false, $action, $args)
    {
        if ($action !== 'plugin_information' || $args->slug !== $this->plugin_slug) {
            return $false;
        }

        $remote_version = $this->get_remote_version();
        $changelog = $this->get_changelog();

        return (object) array(
            'name' => 'EchBay Shopee Link Simple',
            'slug' => $this->plugin_slug,
            'version' => $remote_version,
            'author' => 'EchBay',
            'homepage' => "https://github.com/{$this->github_user}/{$this->github_repo}",
            'short_description' => 'Plugin đơn giản để thêm nút Shopee vào trang sản phẩm WooCommerce',
            'sections' => array(
                'description' => 'Plugin này tích hợp nút "Mua ngay tại Shopee" vào trang chi tiết sản phẩm WooCommerce. Yêu cầu WooCommerce và Advanced Custom Fields.',
                'installation' => 'Upload plugin vào thư mục /wp-content/plugins/ và kích hoạt trong WordPress Admin.',
                'changelog' => $changelog
            ),
            'download_link' => "https://github.com/{$this->github_user}/{$this->github_repo}/archive/refs/heads/main.zip",
            'requires' => '5.0',
            'tested' => '6.4',
            'requires_php' => '7.4'
        );
    }

    private function get_current_version()
    {
        $version_file = __DIR__ . '/VERSION';
        if (file_exists($version_file)) {
            return trim(file_get_contents($version_file));
        }
        return '1.0.0'; // Fallback version
    }

    private function get_remote_version()
    {
        // Kiểm tra cache trước
        $cache_key = 'echbay_shopee_link_remote_version';
        $cached_version = get_transient($cache_key);

        if ($cached_version !== false) {
            return $cached_version;
        }

        $api_url = "https://raw.githubusercontent.com/{$this->github_user}/{$this->github_repo}/main/VERSION";
        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            // Cache phiên bản hiện tại nếu không kết nối được
            set_transient($cache_key, $this->version, 30 * MINUTE_IN_SECONDS);
            return $this->version;
        }

        $body = wp_remote_retrieve_body($response);
        $remote_version = trim($body);

        if (!empty($remote_version) && preg_match('/^\d+\.\d+\.\d+$/', $remote_version)) {
            // Cache phiên bản remote trong 1 giờ
            set_transient($cache_key, $remote_version, HOUR_IN_SECONDS);
            return $remote_version;
        }

        // Cache phiên bản hiện tại nếu không parse được
        set_transient($cache_key, $this->version, 30 * MINUTE_IN_SECONDS);
        return $this->version;
    }

    private function get_changelog()
    {
        // Kiểm tra cache trước
        $cache_key = 'echbay_shopee_link_changelog';
        $cached_changelog = get_transient($cache_key);

        if ($cached_changelog !== false) {
            return $cached_changelog;
        }

        // Lấy changelog từ README.md trên GitHub
        $api_url = "https://raw.githubusercontent.com/{$this->github_user}/{$this->github_repo}/main/README.md";
        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            $fallback = 'Không thể tải changelog từ GitHub.';
            // Cache lỗi trong 15 phút
            set_transient($cache_key, $fallback, 15 * MINUTE_IN_SECONDS);
            return $fallback;
        }

        $body = wp_remote_retrieve_body($response);

        if (empty($body)) {
            $fallback = 'Chưa có changelog nào trên GitHub.';
            // Cache lỗi trong 15 phút
            set_transient($cache_key, $fallback, 15 * MINUTE_IN_SECONDS);
            return $fallback;
        }

        // Tìm phần changelog trong README
        $changelog_section = '';
        if (preg_match('/## Changelog\s*(.*?)(?=##|$)/s', $body, $matches)) {
            $changelog_section = $matches[1];
        } else {
            $changelog_section = 'Xem chi tiết tại: https://github.com/' . $this->github_user . '/' . $this->github_repo;
        }

        $changelog = '<h3>Changelog</h3>' . wpautop($changelog_section);

        // Cache changelog trong 2 giờ
        set_transient($cache_key, $changelog, 2 * HOUR_IN_SECONDS);

        return $changelog;
    }

    /**
     * Xóa cache để force check version mới
     */
    public function clear_cache()
    {
        delete_transient('echbay_shopee_link_remote_version');
        delete_transient('echbay_shopee_link_changelog');
    }
}

// Kiểm tra nếu đang ở trang plugins.php
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/plugins.php') !== false) {
    new EchBayShopeeLink_GitHub_Updater(__FILE__);
}
