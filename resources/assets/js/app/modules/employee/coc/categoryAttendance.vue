<template>
    <div>
        <loading-modal v-if="isLoading" />
        <div v-if="categoriesProps">
            <the-ibox :title="categoryTitle">
                <v-client-table :data="rows" :columns="columns" :options="options">
                    <template slot="type_infraction" slot-scope="props">
                       <div v-html="props.row.type_infraction"/>
                    </template>
                    <template slot="attendancepoint" slot-scope="props">
                       <div style="width: 80px;">{{ props.row.attendancepoint.attendance_points }}</div>
                    </template>
                    <template slot="definition" slot-scope="props">
                       <div v-html="props.row.definition"/>
                    </template>
                </v-client-table>
            </the-ibox>
        </div>
    </div>
</template>
<script>

    export default {
        props: {
            categoriesProps: Number,
        },
        data: function () {
            return {
                isLoading: false,
                columns: ['type_infraction', 'attendancepoint', 'definition'],
                rows:  [],
                options: {
                    headings: {
                        'attendancepoint': 'Attendance Point'
                    },
                    sortable: ['type_infraction', 'attendancepoint', 'definition'],
                    filterable: ['type_infraction', 'attendancepoint', 'definition']
                },
                headerName: '',
                categoryTitle: '',
            }
        },
        created(){
            this.getList();
        },
        methods:{
            getList: function() {
                this.isLoading = true
                this.$constants.Coc_API.get("/attendance/points/system")
                    .then(response => {
                        this.rows = response.data;
                        this.isLoading = false
                        this.categoryTitle = "List of Attendance Point System";
                    })
                    .catch(error => {
                        this.globalErrorHandling(error);
                    });
            },
        }
    }
</script>
