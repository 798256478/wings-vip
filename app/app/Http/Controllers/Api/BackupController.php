<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class BackupController extends Controller
{
    public function __construct()
    {
    }

    public function getLastBackTime()
    {
        try {
            $dir = '../storage/app/'.user_domain().'/';
            $fileList = scandir($dir);
            $time = '';
            foreach ($fileList as $file) {
                if($file != '.' && $file != '..' && is_dir($dir.$file)) {
                    $time = $file;
                }
            }
            if (strlen($time) > 10) {
                return substr($time, 7);
            }else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('', []);
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function backup()
    {
        try {
            $dir = '../storage/app/'.user_domain();
            $backName = user_domain().'_'.date('Y-m-d', time());
            //清空
            if (file_exists($dir)) {
                $this->deleteDir($dir);
            }
            //备份mysql
            \Artisan::call('backup:run');
            $fileList = scandir($dir);
            $mysqlZip = '';
            if ($fileList) {
                $mysqlZip = $dir.'/'.$fileList[count($fileList) - 1];
            }
            //备份mongo
            $mongoZip = $this->backupMongodb();
            // if (file_exists($mysqlZip) && file_exists($mongoZip)) {
                // $zip = new \ZipArchiveEx();
                // $zip->open($backName.'.zip', ZIPARCHIVE::OVERWRITE);
                // $zip->addDir($mysqlZip);
                // $zip->addDir($mongoZip);
                // $zip->close();
                // $headers = [
                //     'Content-Type: application/zip',
                // ];
                // return \Response::download($file, $backName.".zip", $headers);
                return ['message' => '备份成功'];
            // }else {
            //     throw new \Dingo\Api\Exception\StoreResourceFailedException('备份失败', []);
            // }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    private function backupMongodb()
    {
        $folder = '..\storage\app\\'.user_domain().'\\mongoDB'.date('Y-m-d', time());
        exec('"d:\mongodb\server\3.0\bin\mongodump.exe" -d '.user_domain().' -o '.$folder);
        $folder .=  '/'.user_domain();
        // $zip = new \ZipArchiveEx();
        $mongoZip = '../storage/app/'.user_domain().'/'.date('Y-m-d', time()).'.zip';
        // $zip->open($mongoZip, ZIPARCHIVE::OVERWRITE);
        // $zip->addDir($folder);
        // $zip->close();
        return $mongoZip;
    }

    private function deleteDir($pathdir)
    {
        if ($this->is_empty_dir($pathdir)) {
            rmdir($pathdir);
        } else {
            $d = dir($pathdir);
            while ($a = $d->read()) {
                if (is_file($pathdir.'/'.$a) && ($a != '.') && ($a != '..')) {
                    unlink($pathdir.'/'.$a);
                }
                if (is_dir($pathdir.'/'.$a) && ($a != '.') && ($a != '..')) {
                    if (!$this->is_empty_dir($pathdir.'/'.$a)) {
                        $this->deleteDir($pathdir.'/'.$a);
                    }
                    if ($this->is_empty_dir($pathdir.'/'.$a)) {
                        rmdir($pathdir.'/'.$a);
                    }
                }
            }
            $d->close();
        }
    }

    private function is_empty_dir($pathdir)
    {
        $d = opendir($pathdir);
        $i = 0;
        while ($a = readdir($d)) {
            ++$i;
        }
        closedir($d);
        if ($i > 2) {
            return false;
        } else {
            return true;
        }
    }
}
