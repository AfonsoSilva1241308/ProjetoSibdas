// 1. UTILIZADORES E GESTÃO DE SITE
Table utilizador {
  id int [pk, increment]
  email varchar(100) [not null, unique]
  password varchar(255) [not null]
  papel varchar(20) [not null, default: 'admin']
  criado_em datetime [not null, default: `now()`]
  ultimo_acesso datetime
}

Table gestao_site {
  id int [pk, increment]
  texto_sobre_nos text
  texto_solucao text
  texto_funcionalidades text
  num_instituicoes int
  num_dispositivos int
  perc_monitorizacao int
  perc_suporte int
  perc_terapia int
  perc_diagnostico int
  banner_imagem varchar(255)
  titulo_form varchar(150)
  texto_apoio text
  morada_rua varchar(150)
  morada_cod_postal varchar(50)
  horario_semana varchar(100)
  horario_fim_semana varchar(100)
  email_contato varchar(100)
  telefone_contato varchar(20)
  atualizado_em datetime [default: `now()`]
  atualizado_por int [ref: > utilizador.id]
}

Table mensagem_contacto {
  id int [pk, increment]
  nome varchar(150) [not null]
  instituicao varchar(150) [not null]
  email varchar(100) [not null]
  mensagem text [not null]
  data_envio datetime [not null, default: `now()`]
  lida boolean [not null, default: false]
}

// 2. ENTIDADES PRINCIPAIS
Table localizacao {
  id int [pk, increment]
  edificio varchar(100) [not null]
  piso varchar(20) [not null]
  servico varchar(100) [not null]
  sala varchar(100) [not null]
  capacidade_maxima int
  infraestrutura boolean [default: false]
  observacoes text
}

Table fornecedor {
  id int [pk, increment]
  nome varchar(100) [not null]
  nif varchar(20) [unique, not null]
  telefone varchar(20)
  email varchar(100)
  morada varchar(255)
  website varchar(255)
  pessoa_contacto varchar(100)
  telefone_contacto varchar(20)
  tipo_fornecedor varchar(50) [not null] // fabricante, distribuidor, assistência, consumíveis
  observacoes text
}

Table equipamento {
  id int [pk, increment]
  codigo_interno varchar(50) [not null, unique]
  num_serie varchar(100) [unique]
  designacao varchar(150) [not null]
  marca varchar(100)
  modelo varchar(100)
  fabricante varchar(100) // Adicionado conforme sugerido
  categoria varchar(100) [not null]
  ano_fabrico int
  data_aquisicao date
  custo_aquisicao decimal(10,2)
  tipo_entrada varchar(50) 
  estado varchar(50) [not null] 
  criticidade varchar(50) [not null]
  observacoes text
  localizacao_id int [not null, ref: > localizacao.id]
  equipamento_pai_id int [ref: > equipamento.id]
  criado_em datetime [not null, default: `now()`]
  atualizado_em datetime [default: `now()`]
}

Table equipamento_fornecedor {
  equipamento_id int [not null, ref: > equipamento.id]
  fornecedor_id int [not null, ref: > fornecedor.id]
  indexes {
    (equipamento_id, fornecedor_id) [pk]
  }
}

// 3. MÓDULOS DEPENDENTES
Table documento {
  id int [pk, increment]
  titulo varchar(150) [not null]
  categoria varchar(100) [not null]
  nome_ficheiro varchar(255) [not null]
  data_validade date
  alerta_expiracao boolean [not null, default: false]
  equipamento_id int [not null, ref: > equipamento.id]
  fornecedor_id int [ref: > fornecedor.id]
}

Table consumivel {
  id int [pk, increment]
  designacao varchar(150) [not null]
  categoria varchar(100) [not null]
  frequencia varchar(50) [not null]
  equipamento_id int [not null, ref: > equipamento.id]
}

Table manutencao {
  id int [pk, increment]
  tipo varchar(50) [not null]
  descricao text
  data_planeada date
  data_realizacao date
  custo decimal(10,2)
  estado varchar(50) [not null]
  equipamento_id int [not null, ref: > equipamento.id]
  fornecedor_id int [ref: > fornecedor.id]
  utilizador_id int [ref: > utilizador.id]
  criado_em datetime [not null, default: `now()`]
}

Table garantia_contrato {
  id int [pk, increment]
  tipo varchar(50) [not null]
  contrato_manutencao varchar(30) // Adicionado conforme sugerido
  data_inicio date [not null]
  data_fim date [not null]
  periodicidade varchar(50)
  equipamento_id int [not null, ref: > equipamento.id]
  entidade_responsavel_id int [not null, ref: > fornecedor.id]
}

Ref: "fornecedor"."email" < "fornecedor"."tipo_fornecedor"

Ref: "fornecedor"."telefone" < "fornecedor"."tipo_fornecedor"

Ref: "utilizador"."id" < "utilizador"."papel"