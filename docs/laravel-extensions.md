# 🔌 Laravel Extensions – Intelli Lab

## 📌 Overview

This document lists all Laravel extensions and external packages used in **Intelli Lab**, including their purpose and role within the system.

The goal is to keep the backend modular, maintainable, and scalable.

---

## 🔐 Authentication

### Laravel Sanctum
**Purpose:** API authentication for SPA (Vue.js frontend)

**Why used:**
- Lightweight authentication system
- Supports cookie-based auth (ideal for SPA)
- Secure handling of user sessions

**Use cases:**
- Login / Logout
- Protected API routes
- User session management

---

## 📄 PDF Processing

### smalot/pdfparser
**Purpose:** Extract text from uploaded PDF documents

**Why used:**
- Simple integration into Laravel
- Enables processing of assignment sheets

**Limitations:**
- Struggles with complex layouts
- No OCR support

---

### Tesseract OCR (optional)
**Purpose:** Text recognition for scanned PDFs

**Why used:**
- Required when PDFs contain images instead of text

**Note:**
- Runs as external service (not native Laravel package)

---

## 🤖 AI Integration

### OpenAI API
**Purpose:** Intelligent task analysis and feedback generation

**Why used:**
- Converts raw PDF text into structured tasks
- Generates hints and explanations
- Evaluates submitted code (basic level)

**Integration:**
- Called via Laravel services
- Used inside queued jobs

---

## 🧩 API Structuring

### Laravel API Resources
**Purpose:** Transform models into clean JSON responses

**Why used:**
- Consistent API structure
- Separation of logic and presentation
- Easier frontend integration

---

## 🧠 Roles & Permissions

### Spatie Laravel Permission
**Purpose:** Role and permission management

**Why used:**
- Define roles (e.g. student, admin)
- Restrict access to certain features

**Example:**
- Only admins can manage tasks
- Students can submit solutions

---

## ⚡ Asynchronous Processing

### Laravel Queue System
**Purpose:** Handle heavy tasks in the background

**Why used:**
- Prevent slow API responses
- Improve user experience

**Jobs include:**
- PDF parsing
- AI requests
- Code analysis

---

### Laravel Horizon (optional)
**Purpose:** Queue monitoring dashboard

**Why used:**
- Visualize job processing
- Debug queue-related issues

---

## 🧪 Debugging & Development

### Laravel Telescope
**Purpose:** Debugging and request inspection

**Why used:**
- Monitor API requests
- Inspect database queries
- Track errors and exceptions

---

## 💬 Real-Time Communication (Optional)

### Laravel Echo
**Purpose:** Real-time event broadcasting

**Why used:**
- Live feedback updates
- Status updates (e.g. "analysis in progress")

**Alternative:**
- Polling via REST (simpler for MVP)

---

## 📦 Summary

| Category            | Tool |
|--------------------|------|
| Authentication     | Laravel Sanctum |
| PDF Processing     | smalot/pdfparser, Tesseract OCR |
| AI Integration     | OpenAI API |
| API Structuring    | Laravel API Resources |
| Roles & Permissions| Spatie Permission |
| Background Jobs    | Laravel Queue, Horizon |
| Debugging          | Telescope |
| Realtime (optional)| Echo |

---

## ⚠️ Notes

- Not all extensions are required for the MVP.
- Focus should be on:
  - PDF parsing
  - AI integration
  - Clean API design

Advanced features (e.g. real-time updates) can be added later.