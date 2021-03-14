<?php


namespace App\Repositories;


use App\Models\Institution;
use Illuminate\Support\Facades\Auth;
use TomorrowIdeas\Plaid\Entities\User;
use TomorrowIdeas\Plaid\Plaid;

class PlaidRepository
{
    private $plaid;
    public function __construct()
    {
        $this->plaid = new Plaid(
            env('PLAID_CLIENT_ID'),
            env('PLAID_CLIENT_SECRET'),
            env('PLAID_ENVIRONMENT', 'sandbox')
        );
    }

    public function createLinkToken(): object
    {
        $user = Auth::user();
        $plaidUser = new User($user->id);
        return $this->plaid->tokens->create(
            $user->profile->full_name,
            'en',
            ['US'],
            $plaidUser,
            ['transactions']
        );
    }

    public function exchangePublicToken($publicToken)
    {
        $exchange = $this->plaid->items->exchangeToken($publicToken);
        $accessToken = $exchange->access_token;

        $item = $this->plaid->items->get($accessToken);
        $instId = $item->item->institution_id;

        $response = $this->plaid->institutions->get($instId, ['US']);
        $instName = $response->institution->name;
//        $instLogo = (isset($response->institution->logo))? $response->institution->logo : '';
        $instLogo = '';

        $institution = new Institution();
        $institution->user_id = Auth::id();
        $institution->name = $instName;
        $institution->access_token = $accessToken;
        $institution->logo = $instLogo;
        $institution->save();
    }

    public function getInstitutions()
    {
        return Auth::user()->institutions;
    }

    public function getAccounts($institutionId)
    {
        $accessToken = $this->getAccessToken($institutionId);
        $response = $this->plaid->accounts->list($accessToken);

        return $response->accounts;
    }

    private function getAccessToken($institutionId)
    {
        $institution = Auth::user()->institutions->where('id', $institutionId)->first();

        return $institution->access_token;
    }

    public function getTransactions($institutionId, $accountsIds, $start, $end, $count = null, $offset = null)
    {
        $accessToken = $this->getAccessToken($institutionId);
        $options = ['account_ids' => $accountsIds];
        if ($count) $options['count'] = (int) $count;
        if ($offset) $options['offset'] = (int) $offset;

        $startDate = \DateTime::createFromFormat('Y-m-d', $start);
        $endDate = \DateTime::createFromFormat('Y-m-d', $end);

        $response = $this->plaid->transactions->list($accessToken, $startDate, $endDate, $options);

        return $response->transactions;
    }
}
