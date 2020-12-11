<?php
namespace Alxche\Cipher;

use Exception;
use PHP_Token_PUBLIC;

class StudyBegin 
{   
    /**
     * HKDF позволяет указывать информационные строки, специфичные для контекста/приложения.
     * В данном случае контекстом является тип файла, для каждого из которых своя информационная строка:
     * | Media Type     | Application Info         |
     */
    const HKDF_IMAGE     =  'WhatsApp Image Keys';
    const HKDF_VIDEO     =  'WhatsApp Video Keys';
    const HKDF_AUDIO     =  'WhatsApp Audio Keys';
    const HKDF_DOCUMENT  =  'WhatsApp Document Keys';


    private $mediakey;
    private $mediaKeyExpanded;
    private $appinfo;
    private $iv;
    private $cipherKey;
    private $macKey;
    
    /**
     * @param string $mediatype String "AUDIO | VIDEO | IMAGE"
     * @return void
     */
    public function setKey(string $mediatype): StudyBegin
    {
        
        $file = fopen(__DIR__.'/keys/'.$mediatype.'.key', 'r');
        $key = fread($file, '32');
        fclose($file);
        $this->mediakey = $key;
        $this->setAppInfo($mediatype);
        return $this;
    }
    /**
     * Expand mediaKey to 112 bytes using HKDF with algo and type-specific application info. 
     * 
     * @param string $algo - Crypto algorythm from list hash_algos()
     * @param int $length - Target length of hkdf key 
     * @return StudyBegin
     */
    public function hkdfKey(string $algo, int $length): StudyBegin
    {
        if($this->mediakey === NULL){
            throw new Exception('Need to set key first');
        }

        $key = $this->mediakey;
        $info = $this->appinfo;
        $k = hash_hkdf($algo, $key, $length, $info);
        if($k !== false){
            $this->mediaKeyExpanded = $k;
            $hkdf_length = file_put_contents('hkdf.key', $k);
        } else {
            throw new Exception('Hash_hkdf error');
        }

        return $this;
    }

    public function splitKeyExpanded()
    {
        $k = $this->mediaKeyExpanded;
        $this->iv = substr($k, 0, 16);
        $this->cipherKey = substr($k, 16, 32);
        $this->macKey = substr($k, 48, 32);
        
    }

    public function showProp()
    {
        
        return [
            'mediaKey'=>$this->mediakey,
            'mediaKeyLength'=>strlen($this->mediakey),
            'mediaKeyExpanded'=> $this->mediaKeyExpanded,
            'mediaKeyExpandedLength'=> strlen($this->mediaKeyExpanded),
            'iv'=>$this->iv,
            'ivLength'=>strlen($this->iv),
            'cipherKey'=> $this->cipherKey,
            'cipherKeyLength'=> strlen($this->cipherKey),
            'macKey'=>$this->macKey,
            'macKeyLength'=>strlen($this->macKey),
        ];
    }

    private function setAppInfo(string $mediatype)
    {
        switch($mediatype){
            case 'AUDIO':
                $this->appinfo = self::HKDF_AUDIO;
                break;
            case 'VIDEO':
                $this->appinfo = self::HKDF_VIDEO;
                break;
            case 'IMAGE':
                $this->appinfo = self::HKDF_AUDIO;
                break;
        }
    }
}