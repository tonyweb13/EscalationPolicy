let getters = {
    announcements: state => {
        return state.announcements
    },
    rows: state => {
        return state.rows
    },
    countNotes: state => {
        return state.countNotes
    },
    notesReplies: state => {
        return state.notesReplies
    },
    seriesOpenClosedCurrent: state => {
        return state.seriesOpenClosedCurrent
    },
    chartOpenClosedCurrent: state => {
        return state.chartOpenClosedCurrent
    },
}

export default  getters
