# Instalação

Apenas clonar o repositório e rodar em qualquer servidor apache e rodar o comando abaixo. `composer install `

## Utilização

Estou utilizando o servidor do gmail para poder acessar o email, essa configuração pode ser alterada em application/models/Model_mail.php
```php
 $hostName = "Algum servidor"
```

Também a um limite definido de emails o qual a aplicação vai percorrer para procurar os anexos na mesma model.
```php
 $maxEmails = "2"
```

 O anexo utilizado como exemplo para enviar os dados para API está na raiz do projeto "teste.txt".

As informações estão sendo enviadas para uma api em node que pode ser encontrada no link [api node](https://github.com/douglaspoa/node-api)

`php $url = 'http://localhost:3000/data'; `


## Licença
[MIT](https://choosealicense.com/licenses/mit/)
