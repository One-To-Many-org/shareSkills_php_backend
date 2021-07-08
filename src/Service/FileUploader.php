<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    protected $mapper;
    public function __construct(MediaTypesService $mapper){
        $this->mapper=$mapper;
    }

    public function addFileTorequest($request,$fileBinary,$fieldName='picture'){
            $ext=$this->mapper->sniffData ($fileBinary);
            $fileName='tmp/'.$fieldName.uniqid ().'.'.$ext;
        if(false!==file_put_contents ($fileName,$fileBinary)) {
            //important de mettre test à true pour approuver que notre fichier est sure sinon
            // cette fonction est utilisée https://www.php.net/manual/en/function.is-uploaded-file.php
            $file[$fieldName] = new UploadedFile( $fileName, $fileName, null, null, true );
            $request -> files -> add ( $file );
        }
    }
    /**
   public function addFileTorequest($request,$fileBinary){
           //pour avoir un nom unique ainsi si plusieurs processus utilisaiet le repertire la picture de l'un ne remplacera pas celle de l'autre
           $fileName='tmp/picture'.uniqid ();

           //on depose le fichier dans un dossier temporaire on determine son type et on renomme le fichier
           // puis on crée un UploadFile qu'on rajoute à $_FILE de la requête avec test =true
           if(false!==file_put_contents ($fileName,$fileBinary)){
               $contentType=mime_content_type($fileName);
               $ext=$this->mapper->guessExtension ($contentType);
               rename($fileName,$fileName.'.'.$ext);
               $fileName=$fileName.'.'.$ext;
               //important de mettre test à true pour approuver que notre fichier est sure sinon cette fonction est utilisée https://www.php.net/manual/en/function.is-uploaded-file.php
               $file['picture'] =new UploadedFile($fileName,$fileName,null,null,true);
               $request->files->add ($file);
           }

   }
     * **/
}
