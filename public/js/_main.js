'use strict';

var $trans = function() {
    return $trans.get.apply(arguments);
};
$trans.transMap = null;

$trans.get = function (key, paramData) {
    try {
        if ($trans.transMap  == null) {
            var locSettings = $locSettings || {};
            $trans.transMap = locSettings.trans || {};
        }
        if (typeof $trans.transMap[key] != "undefined") {
            key = $trans.transMap[key];
            if (paramData) {
                for (var i in paramData) {
                    if (paramData.hasOwnProperty(i)) {
                        key = key.replace("{"+i+"}",paramData[i]);
                    }
                }
            }
            return key;
        }
    }
    catch(e){}
    return key;
};

var $main = {};
$main.filterInited = false;

$main.basePath = function(path) {
    return $main.baseUrl + path;
};

$main.resetForm = function(form) {
    $('.form-error', form).text('').closest('.form-box').removeClass('has-error');
};

$main.showErrors = function(errors, form, animate) {
    for (var i in errors) {
        $('#form-error-'+ i.replace(/\./g, '_'), form).text(errors[i]).closest('.form-box').addClass('has-error');
    }
    if (animate) {
        $('html, body').animate({
            scrollTop: $('.has-error:first').offset().top-20
        }, 500);
    }
};

$main.initContactForm = function() {
    $('#contact-form').submit(function() {
        var form = $(this);
        if (form.hasClass('sending')) {
            return false;
        }
        form.addClass('sending');
        $.ajax({
            type: 'post',
            url: form.attr('action'),
            data: form.serializeArray(),
            dataType: 'json',
            success: function(result) {
                $main.resetForm(form);
                if (result.status == 'OK') {
                    alert(result.data.text);
                    form.find('.subject, .message').val('');
                } else {
                    $main.showErrors(result.errors, form, true);
                }
                form.removeClass('sending');
            }
        });
        return false;
    });
};

$main.initSearch = function() {
    var form = $('#search-form');
    $('#search-icon').on('click', function() {
        form.stop().fadeIn(150, function() {
            form.find('input:text').focus();
        });
        $(document).unbind('click').bind('click', function(e) {
            if (!$(e.target).parents().is('#search-form')) {
                form.stop().fadeOut(100).find('input:text').val('');
                $(document).unbind('click');
            }
        });
        return false;
    });
};

$main.initItemsCar = function() {
    $('.items-car').owlCarousel({
        autoPlay: false,
        navigation: true,
        navigationText: ['',''],
        pagination: false,
        slideSpeed: 700,
        itemsCustom: [
            [0, 1],
            [378, 2],
            [550, 3],
            [735, 4],
            [920, 5],
            [1100, 6],
            [1250, 7],
            [1450, 8],
            [1600, 9]
        ]
    });
};

$main.initNewsSlider = function() {
    $('#news-slider').owlCarousel({
        singleItem: true,
        autoPlay: true,
        navigation: true,
        navigationText: ['',''],
        pagination: true,
        paginationSpeed: 1000,
        slideSpeed: 1000,
        rewindSpeed: 1500
    });
};

function reCaptchaCallback() {
    $('#subscribe-form').submit();
}

$main.initSubscribe = function() {
    $('#subscribe-form').submit(function() {
        var form = $(this),
            info = form.find('.info');
        if (form.hasClass('sending')) {
            return false;
        }
        form.addClass('sending');
        $.ajax({
            type: 'post',
            url: form.attr('action'),
            data: form.serializeArray(),
            dataType: 'json',
            success: function(result) {
                info.removeClass('success error');
                if (result.status == 'OK') {
                    info.text(result.data).addClass('success');
                    form.find('input:text').val('');
                    $('#re-captcha').remove();
                } else if (result.status == 'RE_CAPTCHA') {
                    form.append(result.data);
                } else {
                    if (result.errors.email) {
                        info.text(result.errors.email[0]).addClass('error');
                    } else {
                        $('#form-error-re_captcha').text(result.errors.re_captcha);
                    }
                }
                info.addClass('visible');
                form.removeClass('sending');
            }
        });
        return false;
    });
};

$main.initSelect = function(obj) {
    obj.find('select').change(function() {
        var self = $(this),
            text = self.find('option:selected').text();
        self.prev('.select-title').text($.trim(text));
    }).change();
};

$main.initVacancy = function() {
    $('#vacancies').find('.show-more').on('click', function() {
        var self = $(this),
            textBox = self.parent('div').prev('.left').find('.hidden');
        if (self.hasClass('active')) {
            self.removeClass('active').text($trans.get('www.base.label.show_more'));
            textBox.stop().slideUp(400);
        } else {
            self.addClass('active').text($trans.get('www.base.label.show_less'));
            textBox.stop().slideDown(400);
        }
        return false;
    });
};

$main.includeGoogleMap = function() {
    var s = document.createElement("script");
    s.type = "text/javascript";
    s.src = "https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyB-SSsZhHh-0vkfQ6Y8PX6U_95e3hxNp8g";
    $("head").append(s);
};

$main.initMap = function(abPin) {
    $main.map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14,
        styles: [
            {"featureType":"poi","elementType":"geometry","stylers":[{"color":"#F2F2F2"},{"visibility":"on"}]},
            {"featureType":"landscape","elementType":"all","stylers":[{"color":"#F2F2F2"}]},
            {"featureType":"water","elementType":"all","stylers":[{"color":"#FFC001"}]},
            {"featureType":"road","elementType":"geometry","stylers":[{"color":"#D6D6D6"}]},
        ],
        center: {lat: 40.181117, lng: 44.514335}
    });
    $main.latLng = [];
    var bounds = new google.maps.LatLngBounds();
    for (var i in $main.coordinates) {
        var lat = parseFloat($main.coordinates[i].lat),
            lng = parseFloat($main.coordinates[i].lng);
        var latLng = new google.maps.LatLng(lat, lng);
        bounds.extend(latLng);
        new google.maps.Marker({
            position: {lat: lat, lng: lng},
            map: $main.map,
            icon : abPin ? '/imgs/pin-ab.png' : '/imgs/pin.png'
        });
    }
    $main.map.setCenter(bounds.getCenter());
    $main.map.fitBounds(bounds);
    google.maps.event.addListenerOnce($main.map, 'bounds_changed', function() {
        if ($main.map.getZoom() > 15) {
            $main.map.setZoom(15);
        }
    });
};

$main.initFilter = function() {
    var filter = $('#filter');
    var media = filter.find('.media-type').find('select'),
        industry = filter.find('.industry-type').find('select'),
        country = filter.find('.country').find('select'),
        category = filter.find('.category').find('select'),
        month = filter.find('.month').find('select'),
        year = filter.find('.year').find('select'),
        url = '';
    filter.find('select').change(function() {
        if ($(this).hasClass('date')) {
            if ((month.val() && !year.val()) || (!month.val() && year.val())) {
                return false;
            }
            if (!month.val() && !year.val()) {
                var href = document.location.href;
                if (href.indexOf('month') == -1) {
                    return false;
                }
            }
        }
        if (media.val()) {
            url += '&media='+media.val();
        }
        if (industry.val()) {
            url += '&industry='+industry.val();
        }
        if (country.val()) {
            url += '&country='+country.val();
        }
        if (category.val()) {
            url += '&category='+category.val();
        }
        if (month.val() && year.val()) {
            url += '&month='+month.val()+'&year='+year.val();
        }
        url = url ? '?'+url.substr(1) : '';
        document.location.href = $main.filterUrl+url;
    });
};

$main.initAdViews = function(id) {
    $.ajax({
        type: 'post',
        url: $main.basePath('/ads/st'),
        data: {
            id: id,
            _token: $main.token
        },
        dataType: 'json'
    });
};

$main.initRate = function() {
    var rateBox = $('#com-right').find('.rate-box'),
        rate = rateBox.find('.rate'),
        rated = rateBox.find('.rated'),
        stars = rateBox.find('.stars'),
        self, i;
    rated.on('mouseover', function() {
        if (rateBox.hasClass('rated-box')) {
            return false;
        }
        stars.removeClass('dn');
        rated.addClass('dn');
    });
    stars.on('mouseleave', function(e) {
        rated.removeClass('dn');
        stars.addClass('dn');
    });
    rate.on('mouseover', function() {
        for (i = $(this).data('val'); i > 0; i--) {
            rateBox.find('.rate-'+i).addClass('active');
        }
    }).on('mouseout', function() {
        rate.removeClass('active');
    }).on('click', function() {
        if (rateBox.hasClass('rated-box')) {
            return false;
        }
        self = $(this);
        $.ajax({
            type: 'post',
            url: $main.basePath('/api/rate'),
            data: {
                ad_id: self.data('com'),
                val: self.data('val'),
                _token: $main.token
            },
            dataType: 'json',
            success: function(result) {
                if (result.status == 'OK') {
                    rateBox.find('.rating').text(result.data.rating);
                    rateBox.addClass('rated-box');
                    rated.removeClass('dn').attr('title', $trans.get('www.rate.already_rated')).find('div').css('width', result.data.percent+'%');
                    stars.remove();
                }
            }
        });
        return false;
    });
};

$(document).ready(function() {

    $main.initSearch();

    $main.initSubscribe();

    $main.initSelect($('#account'));
    $main.initSelect($('#filter'));

    $main.initFilter();
});
