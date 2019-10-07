<?php

class Mail extends CI_Controller
{
	/**
	 * Carrega o formulário
	 * @param nenhum
	 * @return view
	 */
	function index()
	{
		$this->load->view('mail');
	}

	function email()
	{
		$this->load->model('model_mail');

		$data = [
			'email' => $this->input->post('email', true),
			'password' => $this->input->post('password', true)
		];

		$inbox = $this->model_mail->getEmailAttachments($data);

		if ($inbox == 0) {
			die('Ocorreu um erro ao se conectar com o email');
		}

		if ($inbox == 1) {
			die('Não existem anexos no email!');
		}

		if($inbox == 2) {
			die('Não foi possivel conectar com a API!');
		}

		return;

	}
}

?>
