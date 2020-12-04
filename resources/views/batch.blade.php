<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$batch->created_at}}</title>
    <script src="../js/echarts.min.js"></script>
    <link rel="stylesheet" href="../css/batch.css?v={{time()}}">
</head>
<body>
    <div class="page">
        <div class="batch-created-at">
            {{$batch->created_at}} (第{{$batch->id}}次)
        </div>
        <div class="chart" id="chart">

        </div>
        <div class="l">
            <div class="k b bdr">结果项</div>
            <div class="v b">结果值</div>
        </div>
        <div class="l">
            <div class="k bdr">
                总数
            </div>
            <div class="v">
                {{$batch->total}}
            </div>
        </div>
        <div class="l">
            <div class="k bdr">
                错误数
            </div>
            <div class="v">
                {{$batch->error}}
            </div>
        </div>
        <div class="l">
            <div class="k bdr">
                缓慢数
            </div>
            <div class="v">
                {{$batch->slow}}
            </div>
        </div>
        <div class="l">
            <div class="k bdr">
                较快数
            </div>
            <div class="v">
                {{$batch->fast}}
            </div>
        </div>
        <div class="l">
            <div class="k bdr">
                总耗时
            </div>
            <div class="v">
                {{$batch->timecost}}
            </div>
        </div>
        <div class="l">
            <div class="k bdr">
                正常访问耗时
            </div>
            <div class="v">
                {{$batch->healthcost}}
            </div>
        </div>
        <div class="l">
            <div class="g bdr site b">监测站点</div>
            <div class="g bdr type b">监测项目</div>
            <div class="g bdr url b">监测地址</div>
            <div class="g bdr created-at b">监测时间</div>
            <div class="g bdr status-code b st-200">状态码</div>
            <div class="g timecost b">耗时</div>
        </div>
        @foreach ($results as $result)
        <div class="l">
            <div class="g bdr site" title="{{$result['site_id']}} {{$result['name']}}">
                <a href="../site/{{$result['site_id']}}">{{$result['name']}}</a>
            </div>
            <div class="g bdr type">{{$result['type']}}</div>
            <div class="g bdr url sf">
                <a href="{{$result['url']}}">{{$result['url']}}</a>
            </div>
            <div class="g bdr created-at">{{$result['created_at']}}</div>
            <div class="g bdr status-code st-{{$result['status_code']}}">{{$result['status_code']}}</div>
            <div class="g timecost">{{$result['timecost']}}</div>
        </div>
        @endforeach
    </div>
</body>
<script type="text/javascript">
    var results = @json($results);
    var serviceAreas = {};
    var i;
    for (i = 0; i < results.length; i++) {
        if (!serviceAreas[results[i].service_area_id]) {
            serviceAreas[results[i].service_area_id] = {
                error: 0,
                slow: 0,
                normal: 0,
                fast: 0
            }
        }
        serviceAreas[results[i].service_area_id][results[i].health] ++;
    }
    var serviceAreasIds = [];
    var dataFast = [];
    var dataNormal = [];
    var dataSlow = [];
    var dataError = [];
    for (i in serviceAreas) {
        serviceAreasIds.push(i);
        dataFast.push(serviceAreas[i].fast);
        dataNormal.push(serviceAreas[i].normal);
        dataSlow.push(serviceAreas[i].slow);
        dataError.push(serviceAreas[i].error);
    }
    var optionCount = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        title: {
            text: '服务区状态'
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
        yAxis: {
            type: 'value'
        },
        xAxis: {
            type: 'category',
            data: serviceAreasIds
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
    var chart = echarts.init(document.getElementById('chart'));
    chart.setOption(optionCount);
</script>
</html>