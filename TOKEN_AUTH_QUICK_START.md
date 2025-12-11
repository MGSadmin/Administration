# Token-Based Auth Migration — Quick Start & Checklist

## 🚀 For Developers: Quick Setup

### 1. Pull Latest Code
```bash
cd /var/www/commercial && git pull
cd /var/www/gestion-dossier && git pull
cd /var/www/administration && git pull
```

### 2. Clear Caches
```bash
# Commercial
cd /var/www/commercial
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Gestion-Dossier
cd /var/www/gestion-dossier
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Administration
cd /var/www/administration
php artisan cache:clear
php artisan config:cache
```

### 3. Test Login Locally
```bash
# 1. Ensure a test user exists in administration
mysql -u root mgs_administration
> SELECT * FROM users WHERE email = 'test@example.com';

# 2. Start local server
cd /var/www/administration && php artisan serve --port=8000
cd /var/www/commercial && php artisan serve --port=8001

# 3. Try login on commercial
# Open browser: http://127.0.0.1:8001/login
# Enter: test@example.com / password
# Should redirect to dashboard

# 4. Check session storage
php artisan tinker
>>> session()->all()  # Should show 'admin_token' key
```

### 4. Verify API Endpoints
```bash
# Get a token
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}' | jq -r '.token')

# Test /api/me
curl -H "Authorization: Bearer $TOKEN" \
  http://127.0.0.1:8000/api/me | jq .

# Should return: { "user": { "id", "name", "email", "roles", "permissions" } }
```

---

## ✅ Pre-Deployment Checklist (Staging)

- [ ] **Database**
  - [ ] Ensure `mgs_administration` has test user(s)
  - [ ] Ensure `mgsmg_commercial` and `mgsmg_gestion_dossier` are empty (or have dummy data)
  - [ ] No users table in client databases (or not synced)

- [ ] **Code Review**
  - [ ] All new files present and syntax valid (`php -l`)
  - [ ] Routes registered (`php artisan route:list` works)
  - [ ] No references to `$connection = 'administration'` in client `User` models

- [ ] **Configuration**
  - [ ] `.env` files reviewed (no stray DB_ADMIN_* in clients if not needed)
  - [ ] `SANCTUM_STATEFUL_DOMAINS` set to correct domains
  - [ ] `APP_URL` correct for each app
  - [ ] `ADMIN_APP_URL` correct for clients

- [ ] **Environment & Security**
  - [ ] HTTPS enforced (or will be in prod)
  - [ ] CORS headers set (if APIs called from JS)
  - [ ] Session driver is `database` or `file`, not `cookie`
  - [ ] `APP_KEY` generated for all apps

- [ ] **Testing**
  - [ ] Login works on commercial with valid credentials
  - [ ] Login works on gestion-dossier with same credentials
  - [ ] Logout clears session + token
  - [ ] Redirect on expired token
  - [ ] Roles/permissions returned in `/api/me`
  - [ ] Access control respected (permissions via API)

- [ ] **Logging & Monitoring**
  - [ ] Check `storage/logs/laravel.log` for errors
  - [ ] Monitor API response times
  - [ ] Verify no database connection attempts from client to admin

---

## 🔧 Troubleshooting

| Symptom | Cause | Fix |
|---------|-------|-----|
| "Class not found: LoginController" | Routes file not reloaded | `php artisan route:cache` clear or restart |
| Login redirects to login again | Token not stored in session | Check `AdminAuthService::login()` response |
| 401 Unauthorized on /api/me | Token format wrong or expired | Verify Bearer token header + token validity |
| Roles/permissions empty | User has no roles assigned | Assign roles in administration, re-login |
| Cross-site redirects broken | Redirect URLs hardcoded | Use `route()` helpers, check config |

---

## 📋 Deployment Timeline

**Phase 1: Staging (1 day)**
- Deploy to staging environment
- Run QA tests
- Check logs

**Phase 2: Production Prep (1 day)**
- Create database snapshots/backups
- Write runbook for ops
- Brief support team

**Phase 3: Production Rollout (1–2 hours)**
- Deploy code to production
- Clear caches
- Monitor logs
- Verify a few logins work

**Phase 4: Monitoring (1 week)**
- Watch for errors in logs
- Monitor API performance
- Collect user feedback

---

## 🚨 Rollback (If Needed)

```bash
# On all servers
cd /var/www/commercial
git revert HEAD  # or checkout old version
php artisan cache:clear

cd /var/www/gestion-dossier
git revert HEAD
php artisan cache:clear

# Restart web server
sudo systemctl restart php-fpm  # or apache2
```

---

## 📞 Support Contacts

- **Backend Issues**: Check logs in `storage/logs/laravel.log`
- **API Issues**: Test with curl/Postman, verify token format
- **Session Issues**: Check session config, verify middleware registered
- **Database Issues**: Verify DB connections NOT used from clients

---

**Status**: Ready for Staging Deployment ✅  
**Last Updated**: 5 décembre 2025
