(function(){
    window.loadQQMap = function(callback) {
        if (window.qq && window.qq.map) {  //已经加载完成
            callback();
            return;
        }
        
        window.init_qq_map = function(){
            callback();
        }

        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "http://map.qq.com/api/js?v=2.exp&key="+ config.map_key +"&libraries=convertor&callback=init_qq_map";
        document.body.appendChild(script);
    }

    function show(options) {
        var html = '<div class="map-dlg">'
            + ' <div class="map-container" style="width:800px;height:600px;"></div>'
            + '</div>';
        var lat = options.lat || '';
        var lng = options.lng || '';

        if (lat == '' || lng == '') {
            alert('没有坐标');
            return;
        }

        loadQQMap(function(){
            dialog.show({
                title:options.title || '',
                width:800,
                height:600,
                content:html,
                after:function(){
                    var _dlg = this;
                    var center = new qq.maps.LatLng(lat, lng);

                    qq.maps.convertor.translate(center, 1, function(e){
                        var center = e.pop();
                        var map_options = {
                            zoom: 15,
                            center: center,
                            mapTypeId: qq.maps.MapTypeId.ROADMAP
                        };
                        var map = new qq.maps.Map($('.map-container', _dlg.content).get(0), map_options);
                        var marker = new qq.maps.Marker({
                            position: center,
                            map: map
                        });
                    });
                },
                button:[
                    {text:'关闭', primary:true, click:function(){this.hide();}}
                ]
            })
        });
    }
    
    function pickup(options) {
        var html = '<div class="map-dlg">'
            + ' <div class="map-container" style="width:800px;height:500px;"></div>'
            + '</div>';
        var lat = options.lat && options.lat != '' ? options.lat : 29.868782;
        var lng = options.lng && options.lng != '' ? options.lng : 121.549644;
            
        loadQQMap(function(){
            dialog.show({
                title:'选择坐标(拖动地图选择坐标)',
                width:800,
                height:500,
                content:html,
                after:function(){
                    var _dlg = this;
                    var center = new qq.maps.LatLng(lat, lng);
                    var map_options = {
                        zoom:15,
                        center: center,
                        mapTypeId:qq.maps.MapTypeId.ROADMAP
                    };
                    var map = new qq.maps.Map($('.map-container', _dlg.content).get(0), map_options);

                    var marker = new qq.maps.Marker({
                        position: center,
                        map: map
                    });
                    qq.maps.event.addListener(map, 'drag', function(event){
                        marker.setPosition(map.getCenter());
                        var ll = map.getCenter();
                        lng = ll.getLng();
                        lat = ll.getLat();
                    });

                    if (options.address && options.address != '' && options.lat == '' && options.lng == '') {
                        var geocoder = new qq.maps.Geocoder();
                        geocoder.getLocation(options.address);
                        geocoder.setComplete(function(r){
                            if (r.detail && r.detail.location) {
                                marker.setPosition(r.detail.location);
                                map.setCenter(r.detail.location);
                                lat = r.detail.location.getLat();
                                lng = r.detail.location.getLng();
                            }
                        });
                    }
                },
                button:[
                    {text:'确定', primary:true, click:function(){
                        if (options.callback) {
                            options.callback(lat, lng);
                        }
                        this.hide();
                    }},
                    {text:'取消', click:function(){this.hide();}}
                ]
            });
        });
    }
    
    dialog.pickup = pickup;
    dialog.map = show;
})();