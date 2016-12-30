<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use App\Services\AuthService;
use App\Services\MallService;

class ShopConfigController extends Controller
{
    protected $authService;
    protected $mallService;

    public function __construct(AuthService $authService, MallService $mallService)
    {
        $this->authService = $authService;
        $this->mallService = $mallService;
    }

    public function index()
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $data['shop']=$this->mallService->getAllShop();
            return $data;
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function saveShopTitle(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $data=$request->all();
            return $this->mallService->saveShopTitle($data);
        }catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function saveShopCategory(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $data = $request->all();
            return $this->mallService->saveShopCategory($data);
        }catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function deleteShop($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $this->mallService->deleteShop($id);
        }catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function deleteCategory($shopId,$categoryId)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $this->mallService->deleteCategory($shopId,$categoryId);
        }catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function saveShopPage(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $this->mallService->saveShopPage($request->all());
        }catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getShopPage()
    {
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->mallService->getShopPage();
        }catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
}
