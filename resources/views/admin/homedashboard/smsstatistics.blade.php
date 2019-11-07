<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">SMS STATISTICS</h3>

        <!--div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div-->
    </div>

    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <!--tr>
                    <td width="120px">SMS Balance</td>
                    <td>{{ $balance }}</td>
                </tr-->
                <tr>
                    <td width="120px">Today Sent</td>
                    <td>
                        @if($today>0)
                        <div style="width:{{(($today-$todayfailed)/$today)*100}}%;background:green;float:left;color:#fff;text-align: center;">{{ $today-$todayfailed }}</div><div style="width:{{($todayfailed/$today)*100}}%;background:red;float:right;color:#fff;text-align: center;">{{ $todayfailed }}</div>
                        @else
                        <div style="width:100%;background:green;float:left;color:#fff;text-align: center;">0</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="120px">This Month Sent</td>
                    <td>
                        @if($thismonth>0)
                        <div style="width:{{(($thismonth-$thismonthfailed)/$thismonth)*100}}%;background:green;float:left;color:#fff;text-align: center;">{{ $thismonth-$thismonthfailed }}</div><div style="width:{{($thismonthfailed/$thismonth)*100}}%;background:red;float:right;color:#fff;text-align: center;">{{ $thismonthfailed }}</div>
                        @else
                        <div style="width:100%;background:green;float:left;color:#fff;text-align: center;">0</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="120px">This Year Sent</td>
                    <td>
                        @if($thisyear>0)
                        <div style="width:{{(($thisyear-$thisyearfailed)/$thisyear)*100}}%;background:green;float:left;color:#fff;text-align: center;">{{ $thisyear-$thisyearfailed }}</div><div style="width:{{($thisyearfailed/$thisyear)*100}}%;background:red;float:right;color:#fff;text-align: center;">{{ $thisyearfailed }}</div>
                        @else
                        <div style="width:100%;background:green;float:left;color:#fff;text-align: center;">0</div>
                        @endif
                    </td>
                </tr>
                
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>