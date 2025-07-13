<?php

/**
 * Plugin Name: EchBay Shopee Link Simple
 * Plugin URI: https://echbay.com
 * Description: Plugin đơn giản để nhúng tiêu đề sản phẩm vào trang chi tiết sản phẩm WooCommerce
 * Version: 1.0.0
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
add_action('woocommerce_single_product_summary', 'echbay_shopee_link_button', 33);
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
        $this->version = '1.0.0'; // Version hiện tại

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

    private function get_remote_version()
    {
        $api_url = "https://api.github.com/repos/{$this->github_user}/{$this->github_repo}/releases/latest";
        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            return $this->version;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['tag_name'])) {
            return ltrim($data['tag_name'], 'v');
        }

        return $this->version;
    }

    private function get_changelog()
    {
        $api_url = "https://api.github.com/repos/{$this->github_user}/{$this->github_repo}/releases";
        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            return 'Không thể tải changelog từ GitHub.';
        }

        $body = wp_remote_retrieve_body($response);
        $releases = json_decode($body, true);

        if (empty($releases)) {
            return 'Chưa có release nào trên GitHub.';
        }

        $changelog = '<h3>Changelog</h3>';
        foreach (array_slice($releases, 0, 5) as $release) {
            $version = ltrim($release['tag_name'], 'v');
            $date = date('Y-m-d', strtotime($release['published_at']));
            $notes = !empty($release['body']) ? $release['body'] : 'Không có ghi chú cho phiên bản này.';

            $changelog .= "<h4>Version {$version} ({$date})</h4>";
            $changelog .= '<p>' . nl2br(esc_html($notes)) . '</p>';
        }

        return $changelog;
    }
}

// Khởi tạo GitHub updater
new EchBayShopeeLink_GitHub_Updater(__FILE__);
