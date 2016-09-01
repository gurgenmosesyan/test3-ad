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

$main.basePath = function(path) {
    return $main.baseUrl + path;
};

$main.resetForm = function(form) {
    $('.form-error', form).text('').closest('.form-box').removeClass('has-error');
};

$main.showErrors = function(errors, form) {
    for (var i in errors) {
        $('#form-error-'+ i.replace(/\./g, '_'), form).text(errors[i]).closest('.form-box').addClass('has-error');
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
                    alert(result.data);
                } else {
                    $main.showErrors(result.errors, form);
                }
                form.removeClass('sending');
            }
        });
        return false;
    });
};

$main.initItemsCar = function() {
    $('.items-car').owlCarousel({
        autoPlay: true,
        navigation: true,
        navigationText: ['',''],
        pagination: false,
        slideSpeed: 700,
        itemsCustom: [
            [0, 2],
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
                } else {
                    info.text(result.errors.email[0]).addClass('error');
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

$main.initMap = function() {
    var map = new google.maps.Map(document.getElementById('map'), {
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
    for (var i in $main.branches) {
        var lat = parseFloat($main.branches[i].lat),
            lng = parseFloat($main.branches[i].lng);
        var latLng = new google.maps.LatLng(lat, lng);
        bounds.extend(latLng);
        new google.maps.Marker({
            position: {lat: lat, lng: lng},
            map: map,
            icon : "/imgs/pin.png"
        });
    }
    map.setCenter(bounds.getCenter());
    map.fitBounds(bounds);
    google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
        if (map.getZoom() > 15) {
            map.setZoom(15);
        }
    });
};

$(document).ready(function() {

    $main.initSubscribe();

    $main.initSelect($('#account'));
});
