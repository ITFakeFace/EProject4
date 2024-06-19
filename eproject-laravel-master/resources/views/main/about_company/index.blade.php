@extends('main._layouts.master')

@section('css')
    <style>
        .carousel-inner img {
            width: 100%;
            height: 700px;
        }

        .carousel-indicators {
            position: relative;
            margin-right: 2%;
            margin-left: 2%;
        }

        .carousel-indicators li{
            text-indent:0;
            width:220px;
            height: 40px;
            border:none;
            background-color: #046A38;
            font-size: 15px;
            text-align: center;
            border: 1px solid gray;
            color: white;
        }

        .carousel-indicators li p {
                margin-top: 10px;
            }

        @media only screen and (max-width: 1366px) {
            .carousel-indicators li{
                font-size: 13px;
                width:300px;
            }
  
        }

        .title-vct .fa {
            bottom: 0;
            color: #ff9813;
            font-size: 5px;
            left: 0;
            position: absolute;
            right: 0;
            text-align: center;
        }

        .des {
            font-size: 15px;
            text-align: justify
        }
        h1 { color: #7c795d; font-family: 'Trocchi', serif; font-size: 38px; font-weight: normal; line-height: 48px; margin: 0; }
    </style>
@endsection

@section('content')

    <div class="container">
        <div id="demo" class="carousel slide" data-ride="carousel">
        
            <!-- The slideshow -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('images/about_company/slide1.png') }}" alt="">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/about_company/slide2.png') }}" alt="">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/about_company/slide3.png') }}" alt="">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/about_company/slide4.png') }}" alt="">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/about_company/slide5.png') }}" alt="">
                </div>
            </div>
    
            <!-- Indicators -->
            <ul class="carousel-indicators mt-3">
                <li data-target="#demo" data-slide-to="0" class="active"><p>Phân bón Tân Thành Nam</p></li>
                <li data-target="#demo" data-slide-to="1"><p>Dây chuyền sản xuất tiên tiến</p></li>
                <li data-target="#demo" data-slide-to="2"><p>Vụ mùa bội thu</p></li>
                <li data-target="#demo" data-slide-to="3"><p>Kho bãi đạt tiêu chuẩn</p></li>
                <li data-target="#demo" data-slide-to="4"><p>Luôn luôn phát triển</p></li>
            </ul>
    
            <!-- Left and right controls -->
            <a class="carousel-control-prev" href="#demo" data-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#demo" data-slide="next">
                <span class="carousel-control-next-icon"></span>
            </a>
    
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1 class="text-center title-vct mb-2">
                    Về Công Ty Tân Thành Nam
                    <i class="fa"></i>
                </h1>
                <p class="des">Công ty TNHH Xuất Nhập Khẩu Tân Thành Nam Agriculture là doanh nghiệp chuyên nhập khẩu và sản xuất phân bón phức hợp NPK, Phân Hữu cơ, phân vi sinh vi lượng và các sản phẩm kích thích tăng trưởng cho các loại cây trồng.</p>
                <p class="des">Là một doanh nghiệp trẻ, ngay từ ngày đầu thành lập năm 2014 Công ty Xuất Nhập Khẩu Tân Thành Nam Agriculture đã chủ động áp dụng công nghệ sản xuất phân bón mới nhất, tiến tiến nhất của nước ngoài để tạo ra các sản phẩm phân bón đa dạng, cao cấp đạt chất lượng hàng đầu nhưng có giá thành cạnh tranh nhất trên thị trường.</p>
                <p class="des">Công ty đã đầu tư và đưa vào sản xuất dây chuyền sản xuất phân phức hợp NPK công suất lớn, công nghệ hơi nước kết hợp ure hóa lỏng hiện đại và tiên tiến hàng đầu Việt Nam.</p>
            </div>
            <div class="col-6 mt-5">
                <img src="{{ asset('images/about_company/image_about.jpg') }}" alt="">
            </div>
        </div>
    
        <div class="row mt-4" style="border: 1px solid #046A38">
            <div class="col-6 mt-3">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d489.85351186315756!2d106.63539878141007!3d10.824464073127688!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752964a38b6e07%3A0x1607946fdf79ba36!2zQ8O0bmcgVHkgVG5oaCBYdeG6pXQgTmjhuq1wIEto4bqpdSBUw6JuIFRow6BuaCBOYW0gQWdyaWN1bHR1cmU!5e0!3m2!1svi!2s!4v1555037288848!5m2!1svi!2s" width="100%" height="400" frameborder="0" style="border:0" allowfullscreen=""></iframe>
            </div>
            <div class="col-6 mt-2">
                <h1>Địa chỉ liên hệ:</h1>
                <h2>CÔNG TY TNHH XUẤT NHẬP KHẨU TÂN THÀNH NAM AGRICULTURE</h2>
                <p class="des"><i class="icon-location3" style="color: #046A38"></i> 82/1C Hoàng Bật Đạt, Phường 15, Quận Tân Bình, Thành Phố Hồ Chí Minh.</p>
                <p class="des"><i class="icon-mail5" style="color: #046A38"></i> Tanthanhnam.agriculture@gmail.com <br> Tanthanhnamhcm@gmail.com</p>
                
                <p class="des"><i class="icon-phone2" style="color: #046A38"></i> Điện thoại: 02633.797.676 <br> Fax: 02633.797.676</p>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script></script>
@endsection
