<template>
    <div>
		<the-ibox title="">
			<v-client-table
				:data="rows"
				:columns="columns"
				:options="options"
			/>

			<behavioral-management-flowchart/>
		</the-ibox>
    </div>
</template>
<script>

    export default {
		data: function () {
            return {
				columns: [
					'gravity_offense.gravity',
					'first_occur.name',
					'second_occur.name',
					'third_occur.name',
					'fourth_occur.name',
					'fifth_occur.name',
					'sixth_occur.name',
					'seventh_occur.name'
				],
				rows:  [],
				options: {
					headings: {
						'gravity_offense.gravity': 'Gravity of Offense',
						'first_occur.name': '1st Occurrence',
						'second_occur.name': '2nd  Occurrence',
						'third_occur.name': '3rd  Occurrence',
						'fourth_occur.name': '4th  Occurrence',
						'fifth_occur.name': '5th  Occurrence',
						'sixth_occur.name': '6th  Occurrence',
						'seventh_occur.name': '7th Occurrence'
                    },
                    sortable: [],
                    filterable: []
                },
            }
        },
        created() {
            this.$constants.Coc_API.get("/behavioralaction")
			.then(response => {
                this.rows = response.data;
            })
            .catch(error => {
                this.globalErrorHandling(error);
            });
        }
    }
</script>
