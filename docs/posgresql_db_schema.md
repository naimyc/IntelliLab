# 🗄️ Intelli Lab – PostgreSQL Database Schema

This schema supports the full Intelli Lab workflow:

- 📄 PDF Upload → Task Extraction
- 🧠 Modelling Phase → Step-by-step guidance
- 💻 Coding Phase → Monaco Editor submissions
- 🤖 Feedback Phase → AI evaluation loop
- 📈 Progress Tracking → Learning state per user

---

# 👤 1. users

Stores all platform users.

```sql
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
````

---

# 📚 2. assignments

Represents uploaded PDF exercises.

```sql
CREATE TABLE assignments (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(255),
    original_file_path TEXT,
    status VARCHAR(50) DEFAULT 'uploaded',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
```

---

# 🧩 3. tasks

Extracted problems from assignments.

```sql
CREATE TABLE tasks (
    id BIGSERIAL PRIMARY KEY,
    assignment_id BIGINT REFERENCES assignments(id) ON DELETE CASCADE,
    title VARCHAR(255),
    description TEXT,
    difficulty VARCHAR(50),
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT NOW()
);
```

---

# 🧠 4. task_steps (Modelling Phase)

Breaks tasks into guided learning steps.

```sql
CREATE TABLE task_steps (
    id BIGSERIAL PRIMARY KEY,
    task_id BIGINT REFERENCES tasks(id) ON DELETE CASCADE,
    step_number INT,
    instruction TEXT,
    hint TEXT,
    expected_outcome TEXT,
    created_at TIMESTAMP DEFAULT NOW()
);
```

---

# 💻 5. code_submissions (Coding Phase)

Stores code written in Monaco Editor.

```sql
CREATE TABLE code_submissions (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    task_id BIGINT REFERENCES tasks(id) ON DELETE CASCADE,
    code TEXT NOT NULL,
    language VARCHAR(50) DEFAULT 'javascript',
    status VARCHAR(50) DEFAULT 'submitted',
    created_at TIMESTAMP DEFAULT NOW()
);
```

---

# 🧪 6. submission_results (Feedback Phase)

AI/Backend evaluation results.

```sql
CREATE TABLE submission_results (
    id BIGSERIAL PRIMARY KEY,
    submission_id BIGINT REFERENCES code_submissions(id) ON DELETE CASCADE,
    score INT,
    feedback TEXT,
    errors JSONB,
    suggestions TEXT,
    created_at TIMESTAMP DEFAULT NOW()
);
```

---

# 💬 7. ai_feedback_logs

Stores AI prompt/response history.

```sql
CREATE TABLE ai_feedback_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    submission_id BIGINT REFERENCES code_submissions(id),
    prompt TEXT,
    response TEXT,
    model VARCHAR(100),
    created_at TIMESTAMP DEFAULT NOW()
);
```

---

# 📈 8. user_progress

Tracks user progress per task.

```sql
CREATE TABLE user_progress (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    task_id BIGINT REFERENCES tasks(id) ON DELETE CASCADE,
    current_step INT DEFAULT 0,
    status VARCHAR(50) DEFAULT 'in_progress',
    last_code_submission_id BIGINT,
    updated_at TIMESTAMP DEFAULT NOW()
);
```

---

# 🧭 9. sessions (optional)

Only needed if using database sessions instead of file/session storage.

```sql
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT,
    last_activity INT
);
```

---

# 🔄 System Flow Overview

```text
PDF Upload
   ↓
assignments
   ↓
tasks
   ↓
task_steps (Modelling Phase)

User writes code (Monaco Editor)
   ↓
code_submissions
   ↓
Laravel API + AI evaluation
   ↓
submission_results
   ↓
ai_feedback_logs (AI trace/debugging)

Progress tracking
   ↓
user_progress
```

---

# 🧠 Design Highlights

## ✅ Clean separation of concerns

* tasks = WHAT to solve
* task_steps = HOW to think
* submissions = WHAT user wrote
* results = HOW good it is

---

## 🤖 AI-ready architecture

* ai_feedback_logs allows:

  * prompt debugging
  * model comparison
  * improvement of tutoring logic

---

## 📈 Scalable learning system

Supports future features:

* multi-language coding
* teacher dashboards
* peer review system
* grading automation

---

