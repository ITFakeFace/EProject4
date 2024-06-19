@extends('main._layouts.master')

<?php
    // {{ }} <--- cac ky tu dac biet se duoc thay the
    // {!! !!} <--- cac ky tu dac biet se khong thay the
    // {{-- --}} <--- comment code trong blade
    /**
     * section('scripts') <--- coi o? master.blade.php <--- no' la @yield('scripts')
     * section co' mo? la phai co' dong'
     * neu ma soan code php thi nen de? tren dau` de? no' load tuan tu chinh xac hon giong nhu code php nam tren section('scripts') vay ok roi
     * */
?>

@section('css')
   
@endsection

@section('js')    

@endsection

@section('content')
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Chấm công GPS</h1>
        <form method="POST" action="{{ action('CheckInOutController@create') }}" enctype="multipart/form-data">
            @csrf
            <div class="row p-3">
                @if (\Session::has('success'))
                    <div class="col-12">
                        <div class="alert alert-success">
                            {!! \Session::get('success') !!}
                        </div>
                    </div>
                @endif

                @if (\Session::has('error'))
                    <div class="col-12">
                        <div class="alert alert-danger">
                            {!! \Session::get('error') !!}
                        </div>
                    </div>
                @endif
    
                <div class="col-12 col-md-6">
                    
                    <div class="form-group">
                        <label class="font-weight-semibold" style="font-size: 0.9rem">Ngày chấm công:</label>
                        <div class="form-control-plaintext" style="font-size: 0.9rem">
                            <?php echo date('d/m/Y') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" style="font-size: 0.9rem">Tên nhân viên:</label>
                        <div class="form-control-plaintext" style="font-size: 0.9rem">
                            <?php echo auth()->user()->firstname . " " . auth()->user()->lastname ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" style="font-size: 0.9rem">Mã nhân viên:</label>
                        <div class="form-control-plaintext" style="font-size: 0.9rem">
                            <?php echo auth()->user()->code ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" style="font-size: 0.9rem">Phòng ban:</label>
                        <div class="form-control-plaintext" style="font-size: 0.9rem">
                            <?php echo $staff[0][2] ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" style="font-size: 0.9rem">Vị trí GPS nhân viên: </label>
                        <input id="latitude" name="latitude" value="" readonly/> 
                        <input id="longitude" name="longitude" value="" readonly/>
                        <input type="hidden" id="latitude1" name="latitude1" value="" readonly/> 
                        <input type="hidden" id="longitude1" name="longitude1" value="" readonly/>
                    </div>
        
                    <div class="warning">
                        <span id="fail-message" class="warning-msg"></span>
                    </div>
        
                </div>
        
                <div class="col-12 col-md-6">
                    {{-- <div class="title-open-gps">
                        <p><a target="_blank" href="https://drive.google.com/open?id=0Bw9Gp6m1QiZjYWRybTBjY1JILVk" style="color: #046A38">* Hướng dẫn bật GPS trên thiết bị Android</a></p>
                        <p><a target="_blank" href="https://drive.google.com/open?id=0Bw9Gp6m1QiZjZEowc2tsOGNkOVk" style="color: #046A38">* Hướng dẫn bật GPS trên thiết bị IOS</a></p>
                        <p><a target="_blank" href="https://drive.google.com/open?id=1yMXMksNrdXEsye3hlVvqBJsGyM1SX1UR" style="color: #046A38">* Hướng dẫn chấm công GPS</a></p>
                        <p><span style="color: red; font-size: 1rem;">* Ghi chú: Ưu tiên sử dụng trình duyệt Chrome đối với chức năng chấm công GPS</span></p>
                        <p><span style="color: red; font-size: 1rem;">* Ưu tiên sử dụng 3G/4G thay Wifi đối với chức năng chấm công GPS</span></p>
                    </div> --}}
                    <div class=video-screenshot><video autoplay id=video></video><div><div id=screenshotsContainer><canvas id=canvas class=is-hidden></canvas></div></div></div>
                    <input id="image_64" type="hidden" name="image_64" value="">
                </div>

                <div class="col-12 col-md-6">
                    <button type="submit" class="btn btn-primary mt-2 w-auto h-auto">Chấm công</button>
                </div>
                <div class="col-12 col-md-6 mt-2">
                    <button type="button" class="btn btn-success" id=btnScreenshot>Chụp hình</button>
                </div>
            </div>
        </form>
    </div>
   
    <style>
    #video {
        width: 350px;
    }
      
    .is-hidden {
        display: none;
    }
      
    .iconfont {
        font-size: 24px;
    }
      
    .btns {
        margin-bottom: 10px;
    }

    footer {
        margin: 20px 0;
        font-size: 16px;
    }
    </style>      
      
    <script>window.onload = async function () {
        if (
          !"mediaDevices" in navigator ||
          !"getUserMedia" in navigator.mediaDevices
        ) {
          document.write('Not support API camera')
          return;
        }
      
        const video = document.querySelector("#video");
        const canvas = document.querySelector("#canvas");
        const screenshotsContainer = document.querySelector("#screenshotsContainer");
        let videoStream = null
        let useFrontCamera = true; //camera trước
        const constraints = {
          video: {
            width: {
              min: 1280,
              ideal: 1920,
              max: 2560,
            },
            height: {
              min: 720,
              ideal: 1080,
              max: 1440,
            }
          },
        };
      
        // play
        // btnPlay.addEventListener("click", function () {
        //   video.play();
        //   btnPlay.classList.add("is-hidden");
        //   btnPause.classList.remove("is-hidden");
        // });
      
        // // pause
        // btnPause.addEventListener("click", function () {
        //   video.pause();
        //   btnPause.classList.add("is-hidden");
        //   btnPlay.classList.remove("is-hidden");
        // });
      
      
        // btnChangeCamera.addEventListener("click", function () {
        //   useFrontCamera = !useFrontCamera;
        //   init();
        // });
      
        function stopVideoStream() {
          if (videoStream) {
            videoStream.getTracks().forEach((track) => {
              track.stop();
            });
          }
        }
      
        btnScreenshot.addEventListener("click", function () {
          let img = document.getElementById('screenshot');
          if (!img) {
            img = document.createElement("img");
            img.id = 'screenshot';
            img.style.width = '350px';
          }
          canvas.width = video.videoWidth;
          canvas.height = video.videoHeight;
          canvas.getContext("2d").drawImage(video, 0, 0);
          img.src = canvas.toDataURL("image/png");
          screenshotsContainer.prepend(img);

          document.getElementById("image_64").value = img.src;

          console.log(img.src);
        });
      
        async function init() {
          stopVideoStream();
          constraints.video.facingMode = useFrontCamera ? "user" : "environment";
          try {
            videoStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = videoStream;
          } catch (error) {
            console.log(error)
          }
        }
        init();
      }</script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-111717926-1"></script>
    <script>function gtag(){dataLayer.push(arguments)}window.dataLayer=window.dataLayer||[],gtag("js",new Date),gtag("config","UA-111717926-1")</script><div><script async src=//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js></script><ins class=adsbygoogle style="display:block; text-align:center;" data-ad-layout=in-article data-ad-format=fluid data-ad-client=ca-pub-1121308659421064 data-ad-slot=8232164616></ins><script>(adsbygoogle=window.adsbygoogle||[]).push({})</script><div></div></div></body></html>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            getLocation();
            setInterval(function(){getLocation();},100000);
        });

        function getLocation() {
            x =  document.getElementById("fail-message");
            if (navigator.geolocation) {
          
                navigator.geolocation.getCurrentPosition(function(position,showError) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                    window.latitude = position.coords.latitude;
                    window.longitude = position.coords.longitude;
                
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    var GEOCODING = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' + position.coords.latitude + '%2C' + position.coords.longitude + '&language=en';
                    console.log(GEOCODING);
                    $.getJSON(GEOCODING).done(function(location) {
 
                    })
                });
            } else { 
                x.innerHTML = "Geolocation is not supported by this browser.";
            }
        }

        function showPosition(position) {            
            window.latitude = position.coords.latitude;
            window.longitude = position.coords.longitude;
            
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('latitude1').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            document.getElementById('longitude1').value = position.coords.longitude;
        }

        function showError(error) {
            x =  document.getElementById("fail-message");
            switch (error.code) {
                case error.PERMISSION_DENIED:
                x.innerHTML = "User denied the request for Geolocation."
                    break;
                case error.POSITION_UNAVAILABLE:
                x.innerHTML = "Location information is unavailable."
                    break;
                case error.TIMEOUT:
                    x.innerHTML = "The request to get user location timed out."
                    break;
                case error.UNKNOWN_ERROR:
                    x.innerHTML = "An unknown error occurred."
                    break;
            }
        }
    </script>
@endsection