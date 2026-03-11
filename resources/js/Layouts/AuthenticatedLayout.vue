<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import PortalLogo from "@/Components/PortalLogo.vue";
import GlobalSearch from "@/Components/GlobalSearch.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import GoogleTranslate from "@/Components/GoogleTranslate.vue";
import PageLoadingIndicator from "@/Components/PageLoadingIndicator.vue";
import GlobalToastStack from "@/Components/GlobalToastStack.vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const showingMobileSidebar = ref(false);
const page = usePage();
const openNavGroups = ref({});

// Icon mapping for menu items
const getMenuIcon = (name) => {
    const iconMap = {
        Dashboard:
            "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6",
        "My Profile":
            "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z",
        Courses:
            "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
        "My Courses":
            "M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10",
        Grades: "M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z",
        Fees: "M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z",
        Timetable:
            "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z",
        Announcements:
            "M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z",
        Notifications:
            "M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9",
        Messages:
            "M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z",
        "Contact Messages":
            "M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z",
        "Feedback Messages":
            "M7 8h10M7 12h6m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v6a2 2 0 01-2 2h-3l-4 4z",
        "My Teaching Subjects":
            "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
        "Mark Attendance":
            "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4",
        Assignments:
            "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
        "Manage Courses":
            "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
        "Enrollment Requests":
            "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
        "Manage Subjects":
            "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
        "Student Records":
            "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z",
        "Manage Users":
            "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z",
        "Manage Fees":
            "M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z",
        "Grade Reviews":
            "M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z",
        "Manage Timetable":
            "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z",
        "Attendance Report":
            "M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
        "Failed Jobs":
            "M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z",
        Student:
            "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z",
        Teaching:
            "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
        Academics:
            "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
        People: "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z",
        Finance:
            "M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z",
        Communication:
            "M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z",
        Settings:
            "M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z",
    };
    return iconMap[name] || "M4 6h16M4 12h16M4 18h16"; // Default icon (menu lines)
};

const navigation = computed(() => {
    const user = page.props.auth?.user;
    const isAdmin = user?.role === "admin";
    const isStaff = user?.role === "staff";
    const isTeacher = user?.role === "teacher";

    const unreadMessages = page.props.unread?.messages ?? 0;
    const unreadNotifications = page.props.unread?.notifications ?? 0;
    const unreadAnnouncements = page.props.unread?.announcements ?? 0;

    // Helper function to create a navigation group
    const createGroup = (name, children) => {
        const groupActive = children.some((child) => child.active);
        return {
            name,
            children,
            active: groupActive,
            icon: getMenuIcon(name),
            isGroup: true,
        };
    };

    const items = [
        {
            name: "Dashboard",
            href: route("dashboard"),
            active: route().current("dashboard"),
            icon: getMenuIcon("Dashboard"),
        },
    ];

    // Student features (show for students only)
    if (user?.role === "student") {
        items.push(
            createGroup("Student", [
                {
                    name: "My Profile",
                    href: route("student.profile.show"),
                    active: route().current("student.profile.*"),
                    icon: getMenuIcon("My Profile"),
                },
            ]),
            createGroup("Academics", [
                {
                    name: "Courses",
                    href: route("courses.index"),
                    active:
                        route().current("courses.*") &&
                        !route().current("admin.*"),
                    icon: getMenuIcon("Courses"),
                },
                {
                    name: "My Courses",
                    href: route("my-courses.index"),
                    active: route().current("my-courses.*"),
                    icon: getMenuIcon("My Courses"),
                },
                {
                    name: "Grades",
                    href: route("student.grades.index"),
                    active: route().current("student.grades.*"),
                    icon: getMenuIcon("Grades"),
                },
                {
                    name: "Attendance",
                    href: route("student.attendance.index"),
                    active: route().current("student.attendance.*"),
                    icon: getMenuIcon("Mark Attendance"),
                },
                {
                    name: "Assignments",
                    href: route("student.assignments.index"),
                    active: route().current("student.assignments.*"),
                    icon: getMenuIcon("Assignments"),
                },
                {
                    name: "Timetable",
                    href: route("student.timetable.index"),
                    active: route().current("student.timetable.*"),
                    icon: getMenuIcon("Timetable"),
                },
            ]),
            createGroup("Finance", [
                {
                    name: "Fees",
                    href: route("student.fees.index"),
                    active: route().current("student.fees.*"),
                    icon: getMenuIcon("Fees"),
                },
            ]),
            createGroup("Communication", [
                {
                    name: "Announcements",
                    href: route("announcements.index"),
                    active: route().current("announcements.*"),
                    badge: unreadAnnouncements,
                    icon: getMenuIcon("Announcements"),
                },
                {
                    name: "Notifications",
                    href: route("notifications.index"),
                    active: route().current("notifications.*"),
                    badge: unreadNotifications,
                    icon: getMenuIcon("Notifications"),
                },
                {
                    name: "Messages",
                    href: route("messages.index"),
                    active: route().current("messages.*"),
                    badge: unreadMessages,
                    icon: getMenuIcon("Messages"),
                },
            ])
        );
    }

    // Teacher features
    if (isTeacher) {
        items.push(
            createGroup("Teaching", [
                {
                    name: "My Teaching Subjects",
                    href: route("teacher.courses.index"),
                    active: route().current("teacher.courses.*"),
                    icon: getMenuIcon("My Teaching Subjects"),
                },
                {
                    name: "Timetable",
                    href: route("teacher.timetable.index"),
                    active: route().current("teacher.timetable.*"),
                    icon: getMenuIcon("Timetable"),
                },
                {
                    name: "Mark Attendance",
                    href: route("teacher.attendance.index"),
                    active: route().current("teacher.attendance.*"),
                    icon: getMenuIcon("Mark Attendance"),
                },
                {
                    name: "Grades",
                    href: route("teacher.grades.index"),
                    active: route().current("teacher.grades.*"),
                    icon: getMenuIcon("Grades"),
                },
                {
                    name: "Assignments",
                    href: route("teacher.assignments.index"),
                    active: route().current("teacher.assignments.*"),
                    icon: getMenuIcon("Assignments"),
                },
                {
                    name: "My Announcements",
                    href: route("teacher.announcements.index"),
                    active: route().current("teacher.announcements.*"),
                    icon: getMenuIcon("Announcements"),
                },
            ]),
            createGroup("Communication", [
                {
                    name: "Announcements",
                    href: route("announcements.index"),
                    active: route().current("announcements.*"),
                    badge: unreadAnnouncements,
                    icon: getMenuIcon("Announcements"),
                },
                {
                    name: "Notifications",
                    href: route("notifications.index"),
                    active: route().current("notifications.*"),
                    badge: unreadNotifications,
                    icon: getMenuIcon("Notifications"),
                },
                {
                    name: "Messages",
                    href: route("messages.index"),
                    active: route().current("messages.*"),
                    badge: unreadMessages,
                    icon: getMenuIcon("Messages"),
                },
            ])
        );
    }

    if (isAdmin) {
        items.push(
            createGroup("Communication", [
                {
                    name: "Contact Messages",
                    href: route("admin.contact-messages.index"),
                    active: route().current("admin.contact-messages.*"),
                    icon: getMenuIcon("Contact Messages"),
                },
                {
                    name: "Feedback Messages",
                    href: route("admin.feedback-messages.index"),
                    active: route().current("admin.feedback-messages.*"),
                    icon: getMenuIcon("Feedback Messages"),
                },
                {
                    name: "Notifications",
                    href: route("notifications.index"),
                    active: route().current("notifications.*"),
                    badge: unreadNotifications,
                    icon: getMenuIcon("Notifications"),
                },
                {
                    name: "Messages",
                    href: route("messages.index"),
                    active: route().current("messages.*"),
                    badge: unreadMessages,
                    icon: getMenuIcon("Messages"),
                },
            ])
        );
    }

    // Staff admin features - organized into groups
    if (isStaff) {
        items.push(
            createGroup("Academics", [
                {
                    name: "Manage Courses",
                    href: route("admin.courses.index"),
                    active: route().current("admin.courses.*"),
                    icon: getMenuIcon("Manage Courses"),
                },
                {
                    name: "Manage Subjects",
                    href: route("admin.subjects.index"),
                    active: route().current("admin.subjects.*"),
                    icon: getMenuIcon("Manage Subjects"),
                },
                {
                    name: "Manage Timetable",
                    href: route("admin.timetables.index"),
                    active: route().current("admin.timetables.*"),
                    icon: getMenuIcon("Manage Timetable"),
                },
                {
                    name: "Enrollment Requests",
                    href: route("admin.enrollments.index"),
                    active: route().current("admin.enrollments.*"),
                    icon: getMenuIcon("Enrollment Requests"),
                },
                {
                    name: "Grade Reviews",
                    href: route("admin.grades.index"),
                    active: route().current("admin.grades.*"),
                    icon: getMenuIcon("Grade Reviews"),
                },
                {
                    name: "Attendance Report",
                    href: route("admin.attendance.report"),
                    active: route().current("admin.attendance.*"),
                    icon: getMenuIcon("Attendance Report"),
                },
                {
                    name: "Failed Jobs",
                    href: route("admin.failed-jobs.index"),
                    active: route().current("admin.failed-jobs.*"),
                    icon: getMenuIcon("Failed Jobs"),
                },
            ]),
            createGroup("People", [
                {
                    name: "Student Records",
                    href: route("students.index"),
                    active: route().current("students.*"),
                    icon: getMenuIcon("Student Records"),
                },
                {
                    name: "Manage Users",
                    href: route("admin.users.index"),
                    active: route().current("admin.users.*"),
                    icon: getMenuIcon("Manage Users"),
                },
            ]),
            createGroup("Finance", [
                {
                    name: "Manage Fees",
                    href: route("admin.fees.index"),
                    active: route().current("admin.fees.*"),
                    icon: getMenuIcon("Manage Fees"),
                },
            ]),
            createGroup("Communication", [
                {
                    name: "Announcements",
                    href: route("admin.announcements.index"),
                    active: route().current("admin.announcements.*"),
                    badge: unreadAnnouncements,
                    icon: getMenuIcon("Announcements"),
                },
                {
                    name: "Contact Messages",
                    href: route("admin.contact-messages.index"),
                    active: route().current("admin.contact-messages.*"),
                    icon: getMenuIcon("Contact Messages"),
                },
                {
                    name: "Feedback Messages",
                    href: route("admin.feedback-messages.index"),
                    active: route().current("admin.feedback-messages.*"),
                    icon: getMenuIcon("Feedback Messages"),
                },
                {
                    name: "Notifications",
                    href: route("notifications.index"),
                    active: route().current("notifications.*"),
                    badge: unreadNotifications,
                    icon: getMenuIcon("Notifications"),
                },
                {
                    name: "Messages",
                    href: route("messages.index"),
                    active: route().current("messages.*"),
                    badge: unreadMessages,
                    icon: getMenuIcon("Messages"),
                },
            ])
        );
    }

    // Settings at bottom (all roles)
    items.push({
        name: "Settings",
        href: route("settings.index"),
        active: route().current("settings.*"),
        icon: getMenuIcon("Settings"),
    });

    return items;
});

const headerStatus = computed(() => {
    const role = page.props.auth?.user?.role;

    if (role === "student") {
        return "Student academic year in progress";
    }

    if (role === "teacher") {
        return "Teaching term in progress";
    }

    if (role === "staff" || role === "admin") {
        return "Administration term in progress";
    }
    return "Academic year in progress";
});

const userRoleMeta = computed(() => {
    const role = page.props.auth?.user?.role;

    if (role === "student") {
        return {
            label: "Student",
            statusClasses:
                "bg-emerald-50 text-emerald-800 border border-emerald-200",
            dotClass: "bg-emerald-500",
            chipClasses:
                "bg-emerald-50 text-emerald-800 border border-emerald-200",
        };
    }

    if (role === "teacher") {
        return {
            label: "Teacher",
            statusClasses: "bg-blue-50 text-blue-800 border border-blue-200",
            dotClass: "bg-blue-500",
            chipClasses: "bg-blue-50 text-blue-800 border border-blue-200",
        };
    }

    if (role === "staff" || role === "admin") {
        return {
            label: "Staff",
            statusClasses: "bg-amber-50 text-amber-800 border border-amber-200",
            dotClass: "bg-amber-500",
            chipClasses: "bg-amber-50 text-amber-800 border border-amber-200",
        };
    }

    return {
        label: null,
        statusClasses:
            "bg-slate-100 text-slate-600 border border-slate-200",
        dotClass: "bg-emerald-500",
        chipClasses:
            "bg-slate-100 text-slate-600 border border-slate-200",
    };
});

const unreadNotificationCount = computed(() =>
    Number(page.props.unread?.notifications ?? 0)
);

const notificationsPreview = computed(
    () => page.props.notificationsPreview?.items ?? []
);

const isMarkingNotificationsRead = ref(false);
const isRefreshingUnreadSharedProps = ref(false);
const UNREAD_REFRESH_INTERVAL_MS = 20000;

let stopRouterSuccessListener = null;
let unreadRefreshTimer = null;

const refreshUnreadSharedProps = () => {
    if (isRefreshingUnreadSharedProps.value) {
        return;
    }

    isRefreshingUnreadSharedProps.value = true;

    router.reload({
        only: ["unread", "notificationsPreview"],
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            isRefreshingUnreadSharedProps.value = false;
        },
    });
};

const startUnreadRefreshTimer = () => {
    if (unreadRefreshTimer !== null || typeof window === "undefined") {
        return;
    }

    unreadRefreshTimer = window.setInterval(() => {
        if (typeof document !== "undefined" && document.hidden) {
            return;
        }

        refreshUnreadSharedProps();
    }, UNREAD_REFRESH_INTERVAL_MS);
};

const stopUnreadRefreshTimer = () => {
    if (unreadRefreshTimer === null || typeof window === "undefined") {
        return;
    }

    window.clearInterval(unreadRefreshTimer);
    unreadRefreshTimer = null;
};

const handleVisibilityChange = () => {
    if (typeof document !== "undefined" && document.hidden) {
        return;
    }

    refreshUnreadSharedProps();
};

const markAllNotificationsRead = () => {
    if (isMarkingNotificationsRead.value) {
        return;
    }

    if (unreadNotificationCount.value <= 0) {
        return;
    }

    isMarkingNotificationsRead.value = true;

    router.post(
        route("notifications.read-all"),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            onFinish: () => {
                isMarkingNotificationsRead.value = false;
            },
        }
    );
};

const openNotificationFromPreview = (notification) => {
    const targetUrl = notification?.url || route("notifications.index");
    const notificationId = notification?.id;

    if (!notificationId || notification?.read_at) {
        router.visit(targetUrl);
        return;
    }

    router.post(
        route("notifications.read", notificationId),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            onSuccess: () => {
                router.visit(targetUrl);
            },
            onError: () => {
                router.visit(targetUrl);
            },
        }
    );
};

const openNotificationCenter = () => {
    router.visit(route("notifications.index"));
};

onMounted(() => {
    stopRouterSuccessListener = router.on("success", (event) => {
        const method = String(event?.detail?.visit?.method ?? "get")
            .trim()
            .toLowerCase();

        const visitOnly = Array.isArray(event?.detail?.visit?.only)
            ? event.detail.visit.only
            : [];
        const updatesUnreadViaPartialReload = visitOnly.some(
            (key) =>
                key === "unread" ||
                key.startsWith("unread.") ||
                key === "notificationsPreview" ||
                key.startsWith("notificationsPreview.")
        );

        if (
            method === "get" &&
            (visitOnly.length === 0 || updatesUnreadViaPartialReload)
        ) {
            return;
        }

        refreshUnreadSharedProps();
    });

    startUnreadRefreshTimer();

    if (typeof document !== "undefined") {
        document.addEventListener("visibilitychange", handleVisibilityChange);
    }
});

onUnmounted(() => {
    if (typeof stopRouterSuccessListener === "function") {
        stopRouterSuccessListener();
    }

    stopUnreadRefreshTimer();

    if (typeof document !== "undefined") {
        document.removeEventListener("visibilitychange", handleVisibilityChange);
    }
});

const truncateNotificationText = (value, max = 96) => {
    const text = String(value ?? "").trim();
    if (!text) return "No details available.";
    return text.length > max ? `${text.slice(0, max)}...` : text;
};
</script>

<template>
    <div class="min-h-screen bg-portal-background">
        <PageLoadingIndicator />
        <GlobalToastStack />

        <div class="flex min-h-screen">
            <!-- Desktop sidebar -->
            <aside
                class="portal-gradient fixed left-0 top-0 z-20 hidden h-screen w-64 flex-col border-r border-slate-800/40 text-slate-100 shadow-xl sm:flex"
            >
                <div class="flex items-center gap-3 px-6 py-5">
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center gap-3 transition-opacity hover:opacity-90"
                    >
                        <PortalLogo class="h-9 w-9 shrink-0" />
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-300"
                            >
                                University Academic
                            </p>
                            <p class="text-sm font-semibold text-portal-gold">
                                Portal
                            </p>
                        </div>
                    </Link>
                </div>

                <nav class="mt-4 flex-1 space-y-1 px-3 text-sm overflow-y-auto">
                    <template v-for="item in navigation" :key="item.name">
                        <!-- Regular menu item -->
                        <template v-if="!item.isGroup">
                            <Link
                                :href="item.href"
                                class="group flex items-center justify-between gap-2 rounded-lg px-3 py-2 font-medium transition"
                                :class="[
                                    item.active
                                        ? 'bg-white/10 text-portal-gold'
                                        : 'text-slate-200 hover:bg-white/5 hover:text-white',
                                ]"
                            >
                                <div class="flex items-center gap-3">
                                    <svg
                                        class="h-5 w-5 shrink-0"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            :d="item.icon"
                                        />
                                    </svg>
                                    <span>{{ item.name }}</span>
                                </div>
                                <span
                                    v-if="item.badge && item.badge > 0"
                                    class="inline-flex items-center justify-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                >
                                    {{ item.badge }}
                                </span>
                            </Link>
                        </template>

                        <!-- Grouped menu items (dropdown) -->
                        <template v-else>
                            <div class="space-y-1">
                                <button
                                    type="button"
                                    class="group flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 font-medium transition"
                                    :class="[
                                        item.active
                                            ? 'bg-white/10 text-portal-gold'
                                            : 'text-slate-200 hover:bg-white/5 hover:text-white',
                                    ]"
                                    @click="
                                        openNavGroups[item.name] = !(
                                            openNavGroups[item.name] ??
                                            item.active
                                        )
                                    "
                                >
                                    <div class="flex items-center gap-3">
                                        <svg
                                            class="h-5 w-5 shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                :d="item.icon"
                                            />
                                        </svg>
                                        <span>{{ item.name }}</span>
                                    </div>
                                    <svg
                                        class="h-4 w-4 transition-transform duration-200"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        :class="[
                                            openNavGroups[item.name] ??
                                            item.active
                                                ? 'rotate-180'
                                                : '',
                                        ]"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>

                                <!-- Dropdown menu items -->
                                <transition
                                    enter-active-class="transition duration-200 ease-out"
                                    enter-from-class="opacity-0 transform -translate-y-1"
                                    enter-to-class="opacity-100 transform translate-y-0"
                                    leave-active-class="transition duration-150 ease-in"
                                    leave-from-class="opacity-100 transform translate-y-0"
                                    leave-to-class="opacity-0 transform -translate-y-1"
                                >
                                    <div
                                        v-show="
                                            openNavGroups[item.name] ??
                                            item.active
                                        "
                                        class="ml-4 space-y-1 border-l-2 border-slate-700/50 pl-2"
                                    >
                                        <Link
                                            v-for="child in item.children"
                                            :key="child.name"
                                            :href="child.href"
                                            class="group flex items-center justify-between gap-2 rounded-lg px-3 py-2 font-medium transition"
                                            :class="[
                                                child.active
                                                    ? 'bg-white/10 text-portal-gold'
                                                    : 'text-slate-300 hover:bg-white/5 hover:text-white',
                                            ]"
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <svg
                                                    class="h-4 w-4 shrink-0"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        :d="child.icon"
                                                    />
                                                </svg>
                                                <span class="text-xs">{{
                                                    child.name
                                                }}</span>
                                            </div>
                                            <span
                                                v-if="
                                                    child.badge &&
                                                    child.badge > 0
                                                "
                                                class="inline-flex items-center justify-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                            >
                                                {{ child.badge }}
                                            </span>
                                        </Link>
                                    </div>
                                </transition>
                            </div>
                        </template>
                    </template>
                </nav>

                <div
                    class="border-t border-slate-800/60 px-4 py-4 text-xs text-slate-300"
                >
                    <p class="font-medium">Logged in as</p>
                    <p class="truncate text-slate-100">
                        {{ $page.props.auth.user.name }}
                    </p>
                </div>
            </aside>

            <!-- Mobile sidebar -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showingMobileSidebar"
                    class="fixed inset-0 z-40 flex sm:hidden"
                >
                    <div
                        class="fixed inset-0 bg-slate-900/70"
                        @click="showingMobileSidebar = false"
                    />

                    <aside
                        class="portal-gradient relative flex w-64 flex-col border-r border-slate-800/40 text-slate-100 shadow-xl"
                    >
                        <div
                            class="flex items-center justify-between px-4 py-4"
                        >
                            <div class="flex items-center gap-2">
                                <PortalLogo class="h-8 w-8 shrink-0" />
                                <span class="text-sm font-semibold">
                                    University Academic Portal
                                </span>
                            </div>
                            <button
                                type="button"
                                class="rounded-md p-1 text-slate-200 hover:bg-white/10"
                                @click="showingMobileSidebar = false"
                            >
                                <span class="sr-only">Close navigation</span>
                                <svg
                                    class="h-5 w-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <nav
                            class="mt-2 flex-1 space-y-1 px-3 text-sm overflow-y-auto"
                        >
                            <template
                                v-for="item in navigation"
                                :key="item.name"
                            >
                                <!-- Regular menu item -->
                                <template v-if="!item.isGroup">
                                    <Link
                                        :href="item.href"
                                        class="group flex items-center gap-2 rounded-lg px-3 py-2 font-medium transition"
                                        :class="[
                                            item.active
                                                ? 'bg-white/10 text-portal-gold'
                                                : 'text-slate-200 hover:bg-white/5 hover:text-white',
                                        ]"
                                    >
                                        <svg
                                            class="h-5 w-5 shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                :d="item.icon"
                                            />
                                        </svg>
                                        <span>{{ item.name }}</span>
                                        <span
                                            v-if="item.badge && item.badge > 0"
                                            class="ml-auto inline-flex items-center justify-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                        >
                                            {{ item.badge }}
                                        </span>
                                    </Link>
                                </template>

                                <!-- Grouped menu items (dropdown) -->
                                <template v-else>
                                    <div class="space-y-1">
                                        <button
                                            type="button"
                                            class="group flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 font-medium transition"
                                            :class="[
                                                item.active
                                                    ? 'bg-white/10 text-portal-gold'
                                                    : 'text-slate-200 hover:bg-white/5 hover:text-white',
                                            ]"
                                            @click="
                                                openNavGroups[item.name] = !(
                                                    openNavGroups[item.name] ??
                                                    item.active
                                                )
                                            "
                                        >
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <svg
                                                    class="h-5 w-5 shrink-0"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        :d="item.icon"
                                                    />
                                                </svg>
                                                <span>{{ item.name }}</span>
                                            </div>
                                            <svg
                                                class="h-4 w-4 transition-transform duration-200"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                                :class="[
                                                    openNavGroups[item.name] ??
                                                    item.active
                                                        ? 'rotate-180'
                                                        : '',
                                                ]"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>

                                        <!-- Dropdown menu items -->
                                        <transition
                                            enter-active-class="transition duration-200 ease-out"
                                            enter-from-class="opacity-0 transform -translate-y-1"
                                            enter-to-class="opacity-100 transform translate-y-0"
                                            leave-active-class="transition duration-150 ease-in"
                                            leave-from-class="opacity-100 transform translate-y-0"
                                            leave-to-class="opacity-0 transform -translate-y-1"
                                        >
                                            <div
                                                v-show="
                                                    openNavGroups[item.name] ??
                                                    item.active
                                                "
                                                class="ml-4 space-y-1 border-l-2 border-slate-700/50 pl-2"
                                            >
                                                <Link
                                                    v-for="child in item.children"
                                                    :key="child.name"
                                                    :href="child.href"
                                                    class="group flex items-center justify-between gap-2 rounded-lg px-3 py-2 font-medium transition"
                                                    :class="[
                                                        child.active
                                                            ? 'bg-white/10 text-portal-gold'
                                                            : 'text-slate-300 hover:bg-white/5 hover:text-white',
                                                    ]"
                                                >
                                                    <div
                                                        class="flex items-center gap-2"
                                                    >
                                                        <svg
                                                            class="h-4 w-4 shrink-0"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                :d="child.icon"
                                                            />
                                                        </svg>
                                                        <span class="text-xs">{{
                                                            child.name
                                                        }}</span>
                                                    </div>
                                                    <span
                                                        v-if="
                                                            child.badge &&
                                                            child.badge > 0
                                                        "
                                                        class="inline-flex items-center justify-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800"
                                                    >
                                                        {{ child.badge }}
                                                    </span>
                                                </Link>
                                            </div>
                                        </transition>
                                    </div>
                                </template>
                            </template>
                        </nav>
                    </aside>
                </div>
            </transition>

            <!-- Main content area -->
            <div class="flex min-h-screen flex-1 flex-col sm:ml-64">
                <!-- Top bar -->
                <header
                    class="sticky top-0 z-30 flex flex-wrap items-center gap-2 border-b border-slate-200 bg-white/80 px-4 py-2 shadow-sm backdrop-blur-sm md:flex-nowrap md:px-6 lg:px-8"
                >
                    <div class="order-1 flex min-w-0 items-center gap-2 md:gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md p-2 text-slate-600 hover:bg-slate-100 sm:hidden"
                            @click="showingMobileSidebar = true"
                        >
                            <span class="sr-only">Open navigation</span>
                            <svg
                                class="h-6 w-6"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                        </button>

                        <div class="min-w-0">
                            <p
                                class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 md:text-xs"
                            >
                                <span class="md:hidden">UAP</span>
                                <span class="hidden md:inline"
                                    >University Academic Portal</span
                                >
                            </p>
                        </div>
                    </div>

                    <div class="order-2 ml-auto flex items-center gap-2 md:order-3 md:ml-2 md:gap-4">
                        <!-- Google Translate -->
                        <GoogleTranslate />

                        <!-- Notification dropdown -->
                        <Dropdown
                            align="right"
                            width="80"
                            content-classes="overflow-hidden rounded-xl bg-white py-0"
                        >
                            <template #trigger>
                                <button
                                    type="button"
                                    class="relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm hover:bg-slate-50"
                                >
                                    <span class="sr-only">
                                        Open notifications
                                    </span>
                                    <svg
                                        class="h-5 w-5"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1"
                                        />
                                    </svg>
                                    <span
                                        v-if="unreadNotificationCount > 0"
                                        class="absolute -right-0.5 -top-0.5 inline-flex min-w-[1.1rem] items-center justify-center rounded-full bg-portal-gold px-1 text-[10px] font-semibold text-slate-900 ring-2 ring-white"
                                    >
                                        {{
                                            unreadNotificationCount > 9
                                                ? "9+"
                                                : unreadNotificationCount
                                        }}
                                    </span>
                                </button>
                            </template>

                            <template #content>
                                <div class="max-h-[30rem]">
                                    <div
                                        class="flex items-center justify-between border-b border-slate-200 px-4 py-3"
                                    >
                                        <p
                                            class="text-sm font-semibold text-slate-900"
                                        >
                                            Notifications
                                        </p>
                                        <button
                                            type="button"
                                            v-if="unreadNotificationCount > 0"
                                            @click="markAllNotificationsRead"
                                            :disabled="isMarkingNotificationsRead"
                                            class="text-xs font-semibold text-portal-navy hover:text-portal-navy-dark"
                                        >
                                            {{
                                                isMarkingNotificationsRead
                                                    ? "Marking..."
                                                    : "Mark all read"
                                            }}
                                        </button>
                                    </div>

                                    <div
                                        v-if="notificationsPreview.length > 0"
                                        class="max-h-80 overflow-y-auto"
                                    >
                                        <button
                                            type="button"
                                            v-for="notification in notificationsPreview"
                                            :key="notification.id"
                                            @click="
                                                openNotificationFromPreview(
                                                    notification
                                                )
                                            "
                                            class="block w-full border-b border-slate-100 px-4 py-3 text-left transition hover:bg-slate-50"
                                            :class="
                                                notification.read_at
                                                    ? 'bg-white'
                                                    : 'bg-emerald-50/40'
                                            "
                                        >
                                            <div
                                                class="flex items-start justify-between gap-3"
                                            >
                                                <p
                                                    class="text-sm font-semibold text-slate-900"
                                                >
                                                    {{ notification.title }}
                                                </p>
                                                <span
                                                    v-if="!notification.read_at"
                                                    class="mt-1 h-2 w-2 flex-shrink-0 rounded-full bg-emerald-500"
                                                />
                                            </div>
                                            <p
                                                class="mt-1 text-xs text-slate-600"
                                            >
                                                {{
                                                    truncateNotificationText(
                                                        notification.message
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="mt-1 text-[11px] text-slate-500"
                                            >
                                                {{
                                                    notification.created_label ||
                                                    notification.created_at
                                                }}
                                            </p>
                                        </button>
                                    </div>

                                    <div
                                        v-else
                                        class="px-4 py-8 text-center text-sm text-slate-500"
                                    >
                                        No unread notifications.
                                    </div>

                                    <div
                                        class="border-t border-slate-200 px-4 py-2"
                                    >
                                        <button
                                            type="button"
                                            @click="openNotificationCenter"
                                            class="text-xs font-semibold text-portal-navy hover:text-portal-navy-dark"
                                        >
                                            Open notification center
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </Dropdown>

                        <!-- User dropdown -->
                        <div class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-full bg-white p-1.5 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50"
                                    >
                                        <span
                                            class="relative inline-flex h-8 w-8 items-center justify-center rounded-full bg-portal-navy text-xs font-semibold text-white overflow-hidden"
                                        >
                                            <img
                                                v-if="
                                                    $page.props.auth.user.photo
                                                "
                                                :src="`/storage/${$page.props.auth.user.photo}`"
                                                :alt="`Photo for ${$page.props.auth.user.name}`"
                                                class="h-full w-full object-cover"
                                            />
                                            <span v-else>
                                                {{
                                                    $page.props.auth.user.name
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </span>
                                            <span
                                                class="absolute -bottom-0.5 -right-0.5 h-2 w-2 rounded-full border border-white"
                                                :class="userRoleMeta.dotClass"
                                            />
                                        </span>
                                        <svg
                                            class="hidden h-4 w-4 text-slate-400 md:block"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div
                                        class="border-b border-slate-100 px-4 py-3"
                                    >
                                        <p
                                            class="text-sm font-semibold text-slate-900"
                                        >
                                            {{ $page.props.auth.user.name }}
                                        </p>
                                        <p
                                            v-if="userRoleMeta.label"
                                            class="mt-0.5 text-xs text-slate-500"
                                        >
                                            {{ userRoleMeta.label }}
                                        </p>
                                    </div>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink :href="route('settings.index')">
                                        Settings
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <div class="order-3 w-full md:order-2 md:ml-auto md:w-auto">
                        <!-- Global search -->
                        <GlobalSearch />
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    <!-- Page header & context -->
                    <div
                        v-if="$slots.header || $slots.breadcrumb"
                        class="mb-4 space-y-2"
                    >
                        <div
                            v-if="$slots.header || headerStatus"
                            class="flex flex-wrap items-center justify-between gap-3"
                        >
                            <div
                                v-if="$slots.header"
                                class="text-xl font-semibold leading-tight text-slate-900"
                            >
                                <slot name="header" />
                            </div>
                            <div
                                v-if="headerStatus"
                                class="flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                :class="userRoleMeta.statusClasses"
                            >
                                <span
                                    class="h-2 w-2 rounded-full"
                                    :class="userRoleMeta.dotClass"
                                />
                                {{ headerStatus }}
                            </div>
                        </div>

                        <slot name="breadcrumb" />
                    </div>

                    <slot />
                </main>

                <!-- Footer -->
                <footer
                    class="border-t border-slate-200 bg-white px-4 py-4 sm:px-6 lg:px-8"
                >
                    <div
                        class="flex flex-wrap items-center justify-center gap-4 text-xs text-slate-600"
                    >
                        <Link
                            :href="route('privacy-policy')"
                            class="hover:text-portal-navy transition-colors"
                        >
                            Privacy Policy
                        </Link>
                        <span class="text-slate-400">•</span>
                        <span>
                            © {{ new Date().getFullYear() }} University Academic
                            Portal
                        </span>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>
