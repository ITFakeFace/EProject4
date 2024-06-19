@extends('main._layouts.master')

@section('content')

<style>
    .content {
        padding: 0px 10px
    }
    @media only screen and (min-width: 768px) {
        p{
            height:40px;
        }

        .back-link{
            color:#046A38 ;
            font-weight: bold;
            font-size: 16px;
            padding-left: 50px;
            padding-right: 50px;
        }
        .my-link{
            text-align: center;
            width: 100%;
        }

        .outer a{
            padding: 20px;
        }
    }

    .content_layout {
        padding: 0px 10px;
        margin-bottom: -65px;
    }

    .fluid-view-menu {
        padding: 20px 20px;
    }

    .outer {
        position: relative;
        margin: 0 auto;
        text-align: center;
        border-radius: 10px;
        color: #008B56;
        box-shadow: 0 0 11px rgba(33,33,33,.2);
        transition: box-shadow .3s;
        background: #FFF;
    }

    .outer a{
        margin: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-right: -50%;
        transform: translate(-50%, -50%);
        max-height: 105%;
    }
    
    .outer p{
        padding-top: 10px;
        color: #008B56;
        cursor: pointer;
        font-weight: bold;
        margin-bottom: 0px;
        font-size: 1.2vw;
    }

    .outer:hover {      
        opacity: 0.9;   
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .no-tool{
        width: 100%;
        text-align: center;
        margin-top: 30px;
        margin-bottom: 30px;
        color: #008B56;
    }

    .icon-8x {
        color: #046A38;
        font-size: 9vw !important;
    }

    .col-max {
        padding: 20px;
    }

    @media only screen and (max-width: 768px) {
        #kt_header_mobile {
            padding: 0px 20px;
        }

        .icon-8x {
            font-size: 10vw !important;
        }

        .outer{
            padding: 10px;
        }
        .back-link{
            color:#046A38;
            font-weight: bold;
            font-size: 16px;
            padding-left: 30px;
            padding-right: 30px;
        }
        .my-link{
            width: 100%;
            text-align: center;
        }
        .no-tool{
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .row {
            margin: 0;
        }

        .col-max {
            padding: 20px;
        }

        img {
            width: 60px;
            height: 60px;
        }

        .outer p {
            font-size: 1.6vw;
        }

        .outer a {
            padding: 10px;
            max-height: 110%;
        }
    }

    @media only screen and (max-width: 480px) {
        .outer p {
            font-size: 2.5vw;
        }
    }

    @media only screen
    and (min-width: 1024px)
    and (max-width: 1365px) {

        .col-max{
            padding: 10px;
        }

        .fluid-view-menu {
            padding: 10px 0px;
        }

        .icon-8x {
            font-size: 7.5vw !important;
        }

        .outer a {
            top: 48%;
        }

    }

    @media only screen
    and (min-width: 768px)
    and (max-width: 1024px) {
        
        .fluid-view-menu {
            padding: 10px 0px;
        }

        .main{
            margin-top:10px ;
        }
        .back-link{
            color:#046A38 ;
            font-size: 16px;
        }

        .col-max, .outer {
            padding: 10px;
        }
    }

    @media only screen
    and (min-width: 768px)
    and (max-width: 991px) {
        .outer p {
            font-size: 1.6vw;
        }
    }

    @media only screen
    and (min-width: 1365px)
    and (max-width: 1919px) {

        .outer a {
            top: 46%;
        }
    }

    @media only screen and (max-width: 767px) {
        .fluid-view-menu {
            padding: 10px 0px;
        }
        .col-max {
            padding: 10px;
        }
        .icon-8x {
            font-size: 18vw !important;
        }
        .outer p {
            font-size: 3.5vw;
        }
    }

    @media only screen and (max-width: 480px) {
        .outer {
            padding: 0;
        }
        .icon-8x {
            font-size: 24vw !important;
        }
        .outer a {
            max-height: 100%;
            width: 100%;
            height: 100%;
        }
        .outer p {
            font-size: 3.2vw;
        }
    }

    @media only screen and (max-width: 320px) {
        .icon-8x {
            font-size: 20vw !important;
        }
    }

    @media only screen and (min-width: 1920px) {
        .icon-8x {
            font-size: 6vw !important;
        }

        .outer a {
            max-height: 110%;
        }

        .outer p {
            height: 70px;
            font-size: 0.9vw;
        }

        .col-max {
            position: relative;
            width: 100%;
            -webkit-box-flex: 0;
            -ms-flex: 0 0 16.66667%;
            flex: 0 0 16.66667%;
            max-width: 16.66667%;
        }
    }

    @media only screen and (min-width: 2560px){
        .icon-8x {
            font-size: 7vw !important;
        }
        .outer p {
            font-size: 1vw;
        }
    }   

    #loading-image {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 1000;
        background: center no-repeat #fff;
    }
</style>

<div class="container-fluid fluid-view-menu">
    <div class="row text-center">
        @if(auth()->user()->department == 2 || auth()->user()->department == 5)
            <div class="col-6 col-md-3 text-center col-max">
                <div class="outer">
                    <a href="{{ action('DepartmentController@index') }}">
                        <i class="icon-8x icon-list"></i>
                        <p>Danh Sách Phòng Ban</p> 
                    </a>
                </div>
            </div>
            <div class="col-6 col-md-3 text-center col-max">
                <div class="outer">
                    <a href="{{ action('DepartmentController@listUndo') }}">
                        <i class="icon-8x icon-trash"></i>
                        <p>Thùng Rác</p> 
                    </a>
                </div>
            </div>
        @endif
        <div class="no-tool text-center">
            <?php


            ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $( window ).resize(function() {
            var cw = $('.outer').width();
            $('.outer').css({'height':cw+'px'});
        });
        var cw = $('.outer').width();
        $('.outer').css({'height':cw+'px'});
    });
</script>

@endsection

@section('scripts')
    <script></script>
@endsection
