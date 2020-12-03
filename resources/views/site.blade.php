<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$site->name}}</title>
    <script src="../js/echarts.min.js"></script>
    <link rel="stylesheet" href="../css/site.css">
</head>
<body>
    <div class="page">
        <div class="site-name">
            {{$site->name}}
        </div>
        <div class="chart" id="chart">

        </div>
        <div class="l">
            <div class="k b bdr">监测项目</div>
            <div class="v b">监测地址</div>
        </div>
        @foreach ($urls as $urlType => $url)
        <div class="l">
            <div class="k bdr">
                {{$urlType}}
            </div>
            <div class="v">
                {{$url}}
            </div>
        </div>
        @endforeach
        <div class="l">
            <div class="g bdr type b">监测项目</div>
            <div class="g bdr url b">监测地址</div>
            <div class="g bdr created-at b">监测时间</div>
            <div class="g bdr status-code b st-200">状态码</div>
            <div class="g timecost b">耗时</div>
        </div>
        @foreach ($results as $result)
        <div class="l">
            <div class="g bdr type">{{$result['type']}}</div>
            <div class="g bdr url">{{$result['url']}}</div>
            <div class="g bdr created-at">{{$result['created_at']}}</div>
            <div class="g bdr status-code st-{{$result['status_code']}}">{{$result['status_code']}}</div>
            <div class="g timecost">{{$result['timecost']}}</div>
        </div>
        @endforeach
    </div>
</body>
<script type="text/javascript">
    var results = @json($results);
    var dataTime = [];
    var dataTimeCost = [];
    var i;
    for (i = 0; i < results.length; i++) {
        dataTime.push(results[i].created_at);
        dataTimeCost.push(results[i].timecost);
    }
    var chart = echarts.init(document.getElementById('chart'));
    var optionTimecost = option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
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
                name: '耗时',
                data: dataTimeCost,
                type: 'line'
            },
        ],
        dataZoom: {
            type: 'slider'
        }
    };
    chart.setOption(optionTimecost);
</script>
</html>