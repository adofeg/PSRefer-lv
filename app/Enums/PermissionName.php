<?php

namespace App\Enums;

enum PermissionName: string
{
    case UserView = 'user:view';
    case ReferralUpdateStatus = 'referral:update_status';
    case OfferingCreate = 'offering:create';
    case OfferingView = 'offering:view';
    case OfferingEditOwn = 'offering:edit_own';
    case ReferralView = 'referral:view';
    case ReferralViewOwn = 'referral:view_own';
    case ReferralCreate = 'referral:create';
    case CommissionView = 'commission:view';
    case CommissionViewOwn = 'commission:view_own';
    case AnalyticsViewAll = 'analytics:view_all';
    case AnalyticsViewOwn = 'analytics:view_own';
}
