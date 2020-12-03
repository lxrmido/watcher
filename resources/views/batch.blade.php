<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$batch->created_at}}</title>
    <script src="../js/echarts.min.js"></script>
    <link rel="stylesheet" href="../css/batch.css">
</head>
<body>
    <div class="page">
        <div class="batch-created-at">
            {{$batch->created_at}} (第{{$batch->id}}次)
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
            <div class="g bdr site" title="{{$result['site_id']}} {{$result['name']}}">{{$result['name']}}</div>
            <div class="g bdr type">{{$result['type']}}</div>
            <div class="g bdr url sf">{{$result['url']}}</div>
            <div class="g bdr created-at">{{$result['created_at']}}</div>
            <div class="g bdr status-code st-{{$result['status_code']}}">{{$result['status_code']}}</div>
            <div class="g timecost">{{$result['timecost']}}</div>
        </div>
        @endforeach
    </div>
</body>
</html>