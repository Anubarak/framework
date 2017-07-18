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

    /**
     * @param $asset            assetModel
     * @return bool|int|string
     */
    public function saveAsset($asset){
        $this->defineDefaultValues($asset);

        if(!$this->validate($asset)){
            return false;
        }

        if(!$this->table || !$this->primary_key){
            $className = Anu::getClassName($asset);
            $this->table = anu()->$className->table;
            $this->primary_key = anu()->$className->primary_key;
        }

        //check if its a new entry of if we should update an existing one
        if(!$asset->id){
            $data = $asset->getData();

            $values = array();
            $relationsToSave = array();
            foreach ($asset->defineAttributes() as $key => $value){
                if($data[$key] !== 'now()'){
                    $values[$key] = ($data[$key])? $data[$key] : 0;
                }else{
                    $values["#".$key] = $data[$key];
                }
            }

            anu()->database->insert($this->table, $values);
            $id = anu()->database->id();
            $asset->id = $id;
            return $id;
        }else{
            $data = $asset->getData();
            $values = array();
            foreach ($asset->defineAttributes() as $key => $value){
                if($data[$key] !== 'now()'){
                    $values[$key] = ($data[$key])? $data[$key] : 0;
                }else{
                    $values["#".$key] = $data[$key];
                }
            }

            anu()->database->update($this->table, $values, array(
                $this->table . "." . $this->primary_key => $asset->id
            ));

            return anu()->database->id();
        }
    }

    /**
     * @param $entryId
     * @return baseModel|entryModel|null
     * @throws \Exception
     */
    public function getAssetById($assetId){
        if(!isset($assetId) || !is_numeric($assetId))
        {
            throw new \Exception('ID is not specified.');
        }

        $this->id = $assetId;
        if($model = Anu::getClassByName($this, 'Model', true)){
            $where = array($this->table . "." . $this->primary_key => $assetId);
            $select = $this->iterateDBSelect($model->defineAttributes(), null, $this->table);
            $row = anu()->database->select($this->table, $select, $where);
            anu()->database->debugError();
            if(!empty($row) && is_array($row)){
                return $this->populateModel($row[0], $model);
            }
        }else{
            throw new \Exception('could not find ' . Anu::getClassName($this));
        }
        return null;
    }

    /**
     * @param $asset baseModel|assetModel
     */
    public function setDataFromPost($asset){
        $post = anu()->request->getValue('data');
        $attributes = $asset->defineAttributes();
        foreach ($attributes as $k => $v){
            if($v[0] == AttributeType::File){

                $file = $_FILES[$k];
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
            if(array_key_exists($k, $post)){
                $asset->setData($post[$k], $k);
            }
        }
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