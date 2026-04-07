<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Patch;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Prefix;

use App\Services\chatBotService;
class chatBotController
{
    protected $chatBotService;

    /**
     * @param $chatBotService
     */
    public function __construct($chatBotService)
    {
        $this->chatBotService = $chatBotService;
    }


}
