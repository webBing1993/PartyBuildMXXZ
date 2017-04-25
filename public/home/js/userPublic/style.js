/**
 * Created by Administrator on 2016/5/10.
 */
$(function(){
    $('.list').each(function(){
        var sta = $(this).attr('sta');
        if(sta == 0){
            $(this).show();
        }else {
            $(this).hide();
        }
    });
    $('.menu').on('click',function(){
        $(this).find('span').addClass('chosen');
        $(this).siblings().find('span').removeClass('chosen');
        var m_sta = $(this).attr('m_sta');
        $('.list').each(function(){
            var sta = $(this).attr('sta');
            if(sta == m_sta){
                $(this).show();
            }else {
                $(this).hide();
            }
        });
    });
});
