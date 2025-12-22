<?php

namespace Mainul\CustomHelperFunctions\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ViewHelper
{
    protected static $loggedUser, $courseOrder, $examOrder, $examOrders = [], $subscriptionPackage, $subscriptionOrder, $status = 'false';

    /**
     * Check if request is for API and return appropriate response
     */
    public static function checkViewForApi($data = [], $viewPath = null, $jsonErrorMessage = null)
    {
        if (str()->contains(url()->current(), '/api/')) {
            if (empty($data)) {
                return response()->json(['status' => 'empty', 'msg' => config('helper-functions.view_helper.no_data_message', 'No Data found.')], 200);
            } elseif (isset($jsonErrorMessage)) {
                return response()->json($jsonErrorMessage, 422);
            }
            return response()->json($data, 200);
        } else {
            return view($viewPath, $data);
        }
    }

    /**
     * Return back view and send data for API and AJAX requests
     */
    public static function returnBackViewAndSendDataForApiAndAjax($data = [], $viewPath = null, $jsonErrorMessage = null, $successMessage = null, $isReturnBack = false)
    {
        if (str()->contains(url()->current(), '/api/') || request()->ajax()) {
            if (empty($data)) {
                return response()->json(['status' => 'empty', 'msg' => config('helper-functions.view_helper.no_data_message', 'No Data found.')], 200);
            } elseif (isset($jsonErrorMessage)) {
                return response()->json($jsonErrorMessage, 400);
            } elseif (request()->ajax() && $viewPath != null) {
                return response()->json(view($viewPath, $data)->render());
            }
            return response()->json($data, 200);
        } else {
            if ($viewPath != null) {
                return view($viewPath, $data);
            } elseif (count($data) > 0) {
                if (class_exists('\Brian2694\Toastr\Facades\Toastr')) {
                    \Brian2694\Toastr\Facades\Toastr::success($data['msg']);
                }
                return back()->with('data', $data);
            } elseif ($isReturnBack) {
                return back()->with('success', $successMessage);
            } else {
                return back();
            }
        }
    }

    /**
     * Return data for AJAX and API requests
     */
    public static function returnDataForAjaxAndApi($data = [])
    {
        if (empty($data)) {
            return response()->json(isset($jsonErrorMessage) ? $jsonErrorMessage : config('helper-functions.view_helper.default_error_message', 'Something went wrong. Please try again.'), 400);
        }
        return response()->json($data, 200);
    }

    /**
     * Return exception error response
     */
    public static function returnExceptionError($message = null)
    {
        if (str()->contains(url()->current(), '/api/') || request()->ajax()) {
            return response()->json(['error' => $message, 'status' => 'error'], 422);
        } else {
            if (class_exists('\Brian2694\Toastr\Facades\Toastr')) {
                \Brian2694\Toastr\Facades\Toastr::error($message);
            }
            return back()->with('error', $message);
        }
    }

    /**
     * Return redirect with message
     */
    public static function returnRedirectWithMessage($route, $messageType = 'success', $message = null)
    {
        if (str()->contains(url()->current(), '/api/') || request()->ajax()) {
            if ($messageType == 'error') {
                return response()->json(['error' => $message, 'status' => 'error'], 422);
            } else {
                return response()->json(['success' => $message, 'status' => 'success'], 200);
            }
        } else {
            if (class_exists('\Brian2694\Toastr\Facades\Toastr')) {
                $messageType == 'error' ? \Brian2694\Toastr\Facades\Toastr::error($message) : \Brian2694\Toastr\Facades\Toastr::success($message);
            }
            return redirect($route);
        }
    }

    /**
     * Return response from POST request
     */
    public static function returnResponseFromPostRequest($status = false, $message = '')
    {
        if (str()->contains(url()->current(), '/api/') || request()->ajax()) {
            return response()->json(['status' => $status ? 'success' : 'error', 'msg' => $message]);
        } else {
            if (class_exists('\Brian2694\Toastr\Facades\Toastr')) {
                $status ? \Brian2694\Toastr\Facades\Toastr::success($message) : \Brian2694\Toastr\Facades\Toastr::error($message);
            }
            return back();
        }
    }

    /**
     * Return success message
     */
    public static function returnSuccessMessage($message = null)
    {
        if (str()->contains(url()->current(), '/api/') || request()->ajax()) {
            return response()->json(['success' => $message, 'status' => 'success'], 200);
        } else {
            if (class_exists('\Brian2694\Toastr\Facades\Toastr')) {
                \Brian2694\Toastr\Facades\Toastr::success($message);
            }
            return back()->with('success', $message);
        }
    }

    /**
     * Check if user is authenticated
     */
    public static function authCheck()
    {
        if (str_contains(url()->current(), '/api/')) {
            $guard = config('helper-functions.view_helper.api_guard', 'sanctum');
            return auth($guard)->check();
        } else {
            return auth()->check();
        }
    }

    /**
     * Get logged-in user
     */
    public static function loggedUser()
    {
        if (str_contains(url()->current(), '/api/')) {
            $guard = config('helper-functions.view_helper.api_guard', 'sanctum');
            return auth($guard)->user();
        } else {
            return auth()->user();
        }
    }

    /**
     * Check if user is approved or blocked
     */
    public static function checkIfUserApprovedOrBlocked($user)
    {
        $status = false;
        if ($user->is_approved == 0 || $user->status == 'blocked') {
            $status = true;
        }
        return $status;
    }

    /**
     * Generate OTP
     */
    public static function generateOtp($mobile = null)
    {
        $min = config('helper-functions.otp.min', 1000);
        $max = config('helper-functions.otp.max', 9999);
        $generate_otp = rand($min, $max);

        session()->put('otp', $generate_otp);

        if (str()->contains(url()->current(), '/api/') && $mobile) {
            $expiryMinutes = config('helper-functions.otp.expiry_minutes', 5);
            Cache::put('otp_' . $mobile, $generate_otp, now()->addMinutes($expiryMinutes));
        }

        return $generate_otp;
    }

    /**
     * Get session OTP
     */
    public static function getSessionOtp($mobile = null)
    {
        if (str()->contains(url()->current(), '/api/') && $mobile) {
            $cachedOtp = Cache::get('otp_' . $mobile);
        } else {
            $cachedOtp = session('otp');
        }
        return $cachedOtp;
    }

    /**
     * Get duration between two dates
     */
    public static function getDurationAmongTwoDates($startDate, $endDate, $durationUnit = 'years', $isEndDateIsCurrentDate = false)
    {
        $duration = 0;
        $start = Carbon::parse($startDate);

        if ($isEndDateIsCurrentDate) {
            $end = Carbon::parse(now());
        } else {
            $end = Carbon::parse($endDate);
        }

        if ($durationUnit == 'years') {
            $duration = $start->diffInYears($end);
        } elseif ($durationUnit == 'months') {
            $duration = $start->diffInMonths($end);
        } elseif ($durationUnit == 'days') {
            $duration = $start->diffInDays($end);
        }

        $duration = max(1, $duration);
        return (int) round($duration);
    }

    /**
     * Check if request is from API
     */
    public static function checkIfRequestFromApi()
    {
        $patterns = config('helper-functions.view_helper.api_patterns', ['/api/']);

        foreach ($patterns as $pattern) {
            if (str()->contains(url()->current(), $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save image path in JSON format
     */
    public static function saveImagePathInJson($imageFileObject, $imageDirectory, $imageNameString = null, $width = null, $height = null, $previousJsonString = null)
    {
        if ($previousJsonString) {
            foreach (json_decode($previousJsonString) as $previousImage) {
                if (file_exists($previousImage)) {
                    unlink($previousImage);
                }
            }
        }

        $imageFileString = [];
        if ($imageFileObject) {
            foreach ($imageFileObject as $key => $image) {
                // Note: imageUpload function should be defined in your application
                if (function_exists('imageUpload')) {
                    $imageFileString[] = imageUpload($image, $imageDirectory, $imageNameString, $width, $height);
                }
            }
        }

        return json_encode($imageFileString);
    }
}
