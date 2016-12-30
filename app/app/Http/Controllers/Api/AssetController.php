<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\PropertyService;
use App\Services\TicketService;

class AssetController extends Controller
{

    protected $authService;
    protected $propertyService;
    protected $ticketService;

    public function __construct(AuthService $authService,PropertyService $propertyService,TicketService $ticketService)
    {
        $this->authService = $authService;
        $this->propertyService = $propertyService;
        $this->ticketService = $ticketService;
    }
    
    public function  write_off(Request $request)
    {
        try{
            $type=$request->input('type');
            $objId=$request->input('objid');
            if($type=='property'){
                $this->propertyService->writeoffProperty($objId);
            }
            elseif($type=='ticket'){
                $this->ticketService->writeoffTicket($objId);
            }
        }
        catch (\Exception $e) {
              return json_exception_response($e);
         } 
    }
    
}
