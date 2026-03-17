import { computed } from "vue";

const MENU_ICONS = {
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

const ROLE_META = {
    student: {
        label: "Student",
        statusClasses:
            "bg-emerald-50 text-emerald-800 border border-emerald-200",
        dotClass: "bg-emerald-500",
        chipClasses: "bg-emerald-50 text-emerald-800 border border-emerald-200",
        headerStatus: "Student academic year in progress",
    },
    teacher: {
        label: "Teacher",
        statusClasses: "bg-blue-50 text-blue-800 border border-blue-200",
        dotClass: "bg-blue-500",
        chipClasses: "bg-blue-50 text-blue-800 border border-blue-200",
        headerStatus: "Teaching term in progress",
    },
    staff: {
        label: "Staff",
        statusClasses: "bg-amber-50 text-amber-800 border border-amber-200",
        dotClass: "bg-amber-500",
        chipClasses: "bg-amber-50 text-amber-800 border border-amber-200",
        headerStatus: "Administration term in progress",
    },
    default: {
        label: null,
        statusClasses: "bg-slate-100 text-slate-600 border border-slate-200",
        dotClass: "bg-emerald-500",
        chipClasses: "bg-slate-100 text-slate-600 border border-slate-200",
        headerStatus: "Academic year in progress",
    },
};

const getMenuIcon = (name) => MENU_ICONS[name] || "M4 6h16M4 12h16M4 18h16";

const createGroup = (name, children) => ({
    name,
    children,
    active: children.some((child) => child.active),
    icon: getMenuIcon(name),
    isGroup: true,
});

export function useAuthenticatedLayoutNavigation(page) {
    const navigation = computed(() => {
        const user = page.props.auth?.user;
        const role = user?.role;
        const isStaff = role === "staff";
        const isTeacher = role === "teacher";
        const unreadMessages = page.props.unread?.messages ?? 0;
        const unreadNotifications = page.props.unread?.notifications ?? 0;
        const unreadAnnouncements = page.props.unread?.announcements ?? 0;

        const items = [
            {
                name: "Dashboard",
                href: route("dashboard"),
                active: route().current("dashboard"),
                icon: getMenuIcon("Dashboard"),
            },
        ];

        if (role === "student") {
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

        items.push({
            name: "Settings",
            href: route("settings.index"),
            active: route().current("settings.*"),
            icon: getMenuIcon("Settings"),
        });

        return items;
    });

    const userRoleMeta = computed(() => {
        const role = page.props.auth?.user?.role;
        return ROLE_META[role] || ROLE_META.default;
    });

    const headerStatus = computed(() => userRoleMeta.value.headerStatus);

    return {
        navigation,
        headerStatus,
        userRoleMeta,
    };
}
