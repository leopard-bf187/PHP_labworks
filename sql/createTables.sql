CREATE TABLE IF NOT EXISTS moods (
    id SERIAL PRIMARY KEY,
    code VARCHAR(32) NOT NULL UNIQUE,
    title VARCHAR(64) NOT NULL,
    icon VARCHAR(16) NOT NULL
);

CREATE TABLE IF NOT EXISTS mood_entries (
    id SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    mood_id INTEGER NOT NULL REFERENCES moods(id) ON DELETE RESTRICT,
    mood_date DATE NOT NULL,
    energy_level VARCHAR(16) NOT NULL CHECK (energy_level IN ('low', 'medium', 'high')),
    note TEXT NOT NULL,
    author VARCHAR(50) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
);

INSERT INTO moods (code, title, icon) VALUES
('happy', 'Радостное', '😊'),
('calm', 'Спокойное', '😌'),
('sad', 'Грустное', '😢'),
('angry', 'Злое', '😠'),
('tired', 'Уставшее', '😴')
ON CONFLICT (code) DO NOTHING;