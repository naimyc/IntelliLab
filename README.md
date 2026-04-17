# Intelli Lab 🧠💻  
**AI-Powered Learning Platform for Programming Assignments**

---

## 📚 Course Context

This project is developed as part of:

**Webtechnologien II (WT2)**  
SoSe 2026 – Hochschule Bochum  

**Requirements:**
- Backend: PHP (Laravel)
- Frontend: Vue.js
- Communication: REST API
- Database: PostgreSQL 
- Responsive Web Design

---

## 📌 Project Overview

**Intelli Lab** is a web application that helps students solve programming assignments more effectively.

Users can upload exercise sheets (e.g. PDFs), which are automatically analyzed.  
The system then guides them step by step through the solution process while providing **real-time feedback during coding**.

👉 Think of it as a **digital programming tutor**.

---

## ❗ Problem Statement

Students often struggle with:

- ❓ Unclear or complex task descriptions  
- ⏳ Lack of immediate support  
- 📄 Static PDFs without interactivity  
- 🧠 Copying solutions instead of understanding  

### Result:
- Frustration  
- Inefficient workflows  
- Shallow learning  

---

## 💡 Solution

Intelli Lab provides:

- 📄 **Automatic PDF analysis**
- 🧩 **Step-by-step task breakdown**
- 💬 **Guided hints instead of full solutions**
- ⚡ **Real-time feedback while coding**
- 🧑‍🏫 **AI-based tutoring system**

---

## 🏗️ System Architecture

### 🔧 Backend (Laravel)
- RESTful API
- Handles:
  - PDF processing
  - Task analysis
  - User management
  - Feedback logic

### 🎨 Frontend (Vue.js)
- Single Page Application (SPA)
- Features:
  - Code editor interface
  - Interactive task guidance
  - Progress tracking

### 🔗 Communication
- REST API (JSON)
- Example:
  - `GET /tasks`
  - `POST /analyze-pdf`
  - `POST /submit-code`

### 🗄️ Database
- Stores:
  - Users
  - Tasks
  - Submissions
  - Feedback history

---

## 🚀 Features

- Upload and analyze PDF assignments  
- Structured step-by-step guidance  
- Interactive coding environment  
- Real-time feedback and hints  
- Progress tracking  

---

## 👥 Target Audience

- 🎓 Computer science students  
- 👨‍💻 Beginner programmers  
- 🏫 High school students (informatics)  
- 👩‍🏫 Educators  

---

## 📊 Market Analysis

### 🔍 Existing Solutions

| Existing Tools / Platforms | Limitation |
|---------------------------|-----------|
| ChatGPT / AI tools        | Provide full solutions → passive learning |
| GitHub Copilot            | Assists coding but lacks learning guidance |
| IDEs (VS Code, IntelliJ)  | No educational support or task guidance |
| Hyperskill                | Guided learning, but no custom assignment/PDF understanding |
| Exercism                  | Mentor feedback, but no real-time AI guidance |
| Coursebox AI              | AI tutoring, but not focused on coding workflows |
| Tynker                    | Interactive, but limited to beginner/child-level content |
| GPTutor (research)        | Code explanations only, no full learning system |

👉 **Gap:** No system combines *assignment understanding + guided step-by-step solving + real-time feedback*

---

## 🛠️ Tech Stack

| Layer      | Technology |
|------------|-----------|
| Backend    | Laravel (PHP) |
| Frontend   | Vue.js |
| API        | REST |
| Database   | PostgreSQL |
| Styling    | CSS / Responsive Design |

---