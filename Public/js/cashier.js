 //根据时间选择时间段
function checkTime($now){
    var myDate = new Date(),
     date =  myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate(),//获取当前时间,(+1: 月份为0-11)
     beyond = parseInt($('body').data('beyond'));
    if($now ==date){//今天
        $(".js_time_obj").each(function () {
            var arr=$(this).data('date').split('-'),
                arr1= arr[0].split(':'),
                hours= parseInt(myDate.getHours()),
                min= parseInt(myDate.getMinutes());

            //以下午16点为分割线, 16点以前,当前时间加30,反之减300
            if(15>hours && 15>arr1[0]){ //15点以前,
                //当前时间所在的时间段开始30分钟内可以选择 (例:当前时间:14:30;可选14:00-16:00)
                if((hours>arr1[0]) || (hours = parseInt(arr1[0]) && min > beyond)){
                    $(this).addClass('old_time');
                    $(this).removeClass('time_list');
                    $(this).removeClass('click');
                }
            }else{
                //比当前时间所在的时间段提前开始30分钟以上可以选择 (例:当前时间:15:31;不能选16:00-18:00)
                if((hours>arr1[0]) || ((arr1[0]-1) == hours &&  min > beyond )){
                    $(this).addClass('old_time');
                    $(this).removeClass('time_list');
                    $(this).removeClass('click');
                }
            }
        })
        if(!$('.js_time_obj').hasClass('time_list')){
            $(".js_date_obj ").eq(0).removeClass('click');
            $(".js_date_obj ").eq(0).addClass('isholiday');
            $(".js_date_obj ").eq(0).removeClass('date_btn');
            $(".date_btn").eq(0).addClass('click');

            var date = $(".date_btn").eq(0).data('date');
            $('#delivertime').val(date);
            $(".date_btn").eq(0).addClass('click');
            checkTime(date);
            //  $(".date_btn").eq(0).click();

        }
    }else{
        $('.js_time_obj').removeClass('old_time');
        $('.js_time_obj').removeClass('click');
        $('.js_time_obj').addClass('time_list');
    }
}