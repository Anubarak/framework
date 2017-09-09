<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 10:01
 */

namespace Anu;

class assetService extends baseService
{
    protected   $table = 'assets';
    protected   $primary_key = 'asset_id';
    protected   $id = null;
    public $model = 'asset';
    public $tableName = 'assets';

    /**
     * @param $asset            assetModel
     * @return bool|int|string
     */
    public function saveAsset($asset){
        $this->saveElement($asset);
    }

    /**
     * @param $entryId
     * @return baseModel|entryModel|null
     * @throws \Exception
     */
    public function getAssetById($assetId){
        return $this->getElementById($assetId);
    }

    /**
     * @return baseModel|baseService|entryModel|entryService|bool|null|string
     * @throws \Exception
     */
    public function generateEntryFromPost(){
        if(isset($post[$this->primary_key])){
            $asset = $this->getElementById($post[$this->primary_key]);
        }else{
            $asset = Anu::getModelByName('asset');
        }

        $post = anu()->request->getValue('data', array());

        $attributes = $asset->defineAttributes();
        foreach ($attributes as $k => $v){
            if($v[0] == AttributeType::File){
                if(array_key_exists($k, $_FILES)){
                    $file = $_FILES[$k];
                }else{
                    $file = array_values($_FILES)[0];
                }

                $config = anu()->config->get('paths');
                $path = BASE . $config['imgPath'];
                $additionalPath = (isset($v['folder']))? $v['folder'] : '';
                $compleatePath = $path . $additionalPath;
                if($file['error'] == UPLOAD_ERR_OK){
                    if(!@mkdir($compleatePath) && !is_dir($compleatePath)){
                        throw new \Exception('could not find or create folder ' . $compleatePath);
                    }
                    if(move_uploaded_file($file['tmp_name'], "$compleatePath/" . $file['name'])){
                        $post[$k] =  $additionalPath . "/" . $file['name'];
                    }else{
                        throw new \Exception('could not upload file to ' . "$compleatePath/" . $file['name']);
                    }
                }
            }
            if(($k === 'title' || $k === 'name') && (!isset($post[$k]) || !$post[$k])){
                if(array_key_exists($k, $_FILES)){
                    $file = $_FILES[$k];
                }else{
                    $file = array_values($_FILES)[0];
                }
                $post[$k] = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['name']);
            }

            if(array_key_exists($k, $post)){
                $asset->setData($post[$k], $k);
            }
        }
        return $asset;
    }

    public function display($assetId){
        if(!$assetId){
            return false;
        }

        if($asset = $this->getAssetById($assetId)){
            $config = anu()->config->get('paths');
            $path = BASE . $config['imgPath'];
            $imagePath = $path . $asset->getAttribute('path');

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $imagePath);
            finfo_close($finfo);

            switch ($mime_type){
                case "image/jpeg":
                    // Set the content type header - in this case image/jpg
                    header('Content-Type: image/jpeg');

                    // Get image from file
                    $img = imagecreatefromjpeg($imagePath);

                    // Output the image
                    imagejpeg($img);

                    break;
                case "image/png":
                    // Set the content type header - in this case image/png
                    header('Content-Type: image/png');

                    // Get image from file
                    $img = imagecreatefrompng($imagePath);

                    // integer representation of the color black (rgb: 0,0,0)
                    $background = imagecolorallocate($img, 0, 0, 0);

                    // removing the black from the placeholder
                    imagecolortransparent($img, $background);

                    // turning off alpha blending (to ensure alpha channel information
                    // is preserved, rather than removed (blending with the rest of the
                    // image in the form of black))
                    imagealphablending($img, false);

                    // turning on alpha channel information saving (to ensure the full range
                    // of transparency is preserved)
                    imagesavealpha($img, true);

                    // Output the image
                    imagepng($img);

                    break;
                case "image/gif":
                    // Set the content type header - in this case image/gif
                    header('Content-Type: image/gif');

                    // Get image from file
                    $img = imagecreatefromgif($imagePath);

                    // integer representation of the color black (rgb: 0,0,0)
                    $background = imagecolorallocate($img, 0, 0, 0);

                    // removing the black from the placeholder
                    imagecolortransparent($img, $background);

                    // Output the image
                    imagegif($img);

                    break;
            }

            // Free up memory
            imagedestroy($img);
        }
    }

}