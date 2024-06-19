## Cài đặt CURL để dùng POST | GET
- Tải cacert.pem về: https://curl.haxx.se/docs/caextract.html
- Vào file php.ini tìm dòng **curl.cainfo** thay thế bằng curl.cainfo = path_file_pem
- Reset lại **xampp**

## Khởi động server
- Chạy lệnh sau: **php artisan serve**

## Cách dùng guzzle để gửi POST | GET
- Tài liệu: https://laravel.com/docs/8.x/http-client#introduction

## Demo
- Controller tại : app/Http/Controllers/DemoController.php
- Router: routes/web.php
- View: resources/views/demo.blade.php

## Lệnh quan trọng trong laravel
- php artisan make:controller {Path_Nếu_Có/}Tên_Controller


## Lưu ý:
- Copy .env.example và đổi tên thành **.env**
- Tạo key cho app bằng lệnh: **php artisan key:generate**
- Vì là webservice mọi thứ nên thực hiện bằng **JWT Token**

## Database MySQL:
- Sửa lại thông tin sau:
Lưu ý: Key phải giống nhau, còn lại thông tin database thì điền theo nội dung đã cấu hình ở máy mỗi người
***
    APP_KEY=base64:UJ/EGDGpY6BzxlOg62xRznZkoBgrvFOBVEJs7clHhuU=
    DB_DATABASE=hr
    DB_USERNAME=root
    DB_PASSWORD=root
***
