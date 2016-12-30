<?php

namespace App\Http\Controllers\Api\Health;

use Dingo\Api\Http\Request;
use App\Services\Health\CustomerService;
use App\Services\Health\ProgressService;
use App\Services\AuthService;
use App\Http\Controllers\Api\Controller;

class CustomerController extends Controller
{

    protected $authService;
    protected $customerService;
    protected $progressService;

    public function __construct(AuthService $authService,CustomerService $customerService,ProgressService $progressService)
    {
        $this->authService = $authService;
        $this->customerService=$customerService;
        $this->progressService=$progressService;
    }

    public function init()
    {
        try {
            $data['progresses'] = $this->progressService->getProgressConfig();
            $data['total'] = $this->customerService->getCustomerTotal('0');
            $data['customers'] = $this->customerService->getCustomerList(1, '0');
            return $data;
        }catch (\Exception $e){
            return json_exception_response($e);
        }
    }
    public function addCustomer(Request $request)
    {
        try {
            $data = $request->all();

            return $this->customerService->addHealthCustomer($data);
        }catch (\Exception $e){
            return json_exception_response($e);
        }

    }

    public function getCustomerTotal($name)
    {
        return $this->customerService->getCustomerTotal($name);
    }

    public function getCustomerList($page,$name)
    {
        try {
            $data['total'] = $this->customerService->getCustomerTotal($name);
            $data['customers'] = $this->customerService->getCustomerList($page, $name);
            return $data;
        }catch (\Exception $e){
            return json_exception_response($e);
        }
    }

    public function editCustomer(Request $request)
    {
        try{
            $data=$request->all();
            return $this->customerService->editCustomer($data);
        }catch (\Exception $e){
            return json_exception_response($e);
        }

    }




}
