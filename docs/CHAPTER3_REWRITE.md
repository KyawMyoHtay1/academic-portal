# Chapter 3: Feasibility Study and Project Justification

## 3.1 Introduction

This chapter examines the feasibility of the University Academic Portal and justifies the main project decisions. The purpose of the chapter is to show that the project was practical, technically appropriate, and academically defensible before and during implementation. The discussion covers the requirement gathering approach, the selected development methodology, the chosen implementation stack, the database choice, the feasibility of the project in operational and economic terms, and the legal, ethical, social, and professional issues relevant to the system.

The chapter is important because the University Academic Portal is not only a coding exercise. It is a multi-user academic management system that stores sensitive information, supports role-based workflows, and must remain usable, secure, and maintainable. For that reason, each technical and methodological decision needs clear justification.

## 3.2 Requirement Gathering Approach

### 3.2.1 Selected Approach

The original project plan proposed gathering requirements through interviews. In practice, the final project used an online questionnaire created with Google Forms. This adjustment was made for practical reasons. A questionnaire was easier to distribute, easier to complete asynchronously, and more suitable for collecting responses from a wider group of potential users, including students, teachers, and administrative staff.

For a final-year project with limited time and limited access to stakeholders, the questionnaire approach was feasible because it allowed responses to be collected without requiring formal interview scheduling. It also provided a simple way to gather comparable answers from different user types about the weaknesses of current academic processes and the features expected in a new portal.

### 3.2.2 Purpose of the Questionnaire

The questionnaire was used to identify:

1. The main problems users experience in manual or spreadsheet-based academic administration.
2. The most important features expected in a university academic portal.
3. User expectations regarding accessibility, ease of use, online availability, and workflow efficiency.

These findings helped shape the system scope described in Chapter 1 and supported the definition of functional and non-functional requirements for the proposed portal.

### 3.2.3 Use of the Collected Responses

The responses were used as practical requirement elicitation evidence rather than as a large-scale statistical study. In other words, the questionnaire was intended to support design decisions and feature prioritisation, not to produce a broad quantitative model of all university users.

The responses confirmed demand for the core functions already identified in the proposal: student registration, course enrolment, grades, fees, timetable access, attendance tracking, and communication. They also reinforced several non-functional needs, including clear navigation, role-based access control, simple interfaces, and online availability.

### 3.2.4 Strengths and Limitations of the Approach

The main strength of the questionnaire approach is that it is efficient, scalable, and suitable for a student project with limited access to participants. It allows multiple users to respond in their own time and creates a simple record of requirements that can later be analysed.

However, the approach also has limitations. Compared with interviews, questionnaires provide less depth because follow-up questions cannot be explored in real time. The responses may also depend on who chose to respond, which means the findings should be treated as practical guidance rather than exhaustive evidence. Despite this limitation, the method was appropriate for the scope, schedule, and academic level of the project.

## 3.3 Technical Feasibility

### 3.3.1 Methodology Comparison

Two agile approaches were considered for the project: DSDM and Scrum.

#### DSDM

Dynamic Systems Development Method (DSDM) is an agile methodology built around iterative development, active stakeholder involvement, prioritisation, and fixed timeboxes. It is particularly suitable for projects with a clear deadline and evolving detail, because it allows scope to be prioritised while time and quality are protected. The use of MoSCoW prioritisation also makes DSDM suitable for projects in which core features must be delivered first and lower-priority features can be deferred if needed.

#### Scrum

Scrum is another agile approach based on short development cycles called sprints. It emphasises backlog management, sprint planning, review meetings, and retrospective improvement. Scrum is effective when a team can work in clearly defined roles and meet regularly to review progress.

### 3.3.2 Comparison of DSDM and Scrum

| Criteria | DSDM | Scrum |
| --- | --- | --- |
| Delivery structure | Timeboxed phases with prioritised scope | Short sprints with backlog-driven delivery |
| Stakeholder involvement | Strong and continuous | Strong, especially in sprint review cycles |
| Documentation fit | Better suited to projects requiring formal documentation | Often lighter on formal documentation |
| Scope handling | Protects time and quality through MoSCoW prioritisation | Flexible backlog reprioritisation |
| Suitability for academic deadline | Strong, because fixed deadlines are central | Good, but depends more on disciplined sprint management |
| Suitability for this project | Very suitable for phased feature delivery and dissertation reporting | Useful, but less aligned with the documentation-heavy nature of the project |

### 3.3.3 Selected Methodology: DSDM

DSDM was selected as the most appropriate methodology for the University Academic Portal. The main reason is that the project had a fixed academic deadline and needed to be delivered in clearly structured stages. DSDM provided a practical way to organise the portal into timeboxes, prioritise essential functions, gather user feedback, and maintain control over scope.

This methodology also aligned well with the structure of the final system. The project was developed incrementally through three main timeboxes:

1. Timebox 1: user management, student registration, course and subject management, and enrolment workflow.
2. Timebox 2: grades, assignment workflow, fee management, payment handling, and related student and staff features.
3. Timebox 3: timetable management, attendance tracking, low-attendance alerts, announcements, messaging, notifications, and public support forms.

For a final-year dissertation, DSDM offered another important advantage: it supported both iterative development and clear documentation. This made it easier to connect planning, implementation, testing, and evaluation across later chapters.

### 3.3.4 Language and Framework Feasibility

The implemented portal uses PHP with Laravel for the backend, Vue 3 with Inertia.js for the frontend, Tailwind CSS for styling, and MySQL as the primary database. This stack is practical for a web-based academic portal because it supports rapid development, strong database integration, role-based workflows, and maintainable full-stack development.

To justify the selected backend approach, PHP with Laravel was compared with Java-based enterprise development.

| Criteria | PHP with Laravel | Java-based web stack |
| --- | --- | --- |
| Development speed | Fast for web application delivery | Usually slower due to heavier setup and verbosity |
| Learning and productivity | Well suited to rapid full-stack development | More demanding for a small academic project |
| Framework support | Strong MVC structure, routing, validation, ORM, queues, security helpers | Strong enterprise tooling, but often more complex |
| Project fit | Suitable for small to medium web systems with clear CRUD and workflow features | Suitable for larger enterprise systems but heavier for this scope |
| Overall suitability for this project | High | Moderate |

Laravel was the better choice for this project because it provides a strong MVC structure, database migrations, validation, authentication support, queue integration, notification support, and maintainable routing conventions. These features directly support the needs of the University Academic Portal.

The frontend choice is also justified by the project scope. Vue 3 provides reactive user interfaces and supports component-based design, while Inertia.js allows Laravel and Vue to work together without requiring a fully separate REST API frontend architecture. This reduced implementation complexity while still allowing a modern single-page style user experience. Tailwind CSS supported rapid and consistent interface development across the different user roles.

Overall, the selected language and framework stack was technically feasible because it matched the size of the project, the developer skill level, the academic deadline, and the web-based nature of the problem.

### 3.3.5 Database Feasibility

The main database options considered were MySQL and a NoSQL alternative such as MongoDB.

| Criteria | MySQL | NoSQL (e.g. MongoDB) |
| --- | --- | --- |
| Data model | Relational and structured | Flexible document-based model |
| Suitability for linked academic data | Strong | Less natural for highly relational workflows |
| Transaction support | Strong ACID support | Varies by platform and use case |
| Schema control | Structured and predictable | More flexible but less rigid |
| Suitability for this portal | High | Lower |

MySQL was selected because the University Academic Portal manages highly structured and strongly related data. Students, users, courses, subjects, enrolments, grades, fees, timetables, attendance, messages, and logs are all linked through clear relationships. A relational database is therefore a natural fit.

The system also benefits from transactional integrity. For example, enrolment status changes, payment updates, grade review actions, and timetable records should remain consistent and auditable. MySQL supports this requirement well and integrates directly with Laravel's Eloquent ORM and migration system. While NoSQL databases are useful for unstructured or rapidly changing data at very large scale, they would have added unnecessary complexity to this project.

### 3.3.6 Operational Feasibility

The project is operationally feasible because the final system supports real academic workflows and can be used through standard web browsers by students, teachers, and administrative staff. The portal is role-based, which means each user type sees the features relevant to their responsibilities. This improves practicality in real use.

The system is also feasible from a deployment perspective because it can run on standard PHP hosting or a conventional web server environment with MySQL. During development, the project used a typical local web application setup and common open-source development tools. This lowers the barrier to adoption and makes the system realistic for an academic setting.

In addition, the project includes supporting documentation, testing artefacts, and user manuals, which improves operational readiness even if the system is not yet deployed in a real university environment.

### 3.3.7 Economic Feasibility

The project is economically feasible at prototype and small deployment scale because the core technologies used are open source. PHP, Laravel, Vue, Tailwind CSS, MySQL, and many supporting development tools can be used without licence cost. This makes the system more realistic for an educational institution or student project environment than a solution that depends heavily on expensive proprietary software.

Costs would still exist in a real deployment, including hosting, domain management, backup services, maintenance, training, and operational support. However, the use of open-source technologies reduces the initial development barrier and supports long-term maintainability.

## 3.4 DSDM Feasibility and Application to the Project

### 3.4.1 Focus on the Business Need

The main business need of the project is to replace fragmented academic administration with a central web-based portal. DSDM was suitable because it encouraged prioritisation of the features that delivered the highest practical value first, such as registration, enrolment, grades, fees, timetable access, attendance tracking, and communication.

### 3.4.2 Deliver on Time

The project had a fixed academic schedule. DSDM's timeboxing approach supported this constraint by dividing the work into defined phases and feature groups. This reduced the risk of uncontrolled development and helped maintain focus on the most important deliverables within the available time.

### 3.4.3 Collaborate

The project required input from multiple user perspectives, especially students, teachers, and administrative staff. DSDM supports collaboration through continuous stakeholder involvement, which matched the requirement gathering and feedback-based refinement used in the project.

### 3.4.4 Never Compromise Quality

Quality was treated as a fixed expectation rather than something optional to be reduced when time became limited. This principle fits the project well because the portal handles sensitive academic data and role-based actions. Validation, testing, review workflows, and interface refinement were therefore essential parts of development rather than optional extras.

### 3.4.5 Build Incrementally from Firm Foundations

The portal was developed from stable foundations. Core identity, user, and academic data structures were established first, then extended through later modules such as fees, assignments, timetable management, attendance, and notifications. This incremental approach reduced risk and supported maintainability.

### 3.4.6 Develop Iteratively

The system was refined over multiple iterations. Features were not only added, but also improved after testing and review. This was especially visible in the evolution of validation, usability improvements, reporting views, status workflows, and role-specific pages.

### 3.4.7 Communicate Continuously and Clearly

Continuous communication was necessary to keep the scope, progress, and user expectations aligned. Even in a small academic project, regular communication through documentation, progress tracking, and feedback cycles is important for maintaining control and reducing misunderstanding.

### 3.4.8 Demonstrate Control

DSDM also supported project control through planning, timeboxing, prioritisation, and measurable deliverables. This principle was important because the project needed not only to be implemented, but also to be documented and evaluated in a structured dissertation format.

## 3.5 Legal, Ethical, Social, and Professional Issues

### 3.5.1 Legal Issues

#### Data Protection and Privacy

Because the portal processes personal data such as names, contact details, grades, attendance information, and fee status, data protection is a major legal concern. In a UK university context, the most relevant framework is UK GDPR together with the Data Protection Act 2018. This means personal data should be collected only where necessary, processed for legitimate academic purposes, protected appropriately, and restricted according to role.

For the project, this legal issue affects system design directly. Role-based access control, secure authentication, validation, careful handling of uploaded files, and controlled access to academic records are all necessary not only for technical quality, but also for legal compliance.

#### Cybersecurity and Misuse

The portal must also be protected against unauthorised access, tampering, and misuse. In the UK context, the Computer Misuse Act 1990 is relevant because it addresses unauthorised access to computer systems and data. This makes secure authentication, access restriction, logging, and safe handling of user actions particularly important for an academic portal.

#### Intellectual Property

Course materials, assignments, uploaded files, and digital content may be subject to copyright ownership and licensing restrictions. The portal therefore needs to respect intellectual property by controlling access appropriately and avoiding unauthorised copying or redistribution of academic content.

### 3.5.2 Ethical Issues

#### Confidentiality and Fairness

The portal stores information that can affect academic outcomes, including grades, attendance, submissions, and communication records. It is therefore ethically important that users can only access information appropriate to their role and that records are handled fairly and accurately.

#### Academic Integrity

The system must not make it easier to manipulate grades, hide academic information, or misuse assignment-related functions. Ethical system design therefore requires traceability, review workflows, and clear accountability in grade and academic record management.

#### Informed Participation

Where questionnaires, feedback collection, or usability testing involve real users, participation should be informed and respectful. Users should understand the purpose of the activity and how their information will be used.

### 3.5.3 Social Issues

#### Accessibility and Inclusion

The portal should support a wide range of users, including those with different technical skills and accessibility needs. This makes clear navigation, readable interface design, keyboard accessibility, and device compatibility socially important rather than merely cosmetic.

#### Digital Divide

Not all users have the same devices, connectivity, or technical confidence. A web portal intended for academic administration should therefore work reasonably well on common devices and avoid unnecessary complexity. This is particularly important for student access to grades, fees, timetables, and announcements.

#### Community Impact

A well-designed portal can improve communication between students, teachers, and administrative staff. However, if it is confusing or unreliable, it can also increase frustration and reduce trust. Social impact therefore depends not only on system availability, but also on clarity and usability.

### 3.5.4 Professional Issues

#### Competence and Good Practice

The project should be developed using professional software engineering practice, including version control, testing, documentation, validation, and secure coding. This is necessary not only for system quality, but also for academic credibility.

#### Integrity and Accountability

Developers handling academic systems have a professional responsibility to be honest about what the system does, what its limits are, and how user data is protected. This is especially important in a dissertation project, where claims about functionality and quality must be supported by implementation and testing evidence.

#### BCS Code of Conduct Relevance

The professional issues in the project align with the spirit of the BCS Code of Conduct, especially in relation to competence, integrity, public interest, and professional responsibility. In practical terms, this means designing the system to protect users, documenting decisions clearly, and avoiding misleading claims about the capabilities or deployment status of the portal.

## 3.6 Chapter Summary

This chapter has shown that the University Academic Portal is feasible from methodological, technical, operational, and economic perspectives. The Google Forms questionnaire provided a practical requirement gathering method for a final-year project. DSDM was justified as the most suitable methodology because it supported fixed academic deadlines, timeboxed delivery, stakeholder feedback, and structured documentation.

The selected implementation stack of Laravel, Vue, and MySQL was also shown to be appropriate for the size, complexity, and web-based nature of the problem. In addition, the chapter identified the main legal, ethical, social, and professional issues that must be considered when designing and implementing an academic management system.

These feasibility and justification decisions provide the foundation for Chapter 4, which defines the detailed requirements, prioritisation, and design of the University Academic Portal.
