<?php 
namespace App\Helpers;
use App\Models\GeneralSetting;

class CommonMethod {

	/* Format given date to stored date format */
    public static function formatDate($date = null){
    	$settings = GeneralSetting::find(1);
    	return ($date) ? date($settings->date_format, strtotime($date))  : $date;
    }
    /* Format given date to stored date format */
    public static function formatDateWithTime($date = null){
    	$settings = GeneralSetting::find(1);
    	return ($date) ? date($settings->date_format. ' h:i A', strtotime($date)) : $date;
    }
}