INSERT INTO medikament_liste 
(medikament_navn, time_interval, dosis, enhed, tidspunkter_tages, userId, prioritet)
VALUES 
-- ('Panodil', 8, 2, 'mg', '06:00,16:00,23:12', 1, 'Høj'),
-- ('Lamotrigin', 12, 2, 'mg', '06:00,24:00', 1, 'Høj');
('Ibuprofen', 12, 1, 'mg', '05:40,19:00', 1, 'Mellem');

INSERT INTO medikament_log (medikament_navn, taget_tid, taget_status, taget_lokale, userId)
-- VALUES ('Lamotrigin', NOW(), 'glemt', 'Køkken', 1),
VALUES ('Ibuprofen', NOW(), 'glemt', 'Køkken', 1);

