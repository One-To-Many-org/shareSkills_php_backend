<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PutRequestParser
{
    protected $uploader;
    protected $mapper;
    public function __construct(FileUploader $uploader, MediaTypesService $mapperService){
        $this->uploader=$uploader;
        $this->mapper=$mapperService;
    }

    protected function parse(){
        global $_PUT;
        $raw_data = file_get_contents('php://input');
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

         // Fetch each part
        $parts = array_slice(explode($boundary, $raw_data), 1);
        $data = array();

        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") break;

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $raw_headers = explode("\r\n", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                $filename = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;
                isset($matches[4]) and $filename = $matches[4];

                // handle your fields here
                switch ($name) {
                    // this is a file upload
                    case 'userfile':
                        file_put_contents($filename, $body);
                        break;

                    // default for all other files is to populate $data
                    default:
                        $data[$name] = substr($body, 0, strlen($body) - 2);
                        break;
                }
            }

        }

        $GLOBALS[ '_PUT' ] = $_PUT=$data;
    }

    protected function parseFromPUT_to_POST(Request $request, $_fileFields=['picture'], $_postFields=['data']){
        $this->parse ();

        if(array_key_exists ('_PUT',$GLOBALS)){
            $_PUT=$GLOBALS[ '_PUT' ];
            foreach ($_fileFields as $field){
                if(array_key_exists ($field,$_PUT)){
                    $this->uploader->addFileTorequest ($request,$_PUT[$field],$field);
                }
             }
            foreach ($_postFields as $field){
                $data=[];
                if(array_key_exists ($field,$_PUT)){
                    $data[$field]=$_PUT[$field];
                }
                $request->request->add ($data);
              }
            }
        return $request;
    }

    public function parseTo_POST_IfMultipart(Request $request, $_fileFields=['picture'], $_postFields=['data']){
        if($this->mapper->isMultipartFormDataRequest ($request) && $request->isMethod ('PUT')){
            $this->parseFromPUT_to_POST ($request,$_fileFields,$_postFields);
        }
    }


}
