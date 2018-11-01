<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
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

                            .links {
                                text-align: center;
                                margin-bottom: 20px;
                            }

                            .links > a {
                                color: #636b6f;
                                padding: 0 25px;
                                font-size: 12px;
                                font-weight: 600;
                                letter-spacing: .1rem;
                                text-decoration: none;
                                text-transform: uppercase;
                            }
                        </style>
                        <div class='title'>
                            Welcome Admin
                        </div>");
                });
            });
            
            // ->row(Dashboard::title());
            // ->row(function (Row $row) {

            //     $row->column(4, function (Column $column) {
            //         $column->append(Dashboard::environment());
            //     });

            //     $row->column(4, function (Column $column) {
            //         $column->append(Dashboard::extensions());
            //     });

            //     $row->column(4, function (Column $column) {
            //         $column->append(Dashboard::dependencies());
            //     });
            // });
    }
}
