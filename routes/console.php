<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

// Clean up old watermark jobs and files daily
Schedule::command('watermark:cleanup')->daily();
