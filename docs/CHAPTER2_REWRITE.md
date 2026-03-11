# Chapter 2: Literature Review and Comparative Analysis

## 2.1 Introduction

This chapter reviews the literature and existing systems relevant to the University Academic Portal project. The purpose of the chapter is twofold. First, it examines the role of academic portals and student information systems in higher education, identifying the main functional and non-functional themes that appear in the literature. Second, it compares two existing university systems, the University of Greenwich student portal environment and the University of Bath student service environment, in order to identify useful design ideas, common patterns, and gaps that inform the proposed system.

The chapter focuses on the main processes already defined in Chapter 1: student registration, course enrolment, grades and results, fee-related services, timetable access, attendance-related support, and communication. It also considers usability because an academic portal is only valuable if students, teachers, and administrative staff can use it effectively. The comparative analysis in this chapter is based on official university sources available on 11 March 2026, together with established usability guidance from Nielsen Norman Group.

## 2.2 Literature Review

### 2.2.1 Academic Portals and Student Information Systems

An academic portal can be understood as a central digital platform through which students, teachers, and administrative staff access academic services, institutional information, and self-service functions. In practice, academic portals are closely related to student information systems because both aim to support the storage, processing, and communication of academic records and services. Typical portal functions include registration, course information, results access, timetable access, fee-related services, announcements, and links to wider institutional systems.

The literature and current university practice show that the value of an academic portal lies in centralisation. When separate processes are brought together into one digital environment, users do not need to move between paper forms, spreadsheets, e-mail chains, and multiple administrative offices in order to complete basic academic tasks. This reduces duplication, improves data visibility, and supports faster decision-making.

### 2.2.2 Importance of Digital Transformation in Academic Administration

Higher education institutions increasingly require digital systems because academic administration has become more complex. Student numbers are larger, records are more detailed, and users expect online self-service rather than manual office-based procedures. A portal therefore supports operational efficiency by reducing repetitive administrative work, improving record accuracy, and making services available remotely.

For students, digital transformation improves convenience and transparency. A well-designed portal allows students to check academic status, review grades, view timetables, receive updates, and complete administrative steps without depending entirely on office opening hours. For teachers and staff, digital systems improve workflow control, reduce repeated data entry, and make reporting easier.

### 2.2.3 Common Functional Requirements Identified in the Literature

The literature and current institutional practice suggest that effective academic portals usually include several recurring functional requirements:

1. User account access and secure authentication.
2. Online registration and profile management.
3. Course or unit registration and enrolment management.
4. Results or grade access.
5. Timetable access.
6. Fee-related information and payment support.
7. Communication, announcements, and user support.

These functions align closely with the scope of the proposed University Academic Portal in Chapter 1. This is important because it shows that the project is grounded in realistic institutional needs rather than artificial or overly narrow system requirements.

### 2.2.4 Non-Functional Requirements in Academic Portals

In addition to functional features, non-functional requirements are critical in academic portals. A portal may contain useful features, but if it is difficult to use, insecure, unreliable, or poorly organised, it will still fail to support academic administration effectively. The literature and institutional practice suggest four major non-functional concerns:

1. Usability - users must be able to understand and complete tasks easily.
2. Security - academic records and personal data must be protected.
3. Accessibility and availability - services should be available remotely and across common devices.
4. Maintainability and scalability - the system should support future extension without major redesign.

These concerns are especially relevant for the proposed project because it is a multi-user system that handles sensitive academic data and role-based workflows.

### 2.2.5 Usability as a Key Evaluation Area

Usability is particularly important in portal design because the users are not all technically advanced and the tasks are often routine but important. Jakob Nielsen's 10 usability heuristics remain one of the most widely used frameworks for evaluating interface quality. As updated by Nielsen Norman Group in 2024, the heuristics include visibility of system status, match between the system and the real world, user control and freedom, consistency and standards, error prevention, recognition rather than recall, flexibility and efficiency of use, aesthetic and minimalist design, helping users recover from errors, and help and documentation.

These heuristics provide an appropriate framework for comparing existing university systems because they focus on practical interaction quality rather than only visual appearance. They are therefore used later in this chapter to compare the two reference systems and to identify design implications for the proposed portal.

## 2.3 Review of Existing Similar Academic Portals

### 2.3.1 University of Greenwich Student Portal Environment

The University of Greenwich provides an online student portal environment that acts as a central access point for important student services. Official guidance states that all students must register online each year, and that new students must complete document checks before student card issuance. The university also provides access to grades through the portal under the "My Learning" area, timetable access through "My Timetable" and related timetable links, and a Digital Student Centre for support and service queries. Official guidance also shows that the portal provides access to services such as Moodle, library account access, news, and student support resources.

From a system design perspective, the Greenwich environment is significant because it demonstrates the value of a portal as a single point of access rather than as one isolated module. It links academic tasks, support services, and communication functions together. This is especially relevant to the current project because the proposed University Academic Portal also aims to centralise academic workflows rather than treat them as separate applications.

### 2.3.2 University of Bath Student Service Environment

The University of Bath provides a related but slightly different model through services such as online student registration, MyBath, MyTimetable, and SAMIS. Official guidance shows that students register after receiving an invitation by e-mail, and that they must set up Multi-Factor Authentication (MFA) before completing registration. During registration they confirm details, agree to university rules, arrange fee payment, and upload a photo. Bath also provides timetable access through MyTimetable and mobile-friendly access through MyBath. Results are made available through SAMIS, while MyBath provides access to timetable, library functions, course-related systems, e-mail, and wellbeing links.

This environment is significant because it shows a service ecosystem in which multiple connected systems work together to support student administration. It also highlights the importance of mobile access, secure authentication, and role-specific student services. For the proposed project, Bath is a useful comparator because it demonstrates how a portal can provide efficient access to core services while also supporting broader student needs.

### 2.3.3 Initial Comparative Observation

Both universities use digital systems to reduce reliance on manual administration, but they do so through somewhat different service structures. Greenwich presents a strong portal-centred model with linked academic and support services, while Bath uses a connected set of named systems such as MyBath, MyTimetable, and SAMIS. Both models are useful for the proposed project because they show that successful academic systems are built around clear service access, security, self-service, and task-oriented navigation.

## 2.4 Functional Comparative Analysis

### 2.4.1 Comparison Criteria

The functional comparison focuses on features that are relevant to the project scope rather than trying to compare every service each university provides. The main criteria used are:

1. Registration and account setup
2. Secure sign-in
3. Grades and results access
4. Timetable access
5. Fee-related services
6. Academic resources and support
7. Communication and help

### 2.4.2 Functional Comparison

| Function | University of Greenwich | University of Bath | Design implication for proposed system |
| --- | --- | --- | --- |
| Registration and account setup | Online registration each year, with document checks for new students | Invitation-based online registration, MFA setup required, details confirmation, fee arrangement, photo upload | The proposed portal should support structured online registration with validation and document-related workflows where needed |
| Secure sign-in | Portal access through university credentials; linked access to academic services | Username/password plus MFA for university account access | Strong authentication and role-based access control are essential for academic systems |
| Grades and results access | Grades available through the portal under "My Learning" | Results and transcript-related services available through SAMIS | Students need a clear, central place to access results and academic status |
| Timetable access | Personal and faculty timetable access through portal links and My Timetable | Individual, course, and unit timetable access through MyTimetable and MyBath | Timetable services should be visible, easy to access, and regularly updated |
| Fee-related services | Online fee guidance, payment arrangements, and support through official portal-linked services | Fee payment arranged during registration; fees visible in connected student systems | Fee visibility and payment support should be integrated with student records |
| Academic resources and support | Portal links to Moodle, library account, and student services | MyBath links to course systems, library services, study spaces, and wellbeing contacts | A portal should act as a central service hub, even when some resources are connected systems |
| Communication and help | Digital Student Centre, portal announcements, and support access | Student support, IT support, in-app support routes, and help pages | Built-in communication and help resources improve usefulness and reduce confusion |

### 2.4.3 Functional Comparison Discussion

The comparison shows that both universities provide strong support for the core functions expected in a modern academic portal. Registration, account access, timetable viewing, results access, fee-related support, and service guidance are clearly important features in both environments. This confirms that the scope chosen for the University Academic Portal is realistic and well aligned with actual institutional practice.

At the same time, the comparison also reveals an important design lesson: existing university systems are often broader than a final-year project and may be split across several connected tools. For example, Bath uses MyBath, MyTimetable, and SAMIS, while Greenwich combines portal functions with supporting services such as the Digital Student Centre. This suggests that the proposed project should focus on integrating the core academic workflows within one coherent portal, without trying to reproduce every peripheral university service such as full library management.

## 2.5 Non-Functional Comparative Analysis Using Nielsen's 10 Heuristics

### 2.5.1 Rationale

Functional features alone are not enough to judge system quality. The way users interact with a portal is equally important, especially when the system is used frequently by different user groups. Nielsen's 10 heuristics are therefore used here as a structured basis for evaluating the non-functional qualities of the two reference systems.

### 2.5.2 Heuristic Comparison

| Heuristic | University of Greenwich | University of Bath | Design implication for proposed system |
| --- | --- | --- | --- |
| 1. Visibility of system status | Registration guidance and portal-linked services clearly indicate next steps and current task context | Registration guidance, MyTimetable, and service pages provide clear task states and service entry points | The proposed portal should show status, feedback, and workflow progress clearly |
| 2. Match between system and the real world | Uses familiar academic terms such as registration, grades, student card, and My Learning | Uses familiar student terms such as registration, MyTimetable, fees, and support | The system should use language familiar to students, teachers, and staff rather than technical jargon |
| 3. User control and freedom | Service-based navigation gives users clear paths to different tasks and information | Multiple access routes through app and web improve user movement across services | Users should be able to move easily between dashboard, records, and actions without getting stuck |
| 4. Consistency and standards | Institutional service structure and naming appear consistent across linked pages | Consistent naming of systems and services supports learnability | The proposed portal should maintain consistent layout, labels, colours, and interaction patterns |
| 5. Error prevention | Registration and support processes require specific steps, reducing invalid actions | MFA, guided registration, and service prerequisites reduce common errors | The system should prevent invalid submissions through validation, constraints, and clear confirmation messages |
| 6. Recognition rather than recall | Named areas such as My Learning and My Timetable reduce memory burden | Named services such as MyBath, MyTimetable, and SAMIS support recognition, although multiple systems may increase cognitive load | Important actions and navigation choices should remain visible and easy to recognise |
| 7. Flexibility and efficiency of use | Portal and linked service access support common student tasks | App plus web access provides flexibility and faster access to recurring services | The proposed portal should support efficient access for both novice and experienced users |
| 8. Aesthetic and minimalist design | Task-based service pages focus on relevant information | MyBath and service pages are designed around practical student tasks | Interfaces should be clean, task-focused, and free from unnecessary clutter |
| 9. Help users recognise, diagnose, and recover from errors | Support-oriented guidance helps users understand what to do when a problem occurs | Help pages and support routes are clearly available when issues arise | Error states should explain the problem clearly and suggest a next step |
| 10. Help and documentation | Strong support through Digital Student Centre and portal-linked guidance | Strong support through Student Support, IT guidance, and service help pages | The proposed portal should include clear support content and user guidance |

### 2.5.3 Non-Functional Comparison Discussion

The heuristic review suggests that both institutions perform strongly in service clarity, institutional terminology, and help provision. Their systems show that usability in academic portals is not only about visual appearance, but also about task structure, feedback, recognisable service names, and good support documentation.

However, the comparison also shows a limitation in large institutional systems: because services may be distributed across multiple platforms, users sometimes need to understand where one system ends and another begins. This can increase cognitive load, even when each individual service is well designed. For the proposed University Academic Portal, this creates an important design principle: the system should keep core academic tasks within a coherent and clearly navigable interface wherever possible.

## 2.6 Research Gap and Implications for the Proposed System

The literature and system comparison show that digital academic platforms are now expected in higher education, and that core services such as registration, results, timetables, and support are standard features. However, three gaps remain relevant to the proposed project.

First, public information about many university systems focuses mainly on the student side, while internal staff and teacher workflows are less visible. This supports the need for the current project to model not only student access, but also staff-admin and teacher processes such as registration management, grade review, timetable administration, attendance management, and communication.

Second, existing university environments are often distributed across multiple connected services. While this is practical at institutional scale, it can reduce clarity for users who simply want to complete routine academic tasks. The proposed project therefore benefits from providing one integrated academic interface for the core workflows within scope.

Third, the comparison suggests that help, guidance, and usability are as important as core functionality. A system with strong features but weak usability would not address the practical problems identified in Chapter 1. For this reason, the proposed portal must combine functional completeness with clear navigation, validation, user feedback, and role-based design.

Overall, the literature and comparative analysis justify the development of the University Academic Portal as a focused, integrated academic management system. They also provide the design direction for the next chapter: the system should be secure, role-based, task-oriented, and centred on the real needs of students, teachers, and administrative staff.

## 2.7 Chapter Summary

This chapter has reviewed the concept and importance of academic portals, showing that they play a significant role in modernising academic administration and improving access to university services. It has also compared the University of Greenwich and University of Bath systems using both functional criteria and Nielsen's usability heuristics.

The findings show that both universities provide strong digital support for key student services, especially in registration, timetable access, results access, and help provision. At the same time, the comparison highlights the need for integration, clear navigation, secure access, and support for multiple user roles. These findings directly inform the proposed University Academic Portal and provide the foundation for Chapter 3 and Chapter 4, which justify the selected technologies, requirements, and system design.
