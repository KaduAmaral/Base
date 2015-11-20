# Base &ndash; 0.0.0-DEV.2

Base é uma "base" para construção de soluções em MVC com PHP. 
Construi o _framework_ para uso próprio, porém resolvi disponibilizar para a comunidade em geral.

### [Documentação](https://github.com/KaduAmaral/Base/wiki)

## ChangeLog

O framework ainda está em fase de construção.

## TODO

Muita coisa pra fazer. Ainda não foi especificado um roteiro, mas aqui estão as principais:

1. <s>Criar biblioteca de conexão no estilo do [ConnectionMSi](https://github.com/KaduAmaral/ConnectionMSi), com funções de `select`, `insert`, `update` e etc. Porém utilizando PDO criando métodos para MySQL, MariaDB, PostgreSQL e MSSQL inicialmente;</s>
  - Criado o Projeto [ConnectionPDO](https://github.com/KaduAmaral/ConnectionPDO), porém implementado apenas o drive do MySQL. **Drives TO DO**:
    * <s>MySQL</s>
    * MariaDB
    * PostgreSQL
    * MSSQL
2. Criar estrutura de `Exceptions` e realizar _log_ dos erros e excessões para _debug_;
3. Criar métodos e recursos para facilitar o _debug_ da aplicação;
4. Criar recurso de instalação facilitando a configuração dos arquivos necessários;


## Licença

O _Framwork_ é disponibilizado através da **Licensa Apache 2.0**.