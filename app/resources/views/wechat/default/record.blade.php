@extends('wechat.default.master')

@section('title', '交易记录')

@push('links')
<style type="text/css">

    .header-property{
        background-color: {{ $theme->colors['THEME'] or '#0092DB' }};
    }

    .header-buttons .glyphicon{
        color: {{ $theme->colors['THEME'] or '#0092DB' }};
    }

</style>
@endpush

@section('content')
    <div class="container">
        <ol class="breadcrumb row">
            <li><a href="/wechat">会员卡</a> </li>
            <li class="active">交易记录</li>
        </ol>

        <div class="header-property">
            <label>总消费</label>
            <br/>
            <span> {{$card_info->total_expense}}</span>
        </div>

        <div class="content-title">
            <div class="line"></div>
            <span>&nbsp&nbsp交易记录&nbsp&nbsp</span>
        </div>

        @if (!empty($records) && count($records) > 0)
        <div class="record-group">
            @foreach($records as $record)
            <div class="record">
                <span class="record-time">{{date('Y-m-d H:i:s',$record['create_time'])}}:</span>
                <p>
                    <span class="record-action">{{$record['action']}}&nbsp</span>
                    <span class="record-summary">{{rtrim($record['summary'],'、')}}</span>
                </p>
            </div>
            @endforeach
        </div>
        @else
        <div class="content-none">
            <div class="icon">
                <span class="glyphicon glyphicon-list-alt" ></span>
            </div>

            您尚未进行任何消费~
        </div>
        @endif
    </div>
@endsection

@section('scripts')
    @parent

@endsection