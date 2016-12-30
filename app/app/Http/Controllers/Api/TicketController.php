<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\TicketService;

class TicketController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService, TicketService $ticketService)
    {
        $this->authService = $authService;
        $this->ticketService = $ticketService;
    }

    /**
     * 获取用户现有的所有优惠券
     * @method getCardTicketList
     * @param  int        $cardId 会员id
     * @return array
     */
    public function getCardTicketList($cardId){
        try {
            return $this->ticketService->getCardTicketList($cardId);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
}
