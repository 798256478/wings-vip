@inject('theme', 'App\Services\ThemeService')

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="renderer" content="webkit">
    <meta name="format-detection" content="telephone=no">
    <title>@yield('title','会员中心')</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link href="/components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/components/sweetalert/dist/sweetalert.css">
    <link href="/common/css/wechat-color.css" rel="stylesheet">
    {!! $theme->loadWechatCss('main') !!}
    @stack('links')
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/components/jquery/dist/jquery.min.js"></script>
</head>
<body>

    @yield('content')

    <div class="loading" style="display: none">
        <div class="sk-cube-grid">
            <div class="sk-cube sk-cube1"></div>
            <div class="sk-cube sk-cube2"></div>
            <div class="sk-cube sk-cube3"></div>
            <div class="sk-cube sk-cube4"></div>
            <div class="sk-cube sk-cube5"></div>
            <div class="sk-cube sk-cube6"></div>
            <div class="sk-cube sk-cube7"></div>
            <div class="sk-cube sk-cube8"></div>
            <div class="sk-cube sk-cube9"></div>
        </div>
    </div>

    @section('scripts')
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="/components/sweetalert/dist/sweetalert.min.js"></script>

    @if (isset($wx_js))
    <script src="//res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
        var wxconfig = <?php echo $wx_js->config(['chooseWXPay', 'addCard', 'hideMenuItems'], false) ?>;
        wx.config(wxconfig);

        wx.error(function(res){
            alert('微信接口异常，部分功能可能无法使用。');
        });
    </script>
    @endif

    @if (isset($notices))
    <script>
        //自动显示通知
        var notices = <?php echo $notices; ?>;
        function showNotices(isConfirm){
            if ($.isArray(notices) && notices.length != 0) {
                var notice = notices[0];
                var args = null;

                if (notices.length > 1) {
                    args = {
                        showCancelButton: true,
                        cancelButtonText: '不想看了',
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "下一条",
                        closeOnConfirm: false,
                    }
                }
                else {
                    args = {
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "知道了",
                    }
                }

                args.title = notice.content;

                if (notice.image) {
                    args.imageUrl = notice.image;
                }
                else {
                    args.imageUrl = '/common/imgs/notice.png';
                }

                swal(args, function(isConfirm) {
                    if (isConfirm){
                        receive(false);
                        showNotices(isConfirm);
                    }
                    else {
                        receive(true);
                    }
                });
            }
        }

        function receive(receiveAll) {
            if (receiveAll) {
                $.post('/wechat/api/receive-notice' , {});
                notices = [];
            }
            else {
                $.post('/wechat/api/receive-notice' , {
                    noticeId: notices[0]._id
                });
                notices.splice(0, 1);
            }
        }

        $(function(){
            showNotices(true);
        });
    </script>
    @endif

    <script type="text/javascript">
        $(function(){
            $('[data-toggle="popover"]').popover();

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var $a = $(e.target);
                var $a_span = $a.children('a>span');
                var a_herf = $a.attr('href');

                var $t = $('.content-title');
                var $t_span = $t.children('.content-title>span')
                var t_herf = $t.attr('href');

                $a.attr('href', t_herf);
                $t.attr('href', a_herf);

                $a_span.appendTo($t);
                $t_span.appendTo($a);
            })

        })
    </script>
    @show
</body>
</html>
