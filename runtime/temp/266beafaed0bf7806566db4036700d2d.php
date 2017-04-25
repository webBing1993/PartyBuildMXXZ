<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:65:"D:\partybuildeb\public/../application/admin\view\wechat\user.html";i:1492583058;s:65:"D:\partybuildeb\public/../application/admin\view\base\common.html";i:1492583058;}*/ ?>
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
<!-- Ladda style -->
<link href="/admin/css/plugins/ladda/ladda-themeless.min.css" rel="stylesheet">

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
        <h2>通讯录</h2>
        <ol class="breadcrumb">
            <li>
                <a href="index.html">Wechat</a>
            </li>
            <li class="active">
                <strong>User</strong>
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
                <div class="ibox-title"><form method="get" action="">
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" placeholder="用户名" class="input-sm form-control" name="name" value="<?php if(!(empty($_GET['name']) || ($_GET['name'] instanceof \think\Collection && $_GET['name']->isEmpty()))): ?><?php echo $_GET['name']; endif; ?>">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-sm btn-primary">查询</button>
                            </span>
                        </div>
                    </div></form>
                    <div class="ibox-tools progress-demo">
                        <button class="ladda-button ladda-button-user btn btn-primary"  data-style="zoom-in">同步用户</button>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th><input class="i-checks check-all" type="checkbox"></th>
                                <th>UserID</th>
                                <td>名称</td>
                                <th>所属部门</th>
                                <th>职位信息</th>
                                <th>手机号码</th>
                                <th>性别 </th>
                                <th>邮箱</th>
                                <th>微信号</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($list) || $list instanceof \think\Collection): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <tr>
                                <td><input type="checkbox" class="i-checks ids" name="input[]"></td>
                                <td><?php echo $vo['userid']; ?></td>
                                <td><?php echo $vo['name']; ?></td>
                                <td><?php echo $vo['department']; ?></td>
                                <td><?php echo $vo['position']; ?></td>
                                <td><?php echo $vo['mobile']; ?></td>
                                <td><?php echo $vo['gender_text']; ?></td>
                                <td><?php echo $vo['email']; ?></td>
                                <td><?php echo $vo['weixinid']; ?></td>
                                <td><?php switch($vo['status']): case "2": ?><span class="label label-danger"><?php echo $vo['status_text']; ?></span><?php break; case "4": ?><span class="label label-warning"><?php echo $vo['status_text']; ?></span><?php break; default: ?><span class="label label-info"><?php echo $vo['status_text']; ?></span>
                                    <?php endswitch; ?>
                                </td>
                                <td>查看详情</td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="11">
                                    <div class="page"><?php echo $_page; ?></div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
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

<script src="/admin/js/plugins/iCheck/icheck.min.js"></script>
<script src="/admin/js/plugins/sweetalert/sweetalert.min.js"></script>

<script src="/admin/js/plugins/ladda/spin.min.js"></script>
<script src="/admin/js/plugins/ladda/ladda.min.js"></script>
<script src="/admin/js/plugins/ladda/ladda.jquery.min.js"></script>
<script>
    $(function(){
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });
        $(".check-all").on("ifChanged",function(){
            $("tbody").find(".ids").iCheck("toggle");
        });

        var l = $( '.ladda-button-user' ).ladda();
        l.click(function(){
            l.ladda( 'start' );
            $.ajax({
                type: "get",
                url: "<?php echo Url('Wechat/synchronizeUser'); ?>",
                success: function (response) {
                    swal(response.msg, response.data);
                    l.ladda('stop');
                },
                error: function (err) {
                    l.ladda('stop');
                }
            });
        });
    });
</script>

</body>
</html>
