<?php
declare(strict_types=1);

return [
    // TODO v1.2: all of these should probably be in the DB, but it might
    // make sense to wait until configurability is needed

    'moment_send_date_format' => 'YYYY-MM-DD',
    'moment_parse_date_format' => 'MMM D YYYY',
    'moment_display_date_format' => 'MMM D, YYYY',
    'moment_parse_time_format' => ['h:m a', 'h:ma', 'H:m'],
    'moment_display_time_format' => 'h:mm a z',
    'moment_display_date_time_format' => 'MMM D YYYY h:mm a z',
    'moment_display_date_time_ms_format' => 'MMM D YYYY h:mm:ss.SSS a z',
    'moment_time_zone' => 'America/New_York',
    'carbon_time_zone' => 'America/New_York',
    'short_date_filter_format' => 'Y-M-d',
    'jquery_datepicker_display_date_format' => 'M d, yy',
    'time_picker_display_time_format' => 'g:i a',
    // when choosing a time with a time-picker, what minute interval should be used?
    // for example, if set to 15, then 10:00, 10:15, 10:30, .. is shown
    'time_picker_interval_minutes' => 15,
];
