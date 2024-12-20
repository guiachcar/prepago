# language: pt
Funcionalidade: Histórico de Consultas
  Como um usuário do sistema
  Eu quero visualizar o histórico de consultas realizadas
  Para acompanhar e gerenciar as certidões emitidas

  Contexto:
    Dado eu estou autenticado como usuário válido

  Cenário: Visualização do histórico completo
    Quando eu faço uma consulta de certidão para o CPF "36835372800"
    Então eu devo ver o histórico de consultas realizadas

  Cenário: Visualização de detalhes de uma consulta específica
    Quando eu faço uma consulta de certidão para o CPF "36835372800"
    E eu seleciono os seguintes tipos de certidão:
      | Tribunal Federal     |
      | Tribunal do Trabalho |
    Então eu devo ver o resultado da consulta
    E eu devo ver o histórico de consultas realizadas

  Cenário: Múltiplas consultas para o mesmo documento
    Quando eu faço uma consulta de certidão para o CPF "36835372800"
    E eu seleciono os seguintes tipos de certidão:
      | Tribunal Federal |
    E eu faço uma consulta de certidão para o CPF "36835372800"
    E eu seleciono os seguintes tipos de certidão:
      | Tribunal do Trabalho |
    Então eu devo ver o histórico de consultas realizadas
