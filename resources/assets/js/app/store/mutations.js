let mutations = {
    CREATE_ANNOUNCE(state, announcement) {
        state.announcements.unshift(announcement)
    },
    FETCH_ANNOUNCES(state, rows) {
        return state.rows = rows
    },
    DELETE_ANNOUNCE(state, announcement) {
        let index = state.rows.findIndex(item => item.id === announcement.id)
        state.rows.splice(index, 1)
    },
    COUNT_NOTES(state, countNotes) {
        return state.countNotes = countNotes
    },
    NOTES_REPLIED(state, notesReplies) {
        return state.notesReplies = notesReplies
    },
    FETCH_ANNOUNCES(state, rows) {
        return state.rows = rows
    },
    DASHBOARD_OPEN_CURRENT(state, seriesOpenClosedCurrent) {
        return state.seriesOpenClosedCurrent[0].data = seriesOpenClosedCurrent
    },
    DASHBOARD_ClOSED_CURRENT(state, seriesOpenClosedCurrent) {
        return state.seriesOpenClosedCurrent[1].data = seriesOpenClosedCurrent
    },
    DASHBOARD_OPENCLOSED_CHART(state, loadRequired) {
        return state.chartOpenClosedCurrent = {
            xaxis: {
            type: 'category',
            categories: ["Alabang", "Bacolod", "Market Market", "Two Neo"],
            },
            title: {
                    text: loadRequired.currentMonth + " " + loadRequired.currentYear + " | Total Open and Closed Cases",
                    align: 'center',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '17px',
                    fontWeight:  'bold',
                },
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: "horizontal",
                    shadeIntensity: 0.6,
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 100],
                    colorStops: []
                },
            },
        } 
    },
}
export default mutations
