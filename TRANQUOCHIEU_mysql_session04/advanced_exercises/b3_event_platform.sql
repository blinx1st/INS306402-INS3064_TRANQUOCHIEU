-- advanced_exercises/b3_event_platform.sql

-- Drop table if exists (safe re-run)
DROP TABLE IF EXISTS events;

CREATE TABLE events (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  event_code VARCHAR(40) NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,

  -- Required: DATETIME
  start_time DATETIME NOT NULL,
  end_time DATETIME NOT NULL,

  location VARCHAR(255) NULL,

  -- Required: flexible metadata
  event_details JSON NOT NULL,

  status ENUM('draft', 'published', 'cancelled', 'completed')
    NOT NULL DEFAULT 'draft',

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_events_event_code (event_code),
  KEY idx_events_start_time (start_time),

  CHECK (end_time >= start_time)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Example insert (optional)
-- INSERT INTO events (event_code, title, start_time, end_time, event_details)
-- VALUES (
--   'EVT-0001',
--   'Tech Meetup',
--   '2026-03-05 18:00:00',
--   '2026-03-05 20:00:00',
--   JSON_OBJECT(
--     'capacity', 200,
--     'tags', JSON_ARRAY('tech', 'networking'),
--     'speakers', JSON_ARRAY(
--       JSON_OBJECT('name','An', 'topic','AI'),
--       JSON_OBJECT('name','Binh','topic','Cloud')
--     ),
--     'ticket', JSON_OBJECT('price', 99000, 'currency', 'VND')
--   )
-- );