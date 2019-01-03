(function(){
    function material(options) {
        var html = '<div class="imageselect-dialog">'
            + '<div class="right">'
                + '<div class="search form-inline"><input type="text" name="key" class="form-control" value="" placeholder="搜索关键字" /><button type="button" class="btn btn-success btn-search">搜索</button></div>'
                + '<div class="image-list"></div>'
            + '</div>'
        + '</div>';
        
        var item_html = '<div class="image-item">'
            + '<div class="img"><img src="{{ cover }}"></div>'
            + '<div class="title">{{ title }}</div>'
            + '<div class="check-cover"><div class="check-icon fa fa-check fa-3x"></div></div>'
        + '</div>';

        var current_page = 1;
        var total_page = 1;

        function load_list(container)
        {
            var _this = this;
            _this.showLoading();
            var key = $('input[name=key]', _this.content).val();
            $.ajax({
                url:options.url,
                type:'get',
                dataType:'json',
                data:{type:options.type,key:key,page:current_page},
                success:function(json){
                    _this.hideLoading();
                    if (!json.error) {
                        total_page = json.totalpage;
                        current_page++;
                        json.list && $.each(json.list, function(idx, item){
                            var row = $(item_html);
                            $('.img img', row).attr('src', item.preview);
                            $('.title', row).text(item.title);
                            row.data('item', item);
                            row.click(function(){
                                if ($(this).hasClass('checked')) return;
                                $(this).addClass('checked').siblings().removeClass('checked');
                            });
                            container.append(row);
                        });
                    }
                }
            });
        }

        dialog.show({
            title:options.title,
            width:options.width || 700,
            height:options.height || 500,
            content:html,
            after:function(){
                var _dialog = this;
                $('.imageselect-dialog', this.content).css({
                    width: (options.width || 700) + 'px',
                    height: (options.height || 500) + 'px'
                });
                var image_container = $('.image-list', this.content).empty();
                load_list.apply(this, [image_container]);

                image_container.scroll(function(){
                    if (total_page <= current_page) return;
                    if (timer != null) {clearTimeout(timer)};
                    timer = setTimeout(function(){
                        var scroll = image_container.scrollTop() + image_container.height() + 20;
                        if (scroll > image_container.get(0).scrollHeight) {
                            current_page++;
                            load_list.apply(_dialog, [image_container]);
                        }
                    }, 500);
                });
                $('.btn-search', _dialog.content).click(function(){
                    current_page = 1;
                    image_container.empty();
                    load_list.apply(_dialog, [image_container]);
                });
                $('input[name=key]', _dialog.content).keyup(function(e){
                    if (e.keyCode == 13) {
                        current_page = 1;
                        image_container.empty();
                        load_list.apply(_dialog, [image_container]);
                    }
                });
            },
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this;
                    var checked_row = $('.image-item.checked', _this.content);
                    if (checked_row.length <= 0) return;
                    options.callback && options.callback.apply(this, [checked_row.data('item')]);
                }},
                {text:'取消', click:function(){this.hide();}}
            ]
        });
    }

    function activity(options) {
        var html = '<div class="imageselect-dialog">'
            + '<div class="right">'
                + '<div class="search form-inline"><input type="text" name="key" class="form-control" value="" placeholder="搜索关键字" /><button type="button" class="btn btn-success btn-search">搜索</button></div>'
                + '<div class="image-list"></div>'
            + '</div>'
        + '</div>';
        
        var item_html = '<div class="image-item">'
            + '<div class="img"><img src="{{ cover }}"></div>'
            + '<div class="title">{{ title }}</div>'
            + '<div class="check-cover"><div class="check-icon fa fa-check fa-3x"></div></div>'
        + '</div>';

        var current_page = 1;
        var total_page = 1;

        function load_list(container)
        {
            var _this = this;
            _this.showLoading();
            var key = $('input[name=key]', _this.content).val();
            $.ajax({
                url:options.url,
                type:'get',
                dataType:'json',
                data:{key:key,page:current_page},
                success:function(json){
                    _this.hideLoading();
                    if (!json.error) {
                        total_page = json.totalpage;
                        current_page++;
                        json.list && $.each(json.list, function(idx, item){
                            var row = $(item_html);
                            $('.img img', row).attr('src', item.preview);
                            $('.title', row).text(item.title);
                            row.data('item', item);
                            row.click(function(){
                                if ($(this).hasClass('checked')) return;
                                $(this).addClass('checked').siblings().removeClass('checked');
                            });
                            container.append(row);
                        });
                    }
                }
            });
        }

        dialog.show({
            title:options.title,
            width:options.width || 700,
            height:options.height || 500,
            content:html,
            after:function(){
                var _dialog = this;
                $('.imageselect-dialog', this.content).css({
                    width: (options.width || 700) + 'px',
                    height: (options.height || 500) + 'px'
                });
                var image_container = $('.image-list', this.content).empty();
                load_list.apply(this, [image_container]);

                image_container.scroll(function(){
                    if (total_page <= current_page) return;
                    if (timer != null) {clearTimeout(timer)};
                    timer = setTimeout(function(){
                        var scroll = image_container.scrollTop() + image_container.height() + 20;
                        if (scroll > image_container.get(0).scrollHeight) {
                            current_page++;
                            load_list.apply(_dialog, [image_container]);
                        }
                    }, 500);
                });
                $('.btn-search', _dialog.content).click(function(){
                    current_page = 1;
                    image_container.empty();
                    load_list.apply(_dialog, [image_container]);
                });
                $('input[name=key]', _dialog.content).keyup(function(e){
                    if (e.keyCode == 13) {
                        current_page = 1;
                        image_container.empty();
                        load_list.apply(_dialog, [image_container]);
                    }
                });
            },
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this;
                    var checked_row = $('.image-item.checked', _this.content);
                    if (checked_row.length <= 0) return;
                    options.callback && options.callback.apply(this, [checked_row.data('item')]);
                }},
                {text:'取消', click:function(){this.hide();}}
            ]
        });
    }

    function qrcode(options)
    {
        var html = '<div class="qrcode" style="text-align:center;padding-top:10px;margin-bottom:10px;">'
                    + '<img class="qrimg" style="width:150px;height:150px;" />'
                + '</div>'
                + '<div class="table">'
                + '<table class="table table-bordered">'
                    + '<thead>'
                        + '<tr>'
                            + '<th>边长(cm)</th>'
                            + '<th>建议扫描距离(米)</th>'
                            + '<th>下载链接</th>'
                        + '</tr>'
                    + '</thead>'
                    + '<tbody>'
                        + '<tr>'
                            + '<td>8cm</td>'
                            + '<td>0.5m</td>'
                            + '<td>'
                                + '<a href="#" class="link1"><i class="fa fa-download"></i></a>'
                            + '</td>'
                        + '</tr>'
                        + '<tr>'
                            + '<td>12cm</td>'
                            + '<td>0.8m</td>'
                            + '<td>'
                                + '<a href="#" class="link2"><i class="fa fa-download"></i></a>'
                            + '</td>'
                        + '</tr>'
                        + '<tr>'
                            + '<td>15cm</td>'
                            + '<td>1m</td>'
                            + '<td>'
                                + '<a href="#" class="link3"><i class="fa fa-download"></i></a>'
                            + '</td>'
                        + '</tr>'
                        + '<tr>'
                            + '<td>30cm</td>'
                            + '<td>1.5m</td>'
                            + '<td>'
                                + '<a href="#" class="link4"><i class="fa fa-download"></i></a>'
                            + '</td>'
                        + '</tr>'
                        + '<tr>'
                            + '<td>50cm</td>'
                            + '<td>2.5m</td>'
                            + '<td>'
                                + '<a href="#" class="link5"><i class="fa fa-download"></i></a>'
                            + '</td>'
                        + '</tr>'
                    + '</tbody>'
                    + '<tfoot style="display:none;">'
                        + '<tr>'
                            + '<td colspan="3" style="text-align:center;"><a href="#">更多类型二维码下载</a></td>'
                        + '</tr>'
                    + '</tfoot>'
                + '</table>'
            + '</div>';

        dialog.show({
            title:options.title || '二维码',
            width:options.width || 600,
            height:options.height || 440,
            content:html,
            after:function(){
                var _this = this;
                $('.qrimg', this.content).attr('src', options.url);
                $('.link1', this.content).attr('href', options.url.replace('size=150', 'size=258').replace('download=0', 'download=1'));
                $('.link2', this.content).attr('href', options.url.replace('size=150', 'size=344').replace('download=0', 'download=1'));
                $('.link3', this.content).attr('href', options.url.replace('size=150', 'size=430').replace('download=0', 'download=1'));
                $('.link4', this.content).attr('href', options.url.replace('size=150', 'size=860').replace('download=0', 'download=1'));
                $('.link5', this.content).attr('href', options.url.replace('size=150', 'size=1280').replace('download=0', 'download=1'));

                if (options.more) {
                    $('table tfoot a', _this.content).attr('href', options.more);
                    $('table tfoot', _this.content).show();
                }
            },
            button:[
                {text:'关闭', primary:true, click:function(){this.hide();}}
            ]
        });
    }

    function products(options){
        var html = '<div class="imageselect-dialog">'
            + '<div class="right">'
                + '<div class="search form-inline" style="width:100%;"><input type="hidden" name="category_path" id="category" /><input type="text" name="key" class="form-control" value="" placeholder="搜索关键字" /><button type="button" class="btn btn-success btn-search">搜索</button></div>'
                + '<div class="image-list"></div>'
            + '</div>'
        + '</div>';
        
        var item_html = '<div class="image-item">'
            + '<div class="img"><img src="{{ cover }}"></div>'
            + '<div class="title">{{ title }}</div>'
            + '<div class="check-cover"><div class="check-icon fa fa-check fa-3x"></div></div>'
        + '</div>';

        var current_page = 1;
        var total_page = 1;

        function load_list(container)
        {
            var _this = this;
            _this.showLoading();
            var category_path = $('input[name=category_path]', _this.content).val();
            var name = $('input[name=key]', _this.content).val();
            $.ajax({
                url:options.url,
                type:'get',
                dataType:'json',
                data:{category_path:category_path,name:name,page:current_page},
                success:function(json){
                    _this.hideLoading();
                    if (!json.error) {
                        total_page = json.pages;
                        current_page++;
                        json.products && $.each(json.products, function(idx, item){
                            var row = $(item_html);
                            $('.img img', row).attr('src', item.preview);
                            $('.title', row).text(item.name);
                            row.data('item', item);
                            row.click(function(){
                                if ($(this).hasClass('checked')) return;
                                $(this).addClass('checked').siblings().removeClass('checked');
                            });
                            container.append(row);
                        });
                    }
                }
            });
        }

        function load_categories(){
            var _this = this;
            $('#category', _this.content).casecadeSelect({
                categories: options.categories || [],
                url: options.category_url || config.category_list
            });
        }

        dialog.show({
            title:options.title,
            width:options.width || 700,
            height:options.height || 500,
            content:html,
            after:function(){
                var _dialog = this;
                $('.imageselect-dialog', this.content).css({
                    width: (options.width || 700) + 'px',
                    height: (options.height || 500) + 'px'
                });
                load_categories.apply(this, []);
                var image_container = $('.image-list', this.content).empty();
                load_list.apply(this, [image_container]);

                image_container.scroll(function(){
                    if (total_page <= current_page) return;
                    if (timer != null) {clearTimeout(timer)};
                    timer = setTimeout(function(){
                        var scroll = image_container.scrollTop() + image_container.height() + 20;
                        if (scroll > image_container.get(0).scrollHeight) {
                            current_page++;
                            load_list.apply(_dialog, [image_container]);
                        }
                    }, 500);
                });
                $('.btn-search', _dialog.content).click(function(){
                    current_page = 1;
                    image_container.empty();
                    load_list.apply(_dialog, [image_container]);
                });
                $('input[name=key]', _dialog.content).keyup(function(e){
                    if (e.keyCode == 13) {
                        current_page = 1;
                        image_container.empty();
                        load_list.apply(_dialog, [image_container]);
                    }
                });
            },
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this;
                    var checked_row = $('.image-item.checked', _this.content);
                    if (checked_row.length <= 0) return;
                    options.callback && options.callback.apply(this, [checked_row.data('item')]);
                }},
                {text:'取消', click:function(){this.hide();}}
            ]
        });
    }

    function articles(options){
        var html = '<div class="imageselect-dialog">'
            + '<div class="right">'
                + '<div class="search form-inline" style="width:100%;"><select class="form-control" name="categoryid"></select><input type="text" name="key" class="form-control" value="" placeholder="搜索关键字" /><button type="button" class="btn btn-success btn-search">搜索</button></div>'
                + '<div class="image-list"></div>'
            + '</div>'
        + '</div>';
        
        var item_html = '<div class="image-item">'
            + '<div class="img"><img src="{{ cover }}"></div>'
            + '<div class="title">{{ title }}</div>'
            + '<div class="check-cover"><div class="check-icon fa fa-check fa-3x"></div></div>'
        + '</div>';

        var current_page = 1;
        var total_page = 1;

        function load_list(container)
        {
            var _this = this;
            _this.showLoading();
            var categoryid = $('select[name=categoryid]', _this.content).val();
            var key = $('input[name=key]', _this.content).val();
            $.ajax({
                url:options.url,
                type:'get',
                dataType:'json',
                data:{categoryid:categoryid,key:key,page:current_page},
                success:function(json){
                    _this.hideLoading();
                    if (!json.error) {
                        total_page = json.pages;
                        current_page++;
                        json.articles && $.each(json.articles, function(idx, item){
                            var row = $(item_html);
                            $('.img img', row).attr('src', item.preview);
                            $('.title', row).text(item.title);
                            row.data('item', item);
                            row.click(function(){
                                if ($(this).hasClass('checked')) return;
                                $(this).addClass('checked').siblings().removeClass('checked');
                            });
                            container.append(row);
                        });
                    }
                }
            });
        }

        function load_categories(){
            var _this = this;
            _this.showLoading();
            $.ajax({
                url: options.category_url || config.article_category_list,
                type: 'get',
                dataType: 'json',
                success:function(json){
                    var selectItem = $('select[name=categoryid]', _this.content).empty();
                    json.categories && $.each(json.categories, function(idx, item){
                        selectItem.append('<option value="'+ item.id +'">' + item.title + '</option>');
                    });

                    var image_container = $('.image-list', _this.content).empty();
                    load_list.apply(_this, [image_container]);
                    
                    selectItem.change(function(){
                        current_page = 1;
                        image_container.empty();
                        load_list.apply(_this, [image_container]);
                    });
                },
                complete:function(){
                    _this.hideLoading();
                }
            });
        }

        dialog.show({
            title:options.title,
            width:options.width || 700,
            height:options.height || 500,
            content:html,
            after:function(){
                var _dialog = this;
                $('.imageselect-dialog', this.content).css({
                    width: (options.width || 700) + 'px',
                    height: (options.height || 500) + 'px'
                });
                var image_container = $('.image-list', this.content).empty();
                load_categories.apply(this, []);

                image_container.scroll(function(){
                    if (total_page <= current_page) return;
                    if (timer != null) {clearTimeout(timer)};
                    timer = setTimeout(function(){
                        var scroll = image_container.scrollTop() + image_container.height() + 20;
                        if (scroll > image_container.get(0).scrollHeight) {
                            current_page++;
                            load_list.apply(_dialog, [image_container]);
                        }
                    }, 500);
                });
                $('.btn-search', _dialog.content).click(function(){
                    current_page = 1;
                    image_container.empty();
                    load_list.apply(_dialog, [image_container]);
                });
                $('input[name=key]', _dialog.content).keyup(function(e){
                    if (e.keyCode == 13) {
                        current_page = 1;
                        image_container.empty();
                        load_list.apply(_dialog, [image_container]);
                    }
                });
            },
            button:[
                {text:'确定', primary:true, click:function(){
                    var _this = this;
                    var checked_row = $('.image-item.checked', _this.content);
                    if (checked_row.length <= 0) return;
                    options.callback && options.callback.apply(this, [checked_row.data('item')]);
                }},
                {text:'取消', click:function(){this.hide();}}
            ]
        });
    }

    dialog.materialSelect = material;
    dialog.activitySelect = activity;
    dialog.productSelect = products;
    dialog.articleSelect = articles;
    dialog.qrcode = qrcode;
    dialog.minicode = minicode;

    //return {materialSelect:material};
})();