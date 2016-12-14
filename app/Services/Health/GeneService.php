<?php
/**
 * Created by PhpStorm.
 * User: shenzhaoke
 * Date: 2016/5/24
 * Time: 11:16
 */

namespace App\Services\Health;

use DB;
use App\Models\Health\Gene;
use App\Models\Health\Site;

class GeneService
{
    public function get_genes($name,$pageindex,$pagesize)
    {
        if($name == '*')
            $name='';
        $data['genes'] = Gene::with('sites')->where('name', 'like', '%'.$name.'%')
                                            ->skip($pagesize*($pageindex - 1))
                                            ->take($pagesize)->get();
        $data['count'] = Gene::where('name', 'like', '%'.$name.'%')->count();
        return $data;
    }
    
    public function get_gene_by_id($id)
    {
        return Gene::with('sites')->find($id);
    }
    
    public function create_gene($data)
    {
        $gene = new Gene();
        $gene->name = $data['name'];
        $gene->default_is_positive = $data['default_is_positive'];
        $gene->default_effect = $data['default_effect'];
        $gene->site_count = 0; 
        $gene->save();
        return $gene->id;
    }
    
    public function save_site($data)
    {
        $site=Site::find($data['code']);
        if($site==null){
            $this->create_site($data);
        }
        else{
            $this->update_site($site,$data);
        }
        return  Site::where('gene_id',  $data['gene_id'])->get();
    }
    
    private function create_site($data)
    {
        try {
            //事务开始
            DB::beginTransaction();
            
            $site=new Site();
            $site->code = $data['code'];
            $site->rs_code = $data['rs_code'];
            $site->gene_id = $data['gene_id'];
            $site->mutation = $data['mutation'];
            $site->SNPSite = $data['SNPSite'];
            $site->DNASingleType = $data['DNASingleType'];
            $site->save();
            $gene = Gene::find($data['gene_id']);
            $gene->site_count = $gene->site_count+1;
            $gene->save();
            
            //历史记录
            DB::commit();

            return $site->id;
        } catch (\Exception $e) {
            DB::rollback();

            return json_exception_response($e);
        }
      
    }
    
    public function update_gene($data)
    {
        $gene=Gene::find($data['id']);
        $gene->name = $data['name'];
        $gene->default_is_positive = $data['default_is_positive'];
        $gene->default_effect = $data['default_effect'];
        $gene->save();
    }
    
    public function update_site($site,$data)
    {
        // $site->code = $data['code'];
        $site->mutation = $data['mutation'];
        $site->rs_code = $data['rs_code'];
        $site->SNPSite = $data['SNPSite'];
        $site->DNASingleType = $data['DNASingleType'];
        $site->save();
    }
}