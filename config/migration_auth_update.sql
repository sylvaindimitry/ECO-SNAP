-- Migration: Ajouter les champs pour l'authentification avancée
-- Exécuter après la création initiale de la base

USE ecosnap_mvc;

-- Ajouter les colonnes pour la vérification d'email
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS email_verified BOOLEAN DEFAULT FALSE AFTER password,
ADD COLUMN IF NOT EXISTS verification_token VARCHAR(255) NULL AFTER email_verified,
ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) NULL AFTER verification_token,
ADD COLUMN IF NOT EXISTS remember_token VARCHAR(255) NULL AFTER google_id,
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL AFTER remember_token;

-- Index pour les tokens
CREATE INDEX IF NOT EXISTS idx_users_verification_token ON users(verification_token);
CREATE INDEX IF NOT EXISTS idx_users_remember_token ON users(remember_token);
CREATE INDEX IF NOT EXISTS idx_users_google_id ON users(google_id);
CREATE INDEX IF NOT EXISTS idx_users_email_verified ON users(email_verified);
