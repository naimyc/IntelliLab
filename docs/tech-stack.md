# 🧩 Intelli Lab – Tech Stack

## 📌 Overview

This document provides a complete overview of the technologies used in **Intelli Lab**, covering both backend (Laravel) and frontend (Vue.js).

The goal is to ensure a scalable, maintainable, and developer-friendly architecture.

---

# 🖥️ Backend – Laravel

## 🔐 Authentication
**Laravel Sanctum**
- SPA authentication (Vue frontend)
- Cookie-based sessions
- Secure API protection

---

## 📄 PDF Processing
**smalot/pdfparser**
- Extracts text from PDFs

**Tesseract OCR (optional)**
- Handles scanned/image-based PDFs

---

## 🤖 AI Integration
**OpenAI API**
- Task generation from PDF content
- Code evaluation and hints

---

## 🧩 API Layer
**Laravel API Resources**
- Standardized JSON responses
- Clean separation of logic and presentation

---

## 🧠 Roles & Permissions
**Spatie Laravel Permission**
- Role-based access control
- Example: admin vs student

---

## ⚡ Background Processing
**Laravel Queue**
- Handles heavy tasks asynchronously:
  - PDF parsing
  - AI requests
  - Code analysis

**Laravel Horizon (optional)**
- Queue monitoring dashboard

---

## 🧪 Debugging
**Laravel Telescope**
- Request inspection
- Error tracking

---

## 💬 Realtime (Optional)
**Laravel Echo**
- Live updates (analysis status, feedback)

---

# 🎨 Frontend – Vue.js

## 📡 API Communication
**Axios**
- HTTP client for API requests
- Interceptors for auth handling

---

## 🧠 State Management
**Pinia**
- Global state management
- Handles user + task data

---

## 🧭 Routing
**Vue Router**
- SPA navigation
- Example routes:
  - `/dashboard`
  - `/task/:id`
  - `/editor`

---

## 💻 Code Editor
**Monaco Editor**
- VS Code-like experience
- Syntax highlighting

**Alternative:** CodeMirror

---

## 🎨 UI & Styling
**Tailwind CSS**
- Utility-first styling
- Responsive design

---

## 🔔 Notifications
**Vue Toastification**
- User feedback (success/error messages)

---

## 📤 File Upload
**Vue FilePond**
- Drag & drop uploads
- Progress indicators

---

## ⏳ Loading UX
**NProgress**
- Visual loading bar for API calls

---

## 💬 Realtime (Optional)
**Laravel Echo (Frontend)**
- Listens for backend events

---

## 🧪 Dev Tools
**Vue DevTools**
- Debugging Vue components

---

# 📦 MVP Focus

To keep development lean, prioritize:

## ✅ Core Features
- PDF Upload & Parsing
- AI Task Processing
- Code Editor (Monaco)
- API Communication (Axios)
- State Management (Pinia)

## ⏳ Add Later
- Realtime updates (Echo)
- OCR support
- Advanced monitoring (Horizon, Telescope UI)

---

# 🏗️ Architecture Summary

| Layer     | Tech |
|----------|------|
| Backend  | Laravel |
| Frontend | Vue 3 |
| Auth     | Sanctum |
| AI       | OpenAI API |
| State    | Pinia |
| Styling  | Tailwind CSS |

---

# ⚠️ Guiding Principles

- Keep the MVP minimal
- Avoid unnecessary dependencies early
- Use queues for heavy operations
- Maintain clean API structure
- Design for scalability from the start

---