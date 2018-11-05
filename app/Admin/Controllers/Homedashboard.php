<?php

namespace App\Admin\Controllers;

use Encore\Admin\Admin;
use App\Models\Smslog;
class Homedashboard
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function title()
    {
        return view('admin::homedashboard.title');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function smsstatistics()
    {
        
        $balance=10000;
        $today=Smslog::where("created_at",'>', date('Y-m-d'))->count();
        $todayfailed=Smslog::where("created_at",'>', date('Y-m-d'))->where("status",0)->count();
        $thismonth=Smslog::where("created_at",'>', date('Y-m'))->count();
        $thismonthfailed=Smslog::where("created_at",'>', date('Y-m'))->where("status",0)->count();
        $thisyear=Smslog::where("created_at",'>', date('Y'))->count();
        $thisyearfailed=Smslog::where("created_at",'>', date('Y'))->where("status",0)->count();
        return view('admin::homedashboard.smsstatistics', compact('balance','today','thismonth','thisyear','thisyearfailed','thismonthfailed','todayfailed'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function settingblock()
    {
        $extensions = [
            'helpers' => [
                'name' => 'Setting',
                'link' => '/admin/setting/smspisetting',
                'icon' => 'gears',
            ],
            'log-viewer' => [
                'name' => 'Message Logs',
                'link' => '/admin/smslogs',
                'icon' => 'history',
            ],
            'backup' => [
                'name' => 'Send SMS',
                'link' => '/admin/sms/new',
                'icon' => 'envelope',
            ],
            'config' => [
                'name' => 'Bulk SMS',
                'link' => '/admin/sms/new',
                'icon' => 'bulk',
            ],
            'api-tester' => [
                'name' => 'Clear Logs',
                'link' => '/admin/smslogs',
                'icon' => 'refresh',
            ],
            
        ];
        
        $Admin=new Admin();
        if($Admin->user()->isRole('officers')){
            $extensions = [
                'log-viewer' => [
                    'name' => 'Message Logs',
                    'link' => '/admin/smslogs',
                    'icon' => 'fa-history',
                ],
                'backup' => [
                    'name' => 'Send SMS',
                    'link' => '/admin/sms/new',
                    'icon' => 'envelope',
                ],
                'config' => [
                    'name' => 'Bulk SMS',
                    'link' => '/admin/sms/new',
                    'icon' => 'envelope',
                ],
                
            ];
           
            
            }
        return view('admin::homedashboard.settingblock', compact('extensions'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function dependencies()
    {
        $json = file_get_contents(base_path('composer.json'));

        $dependencies = json_decode($json, true)['require'];

        return view('admin::homedashboard.dependencies', compact('dependencies'));
    }
}
