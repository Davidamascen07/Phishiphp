var chart_web_pie;
var chart_email_hit;
var f_all_empty = false;
var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

$("#graph_overview").html(displayLoader("Loading..."));
$("#graph_timeline_all").html(displayLoader("Loading..."));
getGraphsData();

function getGraphsData() {
    $.post({
        url: "manager/home_manager",
        contentType: 'application/json; charset=utf-8',
        data: JSON.stringify({ 
            action_type: "get_home_graphs_data"
        })
    }).done(function (data) {
        var count_mailcamp = data.campaign_info.mailcamp?data.campaign_info.mailcamp.length:0;
        var count_mailcamp_active=0, count_webtracker_active=0, count_quicktracker_active=0;
        var html_cont = `<div class="text-center align-items-center m-t-40">
                             <span class="col-md-5 badge badge-pill badge-warning"><h4>No data</h4></span>
                          </div>`;

        if(count_mailcamp){
            count_mailcamp_active = data.campaign_info.mailcamp.filter(function(x) {
                return x.camp_status == 1 || x.camp_status == 2 || x.camp_status == 4;
            }).length;
        }

        count_webtracker = data.campaign_info.webtracker?data.campaign_info.webtracker.length:0;
        if(count_webtracker){
            count_webtracker_active = data.campaign_info.webtracker.filter(function(x) {
                return x.active == 1;
            }).length;
        }

        count_quicktracker = data.campaign_info.quicktracker?data.campaign_info.quicktracker.length:0;
            if(count_quicktracker){
            count_quicktracker_active = data.campaign_info.quicktracker.filter(function(x) {
                return x.active == 1;
            }).length;
        }

        if(count_mailcamp==0 && count_webtracker==0 && count_quicktracker==0)
            f_all_empty=true;

        $('#lb_mailcamp').text('Total: ' + count_mailcamp + ', Ativas: ' + count_mailcamp_active);
        $('#lb_webtracker').text('Total: ' + count_webtracker + ', Ativos: ' + count_webtracker_active);
        $('#lb_quicktracker').text('Total: ' + count_quicktracker + ', Ativos: ' + count_quicktracker_active);


        $("#graph_timeline_all").html(html_cont);
        if(data.campaign_info.webtracker.length == 0 && data.campaign_info.mailcamp.length == 0  && data.campaign_info.quicktracker.length == 0)
            $("#graph_overview").html(html_cont);
        else{
            $("#graph_overview").html('');
            renderOverviewGraph(data.campaign_info, data.timestamp_conv);  
            $('#graph_overview').css("height","300px");

            if(data.campaign_info.webtracker.some(o => o.start_time!='-') || data.campaign_info.mailcamp.some(o => o.scheduled_time!='-') || data.campaign_info.quicktracker.some(o => o.start_time!='-')){
                $("#graph_timeline_all").html('');
                renderTimelineAllGraph(data.campaign_info,data.timestamp_conv,data.timezone);
                $('#graph_timeline_all').css("height","300px");
            }   
        }
    }); 
}

function getDateMMDDYYYY(unix_timestamp){
    if(unix_timestamp == '-')
        return '-';
    else{
        var ts_milli = new Date(unix_timestamp * 1000);
        var year = ts_milli.getFullYear();
        var month = months[ts_milli.getMonth()];
        var date = ts_milli.getDate();
        return date + '/' + month + '/' + year;
    }
}

function getDTStd(date_string){
    var date_split = date_string.split('/');
    return (date_split[0] + '-' + months.indexOf(date_split[1]) + '-' + date_split[2]);
}

function renderOverviewGraph(cmp_info, timestamp_conv) {
    date_arr = {
        'all': [],
        'webtracker': [],
        'mailcamp': [],
        'quicktracker': []
    };

    $.each(cmp_info['webtracker'], function(key, value) { 
        date = getDateMMDDYYYY(timestamp_conv[value.date]);
        date_arr.webtracker.push(date);

        if (date_arr.all.indexOf(date) == -1)
            date_arr.all.push(date);
    });

    $.each(cmp_info['mailcamp'], function(key, value) {
        date = getDateMMDDYYYY(timestamp_conv[value.date]);
        date_arr.mailcamp.push(date);
        if (date_arr.all.indexOf(date) == -1)
            date_arr.all.push(date);
    });

    $.each(cmp_info['quicktracker'], function(key, value) {
        date = getDateMMDDYYYY(timestamp_conv[value.date]);
        date_arr.quicktracker.push(date);
        if (date_arr.all.indexOf(date) == -1)
            date_arr.all.push(date);
    });

    date_arr.all.sort();
    graph_data_all_count = {
        'webtracker': [date_arr.webtracker.length],
        'mailcamp': [date_arr.mailcamp.length],
        'quicktracker': [date_arr.quicktracker.length]
    };

    $.each(date_arr.all, function(i, value) {
        array_val_count = date_arr.webtracker.filter(function(x) {
            return x === value;
        }).length;
        graph_data_all_count.webtracker[i] = array_val_count;

        array_val_count = date_arr.mailcamp.filter(function(x) {
            return x === value;
        }).length;
        graph_data_all_count.mailcamp[i] = array_val_count;

        array_val_count = date_arr.quicktracker.filter(function(x) {
            return x === value;
        }).length;
        graph_data_all_count.quicktracker[i] = array_val_count;
    });

    var options = {
        series: [{
            name: 'Campanhas de E-mail',
            data: graph_data_all_count.mailcamp,
            color: '#667eea'
        }, {
            name: 'Rastreadores Web',
            data: graph_data_all_count.webtracker,
            color: '#93fbadff'
        }, {
            name: 'Rastreadores Rápidos',
            data: graph_data_all_count.quicktracker,
            color: '#4facfe'
        }],
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                }
            },
            zoom: {
                enabled: true
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            },
            dropShadow: {
                enabled: true,
                color: '#000',
                top: 18,
                left: 7,
                blur: 10,
                opacity: 0.2
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 8,
                dataLabels: {
                    position: 'top'
                }
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        yaxis: {
            show: true,
            forceNiceScale: true,
            labels: {
                formatter: (value) => {
                    return Math.round(value * 100) / 100
                },
                style: {
                    colors: '#64748b',
                    fontSize: '12px'
                }
            },
            title: {
                text: 'Quantidade de Campanhas',
                // rotate should be -90 to place the y-axis title correctly
                rotate: 90,
                // nudge the title slightly to the left so it doesn't get clipped
                offsetX: 10,
                // small vertical adjustment if needed
                offsetY: 0,
                style: {
                    fontSize: '14px',
                    fontFamily: 'Inter, sans-serif',
                    fontWeight: 600,
                    color: '#374151'
                },
            },
        },
        xaxis: {
            type: 'datetime',
            categories: date_arr.all,
            labels: {
                formatter: function(value, timestamp) {
                    return Unix2StdDate(timestamp)
                },
                style: {
                    colors: '#64748b',
                    fontSize: '12px'
                }
            },
            tickAmount: 10,
            axisBorder: {
                show: true,
                color: '#010918ff'
            },
            axisTicks: {
                show: true,
                color: '#e5e7eb'
            }
        },
        tooltip: {
            theme: 'light',
            custom: function({
                series,
                seriesIndex,
                dataPointIndex,
                w
            }) {
                return `<div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                    <div class="font-semibold text-gray-800">` + w.config.series[seriesIndex].name + `</div>
                    <div class="text-sm text-gray-600">Data: ` + getDTStd(w.config.xaxis.categories[dataPointIndex]) + `</div>
                    <div class="text-sm text-gray-600">Quantidade: ` + w.config.series[seriesIndex].data[dataPointIndex] + `</div>
                </div>`;
            }
        },
        legend: {
            position: 'bottom',
            offsetY: 5,
            fontSize: '14px',
            fontFamily: 'Inter, sans-serif',
            markers: {
                width: 12,
                height: 12,
                radius: 6
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 3
        },
        fill: {
            opacity: 0.85,
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.25,
                gradientToColors: undefined,
                inverseColors: false,
                opacityFrom: 0.85,
                opacityTo: 0.55,
                stops: [50, 0, 100]
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                    position: 'bottom',
                    offsetX: -10,
                    offsetY: 0
                }
            }
        }]
    };

    graph_overview = new ApexCharts(document.querySelector("#graph_overview"), options);
    graph_overview.render();
}

function renderTimelineAllGraph(cmp_info,timestamp_conv, timezone) { 
    var time_arr = {
        'webtracker': [],
        'mailcamp': [],
        'quicktracker': []
    };
    var current_time = moment().tz(timezone).valueOf();

    level = 0;
    $.each(cmp_info['webtracker'], function(key, value) {
        if (value.start_time != '-') {
            start_time = timestamp_conv[value.start_time]*1000;

            if (value.stop_time == '-')
                stop_time=current_time;
            else
                stop_time=timestamp_conv[value.stop_time]*1000;
                    
            time_arr.webtracker.push({
                x: level++ + '',
                y: [
                    start_time,
                    stop_time
                ],
                z: [value.tracker_id, value.tracker_name, value.stop_time]  //stores actual value.stop_time
            });
        }
    });

    level = 0;
    $.each(cmp_info['mailcamp'], function(key, value) {
        if ((value.camp_status == 2 || value.camp_status == 3 || value.camp_status == 4)) {
            start_time = timestamp_conv[value.scheduled_time]*1000;

            if (value.stop_time == '-')
                stop_time=current_time;
            else
                stop_time=timestamp_conv[value.stop_time]*1000;

            time_arr.mailcamp.push({
                x: level++ + '',
                y: [
                    start_time ,
                    stop_time 
                ],
                z: [value.campaign_id, value.campaign_name, value.stop_time]  //stores actual value.stop_time
            });
        }
    });

    level = 0;
    $.each(cmp_info['quicktracker'], function(key, value) {
        if (value.start_time != '' && value.start_time != undefined) {
            start_time = timestamp_conv[value.start_time]*1000;

            if (value.stop_time == '-')
                stop_time=current_time;
            else
                stop_time=timestamp_conv[value.stop_time]*1000;            

            time_arr.quicktracker.push({
                x: level++ + '',
                y: [
                    start_time ,
                    stop_time 
                ],
                z: [value.tracker_id, value.tracker_name, value.stop_time]  //stores actual value.stop_time
            });
        }
    });

    var options = {
        series: [{
                name: 'Campanhas de E-mail',
                data: time_arr.mailcamp,
                color: '#667eea'
            },
            {
                name: 'Rastreadores Web',
                data: time_arr.webtracker,
                color: '#f093fb'
            },
            {
                name: 'Rastreadores Rápidos',
                data: time_arr.quicktracker,
                color: '#4facfe'
            }
        ],
        chart: {
            height: 350,
            type: 'rangeBar',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            },
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                }
            },
            dropShadow: {
                enabled: true,
                color: '#000',
                top: 3,
                left: 3,
                blur: 8,
                opacity: 0.15
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '70%',
                borderRadius: 6,
                rangeBarOverlap: false,
                rangeBarGroupRows: false
            }
        },
        colors: ['#667eea', '#f093fb', '#4facfe'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'horizontal',
                shadeIntensity: 0.25,
                gradientToColors: ['#764ba2', '#f5576c', '#00f2fe'],
                inverseColors: false,
                opacityFrom: 0.85,
                opacityTo: 0.65,
                stops: [0, 100]
            }
        },
        xaxis: {
            type: 'datetime',
            labels: {
                formatter: function(value, timestamp) {
                    return Unix2StdDate(value)
                },
                style: {
                    colors: '#64748b',
                    fontSize: '12px'
                }
            },
            tickAmount: 8,
            axisBorder: {
                show: true,
                color: '#e5e7eb'
            },
            axisTicks: {
                show: true,
                color: '#e5e7eb'
            }
        },
        yaxis: {
            show: true,
            labels: {
                formatter: (value) => {
                    return Math.round(Number(value))
                },
                style: {
                    colors: '#64748b',
                    fontSize: '12px'
                }
            },
            title: {
                text: 'Índice da Campanha',
                rotate: 90,
                offsetX: 0,
                offsetY: 0,
                style: {
                    fontSize: '14px',
                    fontFamily: 'Inter, sans-serif',
                    fontWeight: 600,
                    color: '#374151'
                },
            },
        },
        stroke: {
            width: 1,
            colors: ['transparent']
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: '14px',
            fontFamily: 'Inter, sans-serif',
            offsetY: 10,
            markers: {
                width: 12,
                height: 12,
                radius: 6
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 3,
            xaxis: {
                lines: {
                    show: true
                }
            },
            yaxis: {
                lines: {
                    show: false
                }
            }
        },
        tooltip: {
            theme: 'light',
            custom: function({
                series,
                seriesIndex,
                dataPointIndex,
                w
            }) {
                var st=w.config.series[seriesIndex].data[dataPointIndex].y[0]/1000;  //unix timestamp
                var et=w.config.series[seriesIndex].data[dataPointIndex].y[1]/1000;  //unix timestamp

                st = Unix2StdDateTime(st,timezone);
                if(w.config.series[seriesIndex].data[dataPointIndex].z[2] == '-')   //if not ended
                    et = 'Em execução';
                else
                    et = Unix2StdDateTime(et,timezone);

                return `<div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                    <div class="font-semibold text-gray-800 mb-2">` + w.config.series[seriesIndex].name + `</div>
                    <div class="text-sm text-gray-600"><strong>Nome:</strong> ` + w.config.series[seriesIndex].data[dataPointIndex].z[1] + `</div>
                    <div class="text-sm text-gray-600"><strong>ID:</strong> ` + w.config.series[seriesIndex].data[dataPointIndex].z[0] + `</div>
                    <div class="text-sm text-gray-600"><strong>Período:</strong> ` + st + ' até ' + et + `</div>
                </div>`;
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                var a = moment(val[0]);
                var b= moment(val[1]);
                var diff_hrs = b.diff(a, 'hours', true);
                if (diff_hrs < 1) {
                    var diff_mins = b.diff(a, 'minutes', true);
                    return diff_mins.toFixed(0) + ' min';
                } else if (diff_hrs < 24) {
                    return diff_hrs.toFixed(1) + ' h';
                } else {
                    var diff_days = b.diff(a, 'days', true);
                    return diff_days.toFixed(1) + ' d';
                }
            },
            style: {
                fontSize: '11px',
                fontWeight: 600,
                colors: ['#e4ddddff']
            },
            background: {
                enabled: true,
                foreColor: '#3a3535ff',
                borderRadius: 4,
                padding: 2,
                opacity: 0.8
            }
        },
        responsive: [{
            breakpoint: 768,
            options: {
                chart: {
                    height: 300
                },
                plotOptions: {
                    bar: {
                        barHeight: '60%'
                    }
                }
            }
        }]
    };

    graph_timeline_all = new ApexCharts(document.querySelector("#graph_timeline_all"), options);
    graph_timeline_all.render();
}