$(function () {
    // delete hosting div
    // 000webhost
    var hostdiv = $('div:last');
    if (hostdiv.css('z-index') == 9999999 && hostdiv.css('position') == 'fixed') {
        //hostdiv.css('display', 'none');
    }
	// zzz.com.ua
	$("body > .cbalink").hide().nextAll().hide();
    // end delete
    var $window = $(window);
    var $body = $("body");
    var $document = $(document);
    var link = $("header .menu ul.menu_links a");
    var scroll = $(".scroll-to-top");
    var linksTop = [];
    var $head = $('.js-fixed-header-menu');
    var headTop = $head.offset().top;
    var headHeigth = $head.height();
    var $header = $("header");
    var padding_for_header = get_header_padding();
    function get_header_padding() {
        return String(String(parseInt(headHeigth) + parseInt($header.css('padding-top'))) +
            'px ' +
            $header.css('padding-right') +
            ' ' +
            $header.css('padding-bottom') +
            ' ' +
            $header.css('padding-left'));
    }
    for (var i = 0; i < link.length; i++) {
        linksTop[i] = $(link.eq(i).attr("href")).offset().top;
    }
    link = link.add("header .wrapper > .more");
    linksTop.sort(function (a, b) {
        if (a > b) {
            return 1;
        } else {
            return -1;
        }
    });
    $window.on('resize', function () {
        check_last_section_top();
        padding_for_header = get_header_padding()
    });
    check_last_section_top();
    var menu_link_flag = true;
    link.on("click", function (e) {
        e.preventDefault();
        link.removeClass('active');
        link.filter('[href="' + $(this).attr('href') + '"]').addClass('active');
        if (menu_link_flag) {
            menu_link_flag = false;
            $("html, body").animate({
                scrollTop: $($(this).attr("href")).offset().top + 35
            }, 700, $.bez([.2, .38, .52, .99]), function () {
                menu_link_flag = true;
            });
        }
    });
    scrollbtn();
    $(document).scroll(function () {
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
        if ((top + 5) >= headTop) {
            if (!$header.hasClass('fixed-head')) {
                $header.addClass("fixed-head");
                $header.css("padding", padding_for_header);
            }
        } else {
            $header.removeAttr('style');
            $header.removeClass('fixed-head');
        }
        if (menu_link_flag) {
            for (var i = linksTop.length - 1; i >= 0; i--) {
                if (top >= linksTop[i]) {
                    link.removeClass('active').eq(i).addClass('active');
                    break;
                }
            }
        }
    }

    // на мобильном закрываем меню при нажатии не на него
    $body.mouseup(function (e) {
        var menu = $head.find(".menu.menu_state_open");
        if (menu.length > 0) {
            if (e.target != $head[0] && $head.has(e.target).length == 0) {
                menu.removeClass('menu_state_open');
                menu.children('.menu_links').slideUp(300, function () {
                    $(this).removeAttr('style');
                });
            }
        }
    });
    scroll.on("click", function () {
        $("body, html").animate({
            scrollTop: 0
        }, 550, $.bez([.2, .38, .52, .99]));
    });
    // adaptive menu Toggle
    var menu_icon_flag = true;
    $('.menu__icon').on('click', function () {
        var menu = $(this).closest('.menu');
        var menu_links = menu.children('.menu_links');
        if (menu_icon_flag) {
            menu_icon_flag = false;
            menu.toggleClass('menu_state_open');
            menu_links.slideToggle(300, function () {
                if (!menu.hasClass('menu_state_open')) {
                    menu_links.removeAttr('style');
                } else {

                }
                menu_icon_flag = true;
            });
        }
        menu_links.find('a').on('click', function () {
            if (menu.hasClass('menu_state_open')) {
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
        if (h_difference <= linksTop[last_elem_number]) {
            linksTop[last_elem_number] = h_difference - 10;
        }
    }
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
                if ($field.val().length < 5) {
                    $field.addClass('err');
                    err = 1;
                } else {
                    $field.removeClass('err');
                    err = 0;
                }
        }
        return err;
    }

    var $form = $("#contact_form");
    $form.find('input:not([type=submit]), textarea').on('change', function () {
        var $inp = $(this);
        checkField($inp, $inp.attr('name'));
    });

    // сама валидация
    var $img_loader = $('#img-loader');
    $form.on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('.send').prop('disabled', true);
        var $info = $('.information');


        var name = $form.find('input[name=name]');
        var email = $form.find('input[name=email]');
        var subject = $form.find('input[name=subject]');
        var message = $form.find('textarea[name=message]');


        var err = 0;
        err += checkField(name, 'name');
        err += checkField(email, 'email');
        err += checkField(subject);
        err += checkField(message);
        if (err > 0) {
            $btn.prop('disabled', false).removeClass('load');
            return false;
        }
        $.ajax({
            url: window.wp_ajax,
            type: "POST",
            data: $form.serialize() + '&action=form',
            success: function (data) {
                $btn.removeClass('load');
                if (data.res) {
                    setTimeout(function () {
                        $btn.prop('disabled', true);
                        $info.text(data.data).fadeIn(200);
                    }, 250);
                } else {
                    $btn.prop('disabled', false);
                    if(data.data) {
                        $info.text(data.data).fadeIn(200);
                    } else {
                        alert(data.err);
                    }
                }
            },
            complete: funcComplete,
            beforeSend: funcBefore,
            dataType: 'json'
        });

        function funcComplete(a, b) {
            $img_loader.fadeOut(150, function () {
                $(this).removeAttr('style');
            });
            console.log(b);
        }

        function funcBefore() {
            $info.fadeOut(100, function () {
                $(this).text(null).removeAttr('style');
            });
            $btn.prop('disabled', true).addClass('load');
            $img_loader.fadeIn(600);
        }
    });
    // для гифки загрузки ajax не передвигать
    $img_loader.on('mousedown', function (e) {
        e.preventDefault();
    });

    // для красивого открытия всплывающего окна портфоло

    //var $sections = $('#services, #portfolio, #about, ');
    var $sections = $('body > section, body>header, body>footer');
    var fix_paddinng = 16.5;
    // Проверка на то, что пользователь с телефона
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Kindle|Silk|Opera Mini/i.test(navigator.userAgent)) {
        fix_paddinng = 0;
    }

    $('.modal_open').on("click", function () {
        var $modal_open = $(this);
        $head.slideUp(200, function () {
            scroll.css('margin-right', fix_paddinng +'px');
            $sections.css({
                "padding-right": "+=" + fix_paddinng +"px"
            });
            $body.addClass('popup_opened');
            $('[data-modal-id =' + $modal_open.attr('data-modal-id') +'].modal_roof').css('display', 'block').addClass('fadeInDown');
        });
    });
    $('.modal_roof .modal_overlay .close').on('click', function() {
        var $modal_roof = $(this).parents('.modal_roof').removeClass('fadeInDown').addClass('fadeOutUp');
        // псле анимации, которая привязана к классу fadeOutUp делаем:
        setTimeout(function () {
            $modal_roof.removeClass("fadeOutUp").removeAttr("style");
            $body.removeClass('popup_opened');
            $sections.css({
                "padding-right": "-=" + fix_paddinng + "px"
            });
            scroll.css('margin-right', '0');
            $head.slideDown(200);
        }, 300);
    });

});