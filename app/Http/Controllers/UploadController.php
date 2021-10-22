<?php

namespace App\Http\Controllers;

use Encore\Admin\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use Aws;

class UploadController extends BaseController
{
    function index(Request $request)
    {
      if ($request->hasFile('upload'))
      {
        $originName = $request->file('upload')->getClientOriginalName();
        $fileName   = pathinfo($originName, PATHINFO_FILENAME);
        $extension  = $request->file('upload')->getClientOriginalExtension();
        $trimmed_name    = str_replace(' ', '', $fileName . '_' . time() . '.');
        $fileName   =  $trimmed_name . $extension;

        $request->file('upload')->move(public_path('images'), $fileName);
        $CKEditorFuncNum = $request->input('CKEditorFuncNum');
        $url             = realpath(public_path('images/' . $fileName));
        $get_url_image   = self::getImageFromAWSByUriImageExternal($url);

        if($get_url_image !== false)
        {
          if (file_exists($url))
          {
            @unlink($url);
          }

          $url_aws =  $get_url_image;
        }
        else
        {
          $msg = 'Error on upload image';
        }

        $msg = 'Image uploaded successfully';
        $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url_aws', '$msg')</script>";

        @header('Content-type: text/html; charset=utf-8');
        echo $response;
      }
    }

    private static function getImageFromAWSByUriImageExternal($uriImageExternal){
      $s3 = new Aws\S3\S3Client([
        'region'  => 'us-east-2',
        'version' => 'latest',
        'credentials' => [
          'key'    => "AKIAIQS7HMCD7GJJMLNA",
          'secret' => "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",
        ]
      ]);

      if (!file_exists('tmp')) 
      {
        mkdir('tmp', 0777, true);
      }

      $getImage = file_get_contents($uriImageExternal);

      if($getImage !== FALSE)
      {
        $nameImage = rand(100000,1000000000).'.jpg';
        $img = 'tmp/'.$nameImage;
        file_put_contents($img, $getImage);
        $size = getimagesize($img);

        if(!empty($size))
        {
          $result = $s3->putObject([
            'Bucket' => 'copoadminpro',
            'Key'    => "images/" . $nameImage,
            'SourceFile' => realpath($img)
          ]);

          $urlImageFromAWS = $result->get("ObjectURL");
          if(!empty($urlImageFromAWS)) {
            unlink($img);
            return $urlImageFromAWS;
          }else return false;
        }
        else
        {
          unlink($img);
          return false;
        }
      }else return false;
    }

}