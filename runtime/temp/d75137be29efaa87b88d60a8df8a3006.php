<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:69:"D:\partybuildeb\public/../application/admin\view\database\export.html";i:1492583058;s:65:"D:\partybuildeb\public/../application/admin\view\base\common.html";i:1492583058;}*/ ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<title><?php echo \think\Config::get('WEB_SITE_TITLE'); ?></title>-->
    <title>香市党建管理后台</title>
    <!-- CSS Files -->
    <link href="/static/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <style type="text/css">
        .profile-element {
            text-align: center;
        }
    </style>
    
<link href="/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
<!-- Sweet Alert -->
<link href="/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">

    <!-- iCheck -->
    <link href="/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <!-- Sweet Alert -->
    <link href="/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <!-- Toastr style -->
    <link href="/admin/css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <!-- CSS Files -->
    <link href="/admin/css/animate.css" rel="stylesheet">
    <link href="/admin/css/style.css" rel="stylesheet">
    <link href="/admin/css/main.css" rel="stylesheet">
    <script src="/admin/js/jquery-2.1.1.js"></script>
</head>
<body>
<div id="wrapper">
    <!-- 左侧系统菜单栏 -->
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span><?php if($user['header'] == ''): ?><img alt="image" class="img-circle" src="/admin/images/profile_small.jpg" /><?php else: ?><img alt="image" style="width: 48px;height: 48px;" class="img-circle" src="<?php echo $user['header']; ?>" /><?php endif; ?></span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $user['username']; ?></strong></span>
                            <span class="text-muted text-xs block"><b class="caret"></b></span> </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="<?php echo Url('Index/editPassWord'); ?>">修改密码</a></li>
                            <!--<li><a href="contacts.html">联系方式</a></li>-->
                            <!--<li><a href="mailbox.html">消息中心</a></li>-->
                            <!--<li class="divider"></li>-->
                            <li><a href="<?php echo Url('Base/logout'); ?>">退出</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        MIN+
                    </div>
                </li>
                <?php if(is_array($__MENU__) || $__MENU__ instanceof \think\Collection): $i = 0; $__LIST__ = $__MENU__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;if(empty($menu['child']) || ($menu['child'] instanceof \think\Collection && $menu['child']->isEmpty())): if($menu['id'] == $subMenu['id']): ?><li class="active"><?php else: ?><li><?php endif; ?>
                <a href="<?php echo Url($menu['url']); ?>"><i class="fa <?php echo $menu['icon']; ?>"></i><span class="nav-label"><?php echo $menu['title']; ?></span></a></li>
                <?php else: if($subMenu['pid'] == $menu['id']): ?><li class="active"><?php else: ?><li><?php endif; ?>
                <a href="<?php echo Url($menu['url']); ?>"><i class="fa <?php echo $menu['icon']; ?>"></i> <span class="nav-label"><?php echo $menu['title']; ?></span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse <?php if($subMenu['pid'] == $menu['id']): ?>in<?php endif; ?>">
                    <?php if(is_array($menu['child']) || $menu['child'] instanceof \think\Collection): $i = 0; $__LIST__ = $menu['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$child): $mod = ($i % 2 );++$i;if($subMenu['id'] == $child['id']): ?><li class="active"><?php else: ?><li><?php endif; ?><a href="<?php echo Url($child['url']); ?>"><?php echo $child['title']; ?></a></li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul></li>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                <!--<li class="landing_link">-->
                    <!--<a target="_blank" href="landing.html"><i class="fa fa-star"></i> <span class="nav-label">最新动态</span> <span class="label label-warning pull-right">NEW</span></a>-->
                <!--</li>-->
                <li class="special_link">
                    <a href="https://qy.weixin.qq.com" target="_blank"><i class="fa fa-wechat"></i> <span class="nav-label">微信管理平台</span></a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- 右侧主体内容 -->
    <div id="page-wrapper" class="gray-bg">
        <!-- 中间头部 -->
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                    <form role="search" class="navbar-form-custom" action="<?php echo Url('Index/search'); ?>">
                        <div class="form-group">
                            <input type="text" placeholder="搜索内容" class="form-control" name="top-search" id="top-search">
                        </div>
                    </form>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message">欢迎使用<?php echo \think\Config::get('WEB_SITE_TITLE'); ?></span>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                            <i class="fa fa-envelope"></i>  <span class="label label-warning">0</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell"></i>  <span class="label label-primary">0</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo Url('Base/logout'); ?>">
                            <i class="fa fa-sign-out"></i> 退出
                        </a>
                    </li>
                    <li>
                        <a class="right-sidebar-toggle">
                            <i class="fa fa-tasks"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- 页面路径 -->
        
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>备份数据</h2>
        <ol class="breadcrumb">
            <li>
                <a href="index.html">Database</a>
            </li>
            <li class="active">
                <strong>export</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

        <!-- 中间主体内容 -->
        
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <div class="ibox-buttons">
                            <a class="btn btn-primary btn-sm" id="export" href="javascript:;" autocomplete="off">立即备份</a>
                            <a class="btn btn-primary btn-sm" id="optimize" href="<?php echo Url('optimize'); ?>">优化表</a>
                            <a class="btn btn-primary btn-sm" id="repair" href="<?php echo Url('repair'); ?>">修复表</a>
                        </div>
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#">筛选条件</a>
                            </li>
                            <li><a href="#">筛选条件</a>
                            </li>
                        </ul>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <form id="export-form" method="post" action="<?php echo Url('export'); ?>">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th><input class="i-checks check-all" checked="chedked" type="checkbox"></th><th>表名</th>
                                <th width="120">数据量</th>
                                <th width="120">数据大小</th>
                                <th width="160">创建时间</th>
                                <th width="160">备份状态</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($list) || $list instanceof \think\Collection): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$table): $mod = ($i % 2 );++$i;?>
                                <tr>
                                    <td class="num">
                                        <input class="i-checks ids" checked="chedked" type="checkbox" name="tables[]" value="<?php echo $table['name']; ?>">
                                    </td>
                                    <td><?php echo $table['name']; ?></td>
                                    <td><?php echo $table['rows']; ?></td>
                                    <td><?php echo format_bytes($table['data_length']); ?></td>
                                    <td><?php echo $table['create_time']; ?></td>
                                    <td class="bf">未备份</td>
                                    <td class="action">
                                        <a class="ajax-get no-refresh" href="<?php echo Url('optimize?tables='.$table['name']); ?>">优化表</a>&nbsp;
                                        <a class="ajax-get no-refresh" href="<?php echo Url('repair?tables='.$table['name']); ?>">修复表</a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

        <!-- 底部信息 -->
        <div class="footer">
            <div class="pull-right">
                版本 <strong>0.3</strong>
            </div>
            <div>
                <strong>Copyright</strong>杭州之图网络科技有限公司 &copy;2016-2017
            </div>
        </div>
    </div>
    <!-- 右侧工具栏 -->
</div>
<!-- Mainly scripts -->
<script src="/admin/js/bootstrap.min.js"></script>
<script src="/admin/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="/admin/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!-- Custom and plugin javascript -->
<script src="/admin/js/inspinia.js"></script>
<script src="/admin/js/plugins/pace/pace.min.js"></script>
<script src="/admin/js/common.js"></script>
<!-- Toastr script -->
<script src="/admin/js/plugins/toastr/toastr.min.js"></script>
<script src="/admin/js/plugins/sweetalert/sweetalert.min.js"></script>

<!-- Peity -->
<script src="/admin/js/demo/peity-demo.js"></script>
<script src="/admin/js/plugins/peity/jquery.peity.min.js"></script>
<!-- iCheck -->
<script src="/admin/js/plugins/iCheck/icheck.min.js"></script>

<!-- Sweet alert -->
<script src="/admin/js/plugins/sweetalert/sweetalert.min.js"></script>

<script>
$(function(){
    $("#action_add").click(function(){
        window.location.href = $(this).attr('url');
    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $(".check-all").on("ifChanged",function(){
        $("tbody").find(".ids").iCheck("toggle");
    });
});
</script>
<script>
(function($){
    var $form = $("#export-form"), $export = $("#export"), tables,
    $optimize = $("#optimize"), $repair = $("#repair");

    $optimize.add($repair).click(function(){
        $.post(this.href, $form.serialize(), function(response){
            if(response.code){
                swal(response.msg,'alert-success','success');
            } else {
                swal(response.msg,'alert-error','error');
            }
            setTimeout(function(){
                $('#top-alert').find('button').click();
                $(that).removeClass('disabled').prop('disabled',false);
            },1500);
        }, "json");
        return false;
    });

    $export.click(function(){
        $export.parent().children().addClass("disabled");
        $export.html("正在发送备份请求...");
        $.post(
            $form.attr("action"),
            $form.serialize(),
            function(response){
                if(response.code){
                    tables = response.data.tables;
                    $export.html(response.msg + "开始备份，请不要关闭本页面！");
                    backup(response.data.tab);
                    window.onbeforeunload = function(){ return "正在备份数据库，请不要关闭！" }
                } else {
                    swal(response.msg,'alert-error',"error");
                    $export.parent().children().removeClass("disabled");
                    $export.html("立即备份");
                    setTimeout(function(){
                        $('#top-alert').find('button').click();
                        $(that).removeClass('disabled').prop('disabled',false);
                    },1500);
                }
            },
            "json"
        );
        return false;
    });

    function backup(tab, status){
        status && showmsg(tab.id, "开始备份...(0%)");
        $.post($form.attr("action"), tab, function(response){
            if(response.code){
                showmsg(tab.id, response.msg);
                if(!$.isPlainObject(response.data.tab)){
                    $export.parent().children().removeClass("disabled");
                    $export.html("备份完成，点击重新备份");
                    window.onbeforeunload = function(){ return null }
                    return;
                }
                backup(response.data.tab, tab.id != response.data.tab.id);
            } else {
                swal(response.msg,'alert-error',"error");
                $export.parent().children().removeClass("disabled");
                $export.html("立即备份");
                setTimeout(function(){
                    $('#top-alert').find('button').click();
                    $(that).removeClass('disabled').prop('disabled',false);
                },1500);
            }
        }, "json");
    }

    function showmsg(id, msg){
        $form.find("input[value=" + tables[id] + "]").closest("tr").find(".bf").html(msg);
    }
})(jQuery);
</script>

</body>
</html>
