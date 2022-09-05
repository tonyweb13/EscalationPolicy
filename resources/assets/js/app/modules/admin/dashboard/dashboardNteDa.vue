<template>
    <div>
        <loading-modal v-if="isLoading" />
        <table style="background:white; width:100%;">
            <tr>
                <td class="pull-left col-lg-6">
                    <apex-charts width="600" type="bar" :options="chartOpenClosedCurrent" :series="seriesOpenClosedCurrent" />
                </td>
                <td class="pull-right col-lg-6">
                    <apex-charts width="600" type="bar" :options="chartOpenClosed" :series="seriesOpenClosed" />
                </td>
            </tr>
            <tr>
                <td class="pull-left col-lg-6">
                    <apex-charts width="600" type="bar" :options="chartTATCurrent" :series="seriesTATCurrent" />
                </td>
                <td class="pull-right col-lg-6">
                    <apex-charts width="600" type="bar" :options="chartTAT" :series="seriesTAT" />
                </td>
            </tr>
            <tr>
                <td class="col-lg-12">
                    <apex-charts style="margin-top:25px;" height="320" type="line" :options="chartYTD" :series="seriesYTD" />
                </td>
            </tr>
        </table>
    </div>
</template>
<script>
import {mapGetters} from 'vuex'
  export default {
    data() {
      return {
        isLoading: false,
        currentMonth: '',
        lastMonth: '',
        currentYear: '',
        chartOpenClosed: {},
        seriesOpenClosed: [
            {
                name: 'Open',
                data: []
            },
            {
                name: 'Closed',
                data: []
            },
        ],
        chartTAT: {},
        chartTATCurrent: {},
        seriesTAT: [
            {
                name: 'Within TAT',
                data: []
            },
            {
                name: 'Over TAT',
                data: []
            },
        ],
        seriesTATCurrent: [
            {
                name: 'Within TAT',
                data: []
            },
            {
                name: 'Over TAT',
                data: []
            },
        ],
        chartYTD: {},
        seriesYTD: [
            {
                name: 'Total Cases (Closed/Open)',
                type: 'column',
                data: []
            },
            {
                name: 'Increase/Decrease (Percentage)',
                type: 'line',
                data: []
            },
        ],
      }
    },
    created() {
        const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];

        let d = new Date();
        this.currentMonth = monthNames[d.getMonth()];
        this.lastMonth = monthNames[d.getMonth()-1];
        this.currentYear = d.getFullYear();
        let sixMonths = [
            monthNames[d.getMonth()-6], monthNames[d.getMonth()-5], monthNames[d.getMonth()-4], 
            monthNames[d.getMonth()-3], monthNames[d.getMonth()-2], monthNames[d.getMonth()-1]
        ];

        this.loadOpenClosedCurrent();
        this.loadOpenClosed(this.lastMonth, this.currentYear);

        this.loadTATCurrent(this.currentMonth, this.currentYear);
        this.loadTAT(this.lastMonth, this.currentYear);

        this.loadYTD(this.currentYear, sixMonths);
    },
    mounted() {
        window.Echo.channel("newOpenClosedCases").listen("OpenClosedCasesCreated", e => {
            this.loadOpenClosedCurrent();
        });
    },
    computed: {
        ...mapGetters([
            'seriesOpenClosedCurrent',
            'chartOpenClosedCurrent'
        ])
    },
    methods: {
        getChartOpenClosed(loadMonth, loadYear) {
        return {
                xaxis: {
                type: 'category',
                categories: ["Alabang", "Bacolod", "Market Market", "Two Neo"],
                },
                title: {
                        text: loadMonth + " " + loadYear + " | Total Open and Closed Cases",
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
                    // colors:['#1c84c6', '#23c6c8'] //change default color
                },
            }    
        },
        getChartTAT(loadMonth, loadYear) {

            let getText = ''
            if (loadMonth == this.currentMonth) {
                getText = loadMonth + " " + loadYear + " | Total Open And Closed Cases TAT Status"
            } else {
                getText = loadMonth + " " + loadYear + " | Total Closed Cases TAT Status"
            }

        return {
                xaxis: {
                type: 'category',
                categories: ["Alabang", "Bacolod", "Market Market", "Two Neo"],
                },
                title: {
                        text: getText,
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
        getChartYTD(loadYear, sixMonths) {
        return {
                xaxis: {
                type: 'category',
                categories: sixMonths,
                },
                title: {
                        text: "YTD " + loadYear + " | PH Total Cases Trend by Month",
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
                        shadeIntensity: 0.2,
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 50, 100],
                        colorStops: []
                    },
                },
                dataLabels: {
                    enabled: true
                },
                stroke: {
                    width: [4, 4]
                },
                plotOptions: {
                    bar: {
                        columnWidth: "30%"
                    }
                },
                yaxis: [
                    {
                        seriesName: 'Total Cases (Closed/Open)',
                        axisTicks: {
                            show: true
                        },
                    }, 
                    {
                        opposite: true,
                        seriesName: 'Increase/Decrease (Percentage)',
                        axisTicks: {
                            show: true
                        },
                    }
                ],
                tooltip: {
                    shared: false,
                    intersect: true,
                    x: {
                    show: false
                    }
                },
                legend: {
                    horizontalAlign: "center",
                    offsetX: 40
                }
            }    
        },
        loadOpenClosed(loadMonth, loadYear) {
            this.isLoading = true;
            this.$constants.Dashboard_API.get("/chart/count/cases/open/lastmonth")
            .then(response => {
                this.seriesOpenClosed[0].data = response.data;
                this.chartOpenClosed = this.getChartOpenClosed(loadMonth, loadYear);
                this.isLoading = false;
            })
            .catch(error => {
                    this.globalErrorHandling(error);
            });

            this.$constants.Dashboard_API.get("/chart/count/cases/closed/lastmonth")
            .then(response => {
                this.seriesOpenClosed[1].data = response.data;
                this.chartOpenClosed = this.getChartOpenClosed(loadMonth, loadYear);
                this.isLoading = false;
            })
            .catch(error => {
                    this.globalErrorHandling(error);
            });

        },
        loadOpenClosedCurrent() {
            let loadRequired = {
                constants: this.$constants.Dashboard_API,
                currentMonth: this.currentMonth,
                currentYear: this.currentYear,
                globalError: this.globalErrorHandling
            }
            this.$store.dispatch('storeOpenClosedCurrent', loadRequired)
        },
        loadTAT(loadMonth, loadYear) {
            this.isLoading = true;
            this.$constants.Dashboard_API.get("/chart/count/tat/within/lastmonth")
            .then(response => {
                this.seriesTAT[0].data = response.data;
                this.chartTAT = this.getChartTAT(loadMonth, loadYear);
                this.isLoading = false;
            })
            .catch(error => {
                    this.globalErrorHandling(error);
            });

            this.$constants.Dashboard_API.get("/chart/count/tat/over/lastmonth")
            .then(response => {
                this.seriesTAT[1].data = response.data;
                this.chartTAT = this.getChartTAT(loadMonth, loadYear);
                this.isLoading = false;
            })
            .catch(error => {
                    this.globalErrorHandling(error);
            });
        },
        loadTATCurrent(loadMonth, loadYear) {
            this.isLoading = true;
            this.$constants.Dashboard_API.get("/chart/count/tat/within/currentmonth")
            .then(response => {
                this.seriesTATCurrent[0].data = response.data;
                this.chartTATCurrent = this.getChartTAT(loadMonth, loadYear);
                this.isLoading = false;
            })
            .catch(error => {
                    this.globalErrorHandling(error);
            });

            this.$constants.Dashboard_API.get("/chart/count/tat/over/currentmonth")
            .then(response => {
                this.seriesTATCurrent[1].data = response.data;
                this.chartTATCurrent = this.getChartTAT(loadMonth, loadYear);
                this.isLoading = false;
            })
            .catch(error => {
                    this.globalErrorHandling(error);
            });
        },
        loadYTD(loadYear, sixMonths) {
            this.isLoading = true;
            this.$constants.Dashboard_API.get("/chart/count/ytd/totalcases")
            .then(response => {
                this.seriesYTD[0].data = response.data;
                this.chartYTD = this.getChartYTD(loadYear, sixMonths);
                this.isLoading = false;
            })
            .catch(error => {
                    this.globalErrorHandling(error);
            });

            this.$constants.Dashboard_API.get("/chart/count/ytd/incdec")
            .then(response => {
                this.seriesYTD[1].data = response.data;
                this.chartYTD = this.getChartYTD(loadYear, sixMonths);
                this.isLoading = false;
            })
            .catch(error => {
                    this.globalErrorHandling(error);
            });
        }
    }
  };
</script>