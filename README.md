# EchBay Shopee Link Simple

Plugin WordPress đơn giản để thêm nút "Mua ngay tại Shopee" vào trang chi tiết sản phẩm WooCommerce.

## Mô tả

Plugin này sẽ hiển thị nút link Shopee trên trang chi tiết sản phẩm WooCommerce nếu sản phẩm có đường dẫn Shopee được cấu hình trong custom field.

## Yêu cầu hệ thống

- ✅ **WordPress** 5.0+
- ✅ **WooCommerce** 5.0+ (Bắt buộc)
- ✅ **Advanced Custom Fields (ACF)** (Bắt buộc)
- ✅ **PHP** 7.4+

## Cài đặt

### Bước 1: Cài đặt plugin yêu cầu

1. Cài đặt và kích hoạt **WooCommerce**
2. Cài đặt và kích hoạt **Advanced Custom Fields**

### Bước 2: Cài đặt plugin EchBay Shopee Link

1. Upload thư mục `echbay-shopee-link` vào `/wp-content/plugins/`
2. Kích hoạt plugin trong WordPress Admin

### Bước 3: Tạo Custom Field

1. Vào **Custom Fields > Field Groups** trong admin
2. Tạo field group mới với tên "Shopee Link"
3. Thêm field:
   - **Field Label**: Shopee Link
   - **Field Name**: `shopee_link`
   - **Field Type**: URL hoặc Text
4. Đặt **Location Rules**: Show this field group if **Post Type** is equal to **Product**
5. Publish field group

## Cách sử dụng

1. **Chỉnh sửa sản phẩm** trong WooCommerce
2. **Scroll xuống** tìm section "Shopee Link"
3. **Nhập URL** Shopee của sản phẩm (ví dụ: `https://shopee.vn/...`)
4. **Cập nhật** sản phẩm
5. **Xem trang sản phẩm** - nút "Mua ngay tại Shopee" sẽ xuất hiện

## Hiển thị

- Nút sẽ hiển thị **sau nút Add to Cart** trên trang chi tiết sản phẩm
- Chỉ hiển thị **khi có URL Shopee** được nhập
- Link sẽ **mở tab mới** khi click
- Có thuộc tính `rel="nofollow"` cho SEO

## Tùy chỉnh

Để thay đổi text hoặc vị trí hiển thị, chỉnh sửa file `echbay-shopee-link.php`:

```php
// Thay đổi text nút
echo '<a href="' . $shopee_url . '" target="_blank" class="button echbay-shopee_link" rel="nofollow">Text mới</a>';

// Thay đổi vị trí (priority)
add_action('woocommerce_single_product_summary', 'echbay_shopee_link_button', 33);
```

## CSS Class

Nút có class `echbay-shopee_link` để custom CSS:

```css
.echbay-shopee_link {
	background: #ee4d2d !important;
	color: white !important;
	border: none !important;
	padding: 10px 20px !important;
	border-radius: 5px !important;
}
```

## Cấu trúc Plugin

```
echbay-shopee-link/
├── echbay-shopee-link.php (File chính - 30 dòng code)
└── README.md (File hướng dẫn này)
```

## Lưu ý

- Plugin **không hoạt động** nếu thiếu WooCommerce hoặc ACF
- Chỉ hiển thị nút khi **custom field có giá trị**
- URL Shopee nên là **đường dẫn đầy đủ** (bắt đầu bằng https://)

## Hỗ trợ

Nếu có vấn đề, kiểm tra:

1. WooCommerce đã được kích hoạt chưa?
2. Advanced Custom Fields đã được kích hoạt chưa?
3. Custom field `shopee_link` đã được tạo chưa?
4. Sản phẩm đã có URL Shopee chưa?

## Changelog

### Version 1.0.1 (2025-07-14)

- Cập nhật phiên bản để test tính năng auto-update
- Tối ưu hóa code GitHub updater
- Cải thiện cache mechanism

### Version 1.0.0 (2025-07-14)

- Phiên bản đầu tiên
- Thêm nút "Mua ngay tại Shopee" vào trang sản phẩm
- Tích hợp với Advanced Custom Fields
- Tính năng auto-update từ GitHub
- Sử dụng file VERSION để kiểm tra phiên bản mới
