var $news = $.extend(true, {}, $main);
$news.listPath = '/admpanel/news';

$news.initSearchPage = function() {
    $news.listColumns = [
        {data: 'id'},
        {data: 'title'},
        {data: 'sub_title'}
    ];
    $news.initSearch();
};

$news.makeAlias = function(title, aliasObj) {
    if ($.trim(aliasObj.val()) != '' || $.trim(title) == '') {
        return;
    }
    aliasObj.loading();
    $.ajax ({
        type : 'post',
        url	: '/admpanel/core/makeAlias',
        data : {
            title  : title,
            _token : $main.token
        },
        dataType : 'json',
        success	 : function (result) {
            aliasObj.removeLoading();
            if ($.trim(aliasObj.val()) != '') {
                return;
            }
            if (result.status == 'OK') {
                aliasObj.val(result.data);
            }
        }
    });
};

$news.generateTypeHtml = function(title, id, type) {
    var dataName = type == 'agency' ? 'agencies' : type+'s';
    var html =  '<div class="clearfix" style="margin-bottom: 2px;">'+
                    '<div class="col-sm-5" style="padding: 4px 7px;background: #eceaea;">'+
                        title+
                        '<input type="hidden" class="'+type+'-input" name="'+dataName+'[]['+type+'_id]" value="'+ id +'" />'+
                    '</div>'+
                    '<div class="col-sm-1 text-right" style="padding: 4px 7px;background: #eceaea;">'+
                        '<a href="#" class="remove"><i class="fa fa-remove"></i></a>'+
                    '</div>'+
                '</div>';
    html = $(html);
    $('.remove', html).click(function() {
        html.remove();
        return false;
    });
    $('#'+type+'-block').append(html);
};

$news.initTypeAutoComplete = function(type) {
    var input = $('#'+type+'-input');
    var onSelect = function (e,ui) {
        if (ui.item) {
            $news.generateTypeHtml(ui.item.label, ui.item.id, type);
        }
        input.val('');
        return false;
    };
    input.autocomplete({
        minLength : 1,
        source : function(request, response) {
            var skipIds = [];
            $('.'+type+'-input').each(function() {
                skipIds.push($(this).val());
            });
            input.loading();
            $.ajax({
                type: 'post',
                url: '/admpanel/'+type,
                dataType: 'json',
                data: {
                    search : {
                        title : request.term,
                        skip_ids: skipIds
                    },
                    _token : $main.token
                },
                success: function(result) {
                    response($.map(result.data, function(item) {
                        item.label = item.title;
                        return item;
                    }));
                    input.removeLoading();
                }
            });
        },
        select : onSelect,
        change : onSelect
    });
};

$news.generateTypes = function(data, type) {
    if (!$.isEmptyObject(data)) {
        for (var i in data) {
            $news.generateTypeHtml(data[i].title, data[i].id, type);
        }
    }
};

$news.initEditPage = function() {

    $news.initForm();

    $('.title').change(function() {
        $news.makeAlias($(this).val(), $('.alias'));
    });

    $news.initTypeAutoComplete('brand');
    $news.generateTypes($news.brands, 'brand');

    $news.initTypeAutoComplete('agency');
    $news.generateTypes($news.agencies, 'agency');

    $news.initTypeAutoComplete('creative');
    $news.generateTypes($news.creatives, 'creative');

    //CKEDITOR.config.height = 120;
};

$news.init();