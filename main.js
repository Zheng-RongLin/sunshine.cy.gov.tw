$.ajaxSetup({async: false});

var map, areas = {}, header;


function initialize() {
    /*map setting*/
    $('#map-canvas').height(window.outerHeight / 2.2);

    map = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom: 9,
        center: {lat: 23.50, lng: 120.50}
    });

    map.data.addListener('mouseover', function (event) {
        $('#content').html('<div>' + event.feature.getProperty('election') + ' </div>').removeClass('text-muted');
        map.data.overrideStyle(event.feature, {fillColor: '#00cfcf'});
    });

    map.data.addListener('click', function (event) {
        var candidates = event.feature.getProperty('candidates');
        var electionId = event.feature.getProperty('election_id');
        var content = '<div class="clearfix"></div><h2>' + event.feature.getProperty('election') + '</h2>';
        content += '<table class="table table-boarded"><thead><tr>';
        for (i = 4; i <= 20; i++) {
            content += '<th>' + header[i] + '</th>';
        }
        content += '</tr></thead><tbody>';
        for (k in candidates) {
            content += '<tr>';
            for (i = 4; i <= 20; i++) {
                if (areas[electionId][candidates[k].id]) {
                    content += '<td>' + areas[electionId][candidates[k].id][i] + '</td>';
                } else {
                    if (i === 4) {
                        content += '<td>' + candidates[k].name + '</td>';
                    } else {
                        content += '<td> - </td>';
                    }
                }
            }
            content += '</tr>';
        }
        content += '</tbody></table>';

        map.data.forEach(function (c) {
            var color = '';
            if (c.getProperty('election_id') === electionId) {
                color = '#00cfcf';
            } else {
                color = '#cfcf00';
            }
            map.data.overrideStyle(c, {fillColor: color});
            c.setProperty('color', color);
        });

        $('#areaDetail').html(content);
    });
    map.data.addListener('mouseout', function (event) {
        $('#content').html('在地圖上滑動或點選以顯示數據').addClass('text-muted');
        map.data.revertStyle();
    });

    $.get('report2elections.csv', {}, function (p) {
        var stack = $.csv.toArrays(p);
        header = stack.shift();
        for (k in stack) {
            if (!areas[stack[k][0]]) {
                areas[stack[k][0]] = {};
            }
            areas[stack[k][0]][stack[k][2]] = stack[k];
        }
        for (k in areas) {
            map.data.loadGeoJson('json/' + k + '.json');
        }
    });
    
    map.data.setStyle(function (feature) {
        var color = feature.getProperty('color');
        if (color === '') {
            color = '#cfcf00';
        }
        return {
            fillColor: feature.getProperty('color'),
            fillOpacity: 0.6,
            strokeWeight: 0
        }
    });

}

google.maps.event.addDomListener(window, 'load', initialize);