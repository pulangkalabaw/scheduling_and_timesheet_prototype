<template>
	<div>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
			<a class="navbar-brand" href="#">Navbar</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">

				</ul>
				<form class="form-inline my-2 my-lg-0">
					<button @click="clock()" class="btn btn-primary my-2 my-sm-0" type="submit">
					{{ button_caption }}
				</button>
				</form>
			</div>
		</nav>

		<div class="container mt-5">
			<div class="row">
				{{ moment().format('Y-MM-DD H:mm:ss') }}
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							Timesheet
						</div>

						<table class="table">
							<thead>
								<tr>
									<th>Name</th>
									<th>Time In</th>
									<th>Time Out</th>
									<th>Schedule</th>
									<th>Late </th>
									<th>Undertime </th>
									<th>Total worked hours</th>
								</tr>
							</thead>

							<tbody v-for="ts in timesheets" v-if="timesheets.length != 0">
								<tr>
									<td>
										{{ ts.get_user.name }}
									</td>
									<td>
										{{ ts.td_in }}
									</td>
									<td>
										{{ ts.td_out }}
									</td>
									<td>
										{{ ts.get_schedule.shift_start }} -
										{{ ts.get_schedule.shift_end }}
									</td>
									<td>
										{{ ts.late }}  min(s)
									</td>
									<td>
										{{ ts.undertime }}  min(s)
									</td>
									<td>
										{{ (ts.worked_mins / 60).toFixed(2) }}
									</td>
								</tr>
							</tbody>
							<tbody v-else>
								<tr>
									<td colspan="3">
										No data yet!
									</td>
								</tr>
							</tbody>
						</table>

					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {

	data() {
		return {
			timesheets: [],
			button_caption: 'Time in',
		}
	},

	created () {
		this.getIndex()
	},

	methods: {
		getIndex () {
			this.$axios.get('http://localhost/www/iplus/prototype/td_prototype/api/ts?uid=1')
			.then(res => {
				this.timesheets = res.data.data
				console.log(res.data)
			})
			.catch(err => console.log(err))
		},

		clock() {
			let current_moment = this.moment().utc().format('X')

			this.$axios.get('http://localhost/www/iplus/prototype/td_prototype/api/ts/add?uid=1&time=' + current_moment)
			.then(res => {
				if (res.data.data.td_out == null) {
					this.button_caption = "Time Out"
				}
				else {
					this.button_caption = "Time In"
				}

				this.getIndex();
			})
			.catch(err => console.log(err))
		}
	}
}
</script>

<style>
</style>
