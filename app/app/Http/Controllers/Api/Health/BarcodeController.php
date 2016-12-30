<?php

namespace App\Http\Controllers\Api\Health;

use Dingo\Api\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\AuthService;
use App\Services\Health\BarcodeService;
use App\Services\Health\ProgressService;
use App\Http\Controllers\Api\Controller;
use App\Services\Health\ExperimentService;

class BarcodeController extends Controller
{

	protected $authService;
	protected $barcodeService;
    protected $progressService;

	public function __construct(AuthService $authService, BarcodeService $barcodeService,ProgressService $progressService)
	{
		$this->authService = $authService;
		$this->barcodeService = $barcodeService;
        $this->progressService = $progressService;
	}




//    得到当前页条码列表和条码总数
//    $page       页数
	public function getBarcodes($page,$pagesize, $code)
	{
		try {
			return $this->barcodeService->getBarcodes($page,$pagesize,$code);
		} catch (\Exception $e) {
			return json_exception_response($e);
		}

	}
    
    public function getProgressConfig()
     {
		try {
			return $this->progressService->getProgressConfig();
		} catch (\Exception $e) {
			return json_exception_response($e);
		}

	}

//    通过code得到条码信息
	public function getBarcodeInfo($code)
	{
		try {
			$result = $this->barcodeService->getBarcodeInfo($code);
            return $result;

		} catch (\Exception $e) {
			return json_exception_response($e);
		}
	}


//    单个进度保存
	public function changeProgress($codeId, $progressId)
	{
		try {
			return $this->barcodeService->changeProgress($codeId, $progressId);
		} catch (\Exception $e) {
			return json_exception_response($e);
		}
	}

//  添加条码
	public function addBarcode($code)
	{
		try {
			return $this->barcodeService->addBarcode($code);
		} catch (\Exception $e) {
			return json_exception_response($e);
		}
	}

//    批量添加条码,上传文件
	public function addBarcodes(Request $request)
	{
		try {
			$file = $request->file('code');
			$data = Excel::load($file)->toArray();
			return $this->barcodeService->addBarcodes($data);
		} catch (\Exception $e) {
			return json_exception_response($e);
		}

	}

//    单个实验保存
	public function changeBarcodeInfo($barcodeId, $experimentId,$progressId)
	{
		try {
			return $this->barcodeService->changeBarcodeInfo($barcodeId, $experimentId,$progressId);
		} catch (\Exception $e) {
			return json_exception_response($e);
		}
	}




}
