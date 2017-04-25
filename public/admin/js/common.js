//dom加载完成后执行的js
;$(function(){

	//全选的实现
	$(".check-all").click(function(){
		$(".ids").prop("checked", this.checked);
	});
	$(".ids").click(function(){
		var option = $(".ids");
		option.each(function(i){
			if(!this.checked){
				$(".check-all").prop("checked", false);
				return false;
			}else{
				$(".check-all").prop("checked", true);
			}
		});
	});

    //ajax delete
    $('.ajax-del').click(function(){
        var href = $(this).attr('href');
        if ( $(this).hasClass('confirm') ) {
            swal({
                title: "你确定要删除吗？",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "是的,我要删除!",
                cancelButtonText: "取消",
                closeOnConfirm: false
            }, function () {
                ajaxdel(href);
            });
            return false;
        } else {
            ajaxdel(href);
        }

        return false;
    });

    //ajax delete
    $('.ajax-print').click(function(){
        var href = $(this).attr('href');
        if ( $(this).hasClass('confirm') ) {
            swal({
                title: "你确定要打印吗？",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "是的,我要打印!",
                cancelButtonText: "取消",
                closeOnConfirm: false
            }, function () {
                ajaxprint(href);
            });
            return false;
        } else {
            ajaxprint(href);
        }

        return false;
    });

    //ajax get请求
    $('.ajax-get').click(function(){
        var href = $(this).attr('href');
        if ( $(this).hasClass('confirm') ) {
            swal({
                title: "你确定要这样操作吗？",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "是的,我确定!",
                cancelButtonText: "取消",
                closeOnConfirm: false
            }, function () {
                ajaxget(href);
            });
            return false;
        }
        ajaxget(href);
        return false;
    });



    //ajax post submit请求
    $('.ajax-post').click(function(){
        var target,query,form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm=false;
        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){
            form = $('.'+target_form);

            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
            	form = $('.hide-data');
            	query = form.serialize();
            } else if (form.get(0)==undefined){
            	return false;
            } else if ( form.get(0).nodeName=='FORM' ){
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                	target = $(this).attr('url');
                }else{
                	target = form.get(0).action;
                }
                query = form.serialize();
            } else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {
                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                })
                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.serialize();
            } else {
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            $.post(target,query).success(function(data){
                if (data.code==1) {
                    if (data.url) {
                        updateAlert(data.msg + ' 页面即将自动跳转~','success');
                    }else{
                        updateAlert(data.msg ,'success');
                    }
                    setTimeout(function(){
                    	$(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        } else {
                            location.reload();
                        }
                    },2000);
                } else {
                    updateAlert(data.msg, 'error');
                    setTimeout(function(){
                    	$(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            //location.href=data.url;
                        }
                    },2000);
                }
            });
        } else {
            swal("操作失败!", "请重试!", "error");
        }
        return false;
    });

    window.updateAlert = function (text, shortCutFunction) {
        shortCutFunction = shortCutFunction || 'info';
        warning({shortCutFunction:shortCutFunction,msg:text});
	};

    // 独立域表单获取焦点样式
    $(".text").focus(function(){
        $(this).addClass("focus");
    }).blur(function(){
        $(this).removeClass('focus');
    });
    $("textarea").focus(function(){
        $(this).closest(".textarea").addClass("focus");
    }).blur(function(){
        $(this).closest(".textarea").removeClass("focus");
    });
});

/* 上传图片预览弹出层 */

//标签页切换(无下一步)
function showTab() {
    $(".tab-nav li").click(function(){
        var self = $(this), target = self.data("tab");
        self.addClass("current").siblings(".current").removeClass("current");
        window.location.hash = "#" + target.substr(3);
        $(".tab-pane.in").removeClass("in");
        $("." + target).addClass("in");
    }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();
}

//标签页切换(有下一步)
function nextTab() {
     $(".tab-nav li").click(function(){
        var self = $(this), target = self.data("tab");
        self.addClass("current").siblings(".current").removeClass("current");
        window.location.hash = "#" + target.substr(3);
        $(".tab-pane.in").removeClass("in");
        $("." + target).addClass("in");
        showBtn();
    }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();

    $("#submit-next").click(function(){
        $(".tab-nav li.current").next().click();
        showBtn();
    });
}

// 下一步按钮切换
function showBtn() {
    var lastTabItem = $(".tab-nav li:last");
    if( lastTabItem.hasClass("current") ) {
        $("#submit").removeClass("hidden");
        $("#submit-next").addClass("hidden");
    } else {
        $("#submit").addClass("hidden");
        $("#submit-next").removeClass("hidden");
    }
}

//ajax del function
function ajaxdel(href) {
    $.get(href).success(function(data){
        if (data.code==1) {
            swal({
                title: "已经删除!",
                text: "你已经删除了这条数据!",
                type: "success",
            }, function(){
                location.reload()
            });
        } else {
            swal("删除失败!", "请重试!", "error");
        }
    });
}

//ajax del function
function ajaxprint(href) {
    $.get(href).success(function(data){
        if (data.code==1) {
            swal({
                title: "已经打印!",
                text: "你已经打印了这条数据!",
                type: "success",
            }, function(){
                location.reload()
            });
        } else {
            swal("打印失败!", "请重试!", "error");
        }
    });
}

//ajax get function
function ajaxget(href) {
    $.get(href).success(function(data){
        if (data.code==1) {
            swal({
                title: "成功!",
                text: "你已经成功执行了这次操作!",
                type: "success",
            }, function(){
                location.reload()
            });
        } else {
            swal("操作失败!", "请重试!", "error");
        }
    });
}

//导航高亮
function highlight_subnav(url){
    $('.metismenu').find('a[href="'+url+'"]').closest('li').addClass('active');
    $('.metismenu').find('a[href="'+url+'"]').closest('ul').addClass('in');
    $('.metismenu').find('a[href="'+url+'"]').closest('ul').closest('li').addClass('active');
}

/*提示窗*/
function warning(opts){
    /*提示窗属性设置*/
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "preventDuplicates": false,
        "positionClass": "toast-top-center",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "2000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    if(opts.shortCutFunction){
        toastr[opts.shortCutFunction](opts.msg);
    } else {
        toastr['warning'](opts.msg);
    }
    
}