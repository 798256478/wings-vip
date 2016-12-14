<?php

namespace App\Services\Health;

use DB;
use App;
use App\Models\Health\Site;
use App\Models\Health\Record;
use App\Models\Health\SiteData;
use App\Models\Health\Project;
use App\Models\Health\ProjectSite;
use App\Models\Health\ExperimentData;
use App\Exceptions\WingException;
use Maatwebsite\Excel\Facades\Excel;

class SitedataService
{
    public function saveSiteData($siteData, $form)
    {
        $progressService = App::make('ProgressService');
        DB::transaction(function () use ($siteData, $form,$progressService) {
            foreach ($siteData as $key => $value) {
                $sql='select * from barcodes where code REGEXP  \'0*'.$key.'\'';
                $barcode = DB::select(DB::raw($sql))[0];
                if($barcode == null){
                    throw new WingException($key.'编码未找到', 402);
                }
                $experimentDatas =  ExperimentData::where('barcode_id',$barcode->id)->where('experiment_id',$form['experimentId'])->get();
                if(count($experimentDatas)==0){
                     throw new WingException($key.'没有该实验', 402);
                }
                $progressService->changeProgress($experimentDatas[0]->id,4);
                $record = new Record();
                $record->experiment_data_id = $experimentDatas[0]->id;
                $record->sampleNo = $barcode->code;
                $record->time = $form['time'];
                $record->inspector = $form['inspector'];
                $record->assessor = $form['assessor'];
                $record->save();
                foreach ($value['data'] as $sitedata) {
                    $site = Site::where('rs_code', $sitedata['assay_id'])->first();
                    $siteData = new SiteData();
                    $siteData->record_id = $record->id;
                    $siteData->code = $site->code;
                    if (strlen($sitedata['call']) == 1) {
                        $siteData->genotype = $sitedata['call'].$sitedata['call'];
                    } else {
                        $siteData->genotype = $sitedata['call'];
                    }
                    $siteData->singleType = 1;
                    $siteData->save();
                }
            }
        });
    }
    
    public function getSiteData($form,$file)
    {
         $experimentDatas =  ExperimentData::with('barcode')->where('progress_id', '<', 4)->where('experiment_id',$form['experimentId'])->get();
         $unableExperimentDatas =  ExperimentData::with('barcode')->where('progress_id', '>=', 4)->where('experiment_id',$form['experimentId'])->get();
         $siteRsList = $this->getSiteListFromExperimentId($form['experimentId']);
         $haveRsList = [];
         $reader = Excel::load($file)->get()->toArray();
         $siteData = [];
         $nullFlag = false;
         $unableCodeString = '';
         $unableArr = [];
         
         foreach ($reader as $value) {
            if (in_array(trim($value['assay_id']), $siteRsList) && $this->matchCode(trim($value['sample_id']), $experimentDatas)) {
                $data = [
                    'assay_id' => trim($value['assay_id']),
                    'call' => trim($value['call']),
                ];
                if ($data['call'] == '') {
                    $nullFlag = true;
                    $siteData[$value['sample_id']]['empty'][] = $data;
                } else {
                    $siteData[$value['sample_id']]['data'][] = $data;
                }
                $haveRsList[$value['sample_id']][] = trim($value['assay_id']);
            }elseif ($this->matchCode(trim($value['sample_id']), $unableExperimentDatas)) {
                if (!in_array(trim($value['sample_id']), $unableArr)) {
                    $unableArr[] = trim($value['sample_id']);
                    $unableCodeString .= trim($value['sample_id']) . ',';
                }
            }
        }
        foreach ($haveRsList as $key => $value) {
            $diff = array_diff($siteRsList, $value);
            if (count($diff) > 0) {
                $nullFlag = true;
                foreach ($diff as $rs) {
                    $siteData[$key]['empty'][] = [
                        'assay_id' => $rs,
                        'call' => null,
                    ];
                }
            }
        }
        if (!$nullFlag) {
            if (count($siteData) > 0) {
                $this->sitedataService->saveSiteData($siteData, $form);
                if ($unableCodeString != '') {
                    return ['status' => 0,'message' => '保存成功.以下样本编号不能重复添加：'.$unableCodeString];
                }else {
                    return ['status' => 0,'message' => '保存成功'];
                }
            }elseif ($unableCodeString != '') {
                return ['status' => 1,'message' => '下列样本号不能上传数据：'.$unableCodeString];
            }else {
                return ['status' => 2,'message' => '没有有效的样本编号或者编号没有对应的实验'];
            }
        } else {
            return ['status'=>3,'data' => $siteData, 'form' => $form];
        }
            
    }
    
    private function matchCode($code, $experimentDatas)
    {
        $flag = false;
        foreach ($experimentDatas as $experimentData) {
            if (preg_match('/0*'.$code.'/', $experimentData->barcode->code)) {
                $flag = true;
            }
        }
        return $flag;
    }


    private function getSiteListFromExperimentId($id)
    {
        $projectsList = Project::select('id')->where('experiment_id', $id)->get()->toArray();
        $siteCodeList = ProjectSite::select('code')->whereIn('project_id', $projectsList)->get()->toArray();
        $siteList = Site::whereIn('code', $siteCodeList)->get()->toArray();
        return array_column($siteList, 'rs_code');
    }
}
