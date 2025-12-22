<?php


namespace App\Helpers;

use App\Models\Backend\EmployeeAppliedJob;
use App\Models\Backend\FollowerHistory;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use phpDocumentor\Reflection\Types\Boolean;
use Xenon\LaravelBDSms\Facades\SMS;
use function Pest\Laravel\json;

class ViewHelper
{
    protected static $loggedUser, $courseOrder, $examOrder,$examOrders = [], $subscriptionPackage, $subscriptionOrder, $status = 'false';

    public static function checkViewForApi ($data=[], $viewPath = null, $jsonErrorMessage = null)
    {
        if (str()->contains(url()->current(), '/api/'))
        {
//            if (str()->contains(url()->current(), '/v1/'))
//            {
                if (empty($data))
                {
                    return response()->json(['status' => 'empty', 'msg' => 'No Data found.'], 200);
                } elseif (isset($jsonErrorMessage))
                {
                    return response()->json($jsonErrorMessage , 422);
                }
                return response()->json($data, 200);
//            }
        } else {

            return view($viewPath, $data);
        }
    }
    public static function returnBackViewAndSendDataForApiAndAjax ($data=[], $viewPath = null, $jsonErrorMessage = null, $successMessage = null, $isReturnBack = false)
    {
        if (str()->contains(url()->current(), '/api/') || \request()->ajax())
        {
//            if (str()->contains(url()->current(), '/v1/'))
//            {
                if (empty($data))
                {
                    return response()->json(['status' => 'empty', 'msg' => 'No Data found.'], 200);
                } elseif (isset($jsonErrorMessage))
                {
                    return response()->json($jsonErrorMessage , 400);
                } elseif (\request()->ajax() && $viewPath != null)
                {
//                    return view($viewPath, $data)->render();
                    return response()->json(view($viewPath, $data)->render());
                }
                return response()->json($data, 200);
//            }
        } else {
            if ($viewPath != null)
            {
                return view($viewPath, $data);
            } elseif (count($data) > 0)
            {
                Toastr::success($data['msg']);
                return back()->with('data', $data);
            } elseif ($isReturnBack)
            {
                return back()->with('success', $successMessage);
            } else {
                return back();
            }
        }
    }

    public static function returnDataForAjaxAndApi($data = [])
    {
        if (empty($data))
        {
            return response()->json(isset($jsonErrorMessage) ? $jsonErrorMessage : 'Something went wrong. Please try again.', 400);
        }
        return response()->json($data, 200);
        if (str()->contains(url()->current(), '/api/'))
        {
//            if (str()->contains(url()->current(), '/v1/'))
//            {

                return response()->json($data, 200);
//            }
        } else {

            return response()->json($data);
        }
    }

    public static function returEexceptionError ($message = null)
    {
        if (str()->contains(url()->current(), '/api/') || \request()->ajax())
        {
            return response()->json(['error' => $message, 'status' => 'error'], 422);
        } else {
            Toastr::error($message);
            return back()->with('error', $message);
        }
    }
    public static function returnRedirectWithMessage ($route, $messageType = 'success', $message = null)
    {
        if (str()->contains(url()->current(), '/api/') || \request()->ajax())
        {
            if ($messageType == 'error')
            return response()->json(['error' => $message, 'status' => 'error'], 422);
                else
            return response()->json(['success' => $message, 'status' => 'success'], 200);
        } else {
            $messageType == 'error' ? Toastr::error($message) : Toastr::success($message);
            return redirect($route);
        }
    }

    public static function returnResponseFromPostRequest($status = false, $message = '')
    {
        if (str()->contains(url()->current(), '/api/') || \request()->ajax())
        {
            return response()->json(['status' => $status  ? 'success' : 'error', 'msg' => $message]);
        } else {
            $status ? Toastr::success($message) : Toastr::error($message);
            return back();
        }
    }
    public static function returnSuccessMessage($message = null)
    {
        if (str()->contains(url()->current(), '/api/') || \request()->ajax())
        {
            return response()->json(['success' => $message, 'status' => 'success'], 200);
        } else {
            Toastr::success($message);
            return back()->with('success', $message);
        }
    }

    public static function authCheck()
    {
        if (str_contains(url()->current(), '/api/'))
        {
            return auth('sanctum')->check();
        } else {
            return auth()->check();
        }
    }

    public static function loggedUser()
    {
        if (str_contains(url()->current(), '/api/'))
        {
            $loggedUser = auth('sanctum')->user();
            if ($loggedUser && $loggedUser->user_type == 'employer')
            {
                return $loggedUser->load('employerCompanies');
            } else {
                return $loggedUser;
            }
        } else {
            return auth()->user();
        }
    }

    public static function checkIfUserApprovedOrBlocked($user)
    {
        $status = false;
        if ($user->is_approved == 0 || $user->status == 'blocked')
            $status = true;

        return $status;
    }

    public static function checkIfUserHasValidSubscription ()
    {
        if (str_contains(url()->current(), '/api/'))
        {
            self::$loggedUser = auth('sanctum')->user();
        } else {
            self::$loggedUser = auth()->user();
        }
        if (!empty(self::$loggedUser->subscriptionOrders))
        {
            foreach (self::$loggedUser->subscriptionOrders as $subscriptionOrder)
            {
                if ($subscriptionOrder->examSubscriptionPackage->valid_to > Carbon::today()->format('d-m-Y'))
                {
                    self::$status = 'true';
                    break;
                }
            }
        }
        return self::$status;
    }

    public static function startQueueWorkManuallyByArtisanCommand()
    {
        // Manually process the queue
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--tries' => 1,
            '--timeout' => 60
        ]);
    }

    public static function sendSms($number = '01646688970', $message = '')
    {
        try {
            SMS::shoot($number, $message);
            return ['status' => 'success'];
        } catch (\Exception $exception)
        {
            return ['status' => 'error', 'msg' => $exception->getMessage()];
        }
    }

    public static function generateOtp($mobile = '01646688970')
    {
        $generate_otp = rand(1000, 9999);
        session()->put('otp', $generate_otp);
        if (str()->contains(url()->current(), '/api/'))
        {
            Cache::put('otp_' . $mobile, $generate_otp, now()->addMinutes(5));
        }
        return $generate_otp;
    }

    public static function getSessionOtp($mobile = '01646688970')
    {
        if (str()->contains(url()->current(), '/api/'))
        {
            $cachedOtp = Cache::get('otp_' . $mobile);
        } else {
            $cachedOtp = session('otp');
        }
        return $cachedOtp;
    }

    public static function getJobSaveApplyInfo($id)
    {
        $isSaved = false;
        $isApplied = false;
        if (ViewHelper::loggedUser())
        {
            $user = ViewHelper::loggedUser();
            if ($user->roles[0]->id == 3 )
            {
                $savedJobsIds = $user->employeeSavedJobs->pluck('id')->toArray();
                $isSaved = in_array($id, $savedJobsIds);
                if (EmployeeAppliedJob::where(['user_id' => $user->id, 'job_task_id' => $id])->first())
                    $isApplied = true;
            }
        }
        return [
            'isSaved'   => $isSaved,
            'isApplied'   => $isApplied,
        ];
    }

    public static function placeSubscriptionOrder($user_id, $subscription_id)
    {
        try {
            $loggedUser = User::find($user_id);
            $loggedUser->subscription_plan_id  = $subscription_id;
            $loggedUser->subscription_started_from  = now();
            $loggedUser->subscription_end_date  = Carbon::now()->addDays($subscriptionPlan->duration_in_days ?? 0);
            $loggedUser->save();
            if ($loggedUser->user_type == 'employer')
            {
                if (count($loggedUser->users) > 0)
                {
                    foreach ($loggedUser->users as $subUser)
                    {
                        $subUser->subscription_plan_id  = $subscription_id;
                        $subUser->subscription_started_from  = now();
                        $subUser->subscription_end_date  = Carbon::now()->addDays($subscriptionPlan->duration_in_days ?? 0);
                        $subUser->save();
                    }
                }
            }
            return ['status' => 'success', 'msg' => 'Your subscription plan is active.'];
        } catch (\Exception $exception)
        {
            return ['status' => 'error', 'msg' => $exception->getMessage()];
        }
    }

    public static function saveImagePathInJson($imageFileObject, $imageDirectory, $imageNameString = null, $width = null, $height = null, $previousJsonString = null)
    {
        if ($previousJsonString)
        {
            foreach (json_decode($previousJsonString) as $previousImage)
            {
                if (file_exists($previousImage))
                {
                    unlink($previousImage);
                }
            }
        }
        $imageFileString = [];
        if ($imageFileObject)
        {
            foreach ($imageFileObject as $key => $image)
            {
                $imageFileString[] = imageUpload($image, $imageDirectory, $imageNameString, $width, $height);
            }

        }
        return json_encode($imageFileString);
    }

    public static function checkFollowHistory($companyEmployerId, $followerId, $unfollowStatus = 0)
    {
        $existHistory = FollowerHistory::where(['employer_id' => $companyEmployerId, 'follower_id' => $followerId])->first();
        if ($existHistory)
        {
            return true;
        }
        return false;
    }

    public static function getDurationAmongTwoDates($startDate, $endDate, $durationUnit = 'years', $isEndDateIsCurrentDate = false)
    {
        $duration = 0;
        $start = Carbon::parse($startDate);
        if ($isEndDateIsCurrentDate)
            $end = Carbon::parse(now());
        else
            $end = Carbon::parse($endDate);
        if ($durationUnit == 'years')
            $duration = $start->diffInYears($end);
        elseif ($durationUnit == 'months')
            $duration = $start->diffInMonths($end);
        elseif ($durationUnit == 'days')
            $duration = $start->diffInDays($end);

        $duration = max(1, $duration);
        return (int) round($duration);
    }

    public static function checkIfRequestFromApi()
    {
        if (str()->contains(url()->current(), '/api/'))
            return true;
        else
            return false;
    }
}
