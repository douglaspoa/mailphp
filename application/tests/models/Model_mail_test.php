<?php

class Mail_model_test extends TestCase
{
    public function test_getValues()
    {
        $this->resetInstance();
        $this->CI->load->model('Model_mail');
        $this->obj = $this->CI->Model_mail;

        $expectedData = [
            'valor' => '1.300,50',
            'nome' => 'Douglas',
            'endereco' => 'Assis Brasil, 1309',
            'vencimento' => '12/19'

        ];

        $contentFile = file_get_contents('../../teste.txt');
        $content = $this->obj->getValues($contentFile);

        foreach ($expectedData as $expected ) {
            foreach ($content as $contentEnd) {
                $this->assertEquals($expected, $contentEnd);
            }
        }
    }

    public function test_detectEncoding()
    {
        $this->resetInstance();
        $this->CI->load->model('Model_mail');
        $this->obj = $this->CI->Model_mail;

        $expected = 'UTF-8';
        $contentFile = file_get_contents('../../teste.txt');

        $typeText = $this->obj->detectEncoding($contentFile);

        $this->assertEquals($expected, $typeText);
    }

    public function test_sendDataToApi()
    {
        $this->resetInstance();
        $this->CI->load->model('Model_mail');
        $this->obj = $this->CI->Model_mail;

        $data = [
            'valor' => '1.300,50',
            'nome' => 'Douglas',
            'endereço' => 'Assis Brasil, 1309',
            'vencimento' => '12/19'

        ];

        $expected = true;
        $result = $this->obj->sendDataToApi($data);

        $this->assertEquals($expected, $result);
    }

}