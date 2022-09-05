<style>
    header {
        width: 100%;
        position: fixed;
        top: -80px;
        left: -5px;
        right: -5px;
        height: 50px;
        z-index:  -999;
        font-size: smaller;
    }
    .pull-left {
        float: left!important;
    }
    .pull-right {
        float: right!important;
    }
</style>
<header >
    <div class="pull-left">
        <img src="img/arb.jpg" style="width:110px; height:50px;">
    </div>
    @if ($work_location)
        <div class="pull-right" style="font-size: smaller; color: gray; width:180px; word-wrap: break-word;">
            {{$work_location}}
        </div>
    @endif
</header>
