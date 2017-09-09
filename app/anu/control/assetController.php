<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 02.08.2017
 * Time: 16:59
 */

namespace Anu;


class assetController extends baseController
{
    /**
     * Store Image from Angular Request
     */
    public function storeAsset(){
        $asset = anu()->asset->generateEntryFromPost();
        anu()->asset->saveAsset($asset);
        $url = $asset->getUrl();
        $this->returnJson(array('url' => $url));
    }

    /**
     * Drag/Drop from Quill Javascript method
     *
     * @throws \Exception
     */
    public function storeAssetFromB64(){
        $asset = new assetModel('asset');
        $b64Image = anu()->request->getValue('image');
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $b64Image));

        $config = anu()->config->get('paths');
        $attribute = $asset->defineAttributes();
        $time = time();
        $path = BASE . $config['imgPath'];
        $additionalPath = (isset($attribute['path']['folder']))? $attribute['path']['folder'] : '';
        $compleatePath = $path . $additionalPath;



        if(!@mkdir($compleatePath) && !is_dir($compleatePath)){
            throw new \Exception('could not find or create folder ' . $compleatePath);
        }
        $filename = "/" . $time . ".jpg";
        // Save the image in a defined path
        file_put_contents($compleatePath . $filename ,$data);

        $asset->path = $additionalPath . $filename;
        $asset->title = $time;
        $asset->name = $time;
        anu()->asset->saveAsset($asset);
        $url = $asset->getUrl();
        $this->returnJson(array('url' => $url));
    }


    /**
     * old Method....
     */
    public function saveAsset(){
        $asset = anu()->asset->generateEntryFromPost();
        anu()->asset->saveAsset($asset);
        anu()->database->debugError();

        if($asset->getErrors()){
            echo "<pre>";
            var_dump($asset->getErrors());
            echo "</pre>";
            die();
        }
        echo "<pre>";
        var_dump($asset);
        echo "</pre>";
        die();
    }
}