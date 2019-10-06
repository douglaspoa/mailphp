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

        $data = $this->imapGetDataFromFile($inbox, $maxEmails);

        imap_close($inbox);

        $result = $this->sendDataToApi($data);

        if ($result === false) {
            return "Ocorreu um erro ao enviar os dados para API";
        }

        return "Dados enviados com sucesso";
    }

    public function sendDataToApi($data)
    {
        $dataJson = json_encode($data);

        $url  = 'http://localhost:3000/data';
        $ch   = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJson);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;

    }

    /**
     * Formata dados da string e retorna em formato de array
     * @param $content. string que contem os dados que o usário vai enviar para API
     * @return array
     */
    public function getValues($content)
    {

        $content = utf8_encode ( $content);

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
          'endereço' => $address[0],
          'vencimento' => $expiry[0]
        ];

        return $data;
    }

    /**
     * Conecta com a caixa de entrada do email
     * @param $hostName. string que contém os dados do host
     * @param $userEmail. string que contém o email do usuário
     * @param $password. string que contém a senha do usuário
     * @return string
     */
    public function connect($hostName, $userEmail, $password) {
        $connection = imap_open($hostName, $userEmail, $password) or die('Ocorreu um problema ao se conectar com o email: ' . imap_last_error());

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

        return $finalData;
    }

}
