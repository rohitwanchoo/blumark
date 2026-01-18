<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

// Clean up old watermark jobs and files daily
Schedule::command('watermark:cleanup')->daily();

// Purge old admin activity logs weekly (keeps 90 days by default)
Schedule::command('admin:purge-activity-logs --no-interaction')->weekly();
