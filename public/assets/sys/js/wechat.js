(function(){
    function wechat(options)
    {
        var html_msgform = '<div class="msgform">'
            + ' <div class="well well-sm msglist">'
            + '     <div class="more"><a href="#" class="lnk-more" data-page="1">查看更多消息</a></div>'
            + '     <div class="nomore"><i class="fa fa-info-circle"></i> 没有更多消息了</div>'
            + '     <div class="loading"><i class="fa fa-spinner"></i> 正在加载</div>'
            + '     <div class="list"></div>'
            + '     <div class="sending"><i class="fa fa-spinner"></i> 正在发送</div>'
            + ' </div>'
            + ' <div class="toolbar">'
            + '     <a href="#" class="lnk-emoji" title="表情"><i class="iconfont icon-xiaolian"></i></a>'
            + '     <a href="#" class="lnk-material" title="图片" data-type="image"><i class="iconfont icon-font29"></i></a>'
            + '     <a href="#" class="lnk-material" title="图文素材" data-type="news"><i class="iconfont icon-xinwen3"></i></a>'
            + '     <a href="#" class="lnk-product" title="产品"><i class="iconfont icon-fenlei"></i></a>'
            + '     <a href="#" class="lnk-article" title="内容文章"><i class="iconfont icon-biaodanyemian"></i></a>'
            + ' </div>'
            + ' <div class="emoji-panel"></div>'
            + ' <div class="well well-sm msgtext">'
            + '     <textarea name="content" id="content" class="content" placeholder="请输入消息"></textarea>'
            + '     <a href="#" class="btn btn-success btn-send">发送</a>'
            + ' </div>'
            + '</div>';
            
        var html_msgitem = '<div class="msg-item">'
            + ' <div class="avatar"><img src="'+ options.logo +'" /></div>'
            + ' <div class="contents well well-sm">'
            + '     <div class="msg-content"></div>'
            + '     <div class="date"></div>'
            + ' </div>'
            + '</div>';

        var html_luckitem = '<div class="luck-item">'
            + ' <div class="luck-title"></div>'
            + ' <div class="luck-money"></div>'
            + ' <div class="luck-wish"></div>'
            + ' <div class="cover">'
            + '     <div class="check-icon fa fa-check fa-3x"></div>'
            + ' </div>'
            + '</div>';

        var emoji_html = '';
        options.emojis && $.each(options.emojis, function(key, img){
            emoji_html += '<a href="#" class="lnk-emoji-key" data-key="'+ key +'">'+ img +'</a>';
        });

        var timer = null;
        var dlg_hide = false;

        function load_new_message(new_url)
        {
            var _this = this;
            $.ajax({
                url: new_url,
                type: 'get',
                data: {},
                dataType: 'json',
                success: function(json) {
                    var container = $('.msglist', _this.content);
                    json.list && $.each(json.list, function(idx, item){
                        add_message(container, item, false);
                    });

                    if (dlg_hide) return;   //防止窗口关闭时正好进行到这里，导致拉取新消息不退出
                    timer = setTimeout(function(){
                        load_new_message.apply(_this, [new_url]);
                    }, 2000);
                }
            });
        }

        function load_message(url)
        {
            var _this = this;
            $('.loading', _this.content).show();
            $('.more', _this.content).hide();
            var page = $('.lnk-more', _this.content).data('page');
            $('.lnk-more', _this.content).data('page', page + 1);
            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                data: {page:page},
                success: function(json) {
                    if (json.count <= 0) {
                        $('.nomore', _this.content).show();
                    }
                    var container = $('.msglist', _this.content);
                    json.list && $.each(json.list, function(idx, item){
                        add_message(container, item, true);
                    });
                },
                complete: function() {
                    $('.loading', _this.content).hide();
                }
            });
        }

        function add_message(container, item, prepend) {
            var row = $(html_msgitem);
            $('.msg-content', row).html(item.content);
            $('.date', row).text(item.date);
            if (item.sys) {
                row.addClass('sys');
            } else {
                $('.avatar img', row).attr('src', item.avatar);
            }
            if (item.readed) {
                row.addClass('readed');
            }
            if (prepend) {
                $('.list', container).prepend(row);
            } else {
                $('.list', container).append(row);
            }
            container.scrollTop(container.get(0).scrollHeight);
        }

        function show_luck_list(memberid)
        {
            var msg_dlg = this;
            dialog.show({
                title: '选择红包',
                width:620,
                height:300,
                content:'<div class="luck-lists"></div>',
                after: function(){
                    var _this = this;
                    _this.showLoading('加载中');
                    $.ajax({
                        url: options.luckmoney_url,
                        type:'get',
                        dataType:'json',
                        success:function(json) {
                            var luck_list = $('.luck-lists', _this.content).empty();
                            json.list && $.each(json.list, function(idx, item){
                                var row = $(html_luckitem);
                                row.data('money', item);
                                $('.luck-money', row).html('￥' + item.money + '元');
                                $('.luck-title', row).html(item.title);
                                $('.luck-wish', row).html(item.wish);
                                row.click(function(){
                                    if (row.hasClass('checked')) return;
                                    row.addClass('checked').siblings().removeClass('checked');
                                });
                                luck_list.append(row);
                            });
                        },
                        complete: function(){
                            _this.hideLoading();
                        }
                    });
                },
                button: [
                    {text:'发送', primary:true, click: function(){
                        var _this = this;
                        var money_item = $('.luck-item.checked', _this.content);
                        if (money_item.length <= 0) {
                            alert('尚未选择要发送的红包');
                            return false;
                        }
                        var money = money_item.data('money');
                        _this.showLoading('正在发送');
                        $.ajax({
                            url:money.send_url,
                            type:'post',
                            data: {memberid:memberid},
                            dataType: 'json',
                            success: function(json) {
                                if (json.error) {
                                    alert(json.message);
                                } else {
                                    add_message($('.msglist', msg_dlg.content), {sys:true, readed:true, content:json.content, date:json.date || ''}, false);
                                }
                            },
                            complete: function(){
                                _this.hideLoading();
                                _this.hide();
                            }
                        });
                    }},
                    {text:'取消', click:function(){
                        this.hide();
                    }}
                ]
            });
        }

        dialog.show({
            title:'和' + options.nickname + '的聊天',
            width: 800,
            height: 500,
            content: html_msgform,
            hide: function(){
                console.log('dialog hide');
                clearTimeout(timer);
                timer = null;
                dlg_hide = true;
            },
            after: function(){
                var _this = this;
                load_message.apply(_this, [options.history_url]);
                timer = setTimeout(function(){
                    load_new_message.apply(_this, [options.new_url]);
                }, 2000);
                
                if (!options.show_luckmoney) {
                    $('.lnk-luck', _this.content).remove();
                }
                if (!options.show_material) {
                    $('.lnk-material', _this.content).remove();
                }
                if (!options.show_product) {
                    $('.lnk-product', _this.content).remove();
                }
                if (!options.show_article){
                    $('.lnk-article', _this.content).remove();
                }

                $('.emoji-panel', _this.content).html(emoji_html);
                $('.lnk-emoji', _this.content).click(function(){
                    $('.emoji-panel', _this.content).show();
                });
                $('.lnk-emoji-key', _this.content).click(function(){
                    var content = $('#content', _this.content).val();
                    $('#content', _this.content).val(content + $(this).data('key'));
                    $('.emoji-panel', _this.content).hide();
                });
                $('.msgform', _this.content).click(function(e){
                    if ($(e.target).closest('.emoji-panel').length <= 0 && $(e.target).closest('.lnk-emoji').length <= 0) {
                        $('.emoji-panel', _this.content).hide();
                    }
                });
                $('#content', _this.content).keyup(function(e){
                    if (e.keyCode == 13) {
                        $('.btn-send', _this.content).click();
                    }
                });
                $('.lnk-material', _this.content).click(function(){
                    var type = $(this).data('type');
                    seajs.use('dialog', function(){
                        dialog.materialSelect({
                            title: '选择素材',
                            url: options.material_url,
                            type: type,
                            callback: function(result){
                                var dlg = this;
                                dlg.showLoading('正在发送'),
                                $.ajax({
                                    url: options.material_send_url,
                                    type: 'post',
                                    data: {id:result.id, memberid:options.memberid},
                                    dataType: 'json',
                                    success:function(json){
                                        dlg.hide();
                                        if (json.error) {
                                            alert(json.message);
                                        } else {
                                            add_message($('.msglist', _this.content), {sys:true, readed:true, content: json.content, date: json.date || ''}, false);
                                        }
                                    },
                                    complete: function(){
                                        dlg.hideLoading();
                                    }
                                });
                            }
                        });
                    })
                });
                $('.lnk-product', _this.content).click(function(){
                    seajs.use('dialog', function(){
                        dialog.productSelect({
                            title:'选择产品',
                            url: options.product_url || config.product_list,
                            categories: options.categories || [],
                            category_url: options.category_url || config.category_list,
                            callback:function(item){
                                var dlg = this;
                                dlg.showLoading('正在发送');
                                $.ajax({
                                    url:options.product_send_url || config.product_send,
                                    type: 'post',
                                    data: {productid:item.id,memberid:options.memberid},
                                    dataType: 'json',
                                    success:function(json){
                                        dlg.hide();
                                        if (json.error) {
                                            alert(json.message);
                                        } else {
                                            add_message($('.msglist', _this.content), {sys:true, readed:true, content:json.content, date:json.date || ''}, false);
                                        }
                                    },
                                    complete:function(){
                                        dlg.hideLoading();
                                    }
                                });
                            }
                        });
                    });
                });
                $('.lnk-article', _this.content).click(function(){
                    seajs.use('dialog', function(){
                        dialog.articleSelect({
                            title: '选择内容',
                            url: options.article_list_url || config.article_list,
                            category_url: options.category_list_url || config.article_category_list,
                            callback:function(item){
                                var dlg = this;
                                dlg.showLoading('正在发送');
                                $.ajax({
                                    url:options.article_send_url || config.article_send,
                                    type: 'post',
                                    data: {articleid:item.id,memberid:options.memberid},
                                    dataType:'json',
                                    success:function(json){
                                        dlg.hide();
                                        if (json.error) {
                                            alert(json.message);
                                        } else {
                                            add_message($('.msglist', _this.content), {sys:true, readed:true, content:json.content, date:json.date || ''}, false);
                                        }
                                    },
                                    complete:function(){
                                        _this.hideLoading();
                                    }
                                });
                            }
                        });
                    });
                });
                $('.btn-send', _this.content).click(function(){
                    var content = $('#content', _this.content).val();
                    if (content == '') return;
                    $('#content', _this.content).val('');

                    $('.sending', _this.content).show();
                    $.ajax({
                        url: options.send_url,
                        type: 'post',
                        dataType: 'json',
                        data: {content:content},
                        success: function(json){
                            if (json.error) {
                                alert(json.message);
                            } else {
                                add_message($('.msglist', _this.content), {sys:true, readed:true, content:content, date:json.date || ''}, false);
                            }
                        },
                        complete: function(){
                            $('.sending', _this.content).hide();
                        }
                    });
                });
                $('.lnk-luck', _this.content).click(function(){
                    show_luck_list.apply(_this, [options.memberid]);
                });
            }
        });
    }

    dialog.wechat = wechat;
})();