var wxmsg = (function(){
    var msg_container = $('<div class="wxmsg-notice-container"></div>');
    var audio = $('<div style="display:none;"><audio src="/assets/sys/img/msg.mp3" preload="auto" id="wxmsg-new"></audio></div>');
    var msg_item_html = '<div class="wxmsg-notice-item"><img src="{avatar}" class="avatar25" /> {nickname}  给您发送了一条消息</div>';
    var dy_item_html = '<div class="wxmsg-notice-item"><img src="{avatar}" class="avatar25" /> {nickname}  关注了公众号</div>';
    
    function check(options) {
        var options = $.extend({time:10000, show:30000, sound:true, logo:'/assets/front/img/avatar.jpg', emojis:[]}, options);
        var time = (new Date()).getTime() / 1000;
        var msg_audio = null;
        var local_notice = window.Notification && Notification.permission == 'granted';

        init();
        function init()
        {
            if (!local_notice) {
                msg_container.appendTo('body');
                audio.appendTo('body');
                $('#wxmsg-new').get(0);
            }
            setTimeout(function(){
                checkMsg();
            }, options.time);
        }

        function checkMsg()
        {
            $.ajax({
                url:options.url,
                type:'post',
                data:{time:time},
                dataType:'json',
                success:function(json) {
                    if (json.error == false) {
                        time = json.time;
                        //todo 根据返回数据显示对话框
                        if (json.list.length > 0 && options.sound && msg_audio && !notifaction) {
                            msg_audio.play();
                        }

                        json.list && $.each(json.list, function(idx, item){
                            showNotice(item);
                        });
                    }
                    
                },
                complete:function(){
                    setTimeout(function(){
                        checkMsg();
                    }, options.time);
                }
            });
        }
        
        function showNotice(item)
        {
            var html = msg_item_html;
            if (item.type == 'subscribe') {
                html = dy_item_html;
            }
            var row = $(html.replace('{nickname}', item.nickname).replace('{avatar}', item.avatar));

            row.click(function(){
                $(this).remove();
                //todo 显示微信对话框
                seajs.use('wechat', function(){
                    dialog.wechat({
                        nickname: item.nickname,
                        memberid: item.id,
                        logo: options.logo,
                        history_url: item.history_url,
                        new_url: item.new_url,
                        send_url: item.send_url,
                        show_material:options.show_material,
                        material_url: options.material,
                        material_send_url: options.material_send,
                        emojis: options.emojis,
                        show_product: options.show_product,
                        show_article: options.show_article
                    });
                });
            });

            if (options.show > 0) {
                setTimeout(function(){
                    row.remove();
                }, options.show);
            }
            
            msg_container.append(row);
        }
    }

    return {check:check}
})();