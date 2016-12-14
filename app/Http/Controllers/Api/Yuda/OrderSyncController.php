<?php 

namespace App\Http\Controllers\Api\Yuda;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Services\Yuda\OrderSyncService;
class OrderSyncController extends Controller
{
    protected $orderSyncService;

    public function __construct(OrderSyncService $orderSyncService)
    {
        $this->orderSyncService = $orderSyncService;
    }

    public function getSyncFailRecord(Request $request)
    {
        $data = $request->all();
        return $this->orderSyncService->getSyncFailRecord($data);
    }

    public function syncSuccess($id)
    {
        $this->orderSyncService->syncSuccess($id);
    }

    public function againSync($id)
    {
        $res = $this->orderSyncService->againSync($id);
        if(!$res){
            return 'FAIL';
        }
    }

}