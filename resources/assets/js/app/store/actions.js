let actions = {
    createAnnounce({commit}, announcement) {
        announcement.constants.get("/announcement/announce/create", announcement.passing)
            .then(res => {
                announcement.laddabtn.stop();
                commit('CREATE_ANNOUNCE', res.data)
                announcement.bus.$emit('init_modal', false);
                announcement.success('Successfully Send!');
            }).catch(error => {
                announcement.globalError(error);
        })
    },
    fetchAnnounces({commit}, loadRequired) {
        loadRequired.setLoading(true);
        loadRequired.constants.get("/announcement/announce")
            .then(res => {
                commit('FETCH_ANNOUNCES', res.data)
                loadRequired.setLoading(false)
            }).catch(error => {
                loadRequired.globalError(error);
        })
    },
    deleteAnnounce({commit}, loadRequired) {
        loadRequired.swal({
            title: 'Are you sure you want to delete ' + loadRequired.rowAnnounce.subject + '?',
            text: "Once deleted, you will not be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
        })
        .then((result) => {
            if (result.value) {
                loadRequired.constants.delete(`/announcement/announce/${loadRequired.rowAnnounce.id}`)
                    .then(res => {
                        if (res.data === 'ok')
                            commit('DELETE_ANNOUNCE', loadRequired.rowAnnounce)
                            loadRequired.getList()
                            loadRequired.success(loadRequired.rowAnnounce.subject + ' has been deleted!')
                    }).catch(error => {
                        loadRequired.globalError(error);
                })
            } else if (result.dismiss === loadRequired.swal.DismissReason.cancel) {
                loadRequired.failure(loadRequired.rowAnnounce.subject + ' has been cancelled!')
            }
        })
    },
    actionCountNotes({commit}, loadRequired) {
        loadRequired.constants.get("/notes/count")
        .then(response => {
                commit('COUNT_NOTES', response.data)
        })
        .catch(error => {
            loadRequired.globalError(error);
        });
    },
    actionNotesReplied({commit}, loadRequired) {
        loadRequired.constants.get("/notes/notify/replied")
        .then(response => {
            commit('NOTES_REPLIED', response.data)
        })
        .catch(error => {
            loadRequired.globalError(error);
        });
    },
    storeOpenClosedCurrent({commit}, loadRequired) {
        loadRequired.constants.get("/chart/count/cases/open/currentmonth")
        .then(response => {
            commit('DASHBOARD_OPEN_CURRENT', response.data)
            commit('DASHBOARD_OPENCLOSED_CHART', loadRequired)
        })
        .catch(error => {
            loadRequired.globalError(error);
        });

        loadRequired.constants.get("/chart/count/cases/closed/currentmonth")
        .then(response => {
            commit('DASHBOARD_ClOSED_CURRENT', response.data)
            commit('DASHBOARD_OPENCLOSED_CHART', loadRequired)
        })
        .catch(error => {
                this.globalErrorHandling(error);
        });
    },
}

export default actions
