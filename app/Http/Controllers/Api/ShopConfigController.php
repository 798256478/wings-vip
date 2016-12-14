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
        $data['shop']=$this->mallService->getAllShop();
        return $data;
    }

    public function saveShopTitle(Request $request)
    {
        try {
            $data=$request->all();
            return $this->mallService->saveShopTitle($data);
        }catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function saveShopCategory(Request $request)
    {
        try {
            $data = $request->all();
            return $this->mallService->saveShopCategory($data);
        }catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function deleteShop($id)
    {
        $this->mallService->deleteShop($id);
    }

    public function deleteCategory($shopId,$categoryId)
    {
        try {
            $this->mallService->deleteCategory($shopId,$categoryId);
        }catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function saveShopPage(Request $request)
    {
        $this->mallService->saveShopPage($request->all());
    }

    public function getShopPage()
    {
        return $this->mallService->getShopPage();
    }
}
