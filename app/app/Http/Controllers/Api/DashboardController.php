<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\EventRuleService;
use App\Services\StatisticsService;
use App\Services\OrderService;
use App\Services\SettingService;
use App\Services\TicketService;
use App\Services\PropertyService;

class DashboardController extends Controller
{

    protected $authService;
    protected $eventRuleService;
    protected $ticketService;
    protected $propertyService;
    protected $settingService;
    protected $orderService;
    protected $statisticsService;
    public function __construct(AuthService $authService, EventRuleService $eventRuleService,StatisticsService $statisticsService,OrderService $orderService,TicketService $ticketService, PropertyService $propertyService,SettingService $settingService)
    {
        $this->authService = $authService;
        $this->eventRuleService = $eventRuleService;
        $this->ticketService = $ticketService;
        $this->propertyService = $propertyService;
        $this->statisticsService = $statisticsService;
        $this->settingService = $settingService;
        $this->orderService = $orderService;
    }

    public function getInitData ($key)
    {
        try{
            $data['eventruleList']=$this->eventRuleService->getEventRuleList();
            $data['actionJobList']=$this->eventRuleService->getJobList();
            $data['totalData']=$this->statisticsService->getDaysData();
            $data['saleTop']=$this->orderService->getSaleTop();
            $data['levels']=$this->settingService->get($key);
            $data['actionPropertyTemplateList']=$this->propertyService->getPropertyTemplateList();
            $data['actionTicketList']=$this->ticketService->getTicketTemplateUsableList();
            $data['orderData']=$this->orderService->getDashboardOrderData();
           
            return $data;
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }

}
