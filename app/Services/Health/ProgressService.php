<?php 

namespace App\Services\Health;

use App\Exceptions\WingException;
use App\Models\Health\ExperimentData;
use App\Models\Health\Progress;
use App\Models\Health\ProgressData;
use App\Events\ProgressChanged;
use App\Models\Health\Customer;
use App\Models\Card;


class ProgressService
{
    public function getProgressConfig()
	{
		$res=Progress::all();
		if(!$res){
			throw new WingException("进度未配置", 401);
		}
		return $res;
	}
    
    public function getAllProgress(){
        return $allProgress=Progress::where('isshow','1')->orderBy('order')->get();
    }
    
    public function changeProgress($experimentDataId,$progressId)
    {
        $experimentData = ExperimentData::find($experimentDataId);
        $experimentData->progress_id = $progressId != 0 ? $progressId:null;
        $res=$experimentData->save();
        if($progressId!=0){
            $progressData = new ProgressData;
            $progressData->progress_id = $progressId;
            $progressData->experiment_data_id = $experimentData->id;
            $res = $progressData->save();
            if(!$res){
                throw new WingException("进度保存失败", 401);
            }
        }
        $progress=Progress::find($progressId);
        if($progress->isshow==1)
        {
            $customer=Customer::where('barcode_id',$experimentData->barcode_id)->first();
            if($customer){
                $card=Card::find($customer->card_id);
                if($card){
                    event(new ProgressChanged($card, $barcode->code,$progress->name));
                }
            }
        }
          
    } 
    

    public function getOneProgress($experiment_data_id){
        $progress=ProgressData::where('experiment_data_id',$experiment_data_id)->where('progress_id','!=','7')->get();
        foreach ($progress as $k => $v) {
        	$progress[$k]['data']=Progress::find($progress[$k]->progress_id);
        }
        return $progress;
    }
    

}