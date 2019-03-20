<?php

namespace App\Http\Controllers;

use Validator;

use App\Timesheet;
use App\UserSched;
use App\Schedule;
use Carbon\Carbon;

use Illuminate\Http\Request;

class TimesheetController extends Controller
{

	public function makeCarbon ($date) {
		return Carbon::parse($date);
	}

	/**
	* Get Edtr
	* @return [type] [description]
	*/

	public function index (Request $request) {

		$ts = new Timesheet();
		if ($request->get('uid')) {
			$ts = $ts->where('user_id', $request->get('uid'));
		}

		$ts = $ts->orderBy('id', 'desc')->with(['getUser','getSchedule'])->get();

		return $this->apiReturn('Success', 'success', $ts);
	}

	/**
	 * Store
	 *
	 * @return [type] [description]
	 */
	public function store (Request $request) {

		$ts = new Timesheet();

		$v = Validator::make($request->all(), [
			'uid' => 'required',
			'time' => 'nullable',
		]);

		if ($v->fails()) {
			return $this->apiReturn('Validation Failed', 'failed', [], $v->errors());
		}

		/**
		 *	Time Sheet Store
		 *		Hey! First, we need to validate
		 *		if its New in (Time in) or Out (Time out)
		 *
		 * 		Check the last row, if exist
		 * 			Check if its time in is not null, if NOT Null
		 * 				** Its TIME OUT
		 * 			If Null,
		 * 				** Its TIME In
		 *
		 * 		If no row retrieve
		 * 				** Its TIME In
		 *
		 *	-------------------------------------------
		 *	Last update: 03-18-2019
		 *	@Khurt
		 */

		// Time in or out Handler
		$col = '';

		// Get the last row
		$last_row = $ts->orderBy('id', 'desc')
		->where('user_id', $request->get('uid'))
		->with(['getUser','getSchedule'])
		->whereDate('created_at', Carbon::today())
		->limit(1)
		->first();

		$request['time'] = date('Y-m-d H:i:s', $request->time);

		// If it is has a record
		if (!empty($last_row)) {

			// ** Its Time out
			if (empty($last_row->td_out)) {

				// We need to calculate the UNDERTIME
				// and WORKED HOURS
				$td_in = $this->makeCarbon($last_row->td_in);
				$td_out = $this->makeCarbon($request->get('time'));

				// Computing
				$worked_mins = $td_in->diffInMinutes($td_out);
				$undertime = 0;

				// Get users schedule's shift end
				$shift_end = strtotime($last_row->getSchedule->shift_end);

				// Condition if undetime
				if ($shift_end >= strtotime($request->get('time'))) {
					// if so, compute undertime in mins
					$time_carbon = $this->makeCarbon(date('H:i:s', strtotime($request->get('time'))));
					$undertime = $this->makeCarbon(date('H:i:s', $shift_end))->diffInMinutes($time_carbon);
				}


				// Update data
				$update_data = [
					'td_out' =>  $td_out,
					'undertime' => $undertime,
					'worked_mins' =>  $worked_mins,
				];

				// Do update
				if ($last_row->update($update_data)) {
					return $this->apiReturn('Update complete!', 'success', $last_row, []);
				}

				$col = 'td_out';
			}

			// ** Its Time in
			else {

				// We need to calculate the UNDERTIME
				// and WORKED HOURS
				$td_in = $this->makeCarbon($request->get('time'));

				$late = 0;

				// Get users schedule's shift end
				$shift_start = strtotime($last_row->getSchedule->shift_start);
				// Condition if Late

				if($shift_start <= strtotime($request->get('time'))){
					// if so, compute lates in mins
					$time_carbon = $this->makeCarbon(date('H:i:s', strtotime($request->get('time'))));
					$late = $this->makeCarbon(date('H:i:s', $shift_start))->diffInMinutes($time_carbon);
				}
				// Update data
				$new_data = [
					'user_id' => $request->get('uid'),
					'td_in' => $td_in,
					'late' => $late,
					'sched_id' => 1111,
				];

				// Do update
				if ($new_row = $ts->create($new_data)) {
					return $this->apiReturn('Insert complete!', 'success', $new_row, []);
				}

				$col = 'td_in';

			}
		}


		// No record at all
		// ** Its Time in
		else {

			$us = new UserSched();
			$sched_id = $us->where('user_id',$request->get('uid'))->value('sched_id');

			// We need to calculate the UNDERTIME
			// and WORKED HOURS
			$td_in = $this->makeCarbon($request->get('time'));

			$late = 0;
			// Get users schedule's shift end
			// possible bugs if user has multiple schedule
			$sched = new Schedule();
			$sched_shift_start = $sched->where('sched_id',$sched_id)->value('shift_start');
			//convert to string time
			$shift_start = strtotime($sched_shift_start);
			// Condition if Late
			if($shift_start <= strtotime($request->get('time'))){
				// if so, compute lates in mins
				$time_carbon = $this->makeCarbon(date('H:i:s', strtotime($request->get('time'))));
				$late = $this->makeCarbon(date('H:i:s', $shift_start))->diffInMinutes($time_carbon);
			}
			// if so, compute lates in mins

			// Update data
			$new_data = [
				'user_id' => $request->get('uid'),
				'td_in' => $td_in,
				'late' => $late,
				'sched_id' => 1111,
			];

			// Do update
			if ($new_row = $ts->create($new_data)) {
				return $this->apiReturn('Insert complete!', 'success', $new_row, []);
			}


			$col = 'td_in';
		}

	}


	public function apiReturn ($message, $status, $data = [], $validation_errors = []) {
		return response()->json([
			'data' => $data,
			'status' => $status,
			'message' => $message,
			'validation_errors' => $validation_errors,
		]);
	}
}
