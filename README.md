# ScanToAttend-web

Web components for ScanToAttend, an IoT-based attendance recording system using fingerprint scanner. This is in partial submission for the final project of CS 397: Internet of Everything, Paragon International University, AY 2025-2026.

The project aims to build a fingerprint-based attendance recording system that can integrate into existings ERP systems, specifically that of Paragon International University. The structure of the web backend tries to mimic the environment of the university structure, with multiple class sections for each course and multiple sessions for each class. The attendance is recorded on a session by session basis.

The backend automatically checks for the session the student is attending when they scan to check-in, prevent overlapping sessions for students and instructors, and aggregate attendance record for many view types.

The frontend provides a human-friendly way to view attendance records and manage the system.

ESP code can be found at [longmanngithub/Scan2Attend-esp](https://github.com/longmanngithub/Scan2Attend-esp)
