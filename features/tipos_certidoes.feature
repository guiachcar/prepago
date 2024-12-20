# language: pt
Funcionalidade: Tipos de Certidões
  Como um usuário do sistema
  Eu quero selecionar diferentes tipos de certidões
  Para obter informações específicas de cada órgão

  Contexto:
    Dado eu estou autenticado como usuário válido

  Cenário: Consulta de certidão do Tribunal Federal
    Quando eu faço uma consulta de certidão para o CPF "36835372800"
    E eu seleciono os seguintes tipos de certidão:
      | Tribunal Federal |
    E eu seleciono a região "TRF3" para o TRF
    Então eu devo ver o resultado da consulta
    E a certidão deve estar disponível para download

  Cenário: Consulta de certidão do Tribunal do Trabalho
    Quando eu faço uma consulta de certidão para o CPF "36835372800"
    E eu seleciono os seguintes tipos de certidão:
      | Tribunal do Trabalho |
    E eu seleciono a região "15" para o TRT
    Então eu devo ver o resultado da consulta
    E a certidão deve estar disponível para download

  Cenário: Consulta de protestos
    Quando eu faço uma consulta de certidão para o CPF "36835372800"
    E eu seleciono os seguintes tipos de certidão:
      | Protestos |
    Então eu devo ver o resultado da consulta
    E a certidão deve estar disponível para download

  Cenário: Consulta da Receita Federal
    Quando eu faço uma consulta de certidão para o CPF "36835372800"
    E eu seleciono os seguintes tipos de certidão:
      | Receita Federal |
    E eu informo a data de nascimento "21/01/1988"
    Então eu devo ver o resultado da consulta
    E a certidão deve estar disponível para download

  Cenário: Consulta de Certidão de Débitos
    Quando eu faço uma consulta de certidão para o CPF "36835372800"
    E eu seleciono os seguintes tipos de certidão:
      | Certidão de Débitos |
    Então eu devo ver o resultado da consulta
    E a certidão deve estar disponível para download

  Cenário: Consulta de CNDT
    Quando eu faço uma consulta de certidão para o CPF "36835372800"
    E eu seleciono os seguintes tipos de certidão:
      | CNDT |
    Então eu devo ver o resultado da consulta
    E a certidão deve estar disponível para download
