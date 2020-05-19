/*
 * 绉诲姩绔ā鎷熷鑸彲鐐瑰嚮鑷姩婊戝姩 0.1.4
 * Date: 2017-01-11
 * by: xiewei
 * 瀵艰埅鍙乏鍙虫粦鍔紝鍙偣鍑昏竟缂樼殑涓€涓紝鑷姩婊氬姩涓嬩竴涓埌鍙鑼冨洿銆愪緷璧栦簬iscroll.js銆�
 */
(function ($) {
    $.fn.navbarscroll = function (options) {
        //鍚勭灞炴€с€佸弬鏁�
        var _defaults = {
            className:'cur', //褰撳墠閫変腑鐐瑰嚮鍏冪礌鐨刢lass绫诲悕
            clickScrollTime:300, //鐐瑰嚮鍚庢粦鍔ㄦ椂闂�
            duibiScreenWidth:0.4, //鍗曚綅浠em涓哄噯锛岄粯璁や负0.4rem
            scrollerWidth:3, //鍗曚綅浠x涓哄噯锛岄粯璁や负3,[浠呯敤浜庣壒娈婃儏鍐碉細澶栧眰瀹藉害鍥犱负灏忔暟鐐归€犳垚鐨勪笉绮惧噯鎯呭喌]
            defaultSelect:0, //鍒濆閫変腑绗琻涓紝榛樿绗�0涓�
            fingerClick:0, //鐩爣绗�0鎴�1涓€夐」瑙﹀彂,蹇呴』姣忎竴椤归暱搴︿竴鑷达紝鏂瑰彲鐢ㄦ椤�
            endClickScroll:function(thisObj){}//鍥炶皟鍑芥暟
        }
        var _opt = $.extend(_defaults, options);
        this.each(function () {
            //鎻掍欢瀹炵幇浠ｇ爜
            var _wrapper = $(this);
            var _win = $(window);
            var _win_width = _win.width(),_wrapper_width = _wrapper.width(),_wrapper_off_left = _wrapper.offset().left;
            var _wrapper_off_right=_win_width-_wrapper_off_left-_wrapper_width;
            var _obj_scroller = _wrapper.children('.scroller');
            var _obj_ul = _obj_scroller.children('ul');
            var _obj_li = _obj_ul.children('li');
            var _scroller_w = 0;
            _obj_li.css({"margin-left":"0","margin-right":"0"});
            for (var i = 0; i < _obj_li.length; i++) {
                _scroller_w += _obj_li[i].offsetWidth;
            }
            _obj_scroller.width(_scroller_w+_opt.scrollerWidth);
            var myScroll = new IScroll('#'+_wrapper.attr('id'), {
                eventPassthrough: true,
                scrollX: true,
                scrollY: false,
                preventDefault: false
            });
            _init(_obj_li.eq(_opt.defaultSelect));
            _obj_li.click(function(){
                _init($(this));
            });
            //瑙ｅ喅PC绔胺姝屾祻瑙堝櫒妯℃嫙鐨勬墜鏈哄睆骞曞嚭鐜拌帿鍚嶇殑鍗￠】鐜拌薄锛屾粦鍔ㄦ椂绂佹榛樿浜嬩欢锛�2017-01-11锛�
            _wrapper[0].addEventListener('touchmove',function (e){e.preventDefault();},false);
            function _init(thiObj){
                var $this_obj=thiObj;
                var duibi=_opt.duibiScreenWidth*_win_width/10,this_index=$this_obj.index(),this_off_left=$this_obj.offset().left,this_pos_left=$this_obj.position().left,this_width=$this_obj.width(),this_prev_width=$this_obj.prev('li').width(),this_next_width=$this_obj.next('li').width();
                var this_off_right=_win_width-this_off_left-this_width;
                if(_scroller_w+2>_wrapper_width){
                    if(_opt.fingerClick==1){
                        if(this_index==1){
                            myScroll.scrollTo(-this_pos_left+this_prev_width,0, _opt.clickScrollTime);
                        }else if(this_index==0){
                            myScroll.scrollTo(-this_pos_left,0, _opt.clickScrollTime);
                        }else if(this_index==_obj_li.length-2){
                            myScroll.scrollBy(this_off_right-_wrapper_off_right-this_width,0, _opt.clickScrollTime);
                        }else if(this_index==_obj_li.length-1){
                            myScroll.scrollBy(this_off_right-_wrapper_off_right,0, _opt.clickScrollTime);
                        }else{
                            if(this_off_left-_wrapper_off_left-(this_width*_opt.fingerClick)<duibi){
                                myScroll.scrollTo(-this_pos_left+this_prev_width+(this_width*_opt.fingerClick),0, _opt.clickScrollTime);
                            }else if(this_off_right-_wrapper_off_right-(this_width*_opt.fingerClick)<duibi){
                                myScroll.scrollBy(this_off_right-this_next_width-_wrapper_off_right-(this_width*_opt.fingerClick),0, _opt.clickScrollTime);
                            }
                        }
                    }else{
                        if(this_index==1){
                            myScroll.scrollTo(-this_pos_left+this_prev_width,0, _opt.clickScrollTime);
                        }else if(this_index==_obj_li.length-1){
                            if(this_off_right-_wrapper_off_right>1||this_off_right-_wrapper_off_right<-1){
                                myScroll.scrollBy(this_off_right-_wrapper_off_right,0, _opt.clickScrollTime);
                            }
                        }else{
                            if(this_off_left-_wrapper_off_left<duibi){
                                myScroll.scrollTo(-this_pos_left+this_prev_width,0, _opt.clickScrollTime);
                            }else if(this_off_right-_wrapper_off_right<duibi){
                                myScroll.scrollBy(this_off_right-this_next_width-_wrapper_off_right,0, _opt.clickScrollTime);
                            }
                        }
                    }
                }
                $this_obj.addClass(_opt.className).siblings('li').removeClass(_opt.className);
                _opt.endClickScroll.call(this,$this_obj);
            }
        });
    };
})(jQuery);