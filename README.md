# Donat NS

Aplikasi website penjualan Donat NS berbasis PHP + MySQL + Bootstrap 5 untuk XAMPP.

## Fitur utama
- Customer dashboard tanpa login
- 3 produk Donat NS dari database
- Keranjang belanja dan checkout
- Login dan registrasi customer
- Riwayat pesanan dan detail pesanan
- Admin login dan dashboard
- Manajemen produk 3 item saja
- Manajemen pesanan dan pelanggan
- Laporan penjualan
- Pengaturan minimal pembelian dan ongkir

## Struktur
- `config/`
- `includes/`
- `customer/`
- `admin/`
- `assets/`
- `uploads/products/`

## Instalasi
1. Jalankan XAMPP Apache dan MySQL.
2. Buat database `donat_ns`.
3. Import file `database.sql` melalui phpMyAdmin.
4. Copy project ke `C:/xampp/htdocs/donat_ns`.
5. Buka browser:
   - Customer: `http://localhost/donat_ns/customer/dashboard.php`
   - Admin: `http://localhost/donat_ns/admin/login.php`

## Login default admin
- Email: `admin@donatns.com`
- Password: `admin123`

## Catatan
- Semua data disimpan di MySQL.
- Semua query menggunakan PDO.
- Password disimpan dengan `password_hash()`.
