<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_Importer
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Blugento_Importer_Helper_Data extends Mage_Core_Helper_Data
{
    const DATA_SOURCE_UPLOAD = 1;
    const DATA_SOURCE_LOCAL  = 2;
    const DATA_SOURCE_REMOTE = 3;
    const DATA_SOURCE_FTP = 4;
    const DATA_SOURCE_SFTP = 5;

    const DATA_TEST_LIMIT = 50;

    /**
     * Maximum size of uploaded files.
     *
     * @return int
     */
    public function getMaxUploadSize()
    {
        return min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
    }

    /**
     * Return the profile file data
     *
     * @param $profile
     * @param bool $onlyColumns
     * @return mixed
     */
    public function getProfileFileData($profile, $onlyColumns=false, $limit=false)
    {
        $dataSource = $profile->getDataSource();
        $fileFormat = $profile->getFileFormat();
        $entityNode = $profile->getXmlEntityNode();

        $filePath = $profile->getFilePath();
        $fileName = $profile->getFileName();
        $file = Mage::getBaseDir() . DS . $filePath . DS . $fileName;

        if ($dataSource == self::DATA_SOURCE_UPLOAD || $dataSource == self::DATA_SOURCE_LOCAL) {
            if ($fileFormat == 'csv') {
                $fileData = $this->_getLocalCsvFileData($file, $onlyColumns, $limit, $profile->getDelimiter(), $profile->getEnclosure());
            } else {
                $fileData = $this->_readXmlFile($file, $entityNode);
            }
        } else if ($dataSource == self::DATA_SOURCE_REMOTE) {
            if ($fileFormat == 'csv') {
                $fileData = $this->_getRemoteCsvFileData($profile, $onlyColumns, $limit);
            } else {
                $this->getRemoteXmlFileData($profile);
                $fileData = $this->_readXmlFile($file, $entityNode);
            }
        } else if ($dataSource == self::DATA_SOURCE_FTP) {
            $data = $this->_getFtpFile($profile);

            if ($downloadedPath = $data->getDownloadedFile()) {
                if ($fileFormat == 'csv') {
                    $fileData = $this->_getLocalCsvFileData($downloadedPath, $onlyColumns, $limit, $profile->getDelimiter(), $profile->getEnclosure());
                } else {
                    $fileData = $this->_readXmlFile($downloadedPath, $entityNode);
                }

                if (file_exists($downloadedPath)) {
                    unlink($downloadedPath);
                }
            } else {
                $fileData = $data;
            }
        } else if ($dataSource == self::DATA_SOURCE_SFTP) {
            $data = $this->_getSftpFile($profile);

            if ($downloadedPath = $data->getDownloadedFile()) {
                if ($fileFormat == 'csv') {
                    $fileData = $this->_getLocalCsvFileData($downloadedPath, $onlyColumns, $limit, $profile->getDelimiter(), $profile->getEnclosure());
                } else {
                    $fileData = $this->_readXmlFile($downloadedPath, $entityNode);
                }

                if (file_exists($downloadedPath)) {
                    unlink($downloadedPath);
                }
            } else {
                $fileData = $data;
            }
        } else {
            return $dataSource;
        }

        return $fileData;
    }

    /**
     * Download ftp file
     *
     * @param $profile
     * @return Varien_Object
     */
    private function _getFtpFile($profile)
    {
        $ftpServer = $profile->getFtpServer();
        $ftpUsername = $profile->getFtpUsername();
        $ftpPassword = $profile->getFtpPassword();
        $ftpFilepath = $profile->getFtpFilepath();

        $localFilename = explode('/', $ftpFilepath);
        $localFilename = end($localFilename);

        $data = new Varien_Object();
        if (!$ftpServer || !$ftpUsername || !$ftpPassword || !$ftpFilepath) {
            $data->setError('All FTP credentials are required!');
            return $data;
        }

        $localFilepath = Mage::getBaseDir() . DS . 'var/importer' . DS . $localFilename;

        $connection = ftp_connect($ftpServer);
        ftp_login($connection, $ftpUsername, $ftpPassword);
        ftp_pasv($connection, true);
        ftp_get($connection, $localFilepath, $ftpFilepath, FTP_BINARY);
        ftp_close($connection);

        $data->setDownloadedFile($localFilepath);

        return $data;
    }

    /**
     * Get data from SFTP
     *
     * @param $profile
     * @return Varien_Object
     */
    private function _getSftpFile($profile)
    {
        $server = $profile->getFtpServer();
        $pass = $profile->getFtpPassword();
        $user = $profile->getFtpUsername();
        $filepath = $profile->getFtpFilepath();

        $data = new Varien_Object();
        if (!$server || !$user || !$pass || !$filepath) {
            $data->setError('All FTP credentials are required!');
            return $data;
        }

        $filename = explode('/', $filepath);
        $filename = end($filename);

        $remote = "sftp://$user:$pass@$server/$filepath";
        $localFilepath = Mage::getBaseDir() . DS . 'var/importer' . DS . $filename;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $remote);
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
        curl_setopt($curl, CURLOPT_USERPWD, "$user:$pass");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($curl);
        curl_close($curl);

        if ($content) {
            file_put_contents($localFilepath, $content);
        }

        $data->setDownloadedFile($localFilepath);

        return $data;
    }

    /**
     *  Return the local CSV file data
     *
     * @param string $file
     * @param bool $onlyColumns
     * @param $limit
     * @param string $delimiter
     * @param string $enclosure
     * @return Varien_Object
     */
    private function _getLocalCsvFileData($file, $onlyColumns=false, $limit, $delimiter , $enclosure)
    {
        $csvData = new Varien_Object();

        /** check if file exist */
        if (!file_exists($file)) {
            $errorMess = $this->__('File %s do not exist', $file);
            $csvData->setError($errorMess);

            return $csvData;
        }

        $limit = $limit && $limit >=0 ? $limit : 200000;

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mtype = finfo_file($finfo, $file);
        finfo_close($finfo);

        $errors = array();


        $content = new Varien_Data_Collection();

        if(strtolower($ext) == 'csv' || in_array($mtype, array('text/csv','text/plain','text/comma-separated-values','application/csv'))){
            $csv = new Varien_File_Csv();
            $csv->setDelimiter($delimiter)->setEnclosure($enclosure);
            try {
                $csvContent = $csv->getData($file);

                $columns = array_shift($csvContent);

                $useDefStore = 0;
                foreach ($columns as $column) {
                    if ($column == 'store') {
                        $useDefStore++;
                    }
                }

//                if ($useDefStore == 0) {
//                    array_unshift($columns, 'store');
//                }

                $csvData->setColumns($columns);
                if ($onlyColumns) {
                    return $csvData;
                }

                foreach($csvContent as $k => $v){
                    /*if ($useDefStore == 0) {
                        array_unshift($v, 'store-test');
                    }*/
                    $csvContent[$k] = array_combine($columns, array_values($v));
                }

                $i = 0;
                foreach($csvContent as $data){
                    if ($i >= $limit) {
                        continue;
                    }

                    $i++;
                    $contentItem = new Varien_Object();

                    $refinedColumns = $columns;
                    if (count($columns) != count(array_values($data))){
                        $refinedColumns = array();
                        foreach ($columns as $column){
                            if (!in_array($column, $refinedColumns)){
                                $refined_columns[] = $column;
                            }
                        }
                    }

                    $dataMap = array_combine($refinedColumns, array_values($data));

                    foreach ($dataMap as $id => $val) {
                        $contentItem->setData($id, $val);
                    }

                    $content->addItem($contentItem);
                }
                $csvData->setItems($content);

            } catch (Exception $e) {
                $errors[] = $this->__("Unable to read csv file '%s'. Error: %s", $file, $e->getMessage());

                Mage::logException($e);
            }
        } else {
            $errors[] = $this->__("Unable to read csv file '%s'. File extension is not in the allowed list.", $file);
        }

        if (count($errors)) {
            $errorMess = implode('; ', $errors);
            $csvData->setError($errorMess);
        }

        return $csvData;
    }

    private function _readXmlFile($file, $entityNode)
    {
        $entityNode = $entityNode ? $entityNode : 'product';

        $xmlObj = new Varien_Simplexml_Config($file);

        $xmlProductsData = new Varien_Object();
        $content = new Varien_Data_Collection();

        $products = array();

        if ($xmlObj->getXpath('channel')) {
            $xmlData = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOCDATA);
            $ns = $xmlData->getNamespaces(true);
            foreach($xmlData->channel->$entityNode as $item) {
                $g = $item->children($ns["g"]);
                foreach ($g as $key => $gItem) {
                    $gItem = (array)$gItem;
                    $item->$key = $gItem[0];
                }
                $products[] = (array)$item;
            }
        } else {
            $xmlData = $xmlObj->getXpath($entityNode);

            if (count($xmlData) == 1) {
                $xmlData = $xmlData[0];
            }
            foreach ($xmlData as $product) {
                $products[] = $product->asArray();
            }
        }

        $columns = array();
        foreach ($products as $product) { // TODO::refine this
            $contentItem = new Varien_Object();
            foreach ($product as $attribute=>$value) {
                if (is_array($value)) {
                    //change xml structure for tier and group price
                    if ($attribute == 'group_price' || $attribute == 'tier_price') {
                        $theValue = array();
                        foreach ($value as $v) {
                            $row = implode('::', $v);
                            $theValue[] = $row;
                        }
                        $theValue = implode('~', $theValue);
                        $contentItem->setData($attribute, $theValue);
                    } else {
                        foreach ($value as $attribute=>$value) {
                            if (is_array($value)) {
                                foreach ($value as $attribute=>$value) {
                                    $contentItem->setData($attribute, $value);
                                    if (is_array($value)) {
                                        foreach ($value as $attribute=>$value) {
                                            $contentItem->setData($attribute, $value);
                                        }
                                    } else {
                                        $contentItem->setData($attribute, $value);
                                    }
                                }
                            } else {
                                $contentItem->setData($attribute, $value);
                            }
                        }
                    }
                } else {
                    $contentItem->setData($attribute, $value);
                }
            }
            $content->addItem($contentItem);
            if (!count($columns)){
                foreach ($contentItem->getData() as $key=>$val) {
                    $columns[] = $key;
                }
            }
        }

        $xmlProductsData->setItems($content);

        $xmlProductsData->setColumns($columns);

        return $xmlProductsData;
    }

    /**
     * Return the remote CSV file data
     *
     * @return null|Varien_Data_Collection
     */
    private function _getRemoteCsvFileData($profile, $onlyColumns=false, $limit)
    {
        $delimiter = $profile->getDelimiter();
        $enclosure = $profile->getEnclosure();
        $remoteUrl = $profile->getRemoteUrl();

        $errors = array();
        $csvData = new Varien_Object();

        $content = new Varien_Data_Collection();
        $limit = $limit ? $limit : 200000;
        try {
            ini_set('auto_detect_line_endings',TRUE);

            $handle = fopen($remoteUrl,'r');
            if (!$handle) {
                $errors[] = $this->__('Remote file: %s > File open failed', $remoteUrl);
            } else {
                $remoteData = array();
                $i = 0;
                while ((($data = fgetcsv($handle, null, $delimiter, $enclosure) ) !== FALSE) && $i <= $limit) {

                    $remoteData[] = str_replace($enclosure, '', $data);
                    $i++;
                }
                ini_set('auto_detect_line_endings',FALSE);

                $columns = array_shift($remoteData);
                $useDefStore = 0;
                foreach ($columns as $column) {
                    if ($column == 'store') {
                        $useDefStore++;
                    }
                }

//                if ($useDefStore == 0) {
//                    array_unshift($columns, 'store');
//                }

                $csvData->setColumns($columns);
                if ($onlyColumns) {
                    return $csvData;
                }

                foreach($remoteData as $data){
                    $contentItem = new Varien_Object();
                    $datas = array_values($data);
//                    if ($useDefStore == 0) {
//                        array_unshift($datas, 'store-test');
//                    }
                    $dataMap = array_combine($columns, $datas);

                    foreach ($dataMap as $id => $val) {
                        $contentItem->setData($id, $val);
                    }

                    $content->addItem($contentItem);
                }


                $csvData->setItems($content);
            }

        } catch (Exception $e) {
            $errors[] = $this->__('Remote file: %s ERROR: %s', $remoteUrl, $e->getMessage());
        }

        if (count($errors)) {
            $errorMess = implode('; ', $errors);
            $csvData->setError($errorMess);
        }

        return $csvData;
    }

    /**
     * Return the remote XML file data
     *
     */
    public function getRemoteXmlFileData($profile)
    {
        try {
            $credentials = $this->getCredentialsFromUrl($profile->getRemoteUrl());

            if ($credentials) {
                $content = $this->getDataByCurl($profile->getRemoteUrl(), $credentials);

                if (!$content) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        $this->__('Cannot get remote file. Possible errors: Curl error; Maximum 50 MB allowed...'));
                    return;
                }
            } else {
                $content = file_get_contents($profile->getRemoteUrl());
            }

            $urlArr = explode('/', $profile->getRemoteUrl());
            $filename = end($urlArr);
            $extArr = explode('.', $filename);
            $ext = end($extArr);
            if (!$filename || $ext == 'php') {
                $fni = count($urlArr) - 2;
                $filename = $urlArr[$fni] . '.xml';
            }
            if (count(explode('.', $filename)) < 2) {
                $filename = $filename . '.xml';
            }

            $filepath = 'var/importer';
            $fullFilepath = Mage::getBaseDir() . '/' . $filepath . '/' . $filename;

            if (!file_exists(Mage::getBaseDir() . '/' . $filepath)) {
                mkdir(Mage::getBaseDir() . '/' . $filepath);
            }

            if (!file_exists($fullFilepath)) {
                $file = fopen($fullFilepath, 'w');
                fclose($file);
            }

            $profile->setFileName($filename);
            $profile->setFilePath($filepath);

            file_put_contents($fullFilepath, $content);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function compressFile($source, $destination, $quality) {
        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);

        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);

        imagejpeg($image, $destination, $quality);
    }

    public function getDataByCurl($url, $credentials)
    {
        // check authentication type
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        if (stripos($data, 'digest') !== false) {
            $type = 'digest';
        } else {
            $type = 'basic';
        }

        // check content type
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $credentials['username'] . ':' . $credentials['password']);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_NOBODY, 1);
        if ($type == 'digest') {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        } elseif ($type == 'basic') {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['download_content_length'] > 52428800) {
            return null;
        } else {
            // get content
            $crl = curl_init();
            curl_setopt($crl, CURLOPT_URL, $url);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($crl, CURLOPT_USERPWD, $credentials['username'] . ':' . $credentials['password']);
            if ($type == 'digest') {
                curl_setopt($crl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            } elseif ($type == 'basic') {
                curl_setopt($crl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            }

            $output = curl_exec($crl);
            curl_close($crl);
            return $output;
        }
    }

    public function getCredentialsFromUrl($url)
    {
        if (strpos($url, '@') !== false) {
            $exploded = explode('://' , $url);
            $exploded = explode('@', $exploded[1]);
            $urlPass = explode(':', $exploded[0]);

            return array (
                'username' => $urlPass[0],
                'password' => $urlPass[1],
            );
        }

        return null;
    }
}
