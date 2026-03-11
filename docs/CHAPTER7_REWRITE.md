# Chapter 7: Evaluation, Discussion, and Conclusion

## 7.1 Introduction

This chapter evaluates the completed University Academic Portal against the project aim, objectives, proposal baseline, comparable university systems, and the main methodological and technical justifications made in earlier chapters. It also reflects on the effectiveness of the timebox plan, identifies the main strengths and limitations of the final system, and outlines realistic directions for future improvement.

The purpose of the chapter is not simply to restate what was implemented in Chapters 5 and 6. Instead, it provides a critical judgement of how well the project met its intended goals, where the system is strong, where it remains limited, and what this means for the overall success of the final-year project.

## 7.2 Evaluation Against Aim and Objectives

### 7.2.1 Evaluation Against the Project Aim

The aim of the project was to design, develop, and evaluate a web-based University Academic Portal using Laravel, Vue.js, and MySQL to manage student registration, course enrolment, grades, fee payment, timetable access, attendance tracking, and academic communication within a single integrated system.

This aim was achieved. The final portal supports the main academic workflows identified in Chapter 1 and implemented across the three timeboxes in Chapter 5. The system includes role-based access for students, teachers, and staff; online enrolment and withdrawal workflows; grade submission and review; fee management and Stripe-based payment handling; timetable and attendance modules; announcements; messaging; notifications; and guest-facing support channels. These features are visible in [web.php](d:/university-portal/academic-portal/routes/web.php), [README.md](d:/university-portal/academic-portal/README.md), and the main service-layer files such as [EnrollmentService.php](d:/university-portal/academic-portal/app/Services/EnrollmentService.php), [PaymentService.php](d:/university-portal/academic-portal/app/Services/PaymentService.php), and [SubjectGradeCalculator.php](d:/university-portal/academic-portal/app/Services/SubjectGradeCalculator.php).

The portal does not yet claim large-scale institutional deployment or full enterprise maturity. However, within the scope of a final-year computing project, it provides a credible and well-integrated academic management prototype that replaces fragmented manual and spreadsheet-based processes with structured digital workflows. For that reason, the project aim can be judged as successfully met.

### 7.2.2 Evaluation Against the Project Objectives

| Objective | Evaluation | Judgement |
| --- | --- | --- |
| 1. Analyse the current academic processes and identify requirements | The project analysed manual and partially digital processes in Chapter 1 and used questionnaire-based requirement gathering in Chapter 3. These findings were converted into structured functional and non-functional requirements in Chapter 4. | Achieved |
| 2. Design the portal using appropriate architecture, database structures, UML models, and interface prototypes | The system design was translated into use case, ERD, class, and sequence artefacts and aligned with the Laravel MVC structure. Key design artefacts include [WholeSystem_UseCaseDiagram.plantuml](d:/university-portal/academic-portal/usecase/WholeSystem_UseCaseDiagram.plantuml), [AcademicPortal_ERD.plantuml](d:/university-portal/academic-portal/class_diagram/erd/AcademicPortal_ERD.plantuml), [Timebox1_Class.plantuml](d:/university-portal/academic-portal/class_diagram/Timebox1_Class.plantuml), [Timebox2_Class.plantuml](d:/university-portal/academic-portal/class_diagram/Timebox2_Class.plantuml), and [Timebox3_Class.plantuml](d:/university-portal/academic-portal/class_diagram/Timebox3_Class.plantuml). | Achieved |
| 3. Implement the portal iteratively in planned timeboxes | The portal was implemented in three coherent timeboxes covering registration and enrolment, grades and fees, and timetable, attendance, and communication. The final codebase and routes show that these modules were integrated successfully. | Achieved |
| 4. Test and refine the system for correctness, reliability, and usability | The repository contains meaningful feature and unit tests, together with quality checks and CI support. Testing evidence includes [CourseEnrollmentTest.php](d:/university-portal/academic-portal/tests/Feature/CourseEnrollmentTest.php), [GradeReviewWorkflowTest.php](d:/university-portal/academic-portal/tests/Feature/GradeReviewWorkflowTest.php), [PaymentWebhookTest.php](d:/university-portal/academic-portal/tests/Feature/PaymentWebhookTest.php), [StaffAttendanceReportTest.php](d:/university-portal/academic-portal/tests/Feature/StaffAttendanceReportTest.php), [RoleAccessPolicyTest.php](d:/university-portal/academic-portal/tests/Feature/RoleAccessPolicyTest.php), [SubjectGradeCalculatorTest.php](d:/university-portal/academic-portal/tests/Unit/SubjectGradeCalculatorTest.php), and [SendLowAttendanceAlertsJobTest.php](d:/university-portal/academic-portal/tests/Unit/SendLowAttendanceAlertsJobTest.php). CI and quality commands are defined in [ci.yml](d:/university-portal/academic-portal/.github/workflows/ci.yml) and [README.md](d:/university-portal/academic-portal/README.md). Large-scale performance and penetration testing were not completed. | Achieved within project scope |
| 5. Evaluate the final system and prepare deployment, training, and documentation support | Chapter 6 established deployment readiness, migration planning, training strategy, and user-manual support. The portal also includes a downloadable manual at [University_Academic_Portal_User_Manual.pdf](d:/university-portal/academic-portal/public/docs/University_Academic_Portal_User_Manual.pdf). However, a live university rollout was not performed. | Substantially achieved |

### 7.2.3 Discussion of Objective Achievement

The objective evaluation shows that the project was strongest in analysis, design, implementation, and scoped testing. The portal clearly moved beyond a prototype of disconnected pages and became a functioning multi-role academic system with realistic business rules and state transitions.

The only area that should not be overstated is operational rollout. The project includes meaningful deployment planning, training preparation, and user documentation, but it does not provide evidence of long-term production use in a real university environment. This limitation does not invalidate the project. Instead, it defines the boundary between a strong academic prototype and a fully adopted institutional system.

## 7.3 Evaluation Against Comparable Academic Portals

Chapter 2 compared the University Academic Portal with the University of Greenwich and University of Bath service environments. That comparison showed that real university systems place strong emphasis on registration, timetable access, results, fee-related services, support, and help documentation. It also showed that larger institutions often use multiple connected services rather than one narrow application.

When the completed portal is evaluated against those reference systems, four conclusions can be drawn.

First, the project compares well in terms of core academic workflow integration. Within its intended scope, it combines registration-related functions, enrolment management, grade handling, fee workflows, timetable access, attendance tracking, and communication in one portal. This gives it a coherent internal structure and reduces the fragmentation that was identified as a problem in Chapter 1.

Second, the project performs strongly in workflow visibility and traceability. The implemented system includes explicit statuses, approval flows, audit-related logs, and notifications for key academic actions. This is visible in features such as enrolment status logs, grade review logs, fee status logs, and low-attendance alert states. These workflow controls provide a level of process transparency that is particularly valuable in academic administration.

Third, the project is weaker than full institutional systems in breadth and service ecosystem maturity. Large university platforms typically provide broader institutional integration, more advanced support ecosystems, dedicated mobile services, stronger authentication measures such as MFA, and long-established user support processes. The current project does not attempt to reproduce that full institutional scale.

Fourth, the project is more focused and easier to analyse than large university ecosystems because it keeps the main academic workflows within one codebase and one user experience. That is an advantage for a final-year project because it supports coherence, maintainability, and demonstrable traceability across requirements, design, implementation, and testing.

Overall, the comparative evaluation suggests that the University Academic Portal performs well as a focused and integrated academic management system within its intended scope, even though it does not yet match the institutional breadth, scale, or maturity of enterprise university platforms.

## 7.4 Evaluation Against Project Justifications

### 7.4.1 Methodology Evaluation

The project justification in Chapter 3 selected DSDM as the preferred methodology because the project had a fixed academic deadline, required structured timeboxing, and needed flexibility in lower-priority scope details. In practice, this was a suitable decision.

The strongest evidence for the suitability of DSDM is the way the project evolved through three clear timeboxes:

1. Timebox 1 established registration, student, course, subject, and enrolment foundations.
2. Timebox 2 delivered grades, fees, payments, and assignment support.
3. Timebox 3 delivered timetables, attendance, communication, and alerting.

This timeboxed structure made the workload more manageable and helped preserve control over the project. It also aligned well with MoSCoW prioritisation because core functions were delivered first, while lower-priority refinements were either added later or identified as future work.

However, the methodology was not without challenges. Feedback was not always available at the ideal time, new ideas emerged during implementation, and documentation sometimes lagged behind coding. These are common risks in iterative student projects. Even so, the timebox approach prevented uncontrolled drift and allowed the portal to be completed in a structured way.

Overall, DSDM can be judged as an appropriate and successful methodological choice for this project.

### 7.4.2 Evaluation of the Selected Languages and Frameworks

The selected technology stack was PHP 8.2 and Laravel 12 for the backend, Vue 3 with Inertia.js for the frontend, Tailwind CSS for styling, and JavaScript for client-side interaction. This selection was justified in Chapter 3 and proved suitable in practice.

Laravel provided a strong MVC structure, routing, validation, authentication support, queues, notifications, migrations, and Eloquent ORM. Vue 3 and Inertia.js made it possible to build interactive user interfaces without separating the system into an independent API backend and SPA frontend. Tailwind CSS supported fast and consistent page implementation across the different user roles.

The suitability of the stack is visible in the final structure of the project:

1. controller-based request handling in [app/Http/Controllers](d:/university-portal/academic-portal/app/Http/Controllers)
2. service-layer business logic in [app/Services](d:/university-portal/academic-portal/app/Services)
3. reusable middleware such as [SecurityHeaders.php](d:/university-portal/academic-portal/app/Http/Middleware/SecurityHeaders.php) and role middleware
4. Vue/Inertia pages under [resources/js/Pages](d:/university-portal/academic-portal/resources/js/Pages)
5. migration-driven database structure in [database/migrations](d:/university-portal/academic-portal/database/migrations)

The main technical difficulties were also clear. The Laravel and Inertia integration required a strong understanding of request flow and shared props, some Eloquent queries became complex, Stripe integration introduced webhook and idempotency concerns, and large role-based pages needed extra usability refinement. These difficulties were real, but they do not weaken the technology choice. Instead, they show that the selected stack was capable enough to support realistic workflows, even if it required increased development discipline.

Overall, the framework and language choices were justified and effective.

### 7.4.3 Evaluation of the Database Choice

The project justification selected MySQL as the main relational database because the portal manages structured and interrelated academic data. This was the correct choice. The system contains clearly related entities such as users, students, courses, subjects, enrolments, grades, fees, timetables, assignments, submissions, announcements, and messages. These relationships are well suited to a relational model.

The database design also improved over time. Later additions such as grade review logs, fee status logs, enrolment status logs, low-attendance alert states, Stripe webhook events, and system settings increased traceability and operational realism. This shows that the schema was not static, but evolved in response to real workflow needs.

The main difficulties were schema evolution, query complexity for reports and filtered views, and the need to maintain consistency while refining relationships and statuses. These problems were handled through migrations, indexes, constraints, and schema cleanup. In addition, the project uses SQLite in automated testing while keeping MySQL as the primary application database choice, which is a practical engineering decision rather than a contradiction.

Overall, the database choice was justified and technically appropriate.

## 7.5 Evaluation Against the Timebox Plan

### 7.5.1 Timebox-by-Timebox Evaluation

| Timebox | Planned Focus | Final Outcome | Evaluation |
| --- | --- | --- | --- |
| Timebox 1 | registration, student records, course and subject management, enrolment workflow | User management, student profiles, courses, subjects, teacher assignment, enrolment and withdrawal workflows, search, profile, and settings support were implemented. | Successful. The timebox created the foundation on which the later portal depended. |
| Timebox 2 | grades, fee payment, and related academic workflows | Grade submission and staff review, GPA support, fee handling, Stripe payment flow, receipts, and assignment support were implemented. | Successful but technically demanding. This timebox contained more complex workflow logic and external integration work. |
| Timebox 3 | timetable, attendance, and communication | Timetable management, attendance tracking and reporting, low-attendance alerts, announcements, messaging, notifications, and guest support forms were implemented. | Successful. The portal became a broader academic coordination system rather than only a registration tool. |

### 7.5.2 Discussion of the Timebox Plan

The timebox plan was effective because it sequenced the work in a logical order. Timebox 1 established the identity, data, and enrolment foundation. Timebox 2 added controlled academic and financial processes. Timebox 3 extended the system into timetable coordination, attendance monitoring, and communication. This order reduced implementation risk because each later timebox built on already stable records and role structures.

The evaluation also shows that complexity increased in later timeboxes. Timebox 2 introduced Stripe integration, audit-oriented workflows, and assignment-related calculations. Timebox 3 introduced low-attendance alert logic, reporting, communication features, and operational support concerns. This confirms that the later phases were not simply larger versions of Timebox 1, but more complex in logic and coordination.

Despite this, the timebox plan remained successful because the core scope was delivered without losing control of the project. Enhancements such as assignment support, payment status logging, and low-attendance alerts were added without undermining the original structure of the plan.

## 7.6 Strengths and Limitations of the Final System

### 7.6.1 Main Strengths

The completed University Academic Portal has several important strengths.

First, it integrates core academic processes in a single system. Registration-related administration, enrolment, grades, fees, timetable access, attendance, and communication are handled in one coherent portal instead of through fragmented manual methods.

Second, the system uses strong role separation. Students, teachers, staff, and guests each interact with features appropriate to their responsibilities. This improves usability and reduces the risk of inappropriate access.

Third, the system includes meaningful workflow traceability. Features such as grade review logs, fee status logs, enrolment status logs, Stripe webhook event storage, and low-attendance alert tracking increase accountability and make the portal more realistic than a simple CRUD-based academic system.

Fourth, the chosen architecture is maintainable. Laravel, Vue, Inertia, Tailwind, migrations, services, middleware, and testing all contribute to a structure that can be extended more safely than a tightly coupled prototype.

Fifth, the portal supports communication and support more effectively than a narrow records-only system. Announcements, messages, notifications, contact forms, feedback forms, and the user manual improve the practical usefulness of the platform.

### 7.6.2 Main Limitations

The system also has important limitations that should be acknowledged clearly.

First, large-scale performance and scalability were not fully evaluated. The portal was built and tested as a realistic academic project, but not under enterprise-scale concurrent load.

Second, accessibility and localisation remain limited. The system is responsive and generally usable, but it would benefit from deeper accessibility work such as enhanced keyboard support, ARIA refinement, broader assistive-technology review, and more deliberate localisation support.

Third, the portal depends on network connectivity and some external services. Online payments depend on Stripe, and notification or verification flows depend on mail and queue-related support. These dependencies are acceptable in a modern web system, but they create operational points of failure that need management in production.

Fourth, administrative analytics are still modest. The system provides filtered views, reports, and status information, but it does not yet provide advanced dashboards, predictive analytics, or richer cross-module decision support.

Fifth, real-world adoption has not yet been validated through live institutional use. This means long-term user behaviour, operational edge cases, and institutional support demands are not yet fully known.

Overall, the strengths outweigh the limitations within the intended scope of the project, but the limitations matter when discussing future readiness and enterprise maturity.

## 7.7 Personal and Professional Reflection

From a personal and professional perspective, the project was highly valuable because it required the integration of analysis, design, implementation, testing, deployment planning, and evaluation into one coherent system. It moved well beyond small programming exercises and required the disciplined completion of a realistic full-stack software project.

The project strengthened skills in Laravel, Vue 3, Inertia, Tailwind CSS, MySQL, validation design, workflow modelling, and structured testing. It also improved understanding of software engineering practices such as requirements analysis, UML modelling, role-based access design, iterative development, documentation maintenance, and scope control.

An important lesson from the project was the value of separating business logic from controllers. Services such as [EnrollmentService.php](d:/university-portal/academic-portal/app/Services/EnrollmentService.php), [PaymentService.php](d:/university-portal/academic-portal/app/Services/PaymentService.php), and [SubjectGradeCalculator.php](d:/university-portal/academic-portal/app/Services/SubjectGradeCalculator.php) show how maintainability improved when complex logic was kept out of page-level handling.

Another lesson was that documentation and testing should be integrated continuously rather than delayed until the end. Where this was done well, the project remained clearer and easier to defend. Where it lagged, later correction effort increased. This is a useful professional lesson for future software projects.

## 7.8 Future Improvements

The current system provides a strong foundation, but several improvements would enhance its long-term value.

### 7.8.1 Functional and Analytical Improvements

Future versions could add:

1. richer administrative dashboards and cross-module analytics
2. identification of academically or financially at-risk students through combined data views
3. deeper self-service functions, such as transcript requests or adviser booking
4. wider integration with learning platforms, plagiarism services, and other institutional systems

### 7.8.2 User Experience and Accessibility Improvements

Future interface improvements could include:

1. a stronger design system with more reusable UI patterns
2. deeper accessibility support
3. improved localisation and multilingual readiness
4. further refinement of large tables, filters, and data-heavy mobile views

### 7.8.3 Technical and Operational Improvements

Future technical work could include:

1. larger-scale performance and load testing
2. more advanced security evaluation
3. broader user acceptance testing with real institutional participants
4. more automated deployment and operational monitoring
5. extended technical documentation for production support

These improvements would not change the core structure of the portal, but they would increase its maturity, institutional readiness, and long-term maintainability.

## 7.9 Conclusion

The final evaluation shows that the University Academic Portal was a successful final-year computing project. The project achieved its core aim by delivering a web-based academic management system that integrates student registration support, enrolment workflows, grades, fees, timetable access, attendance tracking, and academic communication within one coherent portal.

The objectives relating to analysis, design, implementation, and scoped testing were achieved clearly. The objective relating to deployment, training, and documentation was substantially achieved through strong preparation, although not through a full live university rollout. This distinction is important because it keeps the evaluation academically honest and defensible.

Compared with larger university systems, the portal is narrower in institutional scope and maturity, but strong in internal coherence, workflow transparency, and maintainable design. It demonstrates that a well-structured student project can produce a realistic and technically credible academic portal, while still acknowledging the further work needed for enterprise-scale adoption.

Overall, the University Academic Portal provides a solid foundation for future extension and represents a meaningful application of software engineering principles to a real academic administration problem.
