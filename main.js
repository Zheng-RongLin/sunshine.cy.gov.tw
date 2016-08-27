$.ajaxSetup({async: false});

var map, areas = {}, headers;


function initialize() {
    /*map setting*/
    $('#map-canvas').height(window.outerHeight / 2.2);

    map = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom: 11,
        center: {lat: 23.00, lng: 120.30}
    });

    map.data.addListener('mouseover', function (event) {
        //var Area = event.feature.getProperty('COUN_NA') + event.feature.getProperty('TOWN_NA') + '[' + event.feature.getProperty('CODE3') + ']';

    });

    map.data.addListener('click', function (event) {
        console.log(event.feature);
    });

    map.data.addListener('mouseout', function (event) {
        map.data.revertStyle();
        $('#content').html('在地圖上滑動或點選以顯示數據').addClass('text-muted');
    });

    $.get('report2elections.csv', {}, function (p) {
        var stack = $.csv.toArrays(p);
        headers = stack.shift();
        for (k in stack) {
            if (!areas[stack[k][0]]) {
                areas[stack[k][0]] = {};
            }
            areas[stack[k][0]][stack[k][2]] = stack[k];
        }
        for (k in areas) {
            area = map.data.loadGeoJson('json/' + k + '.json');
        }
    });
}

google.maps.event.addDomListener(window, 'load', initialize);