-- =============================================
-- MEDIC - Etapa 2: Estrutura para Análise Farmacogenética Detalhada
-- Nova tabela para textos detalhados dos medicamentos
-- Nova tabela para classes terapêuticas
-- Nova tabela para ancestralidade
-- IMPORTANTE: Este script NÃO altera dados existentes
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- 1. Classes Terapêuticas (reorganização)
-- =============================================
CREATE TABLE IF NOT EXISTS `pgx_drug_classes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(50) DEFAULT 'bi-capsule',
    `color` VARCHAR(20) DEFAULT '#6f42c1',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir as 13 classes terapêuticas
INSERT IGNORE INTO `pgx_drug_classes` (`code`, `name`, `description`, `icon`, `color`, `sort_order`) VALUES
('psiq_neuro', 'Psiquiátricos / Neurológicos', 'Antidepressivos (ISRS, tricíclicos, IRSN), antipsicóticos, ansiolíticos, estabilizadores de humor, psicoestimulantes (TDAH), anticonvulsivantes. Indicados para transtorno depressivo, ansiedade, espectro autista, bipolaridade, síndrome do pânico, TDAH, entre outros.', 'bi-lightning', '#0d6efd', 1),
('onco', 'Oncológicos', 'Quimioterápicos, terapias-alvo, imunoterapia. Medicamentos utilizados no tratamento de diversos tipos de câncer.', 'bi-shield-exclamation', '#dc3545', 2),
('cardio', 'Cardiológicos', 'Anticoagulantes, antiplaquetários, anti-hipertensivos, estatinas, antiarrítmicos. Utilizados no tratamento de doenças cardiovasculares.', 'bi-heart-pulse', '#e63946', 3),
('infecto', 'Infectologia', 'Antivirais (HIV, HCV), antibióticos, antifúngicos. Medicamentos utilizados no tratamento de infecções.', 'bi-bug', '#6610f2', 4),
('gastro', 'Gastroenterologia', 'Inibidores de bomba de prótons (omeprazol, etc.), antieméticos. Utilizados no tratamento de doenças do trato gastrointestinal.', 'bi-droplet-half', '#fd7e14', 5),
('pneumo', 'Antiasmáticos / Pneumologia', 'Broncodilatadores, corticoides inalatórios. Utilizados no tratamento de asma e doenças respiratórias.', 'bi-wind', '#20c997', 6),
('diabetes', 'Antidiabéticos / Endocrinologia', 'Metformina, sulfonilureias, insulinas, medicamentos para tireoide. Utilizados no controle do diabetes e distúrbios endócrinos.', 'bi-droplet', '#6f42c1', 7),
('gota', 'Antigotosos / Reumatologia', 'Alopurinol, colchicina, imunossupressores. Utilizados no tratamento de gota e doenças reumatológicas.', 'bi-bandaid', '#0dcaf0', 8),
('analgesico', 'Analgésicos / Anestésicos', 'Opioides, anti-inflamatórios não esteroidais (AINEs), anestésicos. Utilizados no controle da dor.', 'bi-thermometer-half', '#ffc107', 9),
('pde_inhib', 'Inibidores de Fosfodiesterases', 'Sildenafil e similares. Utilizados para disfunção erétil e hipertensão pulmonar.', 'bi-capsule-pill', '#198754', 10),
('mthfr', 'Análise do MTHFR / Metabolismo do Folato', 'Suplementação de folato, metotrexato. Relacionados ao metabolismo do ácido fólico e homocisteína.', 'bi-dna', '#e63946', 11),
('dermato', 'Dermatológicos', 'Medicamentos com risco de fotossensibilidade, reações cutâneas graves (síndrome de Stevens-Johnson / necrólise epidérmica tóxica).', 'bi-sun', '#ffc107', 12),
('imuno_transplante', 'Imunossupressores / Transplante', 'Tacrolimus, azatioprina, ciclosporina. Utilizados em transplantes e doenças autoimunes.', 'bi-shield-check', '#0dcaf0', 13);

-- =============================================
-- 2. Textos Detalhados dos Medicamentos
-- =============================================
CREATE TABLE IF NOT EXISTS `pgx_drug_details` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `drug_name` VARCHAR(255) NOT NULL,
    `class_id` INT NULL COMMENT 'FK para pgx_drug_classes',
    `commercial_names` TEXT NULL COMMENT 'Nomes comerciais (ex: Advil, Alivium, etc.)',
    `description` TEXT NULL COMMENT 'Descrição completa do medicamento: o que é, para que serve, efeitos adversos',
    `understanding_result` TEXT NULL COMMENT 'Entendendo seu resultado - explicação acessível do gene envolvido',
    `snp_rsid` VARCHAR(30) NULL COMMENT 'SNP principal associado',
    `chromosome` VARCHAR(10) NULL COMMENT 'Cromossomo onde está o gene',
    `gene_symbol` VARCHAR(50) NULL COMMENT 'Gene principal',
    `study_population` VARCHAR(100) NULL COMMENT 'População de estudo (ex: Europeia, Global, etc.)',
    `genotype_results` TEXT NULL COMMENT 'Resultado conforme genótipos - interpretação',
    `suggestions` TEXT NULL COMMENT 'Sugestões personalizadas',
    `disclaimer` TEXT NULL COMMENT 'Aviso legal (nunca altere tratamentos sem orientação médica)',
    `references_urls` TEXT NULL COMMENT 'Links de referências científicas',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pdd_drug` (`drug_name`),
    INDEX `idx_pdd_class` (`class_id`),
    INDEX `idx_pdd_gene` (`gene_symbol`),
    INDEX `idx_pdd_rsid` (`snp_rsid`),
    FOREIGN KEY (`class_id`) REFERENCES `pgx_drug_classes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. Painel de Ancestralidade do Paciente
-- =============================================
CREATE TABLE IF NOT EXISTS `patient_ancestry` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `ancestry_source` VARCHAR(100) NULL COMMENT 'Fonte (Genera, 23andMe, AncestryDNA, informado)',
    `european_pct` DECIMAL(5,2) NULL COMMENT '% Europeu',
    `african_pct` DECIMAL(5,2) NULL COMMENT '% Africano',
    `east_asian_pct` DECIMAL(5,2) NULL COMMENT '% Leste Asiático',
    `south_asian_pct` DECIMAL(5,2) NULL COMMENT '% Sul Asiático',
    `native_american_pct` DECIMAL(5,2) NULL COMMENT '% Nativo Americano',
    `middle_eastern_pct` DECIMAL(5,2) NULL COMMENT '% Oriente Médio',
    `oceanian_pct` DECIMAL(5,2) NULL COMMENT '% Oceania',
    `other_pct` DECIMAL(5,2) NULL COMMENT '% Outros',
    `primary_population` VARCHAR(100) NULL COMMENT 'População predominante para análise',
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_patient_ancestry` (`patient_id`),
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. Glossário de termos (para tooltips)
-- =============================================
CREATE TABLE IF NOT EXISTS `pgx_glossary` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `term` VARCHAR(100) NOT NULL UNIQUE,
    `definition` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir termos básicos para tooltips
INSERT IGNORE INTO `pgx_glossary` (`term`, `definition`) VALUES
('SNP', 'SNP (sigla em inglês para Polimorfismo de Nucleotídeo Único) é uma variação do DNA em uma única base nucleotídica que nos permite traçar propensões e características genéticas.'),
('Genótipo', 'O genótipo é a combinação de dois alelos (trechos de DNA), onde um é recebido do pai e o outro da mãe. Diferentes genótipos podem representar diferentes resultados clínicos.'),
('Alelo', 'Um alelo é uma das formas alternativas de um gene ou marcador genético em um determinado local (locus) do cromossomo. Cada pessoa herda dois alelos para cada gene: um do pai e um da mãe.'),
('Cromossomo', 'Os cromossomos são estruturas dentro das células que contêm o DNA. O ser humano possui 23 pares de cromossomos (46 no total), herdando um conjunto do pai e outro da mãe.'),
('Fenótipo', 'O fenótipo é a manifestação observável das características genéticas de uma pessoa, ou seja, como os genes se expressam no organismo (ex: metabolizador lento, metabolizador rápido).'),
('Metabolizador lento', 'Pessoa cujo organismo processa (metaboliza) determinado medicamento mais lentamente que o normal. Isso pode causar acúmulo do medicamento no corpo e aumentar o risco de efeitos colaterais.'),
('Metabolizador rápido', 'Pessoa cujo organismo processa (metaboliza) determinado medicamento mais rapidamente que o normal. Isso pode fazer com que o medicamento seja eliminado antes de fazer efeito adequado.'),
('Metabolizador ultra-rápido', 'Pessoa cujo organismo processa determinado medicamento muito mais rapidamente que o normal. Doses convencionais podem ser insuficientes.'),
('Metabolizador normal', 'Pessoa cujo organismo processa determinado medicamento na velocidade esperada. As doses padrão tendem a funcionar adequadamente.'),
('Metabolizador intermediário', 'Pessoa cujo organismo processa determinado medicamento em velocidade ligeiramente reduzida. Pode necessitar de ajustes moderados na dosagem.'),
('Heterozigoto', 'Quando uma pessoa possui dois alelos diferentes para um determinado gene (um de cada progenitor). Geralmente resulta em atividade enzimática intermediária.'),
('Homozigoto', 'Quando uma pessoa possui dois alelos iguais para um determinado gene (ambos do mesmo tipo). Pode ser homozigoto normal ou homozigoto variante.'),
('Farmacogenética', 'Área da ciência que estuda como as variações genéticas individuais influenciam a resposta do organismo aos medicamentos, permitindo personalizar tratamentos.'),
('CYP450', 'Família de enzimas do citocromo P450, responsáveis pelo metabolismo (processamento) da maioria dos medicamentos no fígado. Variações genéticas nestas enzimas afetam como processamos os remédios.'),
('DPYD', 'Gene que codifica a enzima DPD (diidropirimidina desidrogenase), essencial para metabolizar quimioterápicos do tipo fluoropirimidinas. Deficiência pode causar toxicidade grave.'),
('MTHFR', 'Gene que codifica a enzima metilenotetrahidrofolato redutase, essencial para o metabolismo do ácido fólico (vitamina B9). Variantes podem afetar os níveis de homocisteína e folato.'),
('Evidência 1A', 'Nível máximo de evidência científica: prescribing guidelines publicadas por consórcios internacionais (CPIC/DPWG) com base em múltiplos estudos.'),
('Evidência 2A', 'Nível alto de evidência: evidência funcional e clínica bem estabelecida, com estudos de associação significativos.'),
('Evidência 2B', 'Nível moderado de evidência: associação moderada identificada em estudos genômicos, com alguma evidência funcional.');

SET FOREIGN_KEY_CHECKS = 1;