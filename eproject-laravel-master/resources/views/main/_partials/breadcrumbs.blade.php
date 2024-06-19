<div class="breadcrumb">
    <a href="{{ action('ViewmenuController@index') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Trang chủ</a>
    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
        @foreach($breadcrumbs as $index => $item)
            @if($index < count($breadcrumbs) - 1)
                <a href="{{ $item['url'] }}" class="breadcrumb-item">{{ $item['text'] }}</a>
            @else
                <span class="breadcrumb-item active">{{ $item['text'] }}</span>
            @endif
        @endforeach
    @endif
</div>
