<?php

namespace App\Enums;

enum W9Status: string
{
    case Pending = 'pending';
    case Submitted = 'submitted';
    case Verified = 'verified';
}
