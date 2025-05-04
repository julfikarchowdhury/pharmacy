<?php

use App\Models\Setting;
use App\Models\Unit;

function setting()
{
    return Setting::first();
}

function units()
{
    return Unit::all();
}