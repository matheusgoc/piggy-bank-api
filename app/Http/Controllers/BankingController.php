<?php

namespace App\Http\Controllers;

use App\Http\Resources\InstitutionResource;
use App\Repositories\PlaidRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class BankingController extends Controller
{
    private $plaidRepo;

    public function __construct()
    {
        $this->plaidRepo = new PlaidRepository();
    }

    public function getLinkToken(): JsonResponse
    {
        return response()->json((array) $this->plaidRepo->createLinkToken());
    }

    public function exchangePublicToken(Request $request): InstitutionResource
    {
        $institution = $this->plaidRepo->exchangePublicToken($request->post('public_token'));

        return new InstitutionResource($institution);
    }

    public function getInstitutions(): AnonymousResourceCollection
    {
        $institutions = $this->plaidRepo->getInstitutions();

        return InstitutionResource::collection($institutions);
    }

    public function getAccounts($institutionId): JsonResponse
    {
        return response()->json((array) $this->plaidRepo->getAccounts($institutionId));
    }

    public function getTransactions(Request $request, $institutionId, $start, $end): JsonResponse
    {
        $accountsIds = [];
        if ($accounts = $request->get('accounts')) {
            $accountsIds = explode(',', $accounts);
        }
        $count = ($request->has('count'))?  $request->get('count') : null;
        $offset = ($request->has('offset'))?  $request->get('offset') : null;

        return response()->json((array) $this->plaidRepo->getTransactions(
            $institutionId,
            $accountsIds,
            $start,
            $end,
            $count,
            $offset
        ));
    }
}
