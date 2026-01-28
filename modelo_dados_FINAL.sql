-- =========================================================
-- MODELO DE DADOS 
-- =========================================================

DROP DATABASE IF EXISTS cesae_estacionamento_trabalho;
CREATE DATABASE cesae_estacionamento_trabalho;
USE cesae_estacionamento_trabalho;

-- =========================
-- UTILIZADOR
-- =========================
CREATE TABLE utilizador (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  obrigar_mudar_password BOOLEAN NOT NULL DEFAULT TRUE,
  role ENUM('ADMIN','SEGURANCA','COLAB') NOT NULL,
  pontos INT NOT NULL DEFAULT 30,
  deleted_at TIMESTAMP NULL DEFAULT NULL -- Soft Delete
);

  
-- =========================
-- LUGAR
-- =========================
CREATE TABLE lugar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero INT NOT NULL UNIQUE,
  ativo BOOLEAN NOT NULL DEFAULT TRUE
  );

-- =========================
-- RESERVA
-- =========================

CREATE TABLE reserva (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilizador_id INT NOT NULL,
  lugar_id INT NOT NULL,
  data DATE NOT NULL,
  estado ENUM('ATIVA','PRESENTE','NAO_COMPARECEU','CANCELADA') NOT NULL,
  validada_por INT,

  CONSTRAINT fk_reserva_utilizador
    FOREIGN KEY (utilizador_id) REFERENCES utilizador(id),
  CONSTRAINT fk_reserva_lugar
    FOREIGN KEY (lugar_id) REFERENCES lugar(id),
  CONSTRAINT fk_reserva_validada_por
    FOREIGN KEY (validada_por) REFERENCES utilizador(id),

  UNIQUE (utilizador_id, data),
  UNIQUE (lugar_id, data)
);

-- =========================
-- LISTA_ESPERA
-- =========================
CREATE TABLE lista_espera (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilizador_id INT NOT NULL,
  data DATE NOT NULL,
  estado ENUM('ATIVO','NOTIFICADO','ACEITE','EXPIRADO') NOT NULL,
  prioridade INT NOT NULL,

  CONSTRAINT fk_lista_utilizador
    FOREIGN KEY (utilizador_id) REFERENCES utilizador(id),

  UNIQUE (utilizador_id, data)
);

-- =========================
-- MOVIMENTO_PONTOS
-- =========================
CREATE TABLE movimento_pontos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilizador_id INT NOT NULL,
  reserva_id INT,
  tipo ENUM('RESERVA','CANCELAMENTO','FALTA', 'RESET_MENSAL','AJUSTE') NOT NULL,
  pontos INT NOT NULL,

  CONSTRAINT fk_movimento_utilizador
    FOREIGN KEY (utilizador_id) REFERENCES utilizador(id),
  CONSTRAINT fk_movimento_reserva
    FOREIGN KEY (reserva_id) REFERENCES reserva(id)
);

-- =========================
-- REPORT
-- =========================
CREATE TABLE report (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilizador_id INT NOT NULL,
  tipo ENUM('LUGAR_OCUPADO','SEM_RESERVA','PROBLEMA') NOT NULL,
  descricao TEXT NOT NULL,
  estado ENUM('PENDENTE','VALIDADO','REJEITADO') NOT NULL,

  CONSTRAINT fk_report_utilizador
    FOREIGN KEY (utilizador_id) REFERENCES utilizador(id)
);

-- =========================
-- HISTORICO_UTILIZADOR
-- =========================
CREATE TABLE historico_eventos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilizador_id INT NOT NULL,
  tipo_evento ENUM('RESERVA','LISTA_ESPERA','REPORT','PONTOS') NOT NULL,
  entidade_id INT NOT NULL,
  acao ENUM('CRIADO','ATUALIZADO','REMOVIDO','VALIDADO','CANCELADO') NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_hist_eventos_utilizador
    FOREIGN KEY (utilizador_id) REFERENCES utilizador(id)
);
