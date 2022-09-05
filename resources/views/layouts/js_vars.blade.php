<script>
    // just in case EP is defined elsewhere, we won't over-write it
    if (typeof EP === 'undefined') {
        EP = {};
    }
    EP.csrf_token = '{{ csrf_token() }}';
    EP.settings = JSON.parse('{!! \App\JSVars::getEPSettingsJSON() !!}');
    EP.echo_channel = '{{ \App\JSVars::getChannelName() }}';

    if (EP.settings.user == null) {
        window.location.href = "/login";
    }

    // just in case Laravel is defined elsewhere, we won't over-write it
    if (typeof window.Laravel === 'undefined') {
        window.Laravel = {};
    }
    // this is here because Echo expects the csrfToken to be in the window.Laravel variable
    window.Laravel.csrfToken = EP.csrf_token;
    window.EP = EP;
</script>
