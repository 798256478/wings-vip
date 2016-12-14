<?php

namespace App\Http\Controllers\Api\Health;

use Dingo\Api\Http\Request;
use App\Services\Health\ReservationService;
use App\Services\AuthService;
use App\Http\Controllers\Api\Controller;

class ReservationController extends Controller
{

    protected $authService;
    protected $customerService;

    public function __construct(AuthService $authService,ReservationService $reservationService)
    {
        $this->authService = $authService;
        $this->reservationService=$reservationService;
    }

    public function getReservations(Request $request)
    {
        try {
            $filter=$request->all();
            $reservations=$this->reservationService->getReservationList($filter);
            $data['total'] = count($reservations);
            $data['reservations'] = $reservations;
            return $data;
        }catch (\Exception $e){
            return json_exception_response($e);
        }
    }

}
