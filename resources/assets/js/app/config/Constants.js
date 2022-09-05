import axios from 'axios';

export const API_URL = window.location.origin;

export const Default_API = axios.create({
    baseURL: API_URL+'/api/',
});

export const Dashboard_API = axios.create({
    baseURL: API_URL+'/api/dashboard/',
});

export const Admin_API = axios.create({
    baseURL: API_URL+'/api/admin/',
});

export const Incident_API = axios.create({
    baseURL: API_URL+'/api/admin/incidentreport/',
});

export const Settings_API = axios.create({
    baseURL: API_URL+'/api/admin/settings/',
});

export const Coc_API = axios.create({
    baseURL: API_URL+'/api/admin/settings/coc/',
});

export const Progression_API = axios.create({
    baseURL: API_URL + '/api/admin/progressionoffense/',
});

export const Coaching_API = axios.create({
    baseURL: API_URL + '/api/admin/coaching/',
});

export const Settings_ACL_API = axios.create({
    baseURL: API_URL + '/api/settings',
});

export const Status = [
    // { "value": 1, "text": "New" },
    { "value": 2, "text": "In Progress" },
    { "value": 3, "text": "On Hold" },
    { "value": 4, "text": "Closed" }
]

export const Stage_case = [
    { "value": 1, "text": 'Closed - Resigned' },
    { "value": 2, "text": 'Closed - Terminated' },
    { "value": 3, "text": 'Closed for 201 Filing' },
]

export const Is_generate_nte = [
    { "value": 1, "text": "Generate NTE" },
    { "value": 2, "text": "Invalid IR" },
    { "value": 3, "text": "In Review" },
    { "value": 4, "text": "Generate NTE with Preventive Suspension and Admin Hearing" },
]

export const Is_under_investigation = [
    { "value": 1, "text": "Generate IR Resolution" },
    { "value": 2, "text": "Still Under Investigation" },
]

export const Time = [
    { "value": '00:00', "text": '00:00' },
    { "value": '01:00', "text": '01:00' },
    { "value": '02:00', "text": '02:00' },
    { "value": '03:00', "text": '03:00' },
    { "value": '04:00', "text": '04:00' },
    { "value": '05:00', "text": '05:00' },
    { "value": '06:00', "text": '06:00' },
    { "value": '07:00', "text": '07:00' },
    { "value": '08:00', "text": '08:00' },
    { "value": '09:00', "text": '09:00' },
    { "value": '10:00', "text": '10:00' },
    { "value": '11:00', "text": '11:00' },
    { "value": '12:00', "text": '12:00' },
    { "value": '13:00', "text": '13:00' },
    { "value": '14:00', "text": '14:00' },
    { "value": '15:00', "text": '15:00' },
    { "value": '16:00', "text": '16:00' },
    { "value": '17:00', "text": '17:00' },
    { "value": '18:00', "text": '18:00' },
    { "value": '19:00', "text": '19:00' },
    { "value": '20:00', "text": '20:00' },
    { "value": '21:00', "text": '21:00' },
    { "value": '22:00', "text": '22:00' },
    { "value": '23:00', "text": '23:00' },
]

export const Meeting_Place = [
    { "value": "Net Square - Conference Room", "text": "Net Square - Conference Room" },
    { "value": "Market Market - Conference Room", "text": "Market Market - Conference Room" },
    { "value": "Alabang - Conference Room", "text": "Alabang - Conference Room" },
    { "value": "Bacolod - Conference Room", "text": "Bacolod - Conference Room" },
]

export const Month_List = [
    {'text': 'January', 'value' : 1},
    {'text': 'Febuary', 'value' : 2},
    {'text': 'March', 'value' : 3},
    {'text': 'April', 'value' : 4},
    {'text': 'May', 'value' : 5},
    {'text': 'June', 'value' : 6},
    {'text': 'July', 'value' : 7},
    {'text': 'August', 'value' : 8},
    {'text': 'September', 'value' : 9},
    {'text': 'October', 'value' : 10},
    {'text': 'November', 'value' : 11},
    {'text': 'December', 'value' : 12}
];

export const Attachment_Type = [
    { "value": 0, "text": "Others" },
    { "value": 1, "text": "Timekeeping Image Attachments" },
    { "value": 2, "text": "Quality Assurance Image Attachments" },
    { "value": 3, "text": "Underwriter Image Attachments" },
    { "value": 4, "text": "Facility and Admin Image Attachments" },
    { "value": 5, "text": "Workforce Management Image Attachments" },
]

export const Instance_List = [
    { "value": 1, "text": "1st Instance" },
    { "value": 2, "text": "2nd Instance" },
    { "value": 3, "text": "3rd Instance" },
    { "value": 4, "text": "4th Instance" },
    { "value": 5, "text": "5th Instance" },
    { "value": 6, "text": "6th Instance" },
    { "value": 7, "text": "7th Instance" },
]

export const Month_List_Text = [
    {'text': 'January', 'value' : 'Jan'},
    {'text': 'Febuary', 'value' : 'Feb'},
    {'text': 'March', 'value' : 'Mar'},
    {'text': 'April', 'value' : 'Apr'},
    {'text': 'May', 'value' : 'May'},
    {'text': 'June', 'value' : 'Jun'},
    {'text': 'July', 'value' : 'Jul'},
    {'text': 'August', 'value' : 'Aug'},
    {'text': 'September', 'value' : 'Sep'},
    {'text': 'October', 'value' : 'Oct'},
    {'text': 'November', 'value' : 'Nov'},
    {'text': 'December', 'value' : 'Dec'}
];

export const Quarter_List = [
    {'text': '1st Quarter', 'value' : 'Q1'},
    {'text': '2nd Quarter', 'value' : 'Q2'},
    {'text': '3rd Quarter', 'value' : 'Q3'},
    {'text': '4th Quarter', 'value' : 'Q4'},
];

export const Site_List = [
    {'text': 'Alabang', 'value' : 'Alabang'},
    {'text': 'Two Neo', 'value' : 'Two Neo'},
    {'text': 'Market Market', 'value' : 'Market'},
    {'text': 'Bacolod', 'value' : 'Bacolod'},
];

export const Year_List = [
    {'text': '2016', 'value' : '2016'},
    {'text': '2017', 'value' : '2017'},
    {'text': '2018', 'value' : '2018'},
    {'text': '2019', 'value' : '2019'},
    {'text': '2020', 'value' : '2020'},
    {'text': '2021', 'value' : '2021'},
    {'text': '2022', 'value' : '2022'},
    {'text': '2023', 'value' : '2023'},
    {'text': '2024', 'value' : '2024'},
    {'text': '2025', 'value' : '2025'},
];

export const CL_Form_Type = [
    { "value": 2, "text": "Admin Form" },
    { "value": 3, "text": "HRIS Form" },
    { "value": 4, "text": "C&B Form" },
    { "value": 5, "text": "Final Pay Form" },
    { "value": 6, "text": "Manager Rating HRBP Site Lead Form" },
    { "value": 7, "text": "Onboarding Form" },
    { "value": 8, "text": "Payroll Form" },
    { "value": 9, "text": "Recruitment Form" },
    { "value": 10, "text": "Self Rating HRBP Form" },
    { "value": 11, "text": "Self Rating HRBP Site Lead  Form" },
    { "value": 12, "text": "Sourcing Form" },
    { "value": 13, "text": "Recruitment Supervisor Rating Form" },
    { "value": 14, "text": "Supervisor Rating HRBP Form" },
    { "value": 15, "text": "Training Form" },
];

export const Nte_Stage_Case = [
    'Awaiting Employee Reply',
    'Turnover of NTE to Ops',
    'For Admin Hearing',
    'NTE - For signature of authorized signatories',
    'NTE - Pending return of NTE to HR from Ops',
    'NTE Draft by HR',
    'NTE for Agent signature',
    'On Preventive Suspension',
];
