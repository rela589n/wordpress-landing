$(function(){
    // delete hosting div
    var hostdiv = $('div:last');
    if(hostdiv.css('z-index') == 9999999 && hostdiv.css('position') == 'fixed') {
        hostdiv.css('display', 'none');
    }
    // end delete
    var $window = $(window);
    var $document = $(document);
    var link = $("header .menu ul.menu_links a");
    var scroll = $(".scroll-to-top");
    var linksTop = [];
    var $head = $('.js-fixed-header-menu');
    var headTop = $head.offset().top;
    var headHeigth = $head.height();
    var $header = $("header");
    var padding_for_header = String(String(parseInt(headHeigth) + parseInt($header.css('padding-top'))) +
        'px ' +
        $header.css('padding-right') +
        ' ' +
        $header.css('padding-bottom') +
        ' ' +
        $header.css('padding-left'));
    for(var i = 0; i< link.length; i++) {
        linksTop[i] = $(link.eq(i).attr("href")).offset().top;
    }
    linksTop.sort(function (a, b) {
        if (a > b) {
            return 1;
        } else {
            return -1;
        }
    });
    $window.on('resize', function () {
        check_last_section_top();
    });
    check_last_section_top();
    var menu_link_flag = true;
    link.on("click", function(e) {
        e.preventDefault();
        link.removeClass('active');
        $(this).addClass('active');
        if(menu_link_flag) {
            menu_link_flag = false;
            $("html, body").animate({
                scrollTop: $($(this).attr("href")).offset().top + 35
            }, 700, $.bez([.2,.38,.52,.99]), function () {
                menu_link_flag = true;
            });
        }
    });
    scrollbtn();
    $(document).scroll(function() {
        scrollbtn();
    });
    function scrollbtn() {
        var top = $window.scrollTop();
        if (top > linksTop[0]) {
            scroll.fadeIn(1000);
        }
        else {
            scroll.fadeOut(1000);
        }
        // 5 = value of property top of the fixed head
        if((top+5) >= headTop) {
            if(!$header.hasClass('fixed-head')) {
                $header.addClass("fixed-head");
                $header.css("padding", padding_for_header);
            }
        } else {
            $header.removeAttr('style');
            $header.removeClass('fixed-head');
        }
        if(menu_link_flag) {
            for (var i = linksTop.length - 1; i >= 0; i--) {
                if (top >= linksTop[i]) {
                    link.removeClass('active').eq(i).addClass('active');
                    break;
                }
            }
        }
    }
    // на мобильном закрываем меню при нажатии не на него
    $('body').mouseup(function (e) {
        var menu = $head.find(".menu.menu_state_open");
        if(menu.length > 0) {
            if(e.target != $head[0] && $head.has(e.target).length == 0){
                menu.removeClass('menu_state_open');
                menu.children('.menu_links').slideUp(300, function () {
                    $(this).removeAttr('style');
                });
            }
        }
    });
    scroll.on("click", function() {
        $("body, html").animate({
            scrollTop: 0
        }, 550, $.bez([.2,.38,.52,.99]));
    });
    // adaptive menu Toggle
    var menu_icon_flag = true;
    $('.menu__icon').on('click', function() {
        var menu = $(this).closest('.menu');
        var menu_links = menu.children('.menu_links');
        if(menu_icon_flag) {
            menu_icon_flag = false;
            menu.toggleClass('menu_state_open');
            menu_links.slideToggle(300, function () {
                if(!menu.hasClass('menu_state_open')) {
                    menu_links.removeAttr('style');
                } else {

                }
                menu_icon_flag = true;
            });
        }
        menu_links.find('a').on('click', function () {
            if(menu.hasClass('menu_state_open')) {
                menu_links.slideUp(300, function () {
                    menu_links.removeAttr('style');
                    menu.removeClass('menu_state_open');
                });
            }
        });
    });
    // Для подсветки посленего пункта меню
    function check_last_section_top() {
        var last_elem_number = linksTop.length - 1;
        var h_difference = $document.height() - $window.height();
        if(h_difference <= linksTop[last_elem_number]) {
            linksTop[last_elem_number] = h_difference - 10;
        }
    }
    // Анимация при помощи wow.js
    // new WOW().init();


    // валидация формы

    // функция проверки поля
    function checkField($field, type) {
        var pattern, err;
        switch (type) {
            case 'name':
                pattern = new RegExp(/^([A-Za-zА-Яа-яЁёЇїІіЄє\s]{0,50}$)/);
                if ($field.val().length < 2 || !pattern.test($field.val())) {
                    $field.addClass('err');
                    err = 1;
                } else {
                    $field.removeClass('err');
                    err = 0;
                }
                break;
            case 'email':
                pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
                if (!pattern.test($field.val())) {
                    $field.addClass('err');
                    err = 1;
                } else {
                    $field.removeClass('err');
                    err = 0;
                }
                break;
            default:
                if($field.val().length < 5) {
                    $field.addClass('err');
                    err = 1;
                } else {
                    $field.removeClass('err');
                    err = 0;
                }
        }
        return err;
    }
    // сама валидация
    var $img_loader = $('#img-loader');
    $("#contact_form").on('submit',function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('.send').prop('disabled', true);
        var pattern = new RegExp(/^([A-Za-zА-Яа-яЁёЇїІіЄє\s]{0,50}$)/);
        var name = $form.find('input[name=name]');
        var err = 0;
        if (name.val().length < 2 || !pattern.test(name.val())) {
            err++;
            name.addClass('err');
        } else {
            name.removeClass('err');
        }
        pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);

        var email = $form.find('input[name=email]');

        if (!pattern.test(email.val())) {
            err++;
            email.addClass('err');
        } else {
            email.removeClass('err');
        }
        var subject = $form.find('input[name=subject]');

        if(subject.val().length < 5) {
            err++;
            subject.addClass('err');
        } else {
            subject.removeClass('err');
        }


        var message = $form.find('textarea[name=message]');

        if(message.val().length < 5) {
            err++;
            message.addClass('err');
        } else {
            message.removeClass('err');
        }
        if (err > 0) {
            $btn.prop('disabled', false).removeClass('load');
            return false;
        }
        //$form.serialize() + '&action=form'
        var $info = $('.information');
        $.ajax({
            url:  window.wp_ajax,
            type: "POST",
            data: $form.serialize() + '&action=form',
            success: function (data) {
                $btn.prop('disabled', false).removeClass('load');
                if (data.res == true) {
                    // $form.children().remove();
                    // $form.children().slideUp(500);
                    // alert(data.data);
                    setTimeout(function() {
                        $info.text(data.data).fadeIn(200);
                    },50);
                } else {
                    alert(data.err);
                }
            },
            complete: funcComplete,
            beforeSend: funcBefore,
            dataType: 'json'
        });

        function funcComplete(a,b) {
            $img_loader.fadeOut(100, function () {
                $(this).removeAttr('style');
            });
            console.log(b);
        }

        function funcBefore() {
            $info.fadeOut(100, function () {
                $(this).text(null).removeAttr('style');
            });
            $btn.prop('disabled', false).removeClass('load');
            $img_loader.fadeIn(600);
        }
    });
    $img_loader.on('mousedown', function(e) {
        e.preventDefault();
    });
});