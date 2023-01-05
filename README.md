# i-angular-php-test-back
Projeto em PHP para o teste técnico da WK Technology 
- [Repositório do Front em Ionic + Angular](https://github.com/leonardofmed/i-angular-php-test-front) 
- [**DEMO FUNCIONAL ONLINE**](http://wktest.epizy.com)

## Apresentação e observações
Esse projeto foi desenvovido de forma integral, sem utilização de templates, e foi realizado entre [30/12/2022 e 05/01/2023](https://github.com/leonardofmed/i-angular-php-test-back/commits?author=leonardofmed&since=2022-12-30&until=2023-01-06), levando cerca de 40h para conclusão da primeira versão com as principais funcionalidades, considerando front e back.

### Formato PWA
O formato PWA (Web-based Progressive Web App) foi escolhido por possibilitar a demonstração da versatilidade dos frameworks utilizados. É possível criar sistema pensados exclusivamente para navagedores no formato widescreen, telas de celulares ou ambos.

### Limitações
O objetivo desse projeto foi fazer um produto mínimo viável com as condições estabelecidas pelo teste. Sendo assim, algumas funcionalidades não foram desenvolvidadas a fundo ou foram feitas de maneira mais simples, fugindo das normas convencionais e utilização de boas práticas. O servidor utilizado atualmente para hospedar o sistema também possui algumas limitações. Por ser um host gratutíto, algumas configurações de segurança foram implementadas automaticamente, o que causa algumas limitações na utilização:
- A API só pode ser utilizada inteiramente no serviço (front) hospedado no servidor atual;
- É possível realizar requisições `GET` externamente (Ex: Postman), outros métodos são bloqueados pelo serviço de hospedagem;

*Desconsiderar essas limitações do host caso utilize um serviço diferente do InifinityFree.*

## Como usar
- Criar um subdomínio ou pasta específica para a API. No exemplo utilizado para esse projeto uma pasta "api" foi criada no root do host, onde os arquivos presentes nesse repositório foram incluídos;
- Acessar a ação que deseja através do caminho: `MÉTODO site.com/pasta_da_api/ação` 
    - As ações disponíveis atualmente são: `clients`, `products` e `sales`
    - Ex: `GET wktest.epizy.com/api/clients`
- Com o método GET é possível obter os dados para cada uma das ações. Se nenhum parâmetro for passado todos os dados serão retornados.
    - Ex: `GET wktest.epizy.com/api/clients` retornará todos os clientes do banco, mas `GET wktest.epizy.com/api/clients/123` retornará o objeto Client com o UID 123.
- O método `POST` irá adicionar ou atualizar um dado existente. O objeto deverá ser enviado no body da requisição no formato JSON.
- O método `DELETE` irá remover um dado existente e não é possível utilizar sem um parâmetro.

## Conteúdo
- Menu Inicial (/menu)
- Listagem e cadastro de clientes, com os seguintes campos: (/clientes)
    - Código de identificação do cliente
    - Nome
    - CPF
    - Endereço Completo (CEP, Logradouro, Número, Bairro, Complemento, Cidade)
    - E-Mail
    - Data de Nascimento
- Listagem e cadastro de produtos (/produtos)
    - Código de identificação do produto
    - Nome
    - Valor Unitário
- Pedido de venda (/vendas)
    - Código de identificação da venda
    - Data e Hora da venda
    - Identificação do cliente
    - Identificação dos itens da venda (Lista de Produtos)
    - Total da venda