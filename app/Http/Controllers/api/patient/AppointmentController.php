<?php

namespace App\Http\Controllers\api\patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    //==================================================================//
    //==================================================================//

    public function index()
    {

        $appointments = Auth::user()->appointments()->get();

        return response()->json(['appointments' => AppointmentResource::collection($appointments)], 200);
    }
    //==================================================================//
    //==================================================================//
    public function show(Appointment $appointment)
    {
        if ($appointment->user_id != Auth::user()->id) {
            return response()->json([
                'error' => 'forbidden access'
            ], 403);
        }
        return response()->json(['appointment' => new AppointmentResource($appointment)], 200);
    }

    //==================================================================//
    //==================================================================//

    public function store(Request $request)
    {
        try {

            // create date from the request
            $date = Carbon::parse($request->date);

            // Check if the minute of the date is either 00 or 30
            if ($date->minute !== 0 && $date->minute !== 30) {
                return response()->json(['status' => 'error', 'message' => 'Appointment time should be on hour or half hour.'], 400);
            }

            // Check if the time is within working hours (08h to 16h)
            if ($date->hour < 8 || $date->hour >= 16) {
                return response()->json(['status' => 'error', 'message' => 'Appointment time should be within working hours (08h to 16h).'], 400);
            }

            // Check if the doctor is available at the specified date and time
            $existingAppointment = Appointment::where('doctor_id',  $request->doctor_id)
                ->where('date', $date)
                ->first();
            if ($existingAppointment) {
                return response()->json(['status' => 'error', 'message' => 'Doctor is already booked at the specified date and time.'], 400);
            }


            DB::beginTransaction();

            // Create a new appointment
            $appointment = Auth::user()->appointments()->create([
                'doctor_id' => $request->doctor_id,
                'date' => $date,
                'reason' => $request->reason,
            ]);


            DB::commit();


            // Return the success response
            return response()->json(['status' => 'success', 'message' => 'Appointment booked successfully.'], 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => "error"], 401);
        }
    }
    //==================================================================//
    //==================================================================//
    public function getAvailabilityDates(Request $request, Doctor $doctor)
    {
        // doctor's working hours
        $startHour = 8;
        $endHour = 16;

        // Get start date and end date from request
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);


        // Get all the appointments of the doctor
        $appointments = $doctor->appointments()->get();

        // Initialize an empty array to store the available datetimes
        $availableDates = array();
        $availableDatesDays = array();


        for ($day_date = $start_date; $day_date->lessThan($end_date); $day_date->addDay()) {
            $hoursInDay = array();
            for ($hour = $startHour; $hour < $endHour; $hour++) {

                for ($minute = 0; $minute < 60; $minute += 30) {
                    $datetime = Carbon::parse($day_date)->hour($hour)->minute($minute)->second(0);
                    $isAvailable = true;
                    // Check if the doctor has an appointment at this datetime
                    foreach ($appointments as $appointment) {
                        if ($datetime->format('Y-m-d H:i:s') === $appointment->date) {
                            $isAvailable = false;
                            break;
                        }
                    }
                    array_push($hoursInDay,  ['date' => $datetime->format('Y-m-d H:i:s'), 'isAvailable' => $isAvailable]);
                    array_push($availableDates,  ['date' => $datetime->format('Y-m-d H:i:s'), 'isAvailable' => $isAvailable]);
                }
            }
            array_push($availableDatesDays, ["date" . $day_date => $hoursInDay]);
        }

        // Return the available datetimes as a response
        //available_dates is all available Dates
        //available_dates_days is all available Dates by day
        return response()->json([
            'available_dates' => $availableDates,
            'available_dates_days' => $availableDatesDays,
        ], 200);
    }

    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//
    //==================================================================//

}
