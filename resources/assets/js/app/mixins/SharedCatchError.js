export default {
    methods: {
        globalErrorHandling: function (error) {
            if (error.response) {
                if (error.response.data.exception == "Swift_TransportException") {
                    
                    this.$swal({
                        title: 'Error Sending Email!',
                        html: 'Please contact Network Admin/Helpdesk<br>'
                            + 'or just click here to <a href="https://webmail.arbcalls.com/" target="_blank">ARB Webmail</a> and<br>'
                            + 'wait for service Up and Running.<br><br>'
                            + "<pre><code><div style='text-align:left'>"
                            + "<b>Path: </b>" + this.$route.path + "<br>"
                            + "<b>Message: </b>" + error.response.data.message
                            + "</div></code></pre>",
                        type: "error",
                        confirmButtonText: 'Refresh Page',
                        confirmButtonClass: 'btn btn-danger',
                    }).then((result) => {
                        window.location.href = '/'
                    })

                } else {

                    this.$swal({
                        title: "Sorry for the Inconvenience!",
                        html: "You are required to re-login to reload your data page.<br>"
                            + "Kindly screenshot this popup and send it to Helpdesk.<br>"
                            + "System Admin alerted and will fix the problem shortly.<br><br>"
                            + "<pre><code><div style='text-align:left'>"
                            + "<b>Path: </b>" + this.$route.path + "<br>"
                            + "<b>Message: </b>" + error.response.data.message
                            + "</div></code></pre>",
                        type: "error",
                        confirmButtonText: 'Re-Login Now',
                        confirmButtonClass: 'btn btn-danger',
                    }).then((result) => {
                        window.location.href = '/logout'
                    })
                }    
            }
        },
        setLoading: function (value) {
            this.isLoading = value
        },
    },
}
