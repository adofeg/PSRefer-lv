<?php

namespace App\Enums;

enum AuditEntity: string
{
    case User = 'USER';
    case Referral = 'REFERRAL';
    case Offering = 'OFFERING';
    case Commission = 'COMMISSION';
}
