# Base &ndash; 1.1.1

Base é uma "base" para construção de soluções em MVC com PHP. 
Construi o _framework_ para uso próprio, porém resolvi disponibilizar 
para a comunidade em geral.

### [Documentação](https://github.com/KaduAmaral/Base/wiki)

## ChangeLog

O framework ainda está em fase de construção.

## TODO

Muita coisa pra fazer. Ainda não foi especificado um roteiro, 
mas aqui estão as principais:

1. <s>Criar biblioteca de conexão no estilo do 
   [ConnectionMSi](https://github.com/KaduAmaral/ConnectionMSi), com 
   funções de `select`, `insert`, `update` e etc. Porém utilizando PDO 
   criando métodos para MySQL, MariaDB, PostgreSQL e MSSQL inicialmente;
   </s>
  - Criado o Projeto 
    [ConnectionPDO](https://github.com/KaduAmaral/ConnectionPDO), porém 
    implementado apenas o driver do MySQL. 
    **Drivers TO DO**:
    * <s>MySQL</s>
    * MariaDB
    * PostgreSQL
    * MSSQL
2. Criar estrutura de `Exceptions` e realizar _log_ dos erros e 
   excessões para _debug_;
3. Criar métodos e recursos para facilitar o _debug_ da aplicação;
4. Criar recurso de instalação facilitando a configuração dos arquivos 
   necessários;
5. <s>Implementar sistema de rotas.</s>


## Licença

O _Framwork_ é disponibilizado através da **Licensa Apache 2.0**.


------------------------------------------------------------------------

English

------------------------------------------------------------------------


# Base &ndash; 1.1.1

Base is a "base" for building solutions in MVC with PHP.
Build the _framework_ for their own use, but decided to make available 
to the community at large.

### [Documentation](https://github.com/KaduAmaral/Base/wiki)

## ChangeLog

The framework is still under construction.

## TODO

A lot of things to do. We have not specified a road map, but here are 
the main ones:

1. <s>Create connection library in the style of 
   [ConnectionMSi] (https://github.com/KaduAmaral/ConnectionMSi), with 
   functions of `select`,` insert`, `update` and etc. However using PDO 
   creating methods for MySQL, MariaDB, PostgreSQL and MSSQL initially; 
   </s>
  - Created Project 
    [ConnectionPDO](https://github.com/KaduAmaral/ConnectionPDO), but 
    only implemented the MySQL. 
    **Drivers TO DO**:
    * <s>MySQL</s>
    * MariaDB
    * PostgreSQL
    * MSSQL
2. Create structure `Exceptions` and perform _log_ of errors and 
   exceptions for _debug_;
3. Create methods and resources to facilitate the implementation 
   of _debug_;
4. Create installation feature facilitates the configuration of the 
   necessary files;
5. <s>Implement routing system. </s>


## License

The _Framework_ is available through the **Apache License 2.0**.
