<style>
    footer {
        position: fixed;
        bottom: -120px;
        left: 0px;
        right: 0px;
        height: 50px;
    }
    .pull-left-footer {
        text-align: left !important;
        position: absolute !important;
    }
    .pull-right-footer {
        text-align: right !important;
        margin-left: 80%;
        position: absolute !important;
    }
</style>
<script type="text/php">
    if (isset($pdf)) {
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 23;
        $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
        $font = $fontMetrics->getFont("Verdana");
        $size = 10;
        $color = array(0, 0, 0);
        $word_space = 0.0;
        $char_space = 0.0;
        $angle = 0.0;
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>
<footer style="width: 100%;" id="footer">
    <div class="pull-left-footer" style="color: red; font-size: smaller;">
        @if ($ir_number)
            <b>Incident Report Number - {{ $ir_number }}</b>
        @elseif ($cl_number)
            <b>Coaching Log Number - {{ $cl_number }}</b>
        @endif
    </div>
    <div class="pull-right-footer" style="color: gray; font-size: smaller;">
        ARB Call Facilities, Inc.
    </div>
</footer>
