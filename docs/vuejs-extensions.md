# 🎨 Vue.js Extensions – Intelli Lab

## 📌 Overview

This document lists the Vue.js libraries and extensions used in **Intelli Lab** to build a modern, responsive, and interactive frontend.

The focus is on usability, performance, and maintainability.

---

## 📡 API Communication

### Axios
**Purpose:** HTTP client for REST API communication

**Why used:**
- Simplifies API requests
- Supports interceptors (e.g. authentication tokens)
- Better error handling than native fetch

**Use cases:**
- Fetch tasks (`GET /tasks`)
- Submit code (`POST /submit-code`)
- Upload PDFs (`POST /analyze-pdf`)

---

## 🧠 State Management

### Pinia
**Purpose:** Global state management

**Why used:**
- Official Vue state library
- Lightweight and easy to use
- Centralized data handling

**Use cases:**
- User authentication state
- Current task data
- Progress tracking

---

## 🧭 Routing

### Vue Router
**Purpose:** Navigation for Single Page Application (SPA)

**Why used:**
- Enables clean URL-based navigation
- Separates views into routes

**Example routes:**
- `/dashboard`
- `/task/:id`
- `/editor`

---

## 💻 Code Editor Integration

### Monaco Editor
**Purpose:** In-browser code editor

**Why used:**
- Same editor as Visual Studio Code
- Syntax highlighting
- Good developer experience

**Use cases:**
- Writing and editing code
- Displaying assignments
- Providing structured coding environment

---

### Alternative: CodeMirror
**Purpose:** Lightweight code editor

**Why used:**
- Easier setup
- Lower performance overhead

---

## 🎨 UI & Styling

### Tailwind CSS
**Purpose:** Utility-first CSS framework

**Why used:**
- Fast UI development
- Responsive design support
- High flexibility

**Use cases:**
- Layout design
- Responsive components
- Styling UI elements

---

## 🔔 User Feedback

### Vue Toastification
**Purpose:** Notification system

**Why used:**
- Provides instant feedback to users
- Improves user experience

**Use cases:**
- Upload success messages
- Error notifications
- Status updates

---

## 📤 File Upload

### Vue FilePond
**Purpose:** File upload component

**Why used:**
- Drag & drop support
- Progress indicators
- Clean user interface

**Use cases:**
- Uploading PDF assignments

---

## ⏳ Loading Indicators

### NProgress
**Purpose:** Visual loading indicator

**Why used:**
- Improves perceived performance
- Shows ongoing background processes

**Use cases:**
- API request loading
- Page transitions

---

## 💬 Real-Time Communication (Optional)

### Laravel Echo (Frontend)
**Purpose:** Real-time event listening

**Why used:**
- Enables live updates
- Improves interactivity

**Use cases:**
- Live feedback during coding
- Status updates (e.g. "analysis in progress")

**Note:**
- Optional feature for advanced implementation

---

## 🧪 Development Tools

### Vue DevTools
**Purpose:** Debugging Vue applications

**Why used:**
- Inspect component state
- Debug application behavior

---

## 📦 Summary

| Category            | Tool |
|--------------------|------|
| API Communication  | Axios |
| State Management   | Pinia |
| Routing            | Vue Router |
| Code Editor        | Monaco Editor / CodeMirror |
| Styling            | Tailwind CSS |
| Notifications      | Vue Toastification |
| File Upload        | Vue FilePond |
| Loading Indicator  | NProgress |
| Realtime (optional)| Laravel Echo |
| Debugging          | Vue DevTools |

---

## ⚠️ Notes

- Focus on core functionality first (editor, API, PDF upload).
- Avoid adding too many libraries early.
- Advanced features (e.g. real-time updates) can be added later.
