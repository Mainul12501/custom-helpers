<?php

namespace Mainul\CustomHelperFunctions\Helpers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class CustomHelper
{
//    return view or data for web or api
    public static function returnDataForWebOrApi(
        array $data = [],
        ?string $viewPath = null,
        ?string $jsonErrorMessage = null,
        bool $isForRender = false,
        bool $isReturnBack = false,
        ?string $successMsg = null
    ){
        if (self::isApiRequest() || request()->ajax())
        {
            if (empty($data))
            {
                return response()->json(['status' => 'empty', 'msg' => 'No Data found.'], 400);
            }
            if (isset($jsonErrorMessage))
            {
                return response()->json($jsonErrorMessage , 400);
            }
            if (\request()->ajax() &&  !is_null($viewPath))
            {
                return response()->json(view($viewPath, $data)->render());
            }
        } else {
            if (!is_null($viewPath)) {
                if ($isForRender)
                    return view($viewPath, $data)->render();
                else
                    return view($viewPath, $data);
            }
            if ($isReturnBack)
            {
                Toastr::success($successMsg);
                return back()->with('success', $successMsg);
            }
            Toastr::error('Something went wrong. Please try again');
            return back();
        }
    }
//    check if request for api
    public static function isApiRequest(): bool
    {
        $route = request()->route();

        return $route && in_array('api', $route->gatherMiddleware());
    }
//    check if request is ajax
    public static function isAjax(): bool
    {
        return request()->ajax();
    }

    public static function wantsJsonResponse(): bool
    {
        return self::isAjax() || self::isApiRequest();
    }
//    return error message
    public static function returErrorMessage ($message = null, $customMsg = null)
    {
        if (self::isApiRequest() || self::isAjax())
        {
            return response()->json(['message' => $customMsg ?? $message, 'status' => 'error'], 422);
        } else {
            Toastr::error($message);
            return back()->with('error', $message);
        }
    }
//    return success message
    public static function returnSuccessMessage($message = null)
    {
        if (self::isApiRequest() || self::isAjax())
        {
            return response()->json(['message' => $message, 'status' => 'success'], 200);
        } else {
            Toastr::success($message);
            return back()->with('success', $message);
        }
    }
//    redirect to other page with custom message
    public static function returnRedirectWithMessage ($route, $messageType = 'success', $message = null)
    {
        if (self::isApiRequest() || self::isAjax())
        {
            if ($messageType == 'error')
                return response()->json(['message' => $message, 'status' => 'error'], 422);
            else
                return response()->json(['message' => $message, 'status' => 'success'], 200);
        } else {
            $messageType == 'error' ? Toastr::error($message) : Toastr::success($message);
            return redirect($route)->with($messageType, $message);
        }
    }
//    check auth status
    public static function authCheck()
    {
        if (self::isApiRequest())
        {
            return auth('sanctum')->check();
        } else {
            return auth()->check();
        }
    }
//    get logged user info
    public static function loggedUser()
    {
        if (self::isApiRequest())
        {
            return auth('sanctum')->user();
        } else {
            return auth()->user();
        }
    }
//    start queue work manually by artisan command
    public static function startQueueWorkManuallyByArtisanCommand()
    {
        // Manually process the queue
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--tries' => 1,
            '--timeout' => 60
        ]);
    }
//    generate Code for OTP(supports random[alpha+number], alpha, number)
    public static function generateCode(int $length = 2, string $type = 'number'): string
    {
        // Enforce minimum length
        $length = max(2, $length);

        // Define character pools
        $pools = [
            'number' => '0123456789',
            'alpha'  => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'random' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        ];

        // Fallback to random if invalid type
        $characters = $pools[$type] ?? $pools['random'];

        $otp = '';
        $maxIndex = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $otp .= $characters[random_int(0, $maxIndex)];
        }

        return $otp;
    }
    //    set generated code from session
    public static function generateSessionCode(int $length = 2, string $type = 'number', string $sessionKey = 'session_key', int $expiryMinutes = 5)
    {
        $generate_code = self::generateCode($length, $type);
        // session()->put('generate_code', $generate_code);
		session()->put('generate_code', [
			'code' => $generate_code,
			'expires_at' => now()->addMinutes($expiryMinutes)->timestamp
		]);
        if (self::isApiRequest())
            Cache::put('code_'.$sessionKey, $generate_code, now()->addMinutes(expiryMinutes));
        return $generate_code;
    }
//    get generated code from session
    public static function getSessionCode($sessionKey = 'session_key')
    {
        // if (self::isApiRequest())
            // $generate_code = Cache::get('code_'.$sessionKey);
        // else
            // $generate_code = session('generate_code');
		
	if (self::isApiRequest()) {
        $generate_code = Cache::get('code_'.$sessionKey);
    } else {
        $data = session('generate_code');
        
        // Check if data exists and has not expired
        if ($data && is_array($data)) {
            if (now()->timestamp > $data['expires_at']) {
                session()->forget('generate_code');
                return null;
            }
            $generate_code = $data['code'];
        } else {
            $generate_code = $data; // Backward compatibility for old format
        }
    }

        return $generate_code;
    }
//    get duration among two dates
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

//    file upload related functions
    public static function getFileExtension($file)
    {
        return $file->extension();
    }
    public static function getFileType($file)
    {
        return $file->getMimeType();
    }

//    date related functions
    public static function showDate($date = null)
    {
        $date = $date ?? now();
        return Carbon::parse($date)->format('d-m-Y');
    }
    public static function showTime($date = null)
    {
        $date = $date ?? now();
        return Carbon::parse($date)->format('g:i A');
    }
    public static function showDateTime($date = null)
    {
        $date = $date ?? now();
        return Carbon::parse($date)->format('d-m-Y g:i A');
    }
    public static function showDateTime24Hours($date = null)
    {
        $date = $date ?? now();
        return Carbon::parse($date)->format('d-m-Y H:i');
    }

    public static function showDateForBlogType($date = null)
    {
        $date = $date ?? now();
        return Carbon::parse($date)->format('F d, Y');
    }

    public static function dateWithTime($date = null)
    {
        $date = $date ?? now();
        return Carbon::parse($date)->format('Y-m-d H:i');
    }
    public static function currentDateWithTime()
    {
        return Carbon::now()->format('Y-m-d H:i');
    }

    public static function differTime($start, $end, $info = 'full')
    {
        $startTime = Carbon::parse($start);
        $endTime   = Carbon::parse($end);

        if ($startTime->greaterThan($endTime)) {
            [$startTime, $endTime] = [$endTime, $startTime];
        }

        $diff = $startTime->diff($endTime);

        $units = [
            'year'   => $diff->y,
            'month'  => $diff->m,
            'day'    => $diff->d,
            'hour'   => $diff->h,
            'minute' => $diff->i,
            'second' => $diff->s,
        ];

        // Filter based on requested info
        if ($info !== 'full') {
            $units = array_slice($units, 0, array_search($info, array_keys($units)) + 1);
        }

        $duration = [];

        foreach ($units as $label => $value) {
            if ($value > 0) {
                $duration[] = $value . ' ' . $label . ($value > 1 ? 's' : '');
            }
        }

        return $duration
            ? implode(' ', $duration)
            : 'Just now';
    }

    public static function clearRouteCache()
    {
        Artisan::call('route:clear');
    }
    public static function CacheRoute()
    {
        Artisan::call('route:cache');
    }
    public static function optimizeClear()
    {
        Artisan::call('optimize:clear');
    }
    public static function clearCache()
    {
        Artisan::call('cache:clear');
    }

//    file upload functions
    public static function fileUpload ($fileObject, $directory, $nameString = null, $modelFileUrl = null)
    {
        if ($fileObject)
        {
            if (isset($modelFileUrl) && file_exists($modelFileUrl))
            {
                unlink($modelFileUrl);
            }
            $fileName       = $nameString.'-'.str_replace(' ', '-', pathinfo($fileObject->getClientOriginalName(), PATHINFO_FILENAME)).'_'.rand(100,100000).'.'.$fileObject->extension();
            $fileDirectory  = 'backend/assets/uploaded-files/'.$directory.'/';
            $fileObject->move($fileDirectory, $fileName);
            return $fileDirectory.$fileName;
        } else {
            if (isset($modelFileUrl))
            {
                return $modelFileUrl;
            } else {
                return null;
            }
        }
    }

    function fileUploadByBase64($base64String, $imageDirectory, $imageNameString = null, $modelFileUrl = null)
    {
        if ($base64String)
        {
            if (isset($modelFileUrl))
            {
                if (file_exists($modelFileUrl))
                {
                    unlink($modelFileUrl);
                }
            }
            $folderPath = public_path('backend/assets/uploaded-files/'.rtrim($imageDirectory));
            if (!File::isDirectory($folderPath))
            {
                File::makeDirectory($folderPath, 0777, true, true);
            }
        }
        // Remove data:image/jpeg;base64, prefix
        $imageData = substr($base64String, strpos($base64String, ',') + 1);
        $imageData = base64_decode($imageData);

        // Generate unique filename
        $filename = $imageNameString.'file_' . time() . '_' . uniqid() . '.jpg';
        $path = '/backend/assets/uploaded-files/'.$filename;
        // Save the file
        file_put_contents(public_path($path), $imageData);
        return $path;
    }

}
