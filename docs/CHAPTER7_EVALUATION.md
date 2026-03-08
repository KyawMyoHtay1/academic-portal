# 7. Evaluation, Discussion and Conclusion

## 7.0 Evaluation Overview

This chapter evaluates the University Academic Portal project against the **aims and objectives** defined in Sections **1.6** and **2.1**, the **methodology and technology choices** justified in **Chapter 3**, and the **timebox plan** described in **Section 4.5**.  
The evaluation considers how well the project was planned and executed, what results it achieved, and what lessons were learned.

**Evaluation objectives:**
- Measure how far the system meets its intended scope and objectives.
- Assess effectiveness, efficiency, usability, quality and maintainability.
- Identify lessons learned and recommendations for future improvement.

**Evaluation criteria:**
- **Effectiveness** – how well the portal achieves its functional aims (registration, enrolment, grades, fees, timetable, attendance, communication).
- **Efficiency** – how far it reduces manual work, duplication, errors and delays compared to the manual system described in **Chapter 1**.
- **Quality & usability** – how reliable, secure and user‑friendly the system is, based on functional and usability testing.
- **Sustainability & maintainability** – how suitable the architecture, database design and documentation are for future changes and extensions.

**Evaluation methods:**
- Evidence from **functional testing and logs** for all three timeboxes (Chapters **5** and **6**).
- **Usability testing** using Nielsen’s 10 Usability Heuristics (Sections **5.1.7**, **5.2.8**, **5.3.8**).
- **Comparison with existing academic portals** (Chapter **2**).
- Review of **design artefacts** (ERD, class diagrams, sequence diagrams, screen designs) against the final implementation (Chapter **4** and Chapter **5**).
- **Personal reflection** on the development process (Section **7.5**).

---

## 7.1 Evaluation against Aim & Objectives

### Aim

The main aim of the University Academic Portal project was to design and implement a web‑based system that replaces manual and spreadsheet‑based processes for managing **student registration, course registration, grades, fee payment, timetable, attendance and academic communication**, as stated in **Section 2.1 Aims**.  
After completing all three timeboxes, the portal now supports end‑to‑end workflows for these areas with role‑based access for students, teachers and staff/admin. The system centralises data, reduces duplication, and provides faster feedback to users (for example, immediate enrolment status, grade visibility and fee status). Based on the implemented features, testing evidence and comparison with the original manual processes, the overall aim of the project is **achieved**.

### Objective 1 – Analysis

The first objective was to analyse current university processes and requirements for student registration, course registration, grading, fees, timetable, attendance and communication (**Section 3.1.1 – Analysis**). I studied existing manual procedures, reviewed similar university portals (Chapter **2**) and defined detailed functional and non‑functional requirements for each timebox (Chapter **4**). The resulting requirements specification, use case descriptions and prioritised timebox scope (using MoSCoW) provided a clear baseline for development.  
Comparing the finished system with these requirements shows that the core needs—registration, enrolment workflow, grade lifecycle (including assignments), fee lifecycle, timetable, attendance and messaging—have been implemented, while a few low‑priority ideas (for example advanced analytics) were intentionally deferred. Overall, **Objective 1 (Analysis)** was **met**.

### Objective 2 – Design

The second objective was to design a structured architecture and models using UML and MVC principles (**Sections 3.1.4 and 4.4**). I produced use case diagrams, class diagrams, the overall ERD, sequence diagrams and screen designs for all three timeboxes, then refined them as the project evolved. These designs guided the Laravel MVC implementation: Eloquent models map closely to the ERD, controllers follow the sequence diagrams, and Vue/Tailwind screens reflect the prototypes in the screen design chapters.  
Some minor changes were made during implementation (for example, adding audit tables such as `fee_status_logs` and `enrollment_status_logs` and refining attendance/alert tables), but these were fed back into the diagrams and ERD, keeping design and code aligned. The consistency between diagrams, database schema and code shows that **Objective 2 (Design)** has been **achieved**.

### Objective 3 – Implementation (Timebox Features)

The third objective was to implement the portal incrementally in three timeboxes, as planned in **Section 3.1.5–3.1.7** and detailed in **Section 4.5 Timebox Plan**:

- **Timebox 1:** User management, student registration, course and subject management, and course registration (enrolment and withdrawal).  
- **Timebox 2:** Grade management (teacher submission and staff review), student grade viewing and GPA, fee and payment workflow (including Stripe integration and receipts), and assignment management.  
- **Timebox 3:** Timetable management, attendance recording and reporting, low attendance alerts, announcements, messaging, notifications and public contact/feedback forms.

All planned modules for each timebox are working in the final system and are documented in Chapter **5**. Some features, such as low‑attendance alerts and payment status logs, went beyond the original scope and improved traceability. A few convenience functions (for example, very advanced filters and combined dashboards) remain as future work, but the main functional coverage for each timebox is complete. **Objective 3 (Implementation)** is therefore **fulfilled**.

### Objective 4 – Testing and Quality

The fourth objective was to ensure that the portal meets its functional and non‑functional requirements through structured testing (**Section 3.1.9**). For each timebox I created detailed functional test plans and logs (Chapters **5** and **6**), executed test cases across all major workflows (registration, enrolment, grades, fees, timetable, attendance, communication) and documented evidence with screenshots.  
Usability testing was also carried out using Nielsen’s heuristics, leading to several UI improvements (such as clearer status badges, better filters, improved pagination, and redesigned timetable/attendance layouts). While there is still scope for deeper performance and security testing under heavy load, the implemented testing has been sufficient to confirm correctness and usability for typical academic use. **Objective 4 (Testing and Quality)** is **largely achieved**.

### Objective 5 – Deployment, Training and Documentation

The fifth objective was to prepare deployment, training and documentation so that the system can be adopted by users (**Section 3.1.8 – Deployment, Integration & Training**). I produced a deployment diagram, data migration and training plans (Chapter **6**), and detailed user manuals for all three timeboxes (`TIMEBOX1_USER_MANUAL.md`, `TIMEBOX2_USER_MANUAL.md`, `TIMEBOX3_USER_MANUAL.md`, combined in `USER_MANUAL.md`).  
The user manuals walk through each main screen with purpose, steps and expected results. The deployment plan covers web server, database server and backup server setup, and the training plan identifies trainee groups and topics. Although the system has not yet been deployed in a real university environment, the documentation and plans are ready. **Objective 5 (Deployment, Training and Documentation)** is **substantially met**.

---

## 7.2 Evaluation against Similar Systems

Chapter **2** compared existing university academic portals (for example, University of Greenwich and University of Bath) in terms of functional features (registration, results, timetables, library access, FAQs) and usability (using Nielsen’s 10 heuristics). The University Academic Portal matches many of these features and improves on several areas:

- **Functional coverage:** Like the reference portals, the system supports user registration, login, course enrolment, grade viewing, fee information and timetables. In addition, it integrates **assignment management, attendance alerts and internal messaging**, which some portals provide only partially or via separate systems.
- **Usability:** The portal adopts similar patterns to the reference systems—clear navigation, status visibility, consistent layout, search, and help content—but tailors them to the university’s academic processes. The iterative usability testing and design improvements (Chapters **5.1.3**, **5.2.4**, **5.3.4**) brought the UI closer to the level of mature portals.
- **Traceability and auditability:** The use of status logs for enrolment, grades, fees and low attendance provides stronger audit trails than the manual system and is comparable to, or better than, what was observed in some existing portals.

Overall, the evaluation against similar systems shows that the Academic Portal provides a **competitive and, in some areas, more integrated solution** than the systems studied in the literature review.

---

## 7.3 Evaluation against Justification Made

### Methodology

#### 1. Selected Methodology

I selected **DSDM Agile** as the development methodology (justified in **Chapter 3**, Sections **3.1.1–3.1.4** and **3.2 DSDM Feasibility**) because the project had a fixed academic deadline and a clear need to deliver working increments (timeboxes) while requirements were still evolving. DSDM’s focus on timeboxing, active user involvement, and iterative refinement matched the way the portal was developed: Timebox 1 focused on core registration and enrolment, Timebox 2 on grades, fees and assignments, and Timebox 3 on timetable, attendance and communication. In practice, this approach allowed me to deliver usable functionality early, respond to supervisor feedback, and keep the project moving even when some details changed.

#### 2. Problems Encountered

The main challenge with DSDM was the **intensive feedback requirement**. Regular reviews with the supervisor were essential but not always easy to schedule, which sometimes delayed decisions. **Scope creep** was another issue: as new ideas emerged (for example, extra audit logs, attendance alerts and notification improvements), it was tempting to add them into the current timebox instead of postponing them. This occasionally put pressure on the schedule and forced re‑prioritisation. Documentation also risked falling behind while I focused on building and refining features.

#### 3. Lessons Learnt

From using DSDM I learned the importance of **strict prioritisation** (Must/Should/Could) and protecting the timebox scope. It is valuable to capture new ideas but equally important to move them to future iterations instead of overloading the current one. I also learned that planning **regular, short feedback sessions** is more effective than long, infrequent meetings. Finally, integrating **testing and documentation into each timebox**—rather than leaving them to the end—helps keep the project under control and reduces last‑minute rush. These lessons will be applied in future projects with similar time‑boxed constraints.

### Language / Framework

#### 4. Selected Languages

The portal is built with **PHP 8.2 (Laravel 12)** for the backend, **Vue 3 + Inertia** for the SPA‑style frontend, **Tailwind CSS** for styling, and **JavaScript** for interactivity, as proposed in **Section 4.3 Tools and Technologies**. This stack integrates well: Laravel provides strong MVC structure and Eloquent ORM, Vue/Inertia gives a smooth user experience without building a completely separate API, and Tailwind enables consistent, responsive UI design. Together they were an appropriate choice for a modern university portal.

#### 5. Problems Encountered

There were several technical challenges. Learning **Inertia and Vue 3** together with Laravel took time, especially understanding how props, routes and form submissions interact between PHP and JavaScript. Complex Eloquent queries for reporting (grades, fees, attendance) sometimes became slow or difficult to read, especially when combining filters, pagination and relationships. Handling **Stripe integration** safely and correctly (webhooks, idempotency, error handling) also required careful reading of the documentation and testing in sandbox mode. On the frontend, ensuring that large tables (grades, fees, attendance) remained usable on smaller screens required iteration of the layout and filters.

#### 6. Lessons Learnt

I learned the value of **layering business logic**: keeping controllers thin, moving calculations (for example GPA and computed grades) into dedicated services or model methods, and using form request classes for validation. Using Laravel’s validation and form request classes reduced duplication and improved security. On the frontend, I realised that **smaller, focused Vue components** are easier to maintain than one very large page. Overall, I gained confidence in building full‑stack Laravel + Vue applications and now understand better how to structure an academic system for future growth.

### Database

#### 7. Selected Database

The project uses **MySQL** as the relational database, consistent with the recommendation in **Section 3.1.3 Databases**. MySQL integrates smoothly with Laravel’s Eloquent ORM and is well suited to the highly relational nature of an academic portal: users, students, courses, subjects, enrolments, grades, fees, timetables, attendance and communication data. The schema was normalised and supported with audit tables (such as `grade_review_logs`, `fee_status_logs` and `enrollment_status_logs`) to provide traceability, which is important in an academic environment.

#### 8. Problems Encountered

Designing a schema that remained **flexible across timeboxes** was challenging. Early on, some tables had to be adjusted (for example, adding new status fields, linking grades more tightly to subjects, and introducing low‑attendance state tracking). Complex queries for dashboards and reports (attendance summaries, overdue fees, low‑attendance lists) could become slow if indexes were not chosen carefully. Managing data consistency during refactoring—such as removing redundant columns and adding foreign keys—required careful migration planning and regression testing.

#### 9. Lessons Learnt

I learned to think about **auditability and reporting** from the beginning, not just minimal tables to “make it work”. Using proper foreign keys, composite unique constraints (such as one grade per subject+student), and status logs improves data quality. I also saw how important good **indexing** is for performance, especially on status and foreign‑key columns. Regularly regenerating the ERD and reviewing it against the actual code helped keep the database and models aligned.

---

## 7.4 Evaluation against Time Box Plan

### Timebox 1: Manage Student Registration & Course Registration Process

Timebox 1 focused on user management, student profiles, courses/subjects, and the enrolment/withdrawal workflow, as planned in **Section 3.1.5**. According to the timebox schedule in **Section 4.5**, these core features were completed on schedule and passed functional and usability testing. Some UI polish (for example improved filters and pagination on management screens) was implemented during later iterations, but the fundamental workflow—register student, manage courses and subjects, enrol and withdraw—was delivered and stable, forming a solid base for later timeboxes.

### Timebox 2: Manage Grades, Fee Payment & Assignment Process

Timebox 2 introduced more complex business rules: grade submission and review, fee creation and payment, Stripe integration, receipts, assignments and submissions, matching **Section 3.1.6**. Most planned features were implemented within the timebox, but integration with Stripe and the design of audit tables (grade and fee logs) required extra research and testing, which slightly compressed the buffer time. Despite this, functional and usability testing for grades, fees and assignments was completed and the main deadlines were met. The experience highlighted the need to allocate more time to external integrations and third‑party services in future plans.

### Timebox 3: Manage Timetable, Attendance & Communication Process

Timebox 3 covered timetable, attendance, attendance alerts, announcements, messaging, notifications and public contact/feedback forms, as specified in **Section 3.1.7**. This timebox also included several refinements based on usability feedback from earlier timeboxes (for example, better status indicators, weekly timetable grid layout and improved notification handling). Most scope was delivered as planned, but low‑attendance alert logic and notification management were more complex than expected. These were still completed within the timebox, but with less buffer than ideal. Overall, the timebox plan worked well: each increment added clear value and kept the project aligned with the overall aim and schedule.

---

## 7.5 Personal Evaluation

Working on the University Academic Portal has been one of the most challenging and rewarding learning experiences of my studies. I moved from building small exercises to delivering a full, multi‑timebox system that real users could imagine using. I developed practical skills in **Laravel, Vue 3, Tailwind CSS, Stripe integration and MySQL**, but also improved my ability to analyse requirements, design UML diagrams, plan iterations and document my work clearly.

The project forced me to balance **technical tasks** (coding, debugging, testing) with **non‑technical tasks** (planning, communication with my supervisor, writing documentation and user manuals). At times it was difficult to manage all responsibilities, especially when new ideas appeared late or when bugs took longer than expected to fix. However, these pressures taught me to **prioritise work**, to break large problems into smaller tasks, and to keep moving forward even when progress felt slow.  
Overall, the project has increased my confidence as a developer and future software engineer, and has shown me that I can manage a substantial system from analysis to implementation, deployment planning and evaluation.

---

## 7.6 Strength & Weakness of University Academic Portal

### Strengths

- **Integrated academic processes:** The portal covers the full lifecycle from registration and enrolment to grades, fees, timetable, attendance and communication in a single system, reducing duplication and manual work compared to the manual processes in **Chapter 1**.
- **Role‑based interfaces:** Separate experiences for Students, Teachers and Staff/Admin make it easier for each group to find relevant functions quickly.
- **Clear workflows and audit trails:** Enrolment status logs, grade review logs, fee status logs and low‑attendance state tracking provide transparency and traceability for academic decisions.
- **Modern technology stack:** Laravel, Vue 3, Inertia and Tailwind CSS give a responsive, modern UI and an architecture that is maintainable and extendable.
- **Strong validation and business rules:** Functional testing and validation rules (for example, preventing duplicate enrolment, schedule conflicts, or invalid payments) reduce errors and improve data quality.
- **Communication features:** Announcements, messaging, notifications and contact/feedback forms create a consistent communication channel between the university and its users.

### Weaknesses

- **Performance and scalability not yet stress‑tested:** The system has been tested with realistic but small datasets; it has not yet been formally tested under very high concurrent load or with very large data volumes.
- **Limited accessibility and localisation:** While the UI is usable, further work is needed on accessibility (keyboard navigation, ARIA labels) and potential multi‑language support.
- **Dependency on network and external services:** As a web‑based system, it depends on stable internet and on external services such as Stripe and email; failure of these services would affect functionality.
- **Administrative tooling still basic:** Some staff reporting and analytics (for example dashboards combining grades, attendance and fees) are functional but could be more visual and configurable.
- **Training and adoption not yet validated with real users:** Training plans and user manuals exist, but large‑scale rollout and feedback from real university staff and students are still future work.

---

## 7.7 Future Amendment

### 10. Program (Functionality)

Future versions of the Academic Portal could include additional functionality such as:

- **Enhanced analytics and dashboards** for programme leaders and administrators (for example combined views of grades, attendance and fees for at‑risk students).
- **Mobile‑optimised or native mobile apps** for students and teachers to access timetable, attendance, grades and notifications more conveniently.
- **Integration with external systems**, such as LMS/VLE platforms, plagiarism detection tools, and HR or finance systems to avoid double data entry.
- **Self‑service features** such as online programme change requests or appointment booking with advisors.

These enhancements would build on the existing architecture without disrupting current workflows.

### 11. Design (User Interface & Experience)

On the design side, there are several potential improvements:

- Implement a richer **design system** with reusable components, dark mode, improved accessibility and clearer visual feedback.
- Further streamline long tables and forms (for grades, fees, attendance) using **progressive disclosure**, inline editing and better filtering/sorting.
- Refine **mobile layouts and touch interactions** to ensure the portal is comfortable to use on phones and tablets.
- Introduce more consistent **micro‑interactions** (confirmation messages, animations, tooltips) to guide new users and reduce errors.

### 12. Report (Documentation & Evaluation)

For the written report and project documentation, future amendments could include:

- Conducting **formal user studies** with real staff and students to collect quantitative and qualitative feedback on usability and effectiveness, then incorporating results into the evaluation chapter.
- Adding more detailed **performance and security evaluation**, including load testing, profiling and penetration‑testing summaries.
- Extending the documentation with **API/service documentation** and more detailed deployment scripts for different environments (development, staging, production).

These improvements would not change the core system, but would strengthen the academic evaluation of the project and make the portal easier to hand over and maintain.

