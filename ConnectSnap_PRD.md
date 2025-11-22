# Product Requirements Document: ConnectSnap

**Version:** 1.0 MVP  
**Date:** November 21, 2025  
**Delivery:** 2 Days  
**Status:** Build-Ready Specification

---

## Tech Stack

- **Backend:** Laravel 12, PHP 8.4, MySQL 8, Sanctum Auth
- **Mobile:** NativePHP (Android)
- **Frontend:** Livewire 3.x, Tailwind CSS, Alpine.js

---

## MVP Scope (2-Day Build)

### Core Features Only
1. User Registration & Login
2. Profile Management with QR Code
3. QR Scanner
4. Connection Management with Notes
5. View Connections List


---

## User Journeys (MVP Only)

### Journey 1: Registration & Profile Setup
```
As a new user,
I want to quickly register and create my profile,
So that I can generate my QR code and start networking.

Steps:
1. Enter email, password, name
2. Verify email
3. Add optional profile info (company, job title, bio, photo)
4. QR code auto-generated
5. Ready to use
```

### Journey 2: Scanning Another Attendee
```
As a user,
I want to scan someone's QR code,
So that I can view their profile and save their contact.

Steps:
1. Tap "Scan" button
2. Camera opens
3. Point at QR code
4. Profile displays automatically
5. Connection saved
```

### Journey 3: Adding Context Notes
```
As a user,
I want to add notes after scanning,
So that I remember our conversation.

Steps:
1. After scanning, notes field visible
2. Type notes (500 char max)
3. Auto-saves
4. Notes are private to me
```

### Journey 4: Reviewing Connections
```
As a user,
I want to see everyone I've met,
So that I can follow up later.

Steps:
1. Open Connections tab
2. See list with photos, names, notes preview
3. Tap to view full profile and notes
4. Search by name
```


---

## Feature Specifications

### 1. Authentication

**Registration:**
- Email, password, name (required)
- Email verification via Sanctum
- Redirects to profile setup

**Login:**
- Email + password
- Returns bearer token
- Token stored in app

### 2. Profile Management

**Fields:**
- Name (required)
- Email (required, unique)
- Phone (optional)
- Company (optional)
- Job Title (optional)
- Bio (optional, 250 chars)
- Profile Photo (optional, 2MB max)
- Social URL (optional) - LinkedIn, Linktree, or any preferred link
- QR Code Hash (auto-generated, unique)

**QR Code:**
- Auto-generated on registration
- Format: `connectsnap://u/{qr_hash}`
- Always visible on "My QR" screen
- Can be downloaded as image

### 3. QR Scanner

**Functionality:**
- Native camera access
- Real-time QR detection
- Haptic feedback on scan
- Displays scanned user profile
- Auto-saves to connections

### 4. Connections & Notes

**After Scanning:**
- Full profile view
- Notes field (500 chars)
- Auto-save notes
- Connection saved automatically

**Connections List:**
- Shows all scanned profiles
- Display: photo, name, company, notes preview, timestamp
- Search by name
- Tap to view full details
- Edit notes anytime


---

## API Endpoints (Laravel 12 + Sanctum)

### Auth
```
POST   /api/register
POST   /api/login
POST   /api/logout
GET    /api/user
```

### Profile
```
GET    /api/profile
PUT    /api/profile
POST   /api/profile/photo
GET    /api/profile/qr-code
```

### Connections
```
POST   /api/connections/scan        // Scan QR, save connection
GET    /api/connections             // List all connections
GET    /api/connections/{id}        // Single connection detail
PUT    /api/connections/{id}/notes  // Update notes
DELETE /api/connections/{id}        // Delete connection
```

### Public
```
GET    /api/u/{qr_hash}  // Public profile view (no auth required)
```


---

## Database Schema

### events
```sql
id                BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
name              VARCHAR(255) NOT NULL
slug              VARCHAR(255) UNIQUE NOT NULL
description       TEXT NULL
location          VARCHAR(255) NULL
starts_at         TIMESTAMP NULL
ends_at           TIMESTAMP NULL
is_active         BOOLEAN DEFAULT TRUE
created_at        TIMESTAMP
updated_at        TIMESTAMP

INDEX idx_slug (slug)
INDEX idx_active (is_active)
```

**MVP Seed Data:**
- Name: "Laravel Bengaluru Nov 2025"
- Slug: "laravel-bengaluru-nov-2025"
- is_active: true

### users (auth only)
```sql
id                 BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
name               VARCHAR(255) NOT NULL
email              VARCHAR(255) UNIQUE NOT NULL
email_verified_at  TIMESTAMP NULL
password           VARCHAR(255) NOT NULL
remember_token     VARCHAR(100) NULL
created_at         TIMESTAMP
updated_at         TIMESTAMP

INDEX idx_email (email)
```

### profiles (event-specific user data)
```sql
id                BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
event_id          BIGINT UNSIGNED NOT NULL
user_id           BIGINT UNSIGNED NOT NULL
phone             VARCHAR(20) NULL
company           VARCHAR(255) NULL
job_title         VARCHAR(255) NULL
bio               TEXT NULL
profile_photo     VARCHAR(255) NULL
social_url        VARCHAR(255) NULL
qr_code_hash      VARCHAR(64) UNIQUE NOT NULL
registered_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
created_at        TIMESTAMP
updated_at        TIMESTAMP

FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
UNIQUE idx_event_user (event_id, user_id)
INDEX idx_qr_hash (qr_code_hash)
```

**MVP Behavior:** Auto-create profile for default event on user signup.

### connections
```sql
id                   BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
scanner_profile_id   BIGINT UNSIGNED NOT NULL
scanned_profile_id   BIGINT UNSIGNED NOT NULL
notes                TEXT NULL
met_at               TIMESTAMP DEFAULT CURRENT_TIMESTAMP
created_at           TIMESTAMP
updated_at           TIMESTAMP

FOREIGN KEY (scanner_profile_id) REFERENCES profiles(id) ON DELETE CASCADE
FOREIGN KEY (scanned_profile_id) REFERENCES profiles(id) ON DELETE CASCADE
INDEX idx_scanner (scanner_profile_id)
INDEX idx_scanned (scanned_profile_id)
UNIQUE idx_unique_connection (scanner_profile_id, scanned_profile_id)
```

### personal_access_tokens (Sanctum)
```sql
Laravel Sanctum auto-creates this table
```


---

## UI Screens (Mobile - NativePHP)

### Bottom Navigation (3 Tabs)
```
├── Home (My QR Code)
├── Scan
└── Connections
```

### 1. Home Screen (My QR Code)
- Large QR code (centered)
- User name + job title below
- "Edit Profile" button
- "Download QR" button

### 2. Scan Screen
- Full-screen camera view
- Scanning frame guide
- Cancel button
- Flash toggle

### 3. Profile View (After Scan)
- Profile photo
- Name, company, job title
- Bio
- Contact info (phone, social link)
- Notes text area (500 chars)
- "Save Connection" button

### 4. Connections Screen
- Search bar
- Connection cards:
  - Photo, name, company
  - Notes preview
  - Timestamp
- Tap to view full details

### 5. Profile Settings
- Edit all profile fields
- Upload photo
- Change password
- Logout


---

## 2-Day Build Plan

### Day 1: Backend Setup (8 hours)

**Morning (4 hours):**
- Laravel 12 project setup
- Database migrations (users, connections, personal_access_tokens)
- User model + authentication (Sanctum)
- QR code hash generation on registration

**Afternoon (4 hours):**
- Profile API endpoints
- Connection API endpoints
- QR code generation (as image)
- Public profile endpoint (/api/u/{hash})
- Basic testing

### Day 2: NativePHP Mobile (8 hours)

**Morning (4 hours):**
- NativePHP project setup
- Auth screens (register, login)
- Profile management screen
- Home screen (display QR code)

**Afternoon (4 hours):**
- QR scanner implementation
- Scan result → profile view → save connection
- Connections list with search
- Connection detail with notes editing
- Final testing & demo prep

---

## Critical Implementation Notes

### QR Code Hash Generation
```php
// On profile creation (auto-created for default event on signup)
$profile->qr_code_hash = Str::random(32);
```

### QR Code Format
```
connectsnap://u/{qr_code_hash}
```

### Notes Storage
- Always encrypted at rest
- Never shared with anyone
- Editable anytime

### Authentication Flow
1. Register → Email verification → Login
2. Login → Get Sanctum token
3. Store token in NativePHP secure storage
4. Include token in all API headers: `Authorization: Bearer {token}`

---

**Document End**
