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
		/*carrega a nossa view */
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
		$this->load->view('mail', $inbox);
	}
}

?>
