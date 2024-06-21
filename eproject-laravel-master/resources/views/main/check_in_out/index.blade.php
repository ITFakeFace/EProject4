@extends('main._layouts.master')

<?php
/**
 * section('scripts') <--- this corresponds to @yield('scripts') in master.blade.php
 * section must have both an opening and closing tag
 * for better performance, PHP code should be placed at the top, similar to section('scripts') in this template
 * */
?>

@section('css')
@endsection

@section('js')
@endsection

@section('content')
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">GPS Check-in</h1>
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
                        <label class="font-weight-semibold" style="font-size: 0.9rem">Check-in Date:</label>
                        <div class="form-control-plaintext" style="font-size: 0.9rem">
                            <?php echo date('d/m/Y'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" style="font-size: 0.9rem">Employee Name:</label>
                        <div class="form-control-plaintext" style="font-size: 0.9rem">
                            <?php echo auth()->user()->firstname . ' ' . auth()->user()->lastname; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" style="font-size: 0.9rem">Employee Code:</label>
                        <div class="form-control-plaintext" style="font-size: 0.9rem">
                            <?php echo auth()->user()->code; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" style="font-size: 0.9rem">Department:</label>
                        <div class="form-control-plaintext" style="font-size: 0.9rem">
                            <?php echo $staff[0][2]; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" style="font-size: 0.9rem">Employee GPS Location: </label>
                        <input id="latitude" name="latitude" value="" readonly />
                        <input id="longitude" name="longitude" value="" readonly />
                        <input type="hidden" id="latitude1" name="latitude1" value="" readonly />
                        <input type="hidden" id="longitude1" name="longitude1" value="" readonly />
                    </div>

                    <div class="warning">
                        <span id="fail-message" class="warning-msg"></span>
                    </div>

                </div>

                <div class="col-12 col-md-6">
                    <div class=video-screenshot><video autoplay id=video></video>
                        <div>
                            <div id=screenshotsContainer><canvas id=canvas class=is-hidden></canvas></div>
                        </div>
                    </div>
                    <input id="image_64" type="hidden" name="image_64" value="">
                </div>

                <div class="col-12 col-md-6">
                    <button type="submit" class="btn btn-primary mt-2 w-auto h-auto">Check-in</button>
                </div>
                <div class="col-12 col-md-6 mt-2">
                    <button type="button" class="btn btn-success" id=btnScreenshot>Take Photo</button>
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

    <script>
        window.onload = async function() {
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
            let useFrontCamera = true; //front camera
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

            function stopVideoStream() {
                if (videoStream) {
                    videoStream.getTracks().forEach((track) => {
                        track.stop();
                    });
                }
            }

            btnScreenshot.addEventListener("click", function() {
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
        }
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-111717926-1"></script>
    <script>
        function gtag() {
            dataLayer.push(arguments)
        }
        window.dataLayer = window.dataLayer || [], gtag("js", new Date), gtag("config", "UA-111717926-1")
    </script>
    <div>
        <script async src=//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js></script><ins class=adsbygoogle style="display:block; text-align:center;" data-ad-layout=in-article data-ad-format=fluid data-ad-client=ca-pub-1121308659421064 data-ad-slot=8232164616></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({})
        </script>
        <div></div>
    </div>
    </body>

    </html>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            getLocation();
            setInterval(function() {
                getLocation();
            }, 100000);
        });

        function getLocation() {
            x = document.getElementById("fail-message");
            if (navigator.geolocation) {

                navigator.geolocation.getCurrentPosition(function(position, showError) {
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
            x = document.getElementById("fail-message");
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
