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
        var title = event.feature.getProperty('election');
        var content = '<div class="clearfix"></div><hr /><h2>' + title + '</h2>';
        var chartCategories = [], incomes = [], expenses = [];
        content += '<table class="table table-boarded"><thead><tr>';
        for (i = 4; i <= 20; i++) {
            content += '<th>' + header[i] + '</th>';
        }
        content += '</tr></thead><tbody>';
        for (k in candidates) {
            var income = expense = 0;
            content += '<tr>';
            content += '<td><a href="http://k.olc.tw/elections/candidates/view/' + candidates[k].id + '" target="_blank">' + candidates[k].name + '</a></td>';
            for (i = 5; i <= 20; i++) {
                if (areas[electionId][candidates[k].id]) {
                    content += '<td>' + areas[electionId][candidates[k].id][i] + '</td>';
                    if (i > 4 && i < 11) {
                        income += parseInt(areas[electionId][candidates[k].id][i]);
                    } else if (i > 10) {
                        expense += parseInt(areas[electionId][candidates[k].id][i]);
                    }
                } else {
                    content += '<td> - </td>';
                }
            }
            content += '</tr>';
            if (areas[electionId][candidates[k].id]) {
                chartCategories.push(candidates[k].name);
                incomes.push(income);
                expenses.push(expense);
            }
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

        $('#chartContainer').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: title
            },
            xAxis: {
                categories: chartCategories
            },
            yAxis: [{
                    min: 0,
                    title: {
                        text: '收入'
                    }
                }, {
                    title: {
                        text: '支出'
                    },
                    opposite: true
                }],
            legend: {
                shadow: false
            },
            tooltip: {
                shared: true
            },
            plotOptions: {
                column: {
                    grouping: false,
                    shadow: false,
                    borderWidth: 0
                }
            },
            series: [{
                    name: '收入',
                    color: 'rgba(248,161,63,1)',
                    data: incomes,
                    tooltip: {
                        valuePrefix: '$'
                    },
                    pointPadding: 0.3,
                    pointPlacement: 0.2,
                    yAxis: 1
                }, {
                    name: '支出',
                    color: 'rgba(186,60,61,.9)',
                    data: expenses,
                    tooltip: {
                        valuePrefix: '$'
                    },
                    pointPadding: 0.4,
                    pointPlacement: 0.2,
                    yAxis: 1
                }]
        });
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