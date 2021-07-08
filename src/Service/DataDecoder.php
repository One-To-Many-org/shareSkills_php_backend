<?php


namespace App\Service;


use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;

class DataDecoder
{
   private $decoders;
    public function __construct(){
        $this->decoders= ['xml'=> new XmlEncoder(),'json'=>new JsonEncoder(),'yaml'=> new YamlEncoder(),'csv'=>new CsvEncoder()];
    }
    /**
     * @param $data
     * @param $type
     * @param array $context
     * @return array
     * @throws \Exception
     */
    public function decode(string $data,$type,array $context=[]){
        return $this->getDecoder ($type)->decode ($data,$type,$context);
    }

    /**
     * non efficace car on doit tout instancier alors qu'on ne veut qu'un
     * à gérer autrement switch ou methode magique
     * @param string $type
     * @return DecoderInterface
     * @throws \Exception
     */
   public function getDecoder(string $type){
       $decoders= ['xml'=> new XmlEncoder(),'json'=>new JsonEncoder(),'yaml'=> new YamlEncoder(),'csv'=>new CsvEncoder()];
       if($type===""){
           throw new \Exception("unable to decode with empty type");
       }
       if(!array_key_exists (trim(strtolower ($type)),$this->decoders)){
           throw new \Exception("Type $type decoder is not supported");
       }
      return $decoders[trim(strtolower ($type))];
   }
}
