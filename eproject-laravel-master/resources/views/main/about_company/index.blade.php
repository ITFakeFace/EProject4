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
            <div class="col-lg-6 py-3">
                <div class="our-story">
                  <h1>Hành trình tạo lập</h1>
                  <p><strong>HUDECO được thành lập trên nền tảng đồng tâm nhất trí, cùng nhau tạo lập nên một tập thể đoàn kết và vững mạnh.</strong></p>
                  <p><strong>Đội ngũ lãnh đạo:</strong></p>
                  <ul>
                    <li><i class="bi bi-check-circle"></i> <span>Tất cả Thành viên Hội đồng quản trị, Ban Tổng Giám đốc và các Ban Nội nghiệp, Ban Dự án đều có nhiều năm gắn bó và cùng nhau tạo dựng nên Hudeco.</span></li>
                    <li><i class="bi bi-check-circle"></i> <span>Nhờ sự đồng lòng và chuyên môn cao, Hudeco cam kết mang đến những sản phẩm xây dựng chất lượng cao nhất, tối ưu nhất cho khách hàng và đối tác, góp phần vào sự phát triển của đất nước.</span></li>
                  </ul>
                  <p><strong>Trách nhiệm xã hội:</strong></p>
                  <ul>
                    <li><i class="bi bi-check-circle"></i> <span>Hudeco luôn ý thức được trách nhiệm xã hội của mình. Từ năm 2019, tập thể HUDECO đã đồng hành cùng các trường Đại học, Trung học trong công tác giảng dạy, hỗ trợ sinh viên nghèo và xây dựng nhà tình thương, nhà tình nghĩa tại các tỉnh thành Việt Nam.</span></li>
                    <li><i class="bi bi-check-circle"></i> <span>HUDECO tâm niệm rằng sự tận tâm vào trách nhiệm xã hội luôn song song với sự phát triển của công ty và sự phát triển chung của xã hội.</span></li>
                  </ul>
                  <p>Chúng tôi, HUDECO, tâm nguyện sự tận tâm vào trách nhiệm xã hội luôn song song
                    sự phát triển Công ty và phát triển xã hội.</p>
    
                  <div class="watch-video d-flex align-items-center position-relative">
                    <i class="bi bi-play-circle"></i>
                    <a href="https://www.youtube.com/watch?v=N5QmTxJ9RAI" class="glightbox stretched-link" target="blank">Watch Video</a>
                  </div>
                </div>
              </div>
            <div class="col-6 mt-5">
                <img src="{{ asset('images/about_company/image_about.jpg') }}" alt="" class="w-100 rounded-circle mb-5">
            </div>
        </div>
    
        <div class="row mt-4" style="border: 1px solid grey; background-color:white" >
            <div class="col-6 mt-2">
                <div class="col-lg-12 d-flex justify-content-center">
                    <img src="{{ asset('images/Logo-FutureHRM-index.svg') }}" alt="" width="250" height="auto" class="mr-2">
                    <img src="{{ asset('images/about_company/logo01.png') }}" alt="" width="200" height="auto">
                </div>
                <div class="col-lg-12 mt-3">
                    <h2>Liên hệ</h2>
                    <p class="des"><i class="icon-location3 mr-2" style="color: #cb370e"></i> 199 Phạm Huy Thông, Phường 6, Quận Gò Vấp Thành Phố Hồ Chí Minh.</p>
                    <p class="des"><i class="icon-mail5 mr-2" style="color: #cb370e"></i>info@hudeco.com.vn </p>
                    <p class="des"><i class="icon-phone2 mr-2" style="color: #cb370e"></i>028.2705.2705 </p>
                    <p class="des"><i class="icon-display mr-2" style="color: #cb370e"></i><a href="https://hudeco.comv.vn">https://hudeco.com.vn</a> </p>
                </div>
            </div>
            <div class="col-6 mt-3">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.6783006987907!2d106.68242287451793!3d10.835912358094777!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528565369a31d%3A0xab3769700b36fa39!2zMTk5IFBo4bqhbSBIdXkgVGjDtG5nLCBQaMaw4budbmcgNiwgR8OyIFbhuqVwLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmggNzAwMDAsIFZpZXRuYW0!5e0!3m2!1sen!2s!4v1713839321189!5m2!1sen!2s" width="100%" height="400" frameborder="0" style="border:0" allowfullscreen=""></iframe>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script></script>
@endsection
