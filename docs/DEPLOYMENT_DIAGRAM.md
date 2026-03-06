# Deployment Diagram – University Academic Portal

## Diagram

The deployment diagram is defined in PlantUML format:

- **File:** `deployment/AcademicPortal_Deployment.plantuml`

Generate the image (e.g. PNG) using PlantUML or your IDE’s PlantUML plugin. The diagram shows the physical deployment of the Academic Portal across client devices, network, web server, database server, and backup server.

---

## Explanation for Diagram

Users access the Academic Portal using **Firefox** or **Google Chrome** on a **mobile device** or **laptop**. Traffic goes through a **router** and **firewall** over **TCP/IP**. The site is hosted on **web servers** (in the diagram, a single Web Server node) that run the application and are connected to the **database server** to perform all functions (authentication, student/course management, grades, fees, assignments, timetable, attendance, announcements, messages, etc.).

The **Web Server** runs **Apache or Nginx**, **PHP 8.2**, **Laravel 12**, and the **Vue 3 / Inertia** frontend. The **Database Server** runs **MySQL** and holds the **academic_portal** database and its tables (users, students, courses, subjects, grades, fees, assignments, timetables, attendances, announcements, messages, notifications, and related tables).

If the database server fails, a **backup server** is available. A **VPN** is used to store and transfer backups from the primary database server to the backup server, so data can be recovered and data loss is reduced.

---

## Summary of Nodes

| Node | Description |
|------|-------------|
| **Mobile / Laptop (Client Device)** | Browsers (Chrome, Firefox), PDF Reader, Antivirus. Users access the portal via HTTPS. |
| **Router** | Forwards client traffic to the firewall. |
| **Firewall** | Filters and secures traffic between clients and the cloud. |
| **Cloud / Internet** | Network path between firewall and web server (e.g. hosting provider). |
| **Web Server** | Hosts Apache/Nginx, PHP, Laravel, and Vue/Inertia. Serves the Academic Portal application. |
| **Database Server** | Runs MySQL with the `academic_portal` database. |
| **Backup Server** | Runs MySQL with the same schema; receives backups over VPN from the primary database server. |

---

## Connections

- **Client devices → Router → Firewall → Cloud → Web Server:** TCP/IP (HTTPS in practice).
- **Web Server → Database Server:** TCP/IP (MySQL protocol).
- **Database Server → Backup Server:** VPN (backup/replication traffic to prevent data loss).
