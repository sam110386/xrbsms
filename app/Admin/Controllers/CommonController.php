<?php 
namespace App\Admin\Controllers;
use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;

class HomeController extends Controller{
	private var $settings = [];
	public function __construct()
    {
    	$this->settings = GeneralSetting::find(1);
    }
}