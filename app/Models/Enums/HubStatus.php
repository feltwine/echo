<?php

namespace App\Models\Enums;

enum HubStatus: string
{
    case PRIVATE =  'private';
    case PUBLIC  = 'public';
}
