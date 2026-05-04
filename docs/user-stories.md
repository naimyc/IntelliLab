# 🧠 Intelli Lab – User Stories

## 📌 Epic 1: User Authentication & Onboarding

### US-1.1 Register Account
As a new user,  
I want to create an account,  
so that I can access the Intelli Lab platform.

**Acceptance Criteria:**
- User can register with email and password
- Validation errors are shown for invalid input
- User is redirected to login or dashboard after successful registration

---

### US-1.2 Login
As a returning user,  
I want to log in,  
so that I can access my personal workspace.

**Acceptance Criteria:**
- Email/password authentication works
- Incorrect credentials show error message
- Session is persisted across page reloads

---

### US-1.3 Logout
As a logged-in user,  
I want to log out,  
so that my session is securely ended.

**Acceptance Criteria:**
- Token/session is invalidated
- User is redirected to login page

---

## 📌 Epic 2: Dashboard & Navigation

### US-2.1 View Dashboard
As a user,  
I want to see an overview dashboard,  
so that I can track my progress and active tasks.

**Acceptance Criteria:**
- Shows active assignments
- Shows progress per module (coding/modelling/feedback)
- Navigation to all main sections works

---

### US-2.2 Navigate Between Phases
As a user,  
I want to switch between modelling, coding, and feedback views,  
so that I can follow the learning workflow.

**Acceptance Criteria:**
- Routes exist for `/dashboard`, `/modelling`, `/coding`
- State is preserved between navigation
- UI clearly shows current phase

---

## 📌 Epic 3: Assignment Handling (PDF → Task)

### US-3.1 Upload Assignment PDF
As a user,  
I want to upload a PDF exercise sheet,  
so that the system can analyze it.

**Acceptance Criteria:**
- PDF upload supported
- File validation (size/type)
- Upload progress indicator

---

### US-3.2 View Extracted Tasks
As a user,  
I want to see extracted tasks from my PDF,  
so that I understand what I need to solve.

**Acceptance Criteria:**
- Tasks are structured and readable
- Tasks are split into steps where possible
- User can select a task to start working on

---

## 📌 Epic 4: Modelling Phase (Understanding the Problem)

### US-4.1 View Task Breakdown
As a user,  
I want to see a step-by-step breakdown of a problem,  
so that I understand how to solve it.

**Acceptance Criteria:**
- Task is decomposed into sub-problems
- Hints are shown, not full solutions
- User can mark steps as understood

---

### US-4.2 Request Hints
As a user,  
I want to request hints for a step,  
so that I can get help without being given the full solution.

**Acceptance Criteria:**
- Hint button available per step
- Hints are progressive (not full solution)
- AI-generated or rule-based feedback

---

## 📌 Epic 5: Coding Phase (Monaco Editor)

### US-5.1 Write Code in Editor
As a user,  
I want to write code in a browser-based editor,  
so that I can solve programming tasks.

**Acceptance Criteria:**
- Monaco Editor integrated
- Syntax highlighting enabled
- Language mode selectable

---

### US-5.2 Run / Submit Code
As a user,  
I want to submit my code,  
so that it can be evaluated.

**Acceptance Criteria:**
- Code is sent to backend API
- Loading state is shown
- Response contains feedback or errors

---

### US-5.3 View Errors Inline
As a user,  
I want to see errors highlighted in my code,  
so that I can fix them quickly.

**Acceptance Criteria:**
- Backend returns error positions
- Monaco highlights errors
- Error messages are readable

---

## 📌 Epic 6: Feedback System (AI Tutor)

### US-6.1 Receive AI Feedback
As a user,  
I want to receive feedback on my solution,  
so that I can improve my understanding.

**Acceptance Criteria:**
- Feedback is contextual to submitted code
- Includes suggestions, not full solutions
- Response time is reasonable

---

### US-6.2 Compare Solution Quality
As a user,  
I want to see how good my solution is,  
so that I can improve my coding skills.

**Acceptance Criteria:**
- Code quality score or evaluation provided
- Explanation of weaknesses included

---

## 📌 Epic 7: Progress Tracking

### US-7.1 Track Learning Progress
As a user,  
I want to see my progress across tasks,  
so that I know what I have completed.

**Acceptance Criteria:**
- Progress stored per task
- Visual progress indicators
- Completion status updates automatically

---

### US-7.2 Resume Work
As a user,  
I want to continue where I left off,  
so that I don’t lose my progress.

**Acceptance Criteria:**
- Last active task is saved
- User returns to same coding state

---

## 📌 Epic 8: System & Experience

### US-8.1 Responsive Design
As a user,  
I want to use the platform on different devices,  
so that I can work anywhere.

**Acceptance Criteria:**
- Mobile + desktop support
- Layout adapts to screen size

---

### US-8.2 Fast Interaction
As a user,  
I want the system to respond quickly,  
so that my workflow is not interrupted.

**Acceptance Criteria:**
- API responses are optimized
- Loading states shown for long operations