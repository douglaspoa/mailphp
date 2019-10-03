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
        include_once('lib/imap.php');

        $hostName = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
        $userName = $data['email'];
        $password = $data['password'];

        $email = new Imap();
        $connect = $email->connect(
            $hostName, //host
            $userName, //username
            $password //password
        );

        printrx($connect);

    }
}
