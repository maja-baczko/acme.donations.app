# API Routes Summary

## Routes List

### Public Routes (No Auth)
```
POST   /api/v1/register
POST   /api/v1/login
GET    /api/v1/campaigns (public browsing)
GET    /api/v1/campaigns/{campaign}
GET    /api/v1/categories
GET    /api/v1/settings/public
```

### Protected Routes (Auth Required)

**Authentication**
```
POST   /api/v1/logout
GET    /api/v1/me
POST   /api/v1/refresh
```

**Users** (permission: view/create/edit/delete users)
```
GET    /api/v1/users
POST   /api/v1/users
GET    /api/v1/users/{user}
PUT    /api/v1/users/{user}
DELETE /api/v1/users/{user}
```

**Campaigns** (permission: view/create/edit/delete campaigns)
```
GET    /api/v1/campaigns
POST   /api/v1/campaigns
GET    /api/v1/campaigns/{campaign}
PUT    /api/v1/campaigns/{campaign}
DELETE /api/v1/campaigns/{campaign}
```

**Categories** (permission: edit campaigns for modifications)
```
GET    /api/v1/categories
POST   /api/v1/categories
PUT    /api/v1/categories/{category}
DELETE /api/v1/categories/{category}
```

**Donations** (permission: view/create/edit/delete donations)
```
GET    /api/v1/donations
POST   /api/v1/donations
GET    /api/v1/donations/{donation}
PUT    /api/v1/donations/{donation}
DELETE /api/v1/donations/{donation}
GET    /api/v1/donations/export
```

**Payments** (READ-ONLY except create)
```
GET    /api/v1/payments
GET    /api/v1/payments/{payment}
GET    /api/v1/payments/{payment}/receipt
```

**Images**
```
Resource routes (GET, POST, PUT, DELETE)
```

**System Settings** (permission: view/edit system settings)
```
GET    /api/v1/settings
POST   /api/v1/settings
GET    /api/v1/settings/{setting}
PUT    /api/v1/settings/{setting}
DELETE /api/v1/settings/{setting}
```

**Audit Logs** (permission: view audit logs, READ-ONLY)
```
GET    /api/v1/audit-logs
GET    /api/v1/audit-logs/{auditLog}
```

---

## Roles & Permissions

### Available Roles:
1. **admin** - Full access to all resources
2. **manager** - Can manage campaigns, donations, and view users
3. **employee** - Can view campaigns and create donations

### Available Permissions:
- `view users`, `create users`, `edit users`, `delete users`
- `view campaigns`, `create campaigns`, `edit campaigns`, `delete campaigns`
- `view donations`, `create donations`, `edit donations`, `delete donations`
- `view system settings`, `edit system settings`
- `view audit logs`

---

## Testing the API with Postman

   → check Route_testing.md
