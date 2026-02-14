<?php

namespace App\Http\Controllers\Public;

use App\Actions\Public\GetHomePageDataAction;
use App\Actions\Public\GetOfferingApplicationViewAction;
use App\Actions\Public\SubmitOfferingApplicationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\PublicRequest;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PublicController extends Controller
{
    public function home(GetHomePageDataAction $action)
    {
        return Inertia::render('Public/Welcome', $action->execute());
    }

    /**
     * Show public offering application page.
     *
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function showOfferingApplication(string $offeringId, PublicRequest $request, GetOfferingApplicationViewAction $action)
    {
        $referrerId = $request->validated('ref');

        return Inertia::render('Public/OfferingApplication', $action->execute(
            (int) $offeringId,
            $referrerId ? (int) $referrerId : null,
            $request
        ));
    }

    /**
     * Submit public offering application.
     *
     * @param  Request  $request
     * @param  FormSchemaValidator  $validator
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitOfferingApplication(
        string $offeringId,
        PublicRequest $request,
        SubmitOfferingApplicationAction $action
    ) {
        try {
            $action->execute((int) $offeringId, $request->toData(), $request);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()->with('success', '¡Gracias! Tu solicitud ha sido enviada exitosamente.');
    }
}
