# Laravel Project

## Giới thiệu

Dự án website bán hàng ecommer :
Công nghệ sử dụng : Laravel 12 + Blade Teamplace , Bootrap 5,

Yêu cầu phiên bản php > 8 (ưu tiên 8.2)


## Mục lục

- [Giới thiệu](#giới-thiệu)
- [Cài đặt](#cài-đặt)
- [Chạy dự án](#chạy-dự-án)
- [Lưu ý](#lưu-ý)

## Cài đặt

### 1. Clone dự án từ Git

Clone dự án về máy và chuyển vào thư mục của dự án:

```bash
git clone https://github.com/codegaeme/laravel_e-commerce.git
```

### 2. Cài đặt Composer Dependencies và Authenication

```bash
composer i

npm i
```

### 3. Tạo file .env

```bash
cp .env.example .env
```

### 4. Tạo Application Key

```bash
php artisan key:generate
```

## Chạy dự án

### 1. Cấu hình env database

- Cấu hình DB_DATABASE
- Cấu hình DB_USERNAME
- Cấu hình DB_PASSWORD


### 2. Tạo dữ liệu database

```bash
php artisan migrate
```

### 3. Khởi chạy Server (ở trang terminal khác, không được đóng)

```bash
php artisan ser
```

### 4. Khởi chạy Queue (ở trang terminal khác, không được đóng)

```bash
php artisan queue:work --queue=high,default
```

### 5. Khởi chạy vite (ở trang terminal khác, không được đóng)

```bash
npm run dev
```

## Lưu ý

### 1. Bắt buộc chạy các lệnh

```bash
php artisan ser
```

```bash
npm run dev
```
