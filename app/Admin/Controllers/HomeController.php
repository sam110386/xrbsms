<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Admin\Controllers\Homedashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('Dashboard')
            ->description('Description...')
            ->row(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->append("<style>
                            .title {
                                font-size: 50px;
                                color: #636b6f;
                                font-family: 'Raleway', sans-serif;
                                font-weight: 100;
                                display: block;
                                text-align: center;
                                margin: 20px 0 10px 0px;
                            }
                        </style>
                        <div class='title'>
                            ZRB SMS PORTAL
                        </div>");
                });
            })
             ->row(function (Row $row) {
                 $row->column(6, function (Column $column) {
                     $column->append(Homedashboard::smsstatistics());
                 });
                 $row->column(6, function (Column $column) {
                     $column->append(Homedashboard::settingblock());
                 });
             });
    }
}
