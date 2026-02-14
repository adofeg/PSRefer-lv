<?php

namespace App\Data\Offerings;

use App\Models\Offering;
use Spatie\LaravelData\Data;

class OfferingData extends Data
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $type = 'service',
        public ?string $category = null,
        public ?int $category_id = null,
        public ?string $description = null,
        public ?float $base_commission = null,
        public bool $is_active = true,
        public ?array $form_schema = [],
        public ?array $commission_rules = [],
        public ?array $notification_emails = [],
        public ?string $share_url = null,
        public string $commission_type = 'percentage',
    ) {}

    public static function fromModel(Offering $offering, ?\App\Models\User $user = null): self
    {
        $shareUrl = null;
        if ($user && $user->associate) {
            $shareUrl = \Illuminate\Support\Facades\URL::signedRoute(
                'site.invite',
                ['offeringId' => $offering->id, 'ref' => $user->profileable_id]
            );
        }

        return new self(
            id: (int) $offering->id,
            name: $offering->name,
            type: $offering->type,
            category: $offering->category_id ? $offering->category?->name : $offering->category,
            category_id: $offering->category_id ? (int) $offering->category_id : null,
            description: $offering->description,
            base_commission: $offering->base_commission !== null ? (float) $offering->base_commission : null,
            is_active: (bool) $offering->is_active,
            form_schema: $offering->form_schema ?? [],
            commission_rules: $offering->commission_rules ?? [],
            notification_emails: $offering->notification_emails ?? [],
            share_url: $shareUrl,
            commission_type: $offering->commission_type ?? 'percentage',
        );
    }
}
