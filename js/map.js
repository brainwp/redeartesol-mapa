jQuery(document).ready(function($){

var _map_id = 'map';
var _map = $('#' + _map_id);

if (!_map.length)
    return false;

request = {
    QueryString : function(item){
        var svalue = location.search.match(new RegExp("[\?\&]" + item + "=([^\&]*)(\&?)","i"));
        return svalue ? svalue[1] : svalue;
    }
}

var options;

var embed = maptheme.embed;

if (embed == true) {
    options = {
        center: new google.maps.LatLng(maptheme.lat, maptheme.lng),
        zoom: 5,
        maxZoom: 17,
        streetViewControl: false,
        scrollwheel: false,
    };
} else {
    options = {
        center: new google.maps.LatLng(maptheme.lat, maptheme.lng),
        zoom: 4,
        maxZoom: 17,
        streetViewControl: false,
    };
}

var map = new google.maps.Map(document.getElementById(_map_id), options);
var hovercard = new google.maps.InfoWindow({
   maxHeight: 9999999999999,
   height: 800,
});

var markers_associacoes = [];
var markers_projetos = [];
var markers_artesao = [];
var markers_lojistas = [];
var markers_agentes = [];

var marker_options = {
    styles: [{
        anchorIcon: [ 45, 39 ],
        //fontFamily: Oswald,
        fontWeight: 'bold',
        width: 45,
        height: 39,
        textSize: 14,
        url: maptheme.imgbase + 'marker.png',
    }]
};
var marker_options_associacoes = {
    styles: [{
        anchorIcon: [ 45, 39 ],
        //fontFamily: Oswald,
        fontWeight: 'bold',
        width: 45,
        height: 39,
        textSize: 14,
        url: maptheme.imgbase + 'pins/associacoes.png',
    }]
};
var marker_options_projetos = {
    styles: [{
        anchorIcon: [ 45, 39 ],
        //fontFamily: Oswald,
        fontWeight: 'bold',
        width: 45,
        height: 39,
        textSize: 14,
        url: maptheme.imgbase + 'pins/projetos.png',
    }]
};
var marker_options_artesao = {
    styles: [{
        anchorIcon: [ 45, 39 ],
        //fontFamily: Oswald,
        fontWeight: 'bold',
        width: 45,
        height: 39,
        textSize: 14,
        url: maptheme.imgbase + 'pins/artesao.png',
    }]
};
var marker_options_lojistas = {
    styles: [{
        anchorIcon: [ 45, 39 ],
        //fontFamily: Oswald,
        fontWeight: 'bold',
        width: 45,
        height: 39,
        textSize: 14,
        url: maptheme.imgbase + 'pins/lojistas.png',
    }]
};
var marker_options_agentes = {
    styles: [{
        anchorIcon: [ 45, 39 ],
        //fontFamily: Oswald,
        fontWeight: 'bold',
        width: 45,
        height: 39,
        textSize: 14,
        url: maptheme.imgbase + 'pins/agentes.png',
    }]
};
google.maps.event.addDomListener(window, 'load', function(e) {

    console.log(maptheme.users);
    for (u in maptheme.users) {
        var user = maptheme.users[u];
        var image = new google.maps.MarkerImage(
            user['icon'],
            null,
            new google.maps.Point(0,0),
            new google.maps.Point(32, 39)
        );
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(user.lat, user.lng),
            map: map,
            icon: image
        });
        marker.set('user', user);
        if ( user['icon'].search('associacoes') != -1 ){
            markers_associacoes.push( marker );
        } 
        else if ( user['icon'].search('projetos') != -1 ){
            markers_projetos.push( marker );
        }
        else if ( user['icon'].search('artesao') != -1 ){
            markers_artesao.push( marker );
        }
        else if ( user['icon'].search('lojistas') != -1 ){
            markers_lojistas.push( marker );
        }
        else if ( user['icon'].search('agentes') != -1 ){
            markers_agentes.push( marker );
        }
        google.maps.event.addListener(marker, 'click', function(){
            $infos = this;
            hovercard.setContent('<div id="loading" style="color:#444">Buscando...</div>');
            hovercard.open(map, this);
            var query_user = this.user;
            var data = {
                'action': 'get_user_info',
                'id': query_user.ID     
            };
            if (typeof query_user.is_post !== 'undefined') {
                var data = {
                    'action': 'get_post_info',
                    'id': query_user.ID     
                };
            }
            // We can also pass the url value separately from ajaxurl for front end AJAX implementation
            $.post(maptheme.ajax_url, data, function(response) {
                hovercard.close( map, $infos );
                hovercard.setContent(response);
                hovercard.open( map, $infos );
            });
        });
    }
    var group_agentes = new MarkerClusterer(map, markers_associacoes, marker_options_associacoes);
    var group_projetos = new MarkerClusterer(map, markers_projetos, marker_options_projetos);
    var group_artesao = new MarkerClusterer(map, markers_artesao, marker_options_artesao);
    var group_lojistas = new MarkerClusterer(map, markers_lojistas, marker_options_lojistas);
    google.maps.event.addListener(group_agentes, 'clusterclick', function (e) {
        console.log( this );
    }); 
});
//filtros
$('#legenda input').on('click',function(e){
    e.preventDefault();
    if($(this).hasClass('current')){
        var _url = window.location.search;
        var _url = _url.replace($(this).attr('data-slug'),'');
        var _url = window.location + _url;
        window.location = _url;
    }
    else{
        var _url = $(this).attr('value');
        window.location = _url;
    }
})
$( '.btn-reset' ).on('click', function(e){
    e.preventDefault();
    window.location = $(this).attr('href');
})
});
