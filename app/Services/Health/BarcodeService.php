<?php

namespace App\Services\Health;

use App\Exceptions\WingException;
use App\Models\Health\Barcode;
use App\Models\Health\ExperimentData;
use App\Models\Health\Customer;
use App\Models\Card;
use App\Services\Health\ProgressService;
use DB;
use App;

class BarcodeService
{

	//依据类型和页数得到当页列表
	public function getBarcodes($page,$pagesize,$code)
	{
		$page = (int)$page;
		if ($page > 0) {
            if($code == '*')
                $code = '';
			$query =  Barcode::where('code', 'like', '%' . $code . '%');
            $data['total'] = $query->count();
            $data['barcodes'] = $query->with('customer')->orderBy('created_at', 'desc')->orderBy('code', 'desc')
					->skip($pagesize * ($page - 1))->take($pagesize)->get();
			return $data;

		} else {
			throw new \Dingo\Api\Exception\StoreResourceFailedException('页码错误', ['page' => ['页码必须大于0']]);
		}
	}


//	依据code得到条码信息
	public function getBarcodeInfo($code)
	{
		$res = Barcode::with(['experimentDatas' => function($query)
                {
                    $query->with('progress', 'experiment');
                }])->where('code', $code)->first();
		if (!$res) {
			throw new WingException("条码不存在", 401);
		}
		return $res;
	}


//	添加条码
	public function addBarcode($code)
	{
		if ($code == 'undefined') {
			throw new WingException("未输入条码", 401);

		}
		if (gettype($code) != 'string') {
			throw new WingException("条码错误", 401);
		}

		$res = Barcode::where('code', $code)->first();
		if ($res) {
			throw new WingException("条码已存在", 401);
		}
		$barcode = new Barcode();
		$barcode->code = $code;
		$res = $barcode->save();
		if (!$res) {
			throw new WingException("添加失败", 401);
		}
		return '添加成功';
	}

//	批量添加条码
	public function addBarcodes($data)
	{
		if (!is_array($data)) {
			throw new WingException("内容错误", 401);
		}

		$sql='insert ignore into barcodes (`code`,`created_at`,`updated_at`) values';
		$nowDate=date('Y-m-d G:i:s');
		foreach ($data as $key => $val) {
			if (!is_array($val)) {
				throw new WingException("内容错误", 401);
			}
			$val = strval($val['code']);
			if (!is_string($val)) {
				throw new WingException("内容错误非字符串", 401);
			}
			$sql=$sql."('".$val."','".$nowDate."','".$nowDate."'),";
		}
		$sql=rtrim($sql,',');
		$res=DB::insert($sql);
		$res=DB::select('select ROW_COUNT()');
		if (!$res) {
			throw new WingException("保存失败", 401);
		}
		$b=(array)$res[0];
		return $b['ROW_COUNT()'];
	}
    
    //更改code的实验信息和进度信息
	public function changeBarcodeInfo($barcodeId,$experimentId,$progressId)
	{
		$barcode = Barcode::where('id',$barcodeId)->first();
		if(!$barcode){
			throw new WingException("条码不存在", 401);
		}
        $experimentData = ExperimentData::where('barcode_id',$barcodeId)->where('experiment_id',$experimentId)->first();
        if($experimentData == null)
        {
             $experimentData = new ExperimentData;
             $experimentData->barcode_id=$barcodeId;
		     $experimentData->experiment_id=$experimentId;
             $experimentData->save();
        }
        else{
            if($progressId==0){
                  throw new WingException("必须选中进度", 401);
            }
        }
        $progress_id = $progressId != 0 ? $progressId:null;
        $progressService = App::make('ProgressService');
        $progressService->changeProgress($experimentData->id,$progress_id);
		return '保存成功';
	}


	//判断条码是否存在
	//判断条码实验是否绑定,状态是否废弃
	//判断该条码是否可以和客户绑定
	public function isBindingCustomer($barcode)
	{
        if(!$barcode){
			return '条码不存在';
		}
		if($barcode->isdisable==1){
            return "条码已废弃";
		}
        if($barcode->customer){
            return "条码已使用";
        }
		return '';
	}

}