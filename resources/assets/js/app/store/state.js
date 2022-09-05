let state = {
    announcements: [],
    rows:  [],
    countNotes: 0,
    notesReplies: [],
    chartOpenClosedCurrent: {},
    seriesOpenClosedCurrent: [
        {
            name: 'Open',
            data: []
        },
        {
            name: 'Closed',
            data: []
        },
    ],
}

export default state
