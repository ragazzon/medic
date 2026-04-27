-- =============================================
-- SEED: Painéis de Análise
-- =============================================

INSERT INTO `pgx_panels` (`code`, `name`, `description`, `icon`, `color`, `sort_order`) VALUES
('pharmaco', 'Farmacogenomica', 'Enzimas CYP450, transportadores, receptores.', 'bi-capsule', '#6f42c1', 1),
('neuro', 'Neuropsiquiatria', 'Dopamina, serotonina, BDNF, estresse. TDAH, depressao.', 'bi-lightning', '#0d6efd', 2),
('cardio', 'Cardiologia', 'Risco CV, trombofilia, PA, colesterol, arritmias.', 'bi-heart-pulse', '#dc3545', 3),
('onco', 'Oncologia', 'Risco canceres, supressores tumorais, reparo DNA.', 'bi-shield-exclamation', '#fd7e14', 4),
('nutri', 'Nutrigenomica', 'Vitaminas, folato, B12, vit D, intolerancias.', 'bi-egg-fried', '#198754', 5),
('musculo', 'Musculoesqueletico', 'Colageno, lesoes, performance atletica.', 'bi-activity', '#20c997', 6),
('derma', 'Dermatologia', 'Pigmentacao, UV, melanoma, reparo DNA solar.', 'bi-sun', '#ffc107', 7),
('immuno', 'Imunologia', 'HLA, autoimunidade, hipersensibilidade farmacos.', 'bi-shield-check', '#0dcaf0', 8),
('endocrino', 'Endocrinologia', 'Tireoide, diabetes tipo 2, glicose.', 'bi-droplet', '#6610f2', 9),
('sleep', 'Sono', 'Cronotipo, melatonina, qualidade sono.', 'bi-moon-stars', '#6c757d', 10);