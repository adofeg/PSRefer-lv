<?php

namespace App\Actions\Public;

use App\Models\Associate;
use App\Models\Offering;
use App\Services\OfferingSchemaService;
use Illuminate\Http\Request;

class GetOfferingApplicationViewAction
{
    public function __construct(
        protected OfferingSchemaService $schemaService,
        protected TrackReferralClickAction $trackReferralClickAction
    ) {}

    public function execute(int $offeringId, ?int $referrerId, Request $request): array
    {
        $offering = Offering::with('owner')
            ->where('id', $offeringId)
            ->where('is_active', true)
            ->firstOrFail();

        $referrer = null;
        if ($referrerId) {
            $referrer = Associate::find($referrerId);
            $this->trackReferralClickAction->execute($referrerId, $offeringId, $request);
        }

        return [
            'offering' => [
                'id' => $offering->id,
                'name' => $offering->name,
                'description' => $offering->description,
                'type' => $offering->type,
                'category' => $offering->category,
                'form_schema' => $this->schemaService->getSchemaForOffering($offering->form_schema),
                'owner' => $offering->owner?->user ? [
                    'id' => $offering->owner->user->id,
                    'name' => $offering->owner->user->name,
                    'email' => $offering->owner->user->email,
                    'phone' => $offering->owner->user->phone,
                    'logo_url' => $offering->owner->user->logo_url,
                ] : null,
            ],
            'referrer' => $referrer?->user ? [
                'id' => $referrer->id,
                'name' => $referrer->user->name,
                'email' => $referrer->user->email,
                'phone' => $referrer->user->phone,
                'logo_url' => $referrer->user->logo_url,
            ] : null,
        ];
    }
}
