<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_mail extends CI_Model {
	/**
	 * Pega anexos do email
	 * @param $data. Array que contém os dados para logar no email
	 * @return boolean
	 */
	public function getEmailAttachments($data)
    {
        error_reporting(0);

        $hostName = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
        $userName = $data['email'];
        $password = $data['password'];
        $maxEmails = 2;

        $inbox = $this->connect(
            $hostName,
            $userName,
            $password
        );

        if ($inbox == false) {
            return 4;
        }

        $data = $this->imapGetDataFromFile($inbox, $maxEmails);

        imap_close($inbox);

        $result = $this->sendDataToApi($data);

        if ($data == false) {
            return 3;
        }

        return $result;
    }

    /**
     * Enviar dados para API
     * @param $data. Array que contém os dados para o envio para API
     * @return string
     */
    public function sendDataToApi($data)
    {

        if ($data == false) {
            return 1;
        }

        $dataJson = json_encode($data);

        $url  = 'http://localhost:3000/data';
        $ch   = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJson);

        $result = curl_exec($ch);

        curl_close($ch);

        if ($result ==  false) {
            return 2;
        }

    }

    /**
     * Formata dados da string e retorna em formato de array
     * @param $content. string que contem os dados que o usário vai enviar para API
     * @return array
     */
    public function getValues($content)
    {
        $content = $this->convertEncoding($content, 'UTF-8');

        $value = explode("R$", $content);
        $value = explode("\n", $value[1]);

        $name = explode('Nome:', $content);
        $name = explode("\n", $name[1]);

        $address = explode("Endereço:", $content);
        $address = explode("\n", $address[1]);

        $expiry = explode("Vencimento:", $content);
        $expiry = explode("\n", $expiry[1]);

        $data = [
          'valor' => $value[0],
          'nome' => $name[0],
          'endereco' => $address[0],
          'vencimento' => $expiry[0]
        ];

        return $data;
    }

    /**
     * Verifica o encode que está o arquivo
     * @param $string. string que contém o texto a ser analisado
     * @return string
     */
    public function detectEncoding($string)
    {
        if (preg_match('%^(?: [\x09\x0A\x0D\x20-\x7E] | [\xC2-\xDF][\x80-\xBF] | \xE0[\xA0-\xBF][\x80-\xBF] | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} | \xED[\x80-\x9F][\x80-\xBF] | \xF0[\x90-\xBF][\x80-\xBF]{2} | [\xF1-\xF3][\x80-\xBF]{3} | \xF4[\x80-\x8F][\x80-\xBF]{2} )*$%xs', $string))
            return 'UTF-8';

        return mb_detect_encoding($string, ['UTF-8', 'ASCII', 'ISO-8859-1', 'JIS', 'EUC-JP', 'SJIS']);
    }

    /**
     * Converte a string para um tipo de encoding
     * @param $string. string que contém o texto a ser convertido
     * @param $toEncoding. string que diz para qual tipo de enconding a string vai ser convertida
     * @param $fromEncoding. string que diz qual o encoding atual do arquivo
     * @return string
     */
    public function convertEncoding($string, $toEncoding, $fromEncoding = '')
    {
        if ($fromEncoding == '')
            $fromEncoding = $this->detectEncoding($string);

        if ($fromEncoding == $toEncoding)
            return $string;

        return mb_convert_encoding($string, $toEncoding, $fromEncoding);
    }

    /**
     * Conecta com a caixa de entrada do email
     * @param $hostName. string que contém os dados do host
     * @param $userEmail. string que contém o email do usuário
     * @param $password. string que contém a senha do usuário
     * @return string
     */
    public function connect($hostName, $userEmail, $password) {
        $connection = @imap_open($hostName, $userEmail, $password);

        if (!$connection) {
            return false;
        }
        return $connection;
    }

    /**
     * Percorre a caixa de emails e pega os dados dos anexos
     * @param $inbox connection conexão com o host email
     * @param $maxEmails integer numero máximo de emails que a função vai percorrer
     * @return array
     */
    public function imapGetDataFromFile($inbox, $maxEmails)
    {
        $emails = imap_search($inbox,'ALL');

        if($emails) {

            $count = 1;
            $finalData = [];

            rsort($emails);

            foreach($emails as $email_number)
            {

                $overview = imap_fetch_overview($inbox,$email_number,0);

                $message = imap_fetchbody($inbox,$email_number,2);

                $structure = imap_fetchstructure($inbox, $email_number);

                $attachments = array();

                if(isset($structure->parts) && count($structure->parts))
                {
                    for($i = 0; $i < count($structure->parts); $i++)
                    {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );

                        if($structure->parts[$i]->ifdparameters)
                        {
                            foreach($structure->parts[$i]->dparameters as $object)
                            {
                                if(strtolower($object->attribute) == 'filename')
                                {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }

                        if($structure->parts[$i]->ifparameters)
                        {
                            foreach($structure->parts[$i]->parameters as $object)
                            {
                                if(strtolower($object->attribute) == 'name')
                                {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if($attachments[$i]['is_attachment'])
                        {
                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);

                            if($structure->parts[$i]->encoding == 3)
                            {
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            }
                            elseif($structure->parts[$i]->encoding == 4)
                            {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }

                foreach($attachments as $attachment)
                {
                    if($attachment['is_attachment'] == 1)
                    {
                        $filename = $attachment['name'];
                        if(empty($filename)) $filename = $attachment['filename'];

                        if(empty($filename)) $filename = time() . ".dat";

                        $file = "./".$email_number."-" . $filename;
                        $fp = fopen($file, "w+");

                        fwrite($fp, $attachment['attachment']);

                        $content = file_get_contents($file);
                        $data = $this->getValues($content);

                        fclose($fp);
                        unlink($file);

                        $finalData[] = $data;

                    }

                }

                if($count++ >= $maxEmails) break;
            }

        }

        if(count($finalData) == 0) {
            return false;
        }

        //Descomentar a linha a baixo para ver os dados que estão sendo enviados para API
        //printrx($finalData);

        return $finalData;
    }

}
