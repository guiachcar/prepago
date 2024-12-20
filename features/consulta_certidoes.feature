# language: pt
Funcionalidade: Consulta de Certidões
  Como um usuário do sistema
  Eu quero consultar certidões de diferentes órgãos
  Para verificar a situação de pessoas físicas e jurídicas

  Contexto:
    Dado eu estou autenticado como usuário válido

  Esquema do Cenário: Consulta de certidões para CPF válido
    Quando eu faço uma consulta de certidão para o CPF "<cpf>"
    E eu informo a data de nascimento "<data_nascimento>"
    E eu seleciono os seguintes tipos de certidão:
      | Tribunal Federal        |
      | Tribunal do Trabalho    |
      | Protestos              |
      | Receita Federal        |
      | Certidão de Débitos    |
      | CNDT                   |
    E eu seleciono a região "<regiao_trf>" para o TRF
    E eu seleciono a região "<regiao_trt>" para o TRT
    Então eu devo ver o resultado da consulta
    E a certidão deve estar disponível para download

    Exemplos:
      | cpf           | data_nascimento | regiao_trf | regiao_trt |
      | 36835372800  | 21/01/1988      | TRF3       | 15         |
      | 12345678900  | 01/01/1990      | TRF1       | 2          |

  Esquema do Cenário: Consulta de certidões para CNPJ válido
    Quando eu faço uma consulta de certidão para o CNPJ "<cnpj>"
    E eu seleciono os seguintes tipos de certidão:
      | Tribunal Federal        |
      | Tribunal do Trabalho    |
      | Protestos              |
      | Receita Federal        |
      | Certidão de Débitos    |
      | CNDT                   |
    E eu seleciono a região "<regiao_trf>" para o TRF
    E eu seleciono a região "<regiao_trt>" para o TRT
    Então eu devo ver o resultado da consulta
    E a certidão deve estar disponível para download

    Exemplos:
      | cnpj              | regiao_trf | regiao_trt |
      | 12345678000199   | TRF3       | 15         |
      | 98765432000188   | TRF1       | 2          |

  Cenário: Tentativa de consulta com CPF inválido
    Quando eu faço uma consulta de certidão para o CPF "12345678901"
    Então eu devo ver uma mensagem de erro informando que o documento é inválido

  Cenário: Tentativa de consulta com CNPJ inválido
    Quando eu faço uma consulta de certidão para o CNPJ "12345678000100"
    Então eu devo ver uma mensagem de erro informando que o documento é inválido

  Cenário: Visualização do histórico de consultas
    Quando eu faço uma consulta de certidão para o CPF "36835372800"
    Então eu devo ver o histórico de consultas realizadas
