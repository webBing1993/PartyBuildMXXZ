<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:64:"D:\partybuildeb\public/../application/admin\view\menu\index.html";i:1492583058;s:65:"D:\partybuildeb\public/../application/admin\view\base\common.html";i:1492583058;}*/ ?>
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
    
<link rel="stylesheet" href="/static/iCheck/custom.css">

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
        <h2>菜单管理</h2>
        <ol class="breadcrumb">
            <li>
                <a href="index.html">Admin</a>
            </li>
            <li class="active">
                <strong>Menu</strong>
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
                    <h5><?php if($pid == ''): ?>顶级菜单列表 <?php else: ?><?php echo $data['title']; endif; ?></h5>
                    <div class="ibox-tools">
                        <?php if($pid != ''): ?>
                            <a href="javascript:window.history.go(-1);" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="返回"><i class="fa fa-reply"></i> 返回上一级</a>
                        <?php endif; ?>
                        <a data-toggle="modal" class="btn btn-sm btn-primary" href="#modal-form" onclick="addMenu(<?php echo (isset($pid) && ($pid !== '')?$pid:0); ?>)">添加菜单</a>
                        <a class="btn btn-sm btn-primary" href="<?php echo Url('sort',array('pid'=>$pid),''); ?>" >排序</a>
                        <div id="modal-form" class="modal fade" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form class="form-horizontal" method="post" action="<?php echo Url('Menu/add'); ?>">
                                            <p class="tt">添加菜单</p>
                                            <div class="form-group"><label class="col-lg-2 control-label">标题</label>
                                                <div class="col-lg-10"><input type="text" placeholder="用于后台显示的配置标题" class="form-control" required="" name="title"></div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">图标</label>
                                                <div class="col-lg-10"><input type="text" placeholder="Font Awesome" class="form-control" name="icon"></div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">排序</label>
                                                <div class="col-lg-10"><input type="number" class="form-control" required="" name="sort" value="0"></div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">链接</label>
                                                <div class="col-lg-10"><input type="text" placeholder="U函数解析的URL或者外链" class="form-control" required="" name="url"></div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">上级菜单</label>
                                                <div class="col-sm-10"><select class="form-control m-b" name="pid">
                                                    <?php if(is_array($Menus) || $Menus instanceof \think\Collection): $i = 0; $__LIST__ = $Menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?>
                                                        <option value="<?php echo $menu['id']; ?>"><?php echo $menu['title_show']; ?></option>
                                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                                </select></div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">分组</label>
                                                <div class="col-lg-10"><input type="text" placeholder="用于左侧分组二级菜单" class="form-control" name="group"></div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">仅开发者可见</label>
                                                <div class="col-sm-10">
                                                <label class="i-checks">
                                                    <input type="radio" value="1" name="is_dev">是
                                                </label>
                                                <label class="i-checks">
                                                    <input type="radio" checked="" value="0" name="is_dev">否
                                                </label>
                                                </div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">是否隐藏</label>
                                                <div class="col-sm-10">
                                                    <label class="i-checks">
                                                        <input type="radio" value="1" name="hide">是
                                                    </label>
                                                    <label class="i-checks">
                                                        <input type="radio" checked="" value="0" name="hide">否
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">说明</label>
                                                <div class="col-lg-10"><input type="text" placeholder="菜单详细说明" class="form-control" name="tip"></div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-offset-2 col-lg-10">
                                                    <button class="btn btn-primary ajax-post" type="submit" target-form="form-horizontal">添加</button>
                                                </div>
                                            </div>
                                            <input type="hidden" name="id" value="">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th><input class="i-checks check-all" type="checkbox"></th>
                                <th>ID</th>
                                <th>名称</th>
                                <th>上级菜单</th>
                                <th>分组</th>
                                <th>URL </th>
                                <th>排序</th>
                                <th>仅开发者模式显示</th>
                                <th>隐藏</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($list) || $list instanceof \think\Collection): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <tr class="menu_<?php echo $vo['id']; ?>">
                                <td><input type="checkbox" class="i-checks" name="input[]"></td>
                                <td><?php echo $vo['id']; ?></td>
                                <td><a href="<?php echo Url('index?pid='.$vo['id']); ?>"><?php echo $vo['title']; ?></a></td>
                                <td><?php echo (isset($vo['up_title']) && ($vo['up_title'] !== '')?$vo['up_title']:'无'); ?></td>
                                <td><?php echo $vo['group']; ?></td>
                                <td><?php echo $vo['url']; ?></td>
                                <td><?php echo $vo['sort']; ?></td>
                                <td>
                                    <a href="<?php echo Url('toogleDev',array('id'=>$vo['id'],'value'=>abs($vo['is_dev']-1))); ?>" class="ajax-get">
                                        <?php echo $vo['is_dev_text']; ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo Url('toogleHide',array('id'=>$vo['id'],'value'=>abs($vo['hide']-1))); ?>" class="ajax-get">
                                        <?php echo $vo['hide_text']; ?>
                                    </a>
                                </td>
                                <td>
                                    <a data-toggle="modal" title="编辑" href="#modal-form" onclick="editMenu(<?php echo $vo['id']; ?>)">编辑</a>
                                    <a href="<?php echo Url('del?id='.$vo['id']); ?>" class="confirm ajax-del" title="删除">删除</a>
                                </td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
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

<script src="/static/iCheck/icheck.min.js"></script>
<script>
    function addMenu(pid) {
        $(".tt").html("新增菜单");
        $(".form-horizontal")[0].reset();
        $("select[name='pid']").val(pid);
    }
    function editMenu(menuId) {
        $.ajax({
            type: "get",
            url: "<?php echo Url('Menu/getInfo'); ?>",
            data: {id:menuId},
            //dataType: "json",
            success: function(response){
                var data = JSON.parse(response);
                $(".tt").html("编辑菜单");
                $("input[name='id']").val(data.id);
                $("input[name='title']").val(data.title);
                $("input[name='icon']").val(data.icon);
                $("input[name='sort']").val(data.sort);
                $("input[name='url']").val(data.url);
                $("input[name='group']").val(data.group);
                $("input[name='tip']").val(data.tip);
                $("input[name='hide'][value="+data.hide+"]").iCheck('check');
                $("input[name='is_dev'][value="+data.is_dev+"]").iCheck('check');
                $("input[name='hide'][value="+data.hide+"]").attr("checked", true);
                $("input[name='is_dev'][value="+data.is_dev+"]").attr("checked", true);
                $("select[name='pid']").val(data.pid);
                $(".form-horizontal").attr('action', '<?php echo Url("Menu/edit"); ?>');
            }
        })
    }

    $(document).ready(function(){
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });
    });
</script>

</body>
</html>
