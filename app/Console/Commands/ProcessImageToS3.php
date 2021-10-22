<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws;

class ProcessImageToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa imagenes al s3 nomas las publicadas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $news = \DB::select("select id, image from news where id_status_news in (1,5) and created_at <= '2020-12-01' and image is not null and image != '' and image not like '%https%' and image not like '%http%' and image not like '%2020/08/%' order by created_at desc LIMIT 99999999 offset 186");

      if(count($news)>0){
        $domain = "http://adminimages.codigopostal.com/uploads/";

        foreach ($news as $new) {
          $image = str_replace(' ', '%20', $new->image);
          $urlImage = $domain.$image;
          $urlS3 = $this->getImageFromAWSByUriImageExternal($urlImage,$new->image);
          if(!empty($urlS3)){
            echo $urlS3."\n";
          }else{
            echo "NO EXISTE IMAGEN O NO SE PUDO DESCARGAR: ".$urlImage."\n";
          }
        }
      }
    }

    public function getImageFromAWSByUriImageExternal($uriImageExternal,$folderImage){
       $s3 = new Aws\S3\S3Client([
         'region'  => 'us-east-2',
         'version' => 'latest',
         'credentials' => [
           'key'    => "AKIAIQS7HMCD7GJJMLNA",
           'secret' => "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",
         ]
       ]);

       $getImage = @file_get_contents($uriImageExternal);
       if($getImage !== FALSE){
         $result = $s3->putObject([
           'Bucket' => 'copoadminpro',
           'Key'    => $folderImage,
           'Body' => $getImage
         ]);

         $urlImageFromAWS = $result->get("ObjectURL");
         if(!empty($urlImageFromAWS)) {
           return $urlImageFromAWS;
         }else return false;
       }else return false;
    }
}
