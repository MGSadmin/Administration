-- Script de migration des utilisateurs de gestion_dossiers vers mgs_administration
-- À exécuter AVANT le déploiement en production

USE mgs_administration;

-- 1. Migrer les utilisateurs (éviter les doublons par email)
INSERT IGNORE INTO users (id, name, email, password, created_at, updated_at)
SELECT id, name, email, password, created_at, updated_at
FROM gestion_dossiers.users
WHERE email NOT IN (SELECT email FROM users);

-- 2. Migrer les rôles (éviter les doublons par name)
INSERT IGNORE INTO roles (id, name, guard_name, created_at, updated_at)
SELECT id, name, guard_name, created_at, updated_at
FROM gestion_dossiers.roles
WHERE name NOT IN (SELECT name FROM roles WHERE guard_name = 'web');

-- 3. Migrer les permissions (éviter les doublons par name)
INSERT IGNORE INTO permissions (id, name, guard_name, created_at, updated_at)
SELECT id, name, guard_name, created_at, updated_at
FROM gestion_dossiers.permissions
WHERE name NOT IN (SELECT name FROM permissions WHERE guard_name = 'web');

-- 4. Migrer les associations utilisateurs-rôles
INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id)
SELECT role_id, model_type, model_id
FROM gestion_dossiers.model_has_roles
WHERE NOT EXISTS (
    SELECT 1 FROM model_has_roles 
    WHERE model_has_roles.model_id = gestion_dossiers.model_has_roles.model_id 
    AND model_has_roles.role_id = gestion_dossiers.model_has_roles.role_id
);

-- 5. Migrer les associations rôles-permissions
INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT permission_id, role_id
FROM gestion_dossiers.role_has_permissions
WHERE NOT EXISTS (
    SELECT 1 FROM role_has_permissions 
    WHERE role_has_permissions.role_id = gestion_dossiers.role_has_permissions.role_id 
    AND role_has_permissions.permission_id = gestion_dossiers.role_has_permissions.permission_id
);

-- 6. Vérification des migrations
SELECT 'Total utilisateurs migrés:' as info, COUNT(*) as count FROM users;
SELECT 'Total rôles migrés:' as info, COUNT(*) as count FROM roles;
SELECT 'Total permissions migrés:' as info, COUNT(*) as count FROM permissions;
SELECT 'Total associations user-role:' as info, COUNT(*) as count FROM model_has_roles;
SELECT 'Total associations role-permission:' as info, COUNT(*) as count FROM role_has_permissions;
