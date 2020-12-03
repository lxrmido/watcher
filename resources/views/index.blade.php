<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="js/echarts.min.js"></script>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="site-list">
        @foreach ($results as $result)
            <a 
                class="site-item {{$result->health}}" 
                href="./site/{{$result->site_id}}"
                title="{{$result->name}} {{$result->type}}"
                >
                {{$result->name}} {{$result->type}}
            </a>
        @endforeach
    </div>
    <div class="chart-count">
        <div class="container" id="chart-count"></div>
    </div>
    <div class="chart-timecost">
        <div class="container" id="chart-timecost"></div>
    </div>
</body>
    <script type="text/javascript">
        var batches = @json($batches);
        var results = @json($results);
        window.onload = function() {
            var chartCount = echarts.init(document.getElementById('chart-count'));
            var chartTimecost = echarts.init(document.getElementById('chart-timecost'));
            var dataTime = [];
            var dataNormal = [];
            var dataFast = [];
            var dataSlow = [];
            var dataError = [];
            var dataTimeCost = [];
            var dataHealthCost = [];
            var i;
            for (i = 0; i < batches.length; i++) {
                dataTime.push(batches[i].created_at);
                dataNormal.push(batches[i].finished - batches[i].error - batches[i].slow - batches[i].fast);
                dataFast.push(batches[i].fast);
                dataSlow.push(batches[i].slow);
                dataError.push(batches[i].error);
                dataTimeCost.push(batches[i].timecost);
                dataHealthCost.push(batches[i].healthcost);
            }
            var optionCount = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                title: {
                    text: '健康状况分布'
                },
                legend: {
                    data: ['快速', '一般', '缓慢', '出错']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                dataZoom: {
                    type: 'slider'
                },
                yAxis: {
                    type: 'value'
                },
                xAxis: {
                    type: 'category',
                    data: dataTime
                },
                series: [
                    {
                        name: '快速',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            show: false,
                        },
                        data: dataFast,
                        color: '#9ec4b3'
                    },
                    {
                        name: '一般',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            show: false,
                        },
                        data: dataNormal,
                        color: '#2f4553'
                    },
                    {
                        name: '缓慢',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            show: false,
                        },
                        data: dataSlow,
                        color: '#cf917d'
                    },
                    {
                        name: '出错',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            show: false,
                        },
                        data: dataError,
                        color: '#c05655'
                    },
                ]
            };
            var optionTimecost = option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                legend: {
                    data: ['总耗时', '正常站点耗时']
                },
                title: {
                    text: '访问时间(ms)'
                },
                xAxis: {
                    type: 'category',
                    data: dataTime
                },
                yAxis: {
                    type: 'value',
                    formatter: '{value} 毫秒'
                },
                series: [
                    {
                        name: '总耗时',
                        data: dataTimeCost,
                        type: 'line'
                    },
                    {
                        name: '正常站点耗时',
                        data: dataHealthCost,
                        type: 'line'
                    }
                ],
                dataZoom: {
                    type: 'slider'
                },
            };

            chartCount.setOption(optionCount);
            chartTimecost.setOption(optionTimecost);

            chartCount.on('click', function (params) {
                window.location.href = './batch/' + batches[params.dataIndex].id;
            });
        }
    </script>
</html>